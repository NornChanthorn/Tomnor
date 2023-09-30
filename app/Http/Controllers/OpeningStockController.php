<?php

namespace App\Http\Controllers;

use App\Constants\UserRole;
use Illuminate\Http\Request;
use Auth;

use App\Constants\FormType;
use App\Constants\Message;
use App\Models\ExtendedProperty;
use App\Models\Product;
use App\Models\ProductWarehouse;
use App\Models\Branch;
use App\Models\Transaction;
use App\Models\TransactionSellLinesPurchaseLines;
use App\Models\PurchaseLine;

use App\Http\Requests\ProductRequest;
use App\Traits\FileHandling;
use Illuminate\Support\Facades\DB;
use \Carbon\Carbon;

use App\Traits\ProductUtil;
use App\Traits\TransactionUtil;

class OpeningStockController extends Controller
{

  use ProductUtil;
  use TransactionUtil;

  /**
   * Constructor
   *
   * @param ProductUtils $product
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function add($product_id)
  {
    //Get the product
    $product = Product::where('id', $product_id)->with(['variations', 'unit'])->first();

    if (!empty($product)) {
      //Get Opening Stock Transactions for the product if exists
      $transactions = Transaction::where('opening_stock_product_id', $product_id)->where('type', 'opening_stock')->with(['purchase_lines'])->get();
        
      $purchases = [];
      foreach ($transactions as $transaction) {
        $purchase_lines = [];

        foreach ($transaction->purchase_lines as $purchase_line) {
          if (!empty($purchase_lines[$purchase_line->variantion_id])) {
            $k = count($purchase_lines[$purchase_line->variantion_id]);
          } 
          else {
            $k = 0;
            $purchase_lines[$purchase_line->variantion_id] = [];
          }

          //Show only remaining quantity for editing opening stock.
          $purchase_lines[$purchase_line->variantion_id][$k]['quantity'] = $purchase_line->quantity_remaining;
          $purchase_lines[$purchase_line->variantion_id][$k]['purchase_price'] = $purchase_line->purchase_price;
          $purchase_lines[$purchase_line->variantion_id][$k]['purchase_line_id'] = $purchase_line->id;
          $purchase_lines[$purchase_line->variantion_id][$k]['exp_date'] = $purchase_line->exp_date;
          $purchase_lines[$purchase_line->variantion_id][$k]['lot_number'] = $purchase_line->lot_number;
        }
        $purchases[$transaction->location_id] = $purchase_lines;
      }
      // dd($purchases);

      $locations = Branch::get()->pluck('location', 'id');

      $enable_expiry = request()->session()->get('business.enable_product_expiry');
      $enable_lot = request()->session()->get('business.enable_lot_number');

      if (request()->ajax()) {
        return view('opening_stock.ajax_add')->with(compact('product', 'locations', 'purchases', 'enable_expiry', 'enable_lot'));
      }

      return view('opening_stock.add')->with(compact('product', 'locations', 'purchases', 'enable_expiry', 'enable_lot'));
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function save(Request $request)
  {
    try {
      $opening_stocks = $request->input('stocks');
      $product_id = $request->input('product_id');
      $user_id = Auth::id();

      $product = Product::where('id', $product_id)->with(['variations'])->first();

      $locations = Branch::pluck('id')->toArray();

      if(!empty($product)) {
        $transaction_date = Carbon::now()->toDateTimeString();
        // dd($locations);

        DB::beginTransaction();
        //$key is the location_id
        foreach($opening_stocks as $key_os => $value) {
          $location_id = $key_os;
          $purchase_total = 0;

          //Check if valid location
          if(in_array($location_id, $locations)) {
            $purchase_lines = [];
            $updated_purchase_line_ids = [];

            //create purchase_lines array
            //$k is the variation id
            foreach($value as $k => $rows) {
              foreach($rows as $key => $v) {
                $purchase_price = !empty($v['purchase_price']) ? trim($v['purchase_price']) : 0;
                $qty_remaining = !empty($v['quantity']) ? trim($v['quantity']) : 0;
                $purchase_price_inc_tax = $purchase_price;

                $exp_date = null;
                if(!empty($v['exp_date'])) {
                  $exp_date = $v['exp_date'];
                }

                $lot_number = null;
                if(!empty($v['lot_number'])) {
                  $lot_number = $v['lot_number'];
                }

                $purchase_line = null;
                if(isset($v['purchase_line_id'])) {
                  $purchase_line = PurchaseLine::findOrFail($v['purchase_line_id']);
                  //Quantity = remaining + used
                  $qty_remaining = $qty_remaining + $purchase_line->quantity_used;

                  if($qty_remaining != 0) {
                    //Calculate transaction total
                    $purchase_total += ($purchase_price_inc_tax * $qty_remaining);

                    $updated_purchase_line_ids[] = $purchase_line->id;

                    $old_qty = $purchase_line->quantity;

                    $this->updateProductQuantity($location_id, $product->id, $k, $qty_remaining, $old_qty, null, false);
                  }
                } 
                else {
                  if ($qty_remaining != 0) {
                    //create newly added purchase lines
                    $purchase_line = new PurchaseLine();
                    $purchase_line->product_id = $product->id;
                    $purchase_line->variantion_id = $k;

                    $this->updateProductQuantity($location_id, $product->id, $k, $qty_remaining, 0, null, false);

                    //Calculate transaction total
                    $purchase_total += ($purchase_price_inc_tax * $qty_remaining);
                  }
                }

                if (!is_null($purchase_line)) {
                  // $purchase_line->item_tax = $item_tax;
                  // $purchase_line->tax_id = $tax_id;
                  $purchase_line->quantity                = $qty_remaining;
                  $purchase_line->pp_without_discount     = $purchase_price;
                  $purchase_line->purchase_price          = $purchase_price;
                  $purchase_line->purchase_price_inc_tax  = $purchase_price_inc_tax;
                  $purchase_line->exp_date                = $exp_date;
                  $purchase_line->lot_number              = $lot_number;

                  $purchase_lines[] = $purchase_line;
                }
              }
            }

            //create transaction & purchase lines
            if (!empty($purchase_lines)) {
              $is_new_transaction = false;

              $transaction = Transaction::where('type', 'opening_stock')
              ->where('opening_stock_product_id', $product->id)
              ->where('location_id', $location_id)
              ->first();
              if (!empty($transaction)) {
                $transaction->total_before_tax = $purchase_total;
                $transaction->final_total = $purchase_total;
                $transaction->update();
              } 
              else {
                $is_new_transaction = true;

                $transaction = Transaction::create([
                  'type'                      => 'opening_stock',
                  'opening_stock_product_id'  => $product->id,
                  'status'                    => 'received',
                  // 'business_id'            => $business_id,
                  'transaction_date'          => $transaction_date,
                  'total_before_tax'          => $purchase_total,
                  'location_id'               => $location_id,
                  'final_total'               => $purchase_total,
                  'payment_status'            => 'paid',
                  'created_by'                => $user_id
                ]);
              }

              //unset deleted purchase lines
              $delete_purchase_line_ids = [];
              $delete_purchase_lines = null;
              $delete_purchase_lines = PurchaseLine::where('transaction_id', $transaction->id)
              ->whereNotIn('id', $updated_purchase_line_ids)
              ->get();

              if ($delete_purchase_lines->count()) {
                foreach ($delete_purchase_lines as $delete_purchase_line) {
                  $delete_purchase_line_ids[] = $delete_purchase_line->id;

                  //decrease deleted only if previous status was received
                  $this->decreaseProductQuantity($delete_purchase_line->product_id, $delete_purchase_line->variation_id, $transaction->location_id, $delete_purchase_line->quantity);
                }

                //Delete deleted purchase lines
                PurchaseLine::where('transaction_id', $transaction->id)
                ->whereIn('id', $delete_purchase_line_ids)
                ->delete();
              }
              $transaction->purchase_lines()->saveMany($purchase_lines);

              //Update mapping of purchase & Sell.
              if (!$is_new_transaction) {
                $this->adjustMappingPurchaseSellAfterEditingPurchase('received', $transaction, $delete_purchase_lines);
              }

              //Adjust stock over selling if found
              $this->adjustStockOverSelling($transaction);
            } 
            else {
              //Delete transaction if all purchase line quantity is 0 (Only if transaction exists)
              $delete_transaction = Transaction::where('type', 'opening_stock')
              ->where('opening_stock_product_id', $product->id)
              ->where('location_id', $location_id)
              ->with(['purchase_lines'])
              ->first();
              
              if (!empty($delete_transaction)) {
                $delete_purchase_lines = $delete_transaction->purchase_lines;

                foreach ($delete_purchase_lines as $delete_purchase_line) {
                  $this->decreaseProductQuantity($product->id, $delete_purchase_line->variation_id, $location_id, $delete_purchase_line->quantity);
                  $delete_purchase_line->delete();
                }

                //Update mapping of purchase & Sell.
                $this->adjustMappingPurchaseSellAfterEditingPurchase('received', $delete_transaction, $delete_purchase_lines);

                $delete_transaction->delete();
              }
            }
          }
        }
        DB::commit();
      }

      $output = [
        'success' => 1,
        'msg' => __('lang_v1.opening_stock_added_successfully')
      ];
    } 
    catch (\Exception $e) {
      DB::rollBack();
      \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
      
      $output = [
        'success' => 0,
        'msg' => $e->getMessage()
      ];
      return back()->with('status', $output);
    }

    if (request()->ajax()) {
      return $output;
    }

    session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
    return redirect('product')->with('status', $output);
  }
}