<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Constants\BranchType;
use App\Constants\Message;
use App\Constants\StockTransaction;
use App\Constants\StockTransferStatus;
use App\Constants\StockType;

use App\Models\Branch;
use App\Models\Product;
use App\Models\PurchaseLine;
use App\Models\Transaction;
use App\Models\TransactionSellLinesPurchaseLines;
use App\Models\VariantionLocationDetails;

use App\Http\Requests\TransferRequest;

use App\Traits\ProductUtil;
use App\Traits\TransactionUtil;

use Auth;
use DB;
use \Carbon\Carbon;

class TransferController extends Controller
{
  use ProductUtil;

  use TransactionUtil;

  /** @var string Folder name to store transfer document */
  private $documentFolder = 'transfer';

  /**
   * Display a listing of stock transfers.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function index(Request $request)
  {
    if(!Auth::user()->can('stock.transfer.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }

    $transfers = Transaction::where('transactions.type', 'sell_transfer');
    $transfers->join('branches AS l1', 'transactions.location_id', '=', 'l1.id');
    $transfers->join('transactions as t2', 't2.transfer_parent_id', '=', 'transactions.id');
    $transfers->join('branches AS l2', 't2.location_id', '=', 'l2.id');
    $transfers->select(
      'transactions.*',
      'l1.location as location_from',
      'l2.location as location_to'
    );

    $itemCount = $transfers->count();
    $transfers = $transfers->sortable()->latest()->paginate(paginationCount());
    $offset = offset($request->page);

    return view('transfer.index', compact('itemCount', 'offset', 'transfers'));
  }

  /**
   * Show form to create stock transfer.
   *
   * @param Transfer $transfer
   *
   * @return Response
   */
  public function create(Transaction $transfer)
  {
    if(!Auth::user()->can('stock.transfer')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }

    $title = trans('app.create');
    $warehouses = Branch::getAll();
    $transferStatuses = stockTransferStatuses();
    $transferredProducts = old('products') ?? []; // When form validation has error
    $products = [];

    // if (old('original_warehouse')) {
    //   $products = $this->getProductsInStock(old('original_warehouse'));
    // }

    return view('transfer.form', compact(
      'products',
      'title',
      'transfer',
      'transferredProducts',
      'transferStatuses',
      'warehouses'
    ));
  }

  /**
   * Show form to edit an existing stock transfer.
   *
   * @param Transfer $transfer
   *
   * @return Response
   */
  public function edit(Transaction $transfer)
  {
    if(!Auth::user()->can('stock.transfer')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }

    abort(404);
  }

