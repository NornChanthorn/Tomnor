<?php

namespace App\Traits;

use App\Models\Loan;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Variantion;
use App\Models\VariantionLocationDetails;
use App\Models\PurchaseLine;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Models\TransactionSellLine;
use App\Models\TransactionSellLinesPurchaseLines;

use App\Models\Invoice;
use App\Models\InvoiceScheme;

use DB;
use \Carbon\Carbon;
use App\Exceptions\PurchaseSellMismatch;

use App\Models\ProductUtil;
use App\Events\InvoiceAdded;
use App\Events\InvoiceDeleted;
trait TransactionUtil
{
  // use ProductUtil;
  /**
   * Add Sell transaction
   *
   * @param int $business_id
   * @param array $input
   * @param float $invoice_total
   * @param int $user_id
   *
   * @return boolean
   */
  public function createSellTransaction($business_id, $input, $invoice_total, $user_id, $uf_data = true)
  {
    $invoice_no = !empty($input['invoice_no']) ? $input['invoice_no'] : $this->getInvoiceNumber($business_id, $input['status'], $input['location_id']);
    
    $transaction = Transaction::create([
      'location_id' => $input['location_id'],
      'type' => 'sell',
      'status' => $input['status'],
      'contact_id' => $input['contact_id'],
      'customer_group_id' => $input['customer_group_id'],
      'invoice_no' => $invoice_no,
      'ref_no' => '',
      'total_before_tax' => $invoice_total['total_before_tax'],
      'transaction_date' => $input['transaction_date'],
      'tax_id' => $input['tax_rate_id'],
      'discount_type' => $input['discount_type'],
      'discount_amount' => $uf_data ? $this->num_uf($input['discount_amount']) : $input['discount_amount'],
      'tax_amount' => $invoice_total['tax'],
      'final_total' => $uf_data ? $this->num_uf($input['final_total']) : $input['final_total'],
      'additional_notes' => !empty($input['sale_note']) ? $input['sale_note'] : null,
      'staff_note' => !empty($input['staff_note']) ? $input['staff_note'] : null,
      'created_by' => $user_id,
      'is_direct_sale' => !empty($input['is_direct_sale']) ? $input['is_direct_sale'] : 0,
      'commission_agent' => $input['commission_agent'],
      'is_quotation' => isset($input['is_quotation']) ? $input['is_quotation'] : 0,
      'shipping_details' => isset($input['shipping_details']) ? $input['shipping_details'] : null,
      'shipping_charges' => isset($input['shipping_charges']) ? $uf_data ? $this->num_uf($input['shipping_charges']) : $input['shipping_charges'] : 0,
      'others_charges' => isset($input['others_charges']) ? $uf_data ? $this->num_uf($input['others_charges']) : $input['others_charges'] : 0,
      'exchange_rate' => !empty($input['exchange_rate']) ? $uf_data ? $this->num_uf($input['exchange_rate']) : $input['exchange_rate'] : 1,
      'selling_price_group_id' => isset($input['selling_price_group_id']) ? $input['selling_price_group_id'] : null,
      'pay_term_number' => isset($input['pay_term_number']) ? $input['pay_term_number'] : null,
      'pay_term_type' => isset($input['pay_term_type']) ? $input['pay_term_type'] : null,
      'is_suspend' => !empty($input['is_suspend']) ? 1 : 0,
      'is_recurring' => !empty($input['is_recurring']) ? $input['is_recurring'] : 0,
      'recur_interval' => !empty($input['recur_interval']) ? $input['recur_interval'] : null,
      'recur_interval_type' => !empty($input['recur_interval_type']) ? $input['recur_interval_type'] : null,
      'subscription_no' => !empty($input['subscription_no']) ? $input['subscription_no'] : null,
      'recur_repetitions' => !empty($input['recur_repetitions']) ? $input['recur_repetitions'] : 0,
      'order_addresses' => !empty($input['order_addresses']) ? $input['order_addresses'] : null,
      'sub_type' => !empty($input['sub_type']) ? $input['sub_type'] : null
    ]);

    return $transaction;
  }

  /**
   * Gives the invoice number for a Final/Draft invoice
   *
   * @param int $business_id
   * @param string $status
   * @param string $location_id
   *
   * @return string
   */
  public function getInvoiceNumber($business_id='', $status, $location_id)
  {
    if ($status == 'final') {
      $scheme = $this->getInvoiceScheme($business_id, $location_id);
      
      if ($scheme->scheme_type == 'blank') {
        $prefix = $scheme->prefix;
      } 
      else {
        $prefix = date('Y') . '-';
      }

      //Count
      $count = $scheme->start_number + $scheme->invoice_count;
      $count = str_pad($count, $scheme->total_digits, '0', STR_PAD_LEFT);

      //Prefix + count
      $invoice_no = $prefix . $count;

      //Increment the invoice count
      $scheme->invoice_count = $scheme->invoice_count + 1;
      $scheme->save();

      return $invoice_no;
    } 
    else {
      return str_random(5);
    }
  }

  private function getInvoiceScheme($business_id, $location_id)
  {
    return $scheme = InvoiceScheme::where('is_default', 1)->first();
  }

