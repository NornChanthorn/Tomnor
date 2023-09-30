<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Constants\Message;
use App\Constants\StockType;

use App\Models\Adjustment;
use App\Models\Branch;
use App\Models\Transaction;
use App\Models\ProductWarehouse;
use App\Http\Requests\AdjustmentRequest;

use Auth;
use DB;
use \Carbon\Carbon;
use App\Traits\ProductUtil;
use App\Traits\TransactionUtil;

class AdjustmentController extends Controller
{

  use TransactionUtil;

  use ProductUtil;

  /** @var string Folder name to store adjustment document */
  private $documentFolder = 'adjustment';

  /**
   * Display a listing of stock adjustments.
   *
   * @param Request $request
   *
   * @return Response
   */
  public function index(Request $request)
  {
    if(!Auth::user()->can('stock.adjust.browse')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }
    $adjustments = Transaction::where('type', 'stock_adjustment');

    $itemCount = $adjustments->count();
    $adjustments = $adjustments->sortable()->latest()->paginate(paginationCount());
    $offset = offset($request->page);

    return view('adjustment.index', compact('adjustments', 'itemCount', 'offset'));
  }

  /**
   * Show form to create stock adjustment.
   *
   * @param Adjustment $adjustment
   *
   * @return Response
   */
  public function create(Adjustment $adjustment)
  {
    if(!Auth::user()->can('stock.adjust')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }

    $title = trans('app.create');
    $stockTypes = stockTypes();
    $warehouses = Branch::getAll();
    // $products = Product::getAll();

    // if (old('warehouse') && old('product')) {
    //   $productStockQty = (ProductWarehouse::selectQuery(old('warehouse'), old('product'))->first()->quantity ?? 0);
    // } 
    // else {
    //   $productStockQty = trans('app.n/a');
    // }
    $productStockQty = trans('app.n/a');

    return view('adjustment.form', compact(
      // 'products',
      'productStockQty',
      'stockTypes',
      'title',
      'warehouses'
    ));
  }

  /**
   * Show form to edit an existing stock adjustment.
   *
   * @param Adjustment $adjustment
   *
   * @return Response
   */
  public function edit(Adjustment $adjustment)
  {
    abort(404);
  }

  /**
   * Save new stock adjustment.
   *
   * @param AdjustmentRequest $request
   *
   * @return Response
   */
  public function save(AdjustmentRequest $request)
  {
    if(!Auth::user()->can('stock.adjust')) {
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'), 
        'alert-type' => 'warning'
      ], 403);
    }

    $products = $request->products;
    $isStockOut = ($request->action == StockType::STOCK_OUT);
    // $inStockProduct = ProductWarehouse::selectQuery($request->warehouse, $request->product)->first();

    // Check if quantity of stock-out adjustment is larger than in-stock quantity
    if ($isStockOut) {
      session()->flash(Message::SAVE_FAILURE_VALUE, trans('message.product_qty_lte_stock_qty'));
      return back()->withInput()->withErrors([
        Message::ERROR_KEY => trans('message.product_qty_lte_stock_qty'),
      ]);
    }

    try {
      DB::beginTransaction();

      $input_data['ref_no']           = $request->ref_no;
      $input_data['location_id']      = $request->warehouse;
      $input_data['type']             = 'stock_adjustment';
      $input_data['transaction_date'] = Carbon::parse($request->adjustment_date)->toDateTimeString();
      $input_data['created_by']       = auth()->user()->id;
      // $input_data['total_amount_recovered'] = $this->productUtil->num_uf($input_data['total_amount_recovered']);
      $input_data['additional_notes'] = $request->reason;

      //Update reference count
      $ref_count = $this->setAndGetReferenceCount('stock_adjustment');
      //Generate reference number
      if (empty($input_data['ref_no'])) {
        $input_data['ref_no'] = $this->generateReferenceNumber('stock_adjustment', $ref_count);
      }

      if(!empty($products)) {
        $productData = [];
        foreach($products as $product) {
          $adjustment_type = $product['action']=='stock_in' ? '' : '-';

          $adjustment_line = [
            'product_id'    => $product['id'],
            'variantion_id' => $product['variantion_id'],
            'quantity'      => $product['quantity'],
            'type'          => $product['action'],
          ];
          if (!empty($product['lot_no_line_id'])) {
            //Add lot_no_line_id to stock adjustment line
            $adjustment_line['lot_no_line_id'] = $product['lot_no_line_id'];
          }
          $productData[] = $adjustment_line;

          //Decrease available quantity
          $this->updateProductQuantity(
            $input_data['location_id'],
            $product['id'],
            $product['variantion_id'],
            ($adjustment_type.$product['quantity'])
          );
        }
        $stock_adjustment = Transaction::create($input_data);
        $stock_adjustment->stock_adjustment_lines()->createMany($productData);

        //Map Stock adjustment & Purchase.
        // $business = [
        //   'location_id' => $input_data['location_id']
        // ];
        // $this->mapPurchaseSell($business, $stock_adjustment->stock_adjustment_lines, 'stock_adjustment');
      }

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

    session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    return redirect(route('adjustment.index'));
  }

  /**
   * Delete Stock Adjustment.
   *
   * @param Adjustmentn $adjustment
   *
   * @return Response
   */
  public function destroy($id, Request $request)
  {
    if(!Auth::user()->can('stock.adjust')) {
      session()->flash(Message::ERROR_KEY, trans('message.no_permission'));
      return back()->with([
        Message::ERROR_KEY => trans('message.no_permission'),
        'alert-type' => 'warning'
      ], 403);
    }

    if (!$request->ajax()) {
      abort(404);
    }

    try {
      //Begin transaction
      DB::beginTransaction();

      $stock_adjustment = Transaction::where('id', $id)->where('type', 'stock_adjustment')->with(['stock_adjustment_lines'])->first();

      //Add deleted product quantity to available quantity
      $stock_adjustment_lines = $stock_adjustment->stock_adjustment_lines;
      if(!empty($stock_adjustment_lines)) {
        $line_ids = [];
        foreach ($stock_adjustment_lines as $stock_adjustment_line) {
          $this->updateProductQuantity(
            $stock_adjustment->location_id,
            $stock_adjustment_line->product_id,
            $stock_adjustment_line->variantion_id,
            ($stock_adjustment_line->type==StockType::STOCK_IN ? '-' : '').$stock_adjustment_line->quantity
          );
          $line_ids[] = $stock_adjustment_line->id;
        }

        $this->mapPurchaseQuantityForDeleteStockAdjustment($line_ids);
      }

      $stock_adjustment->delete();

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
    
    session()->flash(Message::SUCCESS_KEY, trans('message.item_deleted_success'));
  }

  /**
   * Get stock quantity of a product in a warehouse.
   *
   * @param Request $request
   * @param int $warehouseId
   * @param int $productId
   *
   * @return int|string
   */
  public function getStockQuantity(Request $request, $warehouseId, $productId)
  {
    if (!$request->ajax()) {
      abort(404);
    }

    $stockQty = (ProductWarehouse::selectQuery($warehouseId, $productId)->first()->quantity ?? 0);
    return $stockQty;
  }
}