  /**
   * Show stock transfer detail.
   *
   * @param Transfer $transfer
   *
   * @return Response
   */
  public function show($id)
  {
    if(!Auth::user()->can('stock.transfer.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }

    $transfer = Transaction::where('transactions.type', 'sell_transfer')
    ->where('transactions.id', $id)
    ->join('branches AS l1', 'transactions.location_id', '=', 'l1.id')
    ->join('transactions as t2', 't2.transfer_parent_id', '=', 'transactions.id')
    ->join('branches AS l2', 't2.location_id', '=', 'l2.id')
    ->select('transactions.*', 'l1.location as location_from', 'l2.location as location_to','l2.id as location_to_id')
    ->first();
    // dd($stock_transfer);

    return view('transfer.show', compact('transfer'));
  }

  /**
   * Save new stock transfer.
   *
   * @param TransferRequest $request
   *
   * @return Response
   */
  public function save(TransferRequest $request)
  {
    if(!Auth::user()->can('stock.transfer')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }
    
    try {
      DB::beginTransaction();

      // TRANSACTION DATA
      $input_data['final_total']      = 0;
      $input_data['total_before_tax'] = $input_data['final_total'];
      $input_data['type']             = 'sell_transfer';
      $input_data['ref_no']           = $request->ref_no;
      $input_data['location_id']      = $request->original_warehouse;
      $input_data['created_by']       = auth()->user()->id;
      $input_data['transaction_date'] = Carbon::parse($request->transaction_date)->toDateTimeString();
      $input_data['shipping_charges'] = $request->shipping_charges;
      $input_data['additional_notes'] = $request->note;
      $input_data['status']           = $request->status;
      $input_data['payment_status']   = 'paid';

      //Update reference count
      $ref_count = $this->setAndGetReferenceCount('stock_transfer');
      //Generate reference number
      if (empty($input_data['ref_no'])) {
        $input_data['ref_no'] = $this->generateReferenceNumber('stock_transfer', $ref_count);
      }

      $products = $request->input('products');
      $sell_lines = [];
      $purchase_lines = [];
      // dd($products);

      if (!empty($products)) {
        foreach ($products as $product) {
          $sell_line_arr = [
            'product_id'    => $product['id'],
            'variantion_id' => $product['variantion_id'],
            'quantity'      => $product['quantity'],
          ];

          $purchase_line_arr = $sell_line_arr;
          $sell_line_arr['unit_price'] = 0;

          $purchase_line_arr['purchase_price'] = $sell_line_arr['unit_price'];

          if (!empty($product['lot_no_line_id'])) {
            //Add lot_no_line_id to sell line
            $sell_line_arr['lot_no_line_id'] = $product['lot_no_line_id'];

            //Copy lot number and expiry date to purchase line
            $lot_details = PurchaseLine::find($product['lot_no_line_id']);
            $purchase_line_arr['lot_number'] = $lot_details->lot_number;
            $purchase_line_arr['mfg_date'] = $lot_details->mfg_date;
            $purchase_line_arr['exp_date'] = $lot_details->exp_date;
          }

          $sell_lines[] = $sell_line_arr;
          $purchase_lines[] = $purchase_line_arr;
        }
      }

      //Create Sell Transfer transaction
      $sell_transfer = Transaction::create($input_data);

      //Create Purchase Transfer at transfer location
      $input_data['type']               = 'purchase_transfer';
      $input_data['status']             = 'received';
      $input_data['location_id']        = $request->target_warehouse;
      $input_data['transfer_parent_id'] = $sell_transfer->id;

      $purchase_transfer = Transaction::create($input_data);

      //Sell Product from first location
      if (!empty($sell_lines)) {
        $this->createOrUpdateSellLines($sell_transfer, $products, $request->original_warehouse);
      }

      //Purchase product in second location
      if (!empty($purchase_lines)) {
        $purchase_transfer->purchase_lines()->createMany($purchase_lines);
      }

      // Update stock
      if (!empty($products)) {
        foreach ($products as $product) {

          // Decrement target_warehouse stock
          VariantionLocationDetails::where('variantion_id', $product['variantion_id'])
            ->where('product_id', $product['id'])
            ->where('location_id', $request->original_warehouse)
            ->decrement('qty_available', $product['quantity']);

          // Increment target_warehouse stock
          VariantionLocationDetails::where('variantion_id', $product['variantion_id'])
            ->where('product_id', $product['id'])
            ->where('location_id', $request->target_warehouse)
            ->increment('qty_available', $product['quantity']);
        }
      }

      DB::commit();
    } 
    catch (\Exception $e) {
      DB::rollBack();
      
      \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
          
      session()->flash(Message::SAVE_FAILURE_VALUE, trans('message.item_saved_fail'));
      return redirect()->back()->withInput()->withErrors([
        Message::ERROR_KEY => $e->getMessage(),
      ]);
    }

    session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    return redirect(route('transfer.index'));
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id, Request $request)
  {
    if(!Auth::user()->can('po.delete')) {
      abort(404);
    }
    if (!$request->ajax()) {
      abort(404);
    }

    try {
      //Get sell transfer transaction
      $sell_transfer = Transaction::where('id', $id)->where('type', 'sell_transfer')->with(['sell_lines'])->first();

      //Get purchase transfer transaction
      $purchase_transfer = Transaction::where('transfer_parent_id', $sell_transfer->id)->where('type', 'purchase_transfer')->with(['purchase_lines'])->first();

      //Check if any transfer stock is deleted and delete purchase lines
      $purchase_lines = $purchase_transfer->purchase_lines;
      foreach ($purchase_lines as $purchase_line) {
        if ($purchase_line->quantity_sold > 0) {
          return [ 
            'success' => 0,
            'msg' => __('message.unable_perform_action')
          ];
        }
      }

      //Begin transaction
      DB::beginTransaction();

      //Get purchase lines from transaction_sell_lines_purchase_lines and decrease quantity_sold
      $sell_lines = $sell_transfer->sell_lines;
      $deleted_sell_purchase_ids = [];
      $products = []; //variantion_id as array

      foreach ($sell_lines as $sell_line) {
        $purchase_sell_line = TransactionSellLinesPurchaseLines::where('sell_line_id', $sell_line->id)->first();

        if(!empty($purchase_sell_line)) {
          //Decrease quntity sold from purchase line
          PurchaseLine::where('id', $purchase_sell_line->purchase_line_id)->decrement('quantity_sold', $sell_line->quantity);

          $deleted_sell_purchase_ids[] = $purchase_sell_line->id;

          //variation details
          if (isset($products[$sell_line->variantion_id])) {
            $products[$sell_line->variantion_id]['quantity'] += $sell_line->quantity;
            $products[$sell_line->variantion_id]['product_id'] = $sell_line->product_id;
          } 
          else {
            $products[$sell_line->variantion_id]['quantity'] = $sell_line->quantity;
            $products[$sell_line->variantion_id]['product_id'] = $sell_line->product_id;
          }
        }
      }

      //Update quantity available in both location
      if (!empty($products)) {
        foreach ($products as $key => $value) {
          //Decrease from location 2
          $this->decreaseProductQuantity(
            $products[$key]['product_id'],
            $key,
            $purchase_transfer->location_id,
            $products[$key]['quantity']
          );

          //Increase in location 1
          $this->updateProductQuantity(
            $sell_transfer->location_id,
            $products[$key]['product_id'],
            $key,
            $products[$key]['quantity']
          );
        }
      }

      //Delete sale line purchase line
      if (!empty($deleted_sell_purchase_ids)) {
        TransactionSellLinesPurchaseLines::whereIn('id', $deleted_sell_purchase_ids)->delete();
      }

      //Delete both transactions
      $sell_transfer->delete();
      $purchase_transfer->delete();

      DB::commit();
    } 
    catch(\Exception $e) {
      DB::rollBack();

      \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

      session()->flash(Message::SAVE_FAILURE_VALUE, trans('message.item_saved_fail'));
      return redirect()->back()->withInput()->withErrors([
        Message::ERROR_KEY => trans('message.item_saved_fail'),
      ]);
    }

    DB::commit();
    session()->flash(Message::SUCCESS_KEY, trans('message.item_deleted_success'));
  }

  /**
   * Get all products in a specific warehouse.
   *
   * @param int $warehouseId
   *
   * @return Product|array Product data object or empty array
   */
  private function getProductsInStock($warehouseId)
  {
    $products = [];
    $productsInStock = ProductWarehouse::where('warehouse_id', $warehouseId)->get()->toArray();

    if (count($productsInStock) > 0) {
      $products = array_map(function ($value) {
        $product = Product::where('id', $value['product_id'])->first();
        $product->stock_qty = $value['quantity'];
        return $product;
      }, $productsInStock);
    }

    return $products;
  }

  /**
   * Get products by warehouse through AJAX request.
   *
   * @param Request $request
   * @param int $warehouseId
   *
   * @return Json
   */
  public function getProducts(Request $request, $warehouseId)
  {
    if (!$request->ajax()) {
      abort(404);
    }

    $products = $this->getProductsInStock($warehouseId);
    return response()->json(['products' => $products]);
  }
}