  /**
   * Adjust the existing mapping between purchase & sell on edit of
   * purchase
   *
   * @param  string $before_status
   * @param  object $transaction
   * @param  object $delete_purchase_lines
   *
   * @return void
   */
  public function adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, $delete_purchase_lines)
  {
    if ($before_status == 'received' && $transaction->status == 'received') {
      //Check if there is some irregularities between purchase & sell and make appropiate adjustment.

      //Get all purchase line having irregularities.
      $purchase_lines = Transaction::join('purchase_lines AS PL', 'transactions.id', '=', 'PL.transaction_id')
      ->join('transaction_sell_lines_purchase_lines AS TSPL', 'PL.id', '=', 'TSPL.purchase_line_id')
      ->groupBy('TSPL.purchase_line_id')
      ->where('transactions.id', $transaction->id)
      ->havingRaw('SUM(TSPL.quantity) > MAX(PL.quantity)')
      ->select([
        'TSPL.purchase_line_id AS id', DB::raw('SUM(TSPL.quantity) AS tspl_quantity'), DB::raw('MAX(PL.quantity) AS pl_quantity')
      ])
      ->get()->toArray();
    } 
    elseif ($before_status == 'received' && $transaction->status != 'received') {
      //Delete sell for those & add new sell or throw error.
      $purchase_lines = Transaction::join('purchase_lines AS PL','transactions.id', '=', 'PL.transaction_id')
      ->join('transaction_sell_lines_purchase_lines AS TSPL', 'PL.id', '=', 'TSPL.purchase_line_id')
      ->groupBy('TSPL.purchase_line_id')
      ->where('transactions.id', $transaction->id)
      ->select([
        'TSPL.purchase_line_id AS id', DB::raw('MAX(PL.quantity) AS pl_quantity')
      ])
      ->get()->toArray();
    } 
    else {
      return true;
    }

    //Get detail of purchase lines deleted
    if (!empty($delete_purchase_lines)) {
      $purchase_lines = $delete_purchase_lines->toArray() + $purchase_lines;
    }

    //All sell lines & Stock adjustment lines.
    $sell_lines = [];
    $stock_adjustment_lines = [];
    foreach ($purchase_lines as $purchase_line) {
      $tspl_quantity = isset($purchase_line['tspl_quantity']) ? $purchase_line['tspl_quantity'] : 0;
      $pl_quantity = isset($purchase_line['pl_quantity']) ? $purchase_line['pl_quantity'] : $purchase_line['quantity'];

      $extra_sold = abs($tspl_quantity - $pl_quantity);

      //Decrease the quantity from transaction_sell_lines_purchase_lines or delete it if zero
      $tspl = TransactionSellLinesPurchaseLines::where('purchase_line_id', $purchase_line['id'])
      ->leftjoin('transaction_sell_lines AS SL', 'transaction_sell_lines_purchase_lines.sell_line_id', '=', 'SL.id')
      ->leftjoin('stock_adjustment_lines AS SAL', 'transaction_sell_lines_purchase_lines.stock_adjustment_line_id', '=', 'SAL.id')
      ->orderBy('transaction_sell_lines_purchase_lines.id', 'desc')
      ->select([
        'SL.product_id AS sell_product_id', 'SL.variantion_id AS sell_variation_id', 'SL.id AS sell_line_id', 'SAL.product_id AS adjust_product_id', 'SAL.variantion_id AS adjust_variation_id', 'SAL.id AS adjust_line_id', 'transaction_sell_lines_purchase_lines.quantity', 'transaction_sell_lines_purchase_lines.purchase_line_id', 'transaction_sell_lines_purchase_lines.id as tslpl_id'
      ])->get();

      foreach ($tspl as $row) {
        if ($row->quantity <= $extra_sold) {
          if (!empty($row->sell_line_id)) {
            $sell_lines[] = (object)[
              'id'            => $row->sell_line_id,
              'quantity'      => $row->quantity,
              'product_id'    => $row->sell_product_id,
              'variantion_id' => $row->sell_variation_id,
            ];
            PurchaseLine::where('id', $row->purchase_line_id)->decrement('quantity_sold', $row->quantity);
          } 
          else {
            $stock_adjustment_lines[] = (object)[
              'id'            => $row->adjust_line_id,
              'quantity'      => $row->quantity,
              'product_id'    => $row->adjust_product_id,
              'variantion_id' => $row->adjust_variation_id,
            ];
            PurchaseLine::where('id', $row->purchase_line_id)->decrement('quantity_adjusted', $row->quantity);
          }

          $extra_sold = $extra_sold - $row->quantity;
          TransactionSellLinesPurchaseLines::where('id', $row->tslpl_id)->delete();
        } 
        else {
          if (!empty($row->sell_line_id)) {
            $sell_lines[] = (object)[
              'id'            => $row->sell_line_id,
              'quantity'      => $extra_sold,
              'product_id'    => $row->sell_product_id,
              'variantion_id' => $row->sell_variation_id,
            ];
            PurchaseLine::where('id', $row->purchase_line_id)->decrement('quantity_sold', $extra_sold);
          } 
          else {
            $stock_adjustment_lines[] = (object)[
              'id'            => $row->adjust_line_id,
              'quantity'      => $extra_sold,
              'product_id'    => $row->adjust_product_id,
              'variantion_id' => $row->adjust_variation_id,
            ];
            PurchaseLine::where('id', $row->purchase_line_id)->decrement('quantity_adjusted', $extra_sold);
          }

          TransactionSellLinesPurchaseLines::where('id', $row->tslpl_id)->update(['quantity' => $row->quantity - $extra_sold]);
            
          $extra_sold = 0;
        }

        if ($extra_sold == 0) {
          break;
        }
      }
    }

    // $business = Business::find($transaction->business_id)->toArray();
    $business = [];
    $business['location_id'] = $transaction->location_id;

    //Allocate the sold lines to purchases.
    if (!empty($sell_lines)) {
      $sell_lines = (object)$sell_lines;
      $this->mapPurchaseSell($business, $sell_lines, 'purchase');
    }

    //Allocate the stock adjustment lines to purchases.
    if (!empty($stock_adjustment_lines)) {
      $stock_adjustment_lines = (object)$stock_adjustment_lines;
      $this->mapPurchaseSell($business, $stock_adjustment_lines, 'stock_adjustment');
    }
  }

  /**
   * Add a mapping between purchase & sell lines.
   * NOTE: Don't use request variable here, request variable don't exist while adding
   * dummybusiness via command line
   *
   * @param array $business
   * @param array $transaction_lines
   * @param string $mapping_type = purchase (purchase or stock_adjustment)
   * @param boolean $check_expiry = true
   * @param int $purchase_line_id (default: null)
   *
   * @return object
   */
  public function mapPurchaseSell($business, $transaction_lines, $mapping_type = 'purchase', $check_expiry = true, $purchase_line_id = null)
  {
    if (empty($transaction_lines)) {
      return false;
    }

    // $allow_overselling = !empty($business['pos_settings']['allow_overselling']) ? true : false;
    $allow_overselling = false;

    //Set flag to check for expired items during SELLING only.
    $stop_selling_expired = false;
    // if ($check_expiry) {
    //   if (session()->has('business') && request()->session()->get('business')['enable_product_expiry'] == 1 && request()->session()->get('business')['on_product_expiry'] == 'stop_selling') {
    //     if ($mapping_type == 'purchase') {
    //       $stop_selling_expired = true;
    //     }
    //   }
    // }

    $qty_selling = null;
    foreach ($transaction_lines as $line) {
      //Check if stock is not enabled then no need to assign purchase & sell
      $product = Product::find($line->product_id);
      if ($product->enable_stock != 1) {
        continue;
      }

      //Get purchase lines, only for products with enable stock.
      $query = Transaction::join('purchase_lines AS PL', 'transactions.id', '=', 'PL.transaction_id')
      // ->where('transactions.business_id', $business['id'])
      ->where('transactions.location_id', $business['location_id'])
      ->whereIn('transactions.type', ['purchase', 'purchase_transfer', 'opening_stock'])
      ->where('transactions.status', 'received')
      ->whereRaw('(PL.quantity_sold + PL.quantity_adjusted + PL.quantity_returned) < PL.quantity')
      ->where('PL.product_id', $line->product_id)
      ->where('PL.variantion_id', $line->variantion_id);

      //If product expiry is enabled then check for on expiry conditions
      if ($stop_selling_expired && empty($purchase_line_id)) {
        $stop_before = request()->session()->get('business')['stop_selling_before'];
        $expiry_date = Carbon::today()->addDays($stop_before)->toDateString();
        $query->whereRaw('PL.exp_date IS NULL OR PL.exp_date > ?', [$expiry_date]);
      }

      //If lot number present consider only lot number purchase line
      if (!empty($line->lot_no_line_id)) {
        $query->where('PL.id', $line->lot_no_line_id);
      }

      //If purchase_line_id is given consider only that purchase line
      if (!empty($purchase_line_id)) {
        $query->where('PL.id', $purchase_line_id);
      }

      //Sort according to LIFO or FIFO
      // if ($business['accounting_method'] == 'lifo') {
      //   $query = $query->orderBy('transaction_date', 'desc');
      // } 
      // else {
      //   $query = $query->orderBy('transaction_date', 'asc');
      // }
      $query = $query->orderBy('transaction_date', 'asc');

      $rows = $query->select(
        'PL.id as purchase_lines_id',
        DB::raw('(PL.quantity - (PL.quantity_sold + PL.quantity_adjusted +PL.quantity_returned)) AS quantity_available'),
        'PL.quantity_sold as quantity_sold',
        'PL.quantity_adjusted as quantity_adjusted',
        'PL.quantity_returned as quantity_returned',
        'transactions.invoice_no'
      )->get();

      $purchase_sell_map = [];

      //Iterate over the rows, assign the purchase line to sell lines.
      $qty_selling = $line->quantity;
      foreach ($rows as $k => $row) {
        $qty_allocated = 0;

        //Check if qty_available is more or equal
        if ($qty_selling <= $row->quantity_available) {
          $qty_allocated = $qty_selling;
          $qty_selling = 0;
        } 
        else {
          $qty_selling = $qty_selling - $row->quantity_available;
          $qty_allocated = $row->quantity_available;
        }

        //Check for sell mapping or stock adjsutment mapping
        if ($mapping_type == 'stock_adjustment') {
          //Mapping of stock adjustment
          $purchase_adjustment_map[] = [
            'stock_adjustment_line_id'  => $line->id,
            'purchase_line_id'          => $row->purchase_lines_id,
            'quantity'                  => $qty_allocated,
            'created_at'                => \Carbon::now(),
            'updated_at'                => \Carbon::now()
          ];

          //Update purchase line
          PurchaseLine::where('id', $row->purchase_lines_id)->update([
            'quantity_adjusted' => $row->quantity_adjusted + $qty_allocated
          ]);
        } 
        elseif ($mapping_type == 'purchase') {
          //Mapping of purchase
          $purchase_sell_map[] = [
            'sell_line_id'      => $line->id,
            'purchase_line_id'  => $row->purchase_lines_id,
            'quantity'          => $qty_allocated,
            'created_at'        => \Carbon::now(),
            'updated_at'        => \Carbon::now()
          ];

          //Update purchase line
          PurchaseLine::where('id', $row->purchase_lines_id)->update([
            'quantity_sold' => $row->quantity_sold + $qty_allocated
          ]);
        }

        if ($qty_selling == 0) {
          break;
        }
      }

      if (! ($qty_selling == 0 || is_null($qty_selling))) {
        //If overselling not allowed through exception else create mapping with blank purchase_line_id
        if (!$allow_overselling) {
          $variation = Variantion::find($line->variation_id);
          $mismatch_name = $product->name;
          if (!empty($variation->sub_sku)) {
            $mismatch_name .= ' ' . 'SKU: ' . $variation->sub_sku;
          }
          if (!empty($qty_selling)) {
            $mismatch_name .= ' ' . 'Quantity: ' . abs($qty_selling);
          }
          
          if ($mapping_type == 'purchase') {
            $mismatch_error = trans(
              "messages.purchase_sell_mismatch_exception",
              ['product' => $mismatch_name]
            );

            if ($stop_selling_expired) {
              $mismatch_error .= __('lang_v1.available_stock_expired');
            }
          } 
          elseif ($mapping_type == 'stock_adjustment') {
            $mismatch_error = trans(
              "messages.purchase_stock_adjustment_mismatch_exception",
              ['product' => $mismatch_name]
            );
          }

          // $business_name = optional(Business::find($business['id']))->name;
          $business_name = 'warehouse';
          $location_name = optional(Branch::find($business['location_id']))->name;
          \Log::emergency($mismatch_error . ' Business: ' . $business_name . ' Location: ' . $location_name);
          throw new PurchaseSellMismatch($mismatch_error);
        } 
        else {
          //Mapping with no purchase line
          $purchase_sell_map[] = [
            'sell_line_id'      => $line->id,
            'purchase_line_id'  => 0,
            'quantity'          => $qty_selling,
            'created_at'        => \Carbon::now(),
            'updated_at'        => \Carbon::now()
          ];
        }
      }

      //Insert the mapping
      if (!empty($purchase_adjustment_map)) {
        TransactionSellLinesPurchaseLines::insert($purchase_adjustment_map);
      }
      if (!empty($purchase_sell_map)) {
        TransactionSellLinesPurchaseLines::insert($purchase_sell_map);
      }
    }
  }

  /**
   * Decrement quantity adjusted in product line according to
   * transaction_sell_lines_purchase_lines
   * Used in delete of stock adjustment
   *
   * @param  array $line_ids
   *
   * @return boolean
   */
  public function mapPurchaseQuantityForDeleteStockAdjustment($line_ids)
  {
    if (empty($line_ids)) {
      return true;
    }

    $map_line = TransactionSellLinesPurchaseLines::whereIn('stock_adjustment_line_id', $line_ids)->orderBy('id', 'desc')->get();

    foreach ($map_line as $row) {
      PurchaseLine::where('id', $row->purchase_line_id)->decrement('quantity_adjusted', $row->quantity);
    }

    //Delete the tslp line.
    TransactionSellLinesPurchaseLines::whereIn('stock_adjustment_line_id', $line_ids)->delete();

    return true;
  }

  /**
   * Add line for payment
   *
   * @param object/int $transaction
   * @param array $payments
   *
   * @return boolean
   */
  public function createOrUpdatePaymentLines($transaction, $payments, $business_id = null, $user_id = null, $uf_data = true)
  {
    $payments_formatted = [];
    $edit_ids = [0];
    $account_transactions = [];
    
    if (!is_object($transaction)) {
      $transaction = Transaction::findOrFail($transaction);
    }

    //If status is draft don't add payment
    // if ($transaction->status == 'draft') {
    //   return true;
    // }

    $c = 0;
    $total_payment = 0;
    foreach ($payments as $payment) {
      //Check if transaction_sell_lines_id is set.
      if (!empty($payment['payment_id'])) {
        $edit_ids[] = $payment['payment_id'];
        $this->editPaymentLine($payment, $transaction, $uf_data);
      } 
      else {
        $payment_amount = $payment['amount'];
        //If amount is 0 then skip.
        if ($payment_amount > 0) {
          $prefix_type = $transaction->type;
          $total_payment = $total_payment + $payment_amount;

          //Generate reference number
          $lastInvoiceNum = Invoice::latest()->first()->invoice_number ?? 0;
          $ref_count = (int)substr($lastInvoiceNum, 4) + 1; // $this->setAndGetReferenceCount($prefix_type)
          $payment_ref_no = $this->generateReferenceNumber($prefix_type, $ref_count, 'REF', 6);

          $payment_data = [
            'type'            => $transaction->type,
            'user_id'         => empty($user_id) ? auth()->user()->id : $user_id,
            'client_id'       => $transaction->contact_id,
            'invoice_number'  => $payment_ref_no,
            'payment_amount'  => $payment_amount,
            'penalty'         => 0,
            'total'           => $payment_amount,
            'payment_method'  => $payment['method'],
            'payment_date'    => !empty($payment['paid_on']) ? $payment['paid_on'] : Carbon::now()->toDateTimeString(),
            'note'            => $payment['note'],
          ];

          $payments_formatted[] = new Invoice($payment_data);

          $c++;
        }
      }
    }

    //Delete the payment lines removed.
    if (!empty($edit_ids)) {
      $deleted_transaction_payments = $transaction->invoices()->whereNotIn('id', $edit_ids)->get();

      $transaction->invoices()->whereNotIn('id', $edit_ids)->delete();
    }

    if (!empty($payments_formatted)) {
      $transaction->invoices()->saveMany($payments_formatted);
    }

    return true;
  }

  /**
   * Edit transaction payment line
   *
   * @param array $product
   *
   * @return boolean
   */
  public function editPaymentLine($payment, $transaction = null, $uf_data = true)
  {
    if($payment['amount']>0){
      $payment_id = $payment['payment_id'];
      unset($payment['payment_id']);
      
      $payment['amount'] = $payment['amount'];
      $tp = Invoice::where('id', $payment_id)->first();
  
      $transaction_type = !empty($transaction->type) ? $transaction->type : null;
      
      $tp->update([
        'user_id'         => auth()->user()->id,
        'payment_amount'  => $payment_amount,
        'total'           => $payment_amount,
        'payment_method'  => $payment['method'],
        'note'            => $payment['note'],
        'updated_at'      => Carbon::now()->toDateTimeString(),
      ]);
    }else{
      $transaction->invoices()->whereNotIn('id', $payment['payment_id'])->delete();
    }
    

    return true;
  }

  public function deletePayment($payment_id)
  {
      $payment = Invoice::find($payment_id);
      //Update parent payment if exists
      if (!empty($payment->parent_id)) {
          $parent_payment = Invoice::find($payment->parent_id);
          $parent_payment->total -= $payment->total;

          if ($parent_payment->total <= 0) {
              $parent_payment->delete();
          } else {
              $parent_payment->save();
          }
      }
      $payment->delete();

      if(!empty($payment->transaction_id)) {
          //update payment status
          $this->updatePaymentStatus($payment->transaction_id);
          event(new InvoiceDeleted($payment));
      }
      
  }

  /**
   * Get total paid amount for a transaction
   *
   * @param int $transaction_id
   *
   * @return int
   */
  public function getTotalPaid($transaction_id)
  {
    $total_paid = Invoice::where('transaction_id', $transaction_id)
    ->select(DB::raw('SUM(IF(is_return = 0, payment_amount, payment_amount*-1)) AS total_paid'))
    ->first()
    ->total_paid;

    return $total_paid;
  }

  /**
   * Calculates the payment status and returns back.
   *
   * @param int $transaction_id
   * @param float $final_amount = null
   *
   * @return string
   */
  public function calculatePaymentStatus($transaction_id, $final_amount = null)
  {
    $total_paid = $this->getTotalPaid($transaction_id);

    if (is_null($final_amount)) {
      $final_amount = Transaction::find($transaction_id)->final_total;
    }

    $status = 'due';
    if ($final_amount <= $total_paid) {
      $status = 'paid';
    } 
    elseif ($total_paid > 0 && $final_amount > $total_paid) {
      $status = 'partial';
    }

    return $status;
  }

  /**
   * Update the payment status for purchase or sell transactions. Returns
   * the status
   *
   * @param int $transaction_id
   *
   * @return string
   */
  public function updatePaymentStatus($transaction_id, $final_amount = null)
  {
    $status = $this->calculatePaymentStatus($transaction_id, $final_amount);
    Transaction::where('id', $transaction_id)->update(['payment_status' => $status]);

    return $status;
  }

  /**
   * Add/Edit transaction sell lines
   *
   * @param object/int $transaction
   * @param array $products
   * @param array $location_id
   * @param boolean $return_deleted = false
   * @param array $extra_line_parameters = []
   *   Example: ['database_trasnaction_linekey' => 'products_line_key'];
   *
   * @return boolean/object
   */
  public function createOrUpdateSellLines($transaction, $products, $location_id, $return_deleted = false, $status_before = null, $extra_line_parameters = [], $uf_data = true)
  {
    $lines_formatted = [];
    $modifiers_array = [];
    $edit_ids = [0];
    $modifiers_formatted = [];

    foreach ($products as $product) {
      $multiplier = 1;
      if (isset($product['sub_unit_id']) && $product['sub_unit_id'] == $product['product_unit_id']) {
        unset($product['sub_unit_id']);
      }

      if (!empty($product['sub_unit_id']) && !empty($product['base_unit_multiplier'])) {
        $multiplier = $product['base_unit_multiplier'];
      }
      //Check if transaction_sell_lines_id is set.
      if (!empty($product['transaction_sell_lines_id'])) {
        $edit_ids[] = $product['transaction_sell_lines_id'];
        $this->editSellLine($product, $location_id, $status_before, $multiplier);
      } 
      else {
        //calculate unit price and unit price before discount
        $uf_unit_price = (float)$product['price'];
        $unit_price_before_discount = $uf_unit_price / $multiplier;
        $unit_price = $unit_price_before_discount;
        if (!empty($product['line_discount_type']) && $product['line_discount_amount']) {
          $discount_amount = $product['line_discount_amount'];
          if ($product['line_discount_type'] == 'fixed') {
            //Note: Consider multiplier for fixed discount amount
            $unit_price = $unit_price_before_discount - $discount_amount;
          } 
          elseif ($product['line_discount_type'] == 'percentage') {
            $unit_price = ((100 - $discount_amount) * $unit_price_before_discount) / 100;
          }
        }
        $uf_quantity = $product['quantity'];

        $line = [
          'product_id'                  => $product['id'],
          'variantion_id'               => $product['variantion_id'],
          'quantity'                    => ($uf_quantity * $multiplier),
          'unit_price_before_discount'  => $unit_price_before_discount,
          'unit_price'                  => $unit_price,
          'line_discount_type'          => !empty($product['line_discount_type']) ? $product['line_discount_type'] : null,
          'line_discount_amount'        => !empty($product['line_discount_amount']) ? $product['line_discount_amount'] : 0,
          'sell_line_note'              => !empty($product['sell_line_note']) ? $product['sell_line_note'] : '',
          'sub_unit_id'                 => !empty($product['sub_unit_id']) ? $product['sub_unit_id'] : null,
          'discount_id'                 => !empty($product['discount_id']) ? $product['discount_id'] : null,
          'res_service_staff_id'        => !empty($product['res_service_staff_id']) ? $product['res_service_staff_id'] : null,
          'res_line_order_status'       => !empty($product['res_service_staff_id']) ? 'received' : null
        ];

        foreach ($extra_line_parameters as $key => $value) {
          $line[$key] = !empty($product[$value]) ? $product[$value] : '';
        }

        $lines_formatted[] = new TransactionSellLine($line);
      }
    }

    if (!is_object($transaction)) {
      $transaction = Transaction::findOrFail($transaction);
    }

    //Delete the products removed and increment product stock.
    $deleted_lines = [];
    if (!empty($edit_ids)) {
      $deleted_lines = TransactionSellLine::where('transaction_id', $transaction->id)->whereNotIn('id', $edit_ids)->select('id')->get()->toArray();
      $this->deleteSellLines($deleted_lines, $location_id);
    }

    if (!empty($lines_formatted)) {
      $transaction->sell_lines()->saveMany($lines_formatted);
    }

    if ($return_deleted) {
      return $deleted_lines;
    }
    return true;
  }

  /**
   * Delete the products removed and increment product stock.
   *
   * @param array $transaction_line_ids
   * @param int $location_id
   *
   * @return boolean
   */
  public function deleteSellLines($transaction_line_ids, $location_id)
  {
    if (!empty($transaction_line_ids)) {
      $sell_lines = TransactionSellLine::whereIn('id', $transaction_line_ids)->get();

      //Adjust quanity
      foreach ($sell_lines as $line) {
        $this->adjustQuantity($location_id, $line->product_id, $line->variantion_id, $line->quantity);
      }

      TransactionSellLine::whereIn('id', $transaction_line_ids)->delete();
    }
  }

  /**
   * Edit transaction sell line
   *
   * @param array $product
   * @param int $location_id
   *
   * @return boolean
   */
  public function editSellLine($product, $location_id, $status_before, $multiplier = 1)
  {
    //Get the old order quantity
    $sell_line = TransactionSellLine::find($product['transaction_sell_lines_id']);

    //Adjust quanity
    if ($status_before != 'draft') {
      $difference = $sell_line->quantity - ($product['quantity'] * $multiplier);
      $this->adjustQuantity($location_id, $product['id'], $product['variantion_id'], $difference);
    }
   
    $unit_price_before_discount = $product['price'] / $multiplier;
    $unit_price = $unit_price_before_discount;
    if (!empty($product['line_discount_type']) && $product['line_discount_amount']) {
      $discount_amount = $product['line_discount_amount'];
      if ($product['line_discount_type'] == 'fixed') {
        $unit_price = $unit_price_before_discount - $discount_amount;
      } 
      elseif ($product['line_discount_type'] == 'percentage') {
        $unit_price = ((100 - $discount_amount) * $unit_price_before_discount) / 100;
      }
    }

    //Update sell lines.
    $sell_line->fill([
      'product_id'                  => $product['id'],
      'variantion_id'               => $product['variantion_id'],
      'quantity'                    => ($product['quantity'] * $multiplier),
      'unit_price_before_discount'  => $unit_price_before_discount,
      'unit_price'                  => $unit_price,
      'line_discount_type'          => !empty($product['line_discount_type']) ? $product['line_discount_type'] : null,
      'line_discount_amount'        => !empty($product['line_discount_amount']) ? $this->num_uf($product['line_discount_amount']) : 0,
      'sell_line_note'              => !empty($product['sell_line_note']) ? $product['sell_line_note'] : '',
      'sub_unit_id'                 => !empty($product['sub_unit_id']) ? $product['sub_unit_id'] : null,
      'res_service_staff_id'        => !empty($product['res_service_staff_id']) ? $product['res_service_staff_id'] : null
    ]);
    $sell_line->save();
  }

  /**
   * Adjust the quantity of product and its variation
   *
   * @param int $location_id
   * @param int $product_id
   * @param int $variation_id
   * @param float $increment_qty
   *
   * @return boolean
   */
  private function adjustQuantity($location_id, $product_id, $variation_id, $increment_qty)
  {
    if ($increment_qty != 0) {
      $product = Product::find($product_id);

      if (!empty($product) && $product->enable_stock == 1) {
        //Adjust Quantity in variations location table
        VariantionLocationDetails::where('variantion_id', $variation_id)
        ->where('product_id', $product_id)
        ->where('location_id', $location_id)
        ->increment('qty_available', $increment_qty);
      }
    }
  }

  /**
   * Calculates total stock on the given date
   *
   * @param int $business_id
   * @param string $date
   * @param int $location_id
   * @param boolean $is_opening = false
   *
   * @return float
   */
  public function getOpeningClosingStock($date, $location_id, $is_opening = false)
  {
    $query = PurchaseLine::join('transactions as purchase', 'purchase_lines.transaction_id', '=', 'purchase.id');

    //If opening
    if ($is_opening) {
      $next_day = Carbon::createFromFormat('Y-m-d', $date)->addDay()->format('Y-m-d');
      
      $query->where(function ($query) use ($date, $next_day) {
        $query->whereRaw("date(transaction_date) <= '{$date}'")
        ->orWhereRaw("date(transaction_date) = '{$next_day}' AND type='opening_stock' ");
      });
    } 
    else {
      $query->whereRaw("date(transaction_date) <= '{$date}'");
    }
                  
    $query->select(
      DB::raw("SUM(
        (purchase_lines.quantity -
        (SELECT COALESCE(SUM(tspl.quantity - tspl.qty_returned), 0) FROM 
        transaction_sell_lines_purchase_lines AS tspl
        JOIN transaction_sell_lines as tsl ON 
        tspl.sell_line_id=tsl.id 
        JOIN transactions as sale ON 
        tsl.transaction_id=sale.id 
        WHERE tspl.purchase_line_id = purchase_lines.id AND 
        date(sale.transaction_date) <= '{$date}') ) * (purchase_lines.purchase_price + 
        COALESCE(purchase_lines.item_tax, 0))
      ) as stock")
    );

    if (!empty($location_id)) {
      $query->where('purchase.location_id', $location_id);
    }

    $details = $query->first();
    return $details->stock;
  }  
  public function createOpeningBalanceTransaction($location_id, $contact_id, $amount, $created_by, $uf_data = true)
  {
    $opening_balance = $uf_data ? decimalNumber($amount) : $amount;
    $transaction = Transaction::where('contact_id',$contact_id)->where('type','opening_balance')->first();
    if(!empty($transaction)){
      $opening_balance_paid = $this->getTotalPaid($transaction->id);
      if(!empty($opening_balance_paid)){
        $opening_balance += $opening_balance_paid;
      }

      $transaction->final_total = $opening_balance;

    }else{
      $transaction =  new Transaction();
      $transaction->type = 'opening_balance';
      $transaction->payment_status = 'due';
      $transaction->status =  'final';
      $transaction->location_id = $location_id;
      $transaction->contact_id = $contact_id;
      $transaction->transaction_date = Carbon::now();
      $transaction->created_by = $created_by;
      $transaction->final_total = $opening_balance;
    }
    $transaction->save();
    return true;
      
  }
  public function payContact($request, $format_data = true)
  {

      $contact_id = $request->input('contact_id');
      $inputs = $request->only(['payment_amount', 'payment_method']);

      // if (!array_key_exists($inputs['payent_method'], paymentMethods())) {
      //     throw new \Exception("Payment method not found");
      // }
      $inputs['payment_date'] = $request->input('payment_date', Carbon::now()->toDateTimeString());
      if ($format_data) {
          $inputs['payment_date'] = Carbon::parse($inputs['payment_date'])->toDateTimeString();
          $inputs['payment_amount'] = decimalNumber($inputs['payment_amount']);
          $inputs['total'] = decimalNumber($inputs['payment_amount']);
      }
      
     
      $inputs['user_id'] = auth()->user()->id;
      $inputs['client_id'] = $contact_id;
      $inputs['type'] = 'advance';
      $contact = Contact::find($contact_id);
    
      $due_payment_type = $request->input('due_payment_type');
      if (empty($due_payment_type)) {
          $due_payment_type = $contact->type == 'supplier' ? 'purchase' : 'sell';
      }
      $prefix_type = '';
      //Generate reference number
      $lastInvoiceNum = Invoice::latest()->first()->invoice_number ?? 0;
      $ref_count = (int)substr($lastInvoiceNum, 4) + 1; // $this->setAndGetReferenceCount($prefix_type)
      $inputs['invoice_number'] = $this->generateReferenceNumber($prefix_type, $ref_count, 'REF', 6);
      $parent_payment = Invoice::create($inputs);

      $inputs['transaction_type'] = $due_payment_type;
      event(new InvoiceAdded($parent_payment, $inputs));

      //Distribute above payment among unpaid transactions
      $excess_amount = $this->payAtOnce($parent_payment, $due_payment_type);
      //Update excess amount
      if (!empty($excess_amount)) {
          $this->updateContactBalance($contact, $excess_amount);
      }

      return $parent_payment;
  }
  /**
   * Updates contact balance
   * @param obj $contact
   * @param float $amount
   * @param string $type [add, deduct]
   *
   * @return obj $recurring_invoice
   */
  function updateContactBalance($contact, $amount, $type = 'add')
  {
      if (!is_object($contact)) {
          $contact = Contact::find($contact);
      }

      if ($type == 'add') {
          $contact->balance += $amount;
      } elseif ($type == 'deduct') {
          $contact->balance -= $amount;
      }
      $contact->save();
  }
  
  /**
   * Pay contact due at once
   *
   * @param obj $parent_payment, string $type
   *
   * @return void
   */
  public function payAtOnce($parent_payment, $type)
  {
    
      //Get all unpaid transaction for the contact
      $types = ['opening_balance', $type];

      if ($type == 'purchase_return') {
          $types = [$type];
      }

      $due_transactions = Transaction::where('contact_id', $parent_payment->client_id)
                              ->whereIn('type', $types)
                              ->where('payment_status', '!=', 'paid')
                              ->orderBy('transaction_date', 'asc')
                              ->get();

                              // dd(     $due_transactions);
      $total_amount = $parent_payment->total;
      // dd($due_transactions);
      $tranaction_payments = [];
      if ($due_transactions->count()) {
          foreach ($due_transactions as $transaction) {
              //If sell check status is final
              if ($transaction->type == 'sell' && $transaction->status != 'final') {
                  continue;
              }
              if ($total_amount > 0) {
                  $total_paid = $this->getTotalPaid($transaction->id);
                  $due = $transaction->final_total - $total_paid;

                  $now = Carbon::now()->toDateTimeString();
                  $array = [
                          'transaction_id' => $transaction->id,
                          'type' => $transaction->type,
                          'payment_method' => $parent_payment->payment_method,
                          'payment_date' => $parent_payment->payment_date,
                          'user_id' => $parent_payment->user_id,
                          'client_id' => $parent_payment->client_id,
                          'parent_id' => $parent_payment->id,
                          'created_at' => $now,
                          'updated_at' => $now
                        ];
                  $prefix_type ='';
                  //Generate reference number
                  $lastInvoiceNum = Invoice::latest()->first()->invoice_number ?? 0;
                  $ref_count = (int)substr($lastInvoiceNum, 4) + 1; // $this->setAndGetReferenceCount($prefix_type)
                  $array['invoice_number'] = $this->generateReferenceNumber($prefix_type, $ref_count, 'REF', 6);

                  if ($due <= $total_amount) {
                      $array['payment_amount'] = $due;
                      $array['total'] = $due;
                      $tranaction_payments[] = $array;

                      //Update transaction status to paid
                      $transaction->payment_status = 'paid';
                      $transaction->save();

                      $total_amount = $total_amount - $due;
                  } else {
                      $array['payment_amount'] = $total_amount;
                      $array['total'] = $total_amount;
                      $tranaction_payments[] = $array;

                      //Update transaction status to partial
                      $transaction->payment_status = 'partial';
                      $transaction->save();
                      $total_amount = 0;
                      break;
                  }
              }
          }

          //Insert new transaction payments
          if (!empty($tranaction_payments)) {
              Invoice::insert($tranaction_payments);
          }
      }
      return $total_amount;
  }
  /**
   * Calculate loan payment schedule as flat or decline interest.
   *
   * @param LoanRequest $request
   * @param bool $displayMode
   *
   * @return array
   */
  public function calcPaymentSchedule($id)
  {

      $loan = Loan::find($id);
      $loanStartDate = $loan->start_date;
      $firstPaymentDate  = $paymentDate = $loan->first_payment_date;
      $paymentDay = date('d',strtotime($firstPaymentDate));
      $scheduleType = $loan->schedule_type;
      $isEqualSchedule = ($scheduleType == 'ep');
      $isDeclineSchedule = ($scheduleType == 'decline');
      $installment = $loan->installment;
      $downPaymentAmount = $outstandingAmount = $loan->loan_amount;

      $principal = $downPaymentAmount / $installment;
      $firstPayDuration = date_diff(date_create($loanStartDate), date_create($firstPaymentDate))->format('%a');
      if(config('app.schedule_type')=='custom'){
        if ($isEqualSchedule) {
          if ($loan->interest_rate > 0) {
              $loanRate = ($downPaymentAmount * 365)/ 365;
              $totalAmount = pmt($loanRate, $installment, $downPaymentAmount);
          }
          else {
              $interest = 0;
              $principal = $totalAmount = num_f($principal);
          }
        }
        elseif ($isDeclineSchedule) {
            $interestRate = $loan->interest_rate / 365;
            $interest = $downPaymentAmount * $interestRate;

            // Calculate first interest amount of payment schedule
            $firstPayDuration = date_diff(date_create($loanStartDate), date_create($firstPaymentDate))->format('%a');
            $firstInterest = ($interest / 365) * $firstPayDuration;
        }

        $loopCount = ($scheduleType == 'ep' ? ($installment) : $installment); // For flat interest, plus one installment
        $scheduleData = [];

        for ($i = 1; $i <= $loopCount; $i++) {
            $isFirstLoop = ($i == 1);
            $isForeLastLoop = ($i == ($loopCount - 1));
            $paymentDate = $isFirstLoop ?  $paymentDate : addDays($paymentDate, $loan->frequency);

            if ($isEqualSchedule) {
                if ($loan->interest_rate > 0) {
                    $interest = $loanRate * ($loan->interest_rate / 100 ) * $loan->frequency;
                    $principal = $principal;
                }else{
                    $interest = 0;
                    $principal;
                }
                if($installment==1){
                  $outstandingAmount = $outstandingAmount;
                }else {
                  $outstandingAmount = $outstandingAmount - $principal;
                }
            }
            elseif ($isDeclineSchedule) {
                $interest = ($isFirstLoop ? $firstInterest : ($outstandingAmount * $interestRate));
                $totalAmount = ($principal + ($isFirstLoop ? $firstInterest : $interest));
                $outstandingAmount = ($isForeLastLoop ? $principal : ($outstandingAmount - $principal));
            }
            else {
                $interest = 0;
                $totalAmount = $principal *  $firstPayDuration;;
                $outstandingAmount = ($isForeLastLoop ? 0 : ($outstandingAmount - $principal));
            }

            $scheduleData[] = [
                'payment_date' => $paymentDate,
                'principal' => decimalNumber($principal),
                'interest' => decimalNumber($interest),
                'total' =>  decimalNumber($principal + $interest),
                'outstanding' => decimalNumber($outstandingAmount),
            ];
        }

      }else {
        if ($isEqualSchedule) {
          if ($loan->interest_rate > 0) {
              $loanRate = (($downPaymentAmount *$loan->interest_rate) / 100)/ 30;
              $totalAmount = pmt($loanRate, $installment, $downPaymentAmount);
          }
          else {
              $interest = 0;
              $principal = $totalAmount = num_f($principal);
          }
        }
        elseif ($isDeclineSchedule) {
            $interestRate = $loan->interest_rate / 100;
            $interest = $downPaymentAmount * $interestRate;

            // Calculate first interest amount of payment schedule
            $firstPayDuration = date_diff(date_create($loanStartDate), date_create($firstPaymentDate))->format('%a');
            $firstInterest = ($interest / 30) * $firstPayDuration;
        }

        $loopCount = ($scheduleType == 'ep' ? ($installment) : $installment); // For flat interest, plus one installment
        $scheduleData = [];

        for ($i = 1; $i <= $loopCount; $i++) {
            $isFirstLoop = ($i == 1);
            $isForeLastLoop = ($i == ($loopCount - 1));
            $paymentDate = $isFirstLoop ?  $paymentDate : addDays($paymentDate, $loan->frequency);

            if ($isEqualSchedule) {
                if ($loan->interest_rate > 0) {
                    $interest = $loanRate * $loan->frequency;
                    $principal = $principal;
                }else{
                    $interest = 0;
                    $principal;
                }
                if($installment==1){
                  $outstandingAmount = $outstandingAmount;
                }else {
                  $outstandingAmount = $outstandingAmount - $principal;
                }
            }
            elseif ($isDeclineSchedule) {
                $interest = ($isFirstLoop ? $firstInterest : ($outstandingAmount * $interestRate));
                $totalAmount = ($principal + ($isFirstLoop ? $firstInterest : $interest));
                $outstandingAmount = ($isForeLastLoop ? $principal : ($outstandingAmount - $principal));
            }
            else {
                $interest = 0;
                $totalAmount = $principal *  $firstPayDuration;;
                $outstandingAmount = ($isForeLastLoop ? 0 : ($outstandingAmount - $principal));
            }

            $scheduleData[] = [
                'payment_date' => $paymentDate,
                'principal' => decimalNumber($principal),
                'interest' => decimalNumber($interest),
                'total' =>  decimalNumber(($principal + $interest)),
                'outstanding' => decimalNumber($outstandingAmount),
            ];
        }
      }
      
      return $scheduleData;
  }

}
