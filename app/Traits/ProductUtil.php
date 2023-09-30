<?php

namespace App\Traits;

use App\Models\Product;
// use App\Models\ProductVariantion;
use App\Models\Variantion;
use App\Models\VariantionLocationDetails;
use App\Models\TransactionSellLinesPurchaseLines;
use App\Models\Purchase;
use App\Models\PurchaseLine;

use \Carbon\Carbon;

trait ProductUtil
{
  /**
   * Create variable type product variation
   *
   * @param (int or object) $product
   * @param $input_variations
   *
   * @return boolean
   */
  public function createVariableProductVariation($product, $input_variations)
  {
    if (!is_object($product)) {
      $product = Product::find($product);
    }

    $c = Variantion::withTrashed()->where('product_id', $product->id)->count() + 1;
    // dd($input_variations);

    //create product variations
    foreach ($input_variations as $key => $value) {
      //create product variations
      // $product_variation = $product->product_variations()->create([
      //   'name'      => 'DUMMY',
      //   'is_dummy'  => 1
      // ]);

      // $name = !empty($value['value']) ? $value['value'] : "DUMMY";
      // $sub_sku = empty($value['sku']) ? $this->generateSubSku($product->sku, $c, $product->barcode_type) : $value['sku'];
      // $purchase_price = $value['purchase_price'];
      // $selling_price = $value['selling_price'];

      //create variations
      // $product_variation->variantions()->create([
      //   'name'                    => $name,
      //   'product_id'              => $product->id,
      //   'sub_sku'                 => $sub_sku,
      //   'default_purchase_price'  => (float)($purchase_price),
      //   'profit_percent'          => 0,
      //   'default_sell_price'      => (float)($selling_price),
      // ]);
    }

    return true;
  }

  /**
   * Update variable type product variation
   *
   * @param $product_id
   * @param $input_variations_edit
   *
   * @return boolean
   */
  public function updateVariableProductVariations($product_id, $input_variations_edit)
  {

    $product = Product::find($product_id);
    $c = Variantion::withTrashed()->where('product_id', $product->id)->count() + 1;

    //Update product variations
    $variantion_ids = [];
    foreach ($input_variations_edit as $key => $value) {
      $name = !empty($value['value']) ? $value['value'] : "DUMMY";
      $sub_sku = empty($value['sku']) ? $this->generateSubSku(($product->sku ?? $product->code), $c, $product->barcode_type) : $value['sku'];
      $sub_sku = preg_replace('/\s+/', '', $sub_sku);
      $purchase_price = !empty($value['purchase_price']) ? $value['purchase_price'] : 0;
      $selling_price = !empty($value['selling_price']) ? $value['selling_price'] : 0;

      if(!empty($value['variantion_id'])) {

        Variantion::where('id', $value['variantion_id'])->update([
          'name'                     => $name,
          'product_id'               => $product->id,
          'sub_sku'                  => $sub_sku,
          'default_purchase_price'   => (float)($purchase_price),
          'profit_percent'           => 0,
          'default_sell_price'       => (float)($selling_price),
        ]);
        $variantion_ids[] = $value['variantion_id'];
      }
      else {
        if(!empty($value['sku']) || !empty($value['value'])) {
          //create variations
          $variantion = new Variantion;
          $variantion->name                   = $name;
          $variantion->product_id             = $product->id;
          $variantion->sub_sku                = $sub_sku;
          $variantion->default_purchase_price = (float)($purchase_price);
          $variantion->profit_percent         = 0;
          $variantion->default_sell_price     = (float)($selling_price);
          $variantion->save();

          $variantion_ids[] = $variantion->id;
        }
      }
    }

    Variantion::whereNotIn('id', $variantion_ids)->where('product_id', $product->id)->delete();
  }

  /**
   * Checks if products has manage stock enabled then Updates quantity for product and its
   * variations
   *
   * @param $location_id
   * @param $product_id
   * @param $variation_id
   * @param $new_quantity
   * @param $old_quantity = 0
   * @param $number_format = null
   * @param $uf_data = true, if false it will accept numbers in database format
   *
   * @return boolean
   */
  public function updateProductQuantity($location_id, $product_id, $variation_id, $new_quantity, $old_quantity = 0, $number_format = null, $uf_data = true)
  {
    if ($uf_data) {
      $qty_difference = $new_quantity - $old_quantity;
    }
    else {
      $qty_difference = $new_quantity - $old_quantity;
    }

    $product = Product::find($product_id);

    //Check if stock is enabled or not.
    if (!empty($product) && $product->enable_stock == 1 && $qty_difference != 0) {
      $variation = Variantion::where('id', $variation_id)
      ->where('product_id', $product_id)
      ->first();

      //Add quantity in VariationLocationDetails
      $variation_location_d = VariantionLocationDetails::where('variantion_id', $variation->id)
      ->where('product_id', $product_id)
      ->where('location_id', $location_id)
      ->first();
      // dd($variation);

      if (empty($variation_location_d)) {
        $variation_location_d = new VariantionLocationDetails();
        $variation_location_d->variantion_id = $variation->id;
        $variation_location_d->product_id = $product_id;
        $variation_location_d->location_id = $location_id;
        $variation_location_d->qty_available = 0;
      }

      $variation_location_d->qty_available += $qty_difference;
      $variation_location_d->save();
    }

    return true;
  }

  /**
   * Checks if products has manage stock enabled then Decrease quantity for product and its variations
   *
   * @param $product_id
   * @param $variation_id
   * @param $location_id
   * @param $new_quantity
   * @param $old_quantity = 0
   *
   * @return boolean
   */
  public function decreaseProductQuantity($product_id, $variation_id, $location_id, $new_quantity, $old_quantity = 0)
  {
    $qty_difference = $new_quantity - $old_quantity;

    $product = Product::find($product_id);

    //Check if stock is enabled or not.
    if ($product->enable_stock == 1) {
      // Check variantion location detail if empty create new
      $variationDetail = VariantionLocationDetails::where('variantion_id', $variation_id)
      ->where('product_id', $product_id)
      ->where('location_id', $location_id)->first();
      if (empty($variationDetail)) {
        $variationDetail = new VariantionLocationDetails;
        $variationDetail->product_id = $product_id;
        $variationDetail->variantion_id = $variation_id;
        $variationDetail->location_id = $location_id;
        $variationDetail->qty_available = 0;
        $variationDetail->save();
      }
      
      //Decrement Quantity in variations location table
      VariantionLocationDetails::where('variantion_id', $variation_id)
      ->where('product_id', $product_id)
      ->where('location_id', $location_id)
      ->decrement('qty_available', $qty_difference);
    }

    return true;
  }

  /**
   * Generated SKU based on the barcode type.
   *
   * @param string $sku
   * @param string $c
   * @param string $barcode_type
   *
   * @return void
   */
  public function generateSubSku($sku, $c, $barcode_type)
  {
    $sku = $sku ?? mt_rand();
    $sub_sku = $sku . $c;

    if (in_array($barcode_type, ['C128', 'C39'])) {
      $sub_sku = $sku . '-' . $c;
    }

    return $sub_sku;
  }

  /**
   * Adjusts stock over selling with purchases, opening stocks andstock transfers
   * Also maps with respective sells
   *
   * @param obj $transaction
   *
   * @return void
   */
  public function adjustStockOverSelling($transaction)
  {
    if ($transaction->status != 'received') {
      return false;
    }

    foreach ($transaction->purchase_lines as $purchase_line) {
      if ($purchase_line->product->enable_stock == 1) {
        //Available quantity in the purchase line
        $purchase_line_qty_avlbl = $purchase_line->quantity_remaining;

        if ($purchase_line_qty_avlbl <= 0) {
          continue;
        }

        //update sell line purchase line mapping
        $sell_line_purchase_lines = TransactionSellLinesPurchaseLines::where('purchase_line_id', 0)
        ->join('transaction_sell_lines as tsl', 'tsl.id', '=', 'transaction_sell_lines_purchase_lines.sell_line_id')
        ->join('transactions as t', 'tsl.transaction_id', '=', 't.id')
        ->where('t.location_id', $transaction->location_id)
        ->where('tsl.variantion_id', $purchase_line->variantion_id)
        ->where('tsl.product_id', $purchase_line->product_id)
        ->select('transaction_sell_lines_purchase_lines.*')
        ->get();

        foreach ($sell_line_purchase_lines as $slpl) {
          if ($purchase_line_qty_avlbl > 0) {
            if ($slpl->quantity <= $purchase_line_qty_avlbl) {
              $purchase_line_qty_avlbl -= $slpl->quantity;
              $slpl->purchase_line_id = $purchase_line->id;
              $slpl->save();
              //update purchase line quantity sold
              $purchase_line->quantity_sold += $slpl->quantity;
              $purchase_line->save();
            }
            else {
              $diff = $slpl->quantity - $purchase_line_qty_avlbl;
              $slpl->purchase_line_id = $purchase_line->id;
              $slpl->quantity = $purchase_line_qty_avlbl;
              $slpl->save();

              //update purchase line quantity sold
              $purchase_line->quantity_sold += $slpl->quantity;
              $purchase_line->save();

              TransactionSellLinesPurchaseLines::create([
                'sell_line_id'      => $slpl->sell_line_id,
                'purchase_line_id'  => 0,
                'quantity'          => $diff
              ]);
              break;
            }
          }
        }
      }
    }
  }

  /**
   * Increments reference count for a given type and given business
   * and gives the updated reference count
   *
   * @param string $type
   * @param int $business_id
   *
   * @return int
   */
  public function setAndGetReferenceCount($type, $business_id = null)
  {
    if (empty($business_id)) {
      $business_id = request()->session()->get('user.business_id');
    }

    $ref = \App\Models\ReferenceCount::where('ref_type', $type)->first();
    if (!empty($ref)) {
      $ref->ref_count += 1;
      $ref->save();
      return $ref->ref_count;
    }
    else {
      $new_ref = \App\Models\ReferenceCount::create([
        'ref_type' => $type,
        'business_id' => 1,
        'ref_count' => 1
      ]);
      return $new_ref->ref_count;
    }
  }

  /**
   * Generates reference number
   *
   * @param string $type
   * @param int $business_id
   *
   * @return int
   */
  public function generateReferenceNumber($type, $ref_count, $default_prefix = 'REF', $digits = 4)
  {
    $prefix = '';

    if (!empty($default_prefix)) {
      $prefix = $default_prefix;
    }

    $ref_digits =  str_pad($ref_count, $digits, 0, STR_PAD_LEFT);

    if (!in_array($type, ['contacts', 'business_location', 'username'])) {
      $ref_year = Carbon::now()->year;
      $ref_number = $prefix . '-' . $ref_digits;
    }
    else {
      $ref_number = $prefix . $ref_digits;
    }

    return $ref_number;
  }

  /**
   * Add/Edit transaction purchase lines
   *
   * @param object $transaction
   * @param array $input_data
   * @param array $currency_details
   * @param boolean $enable_product_editing
   * @param string $before_status = null
   *
   * @return array
   */
  public function createOrUpdatePurchaseLines($transaction, $input_data)
  {
    $updated_purchase_lines = [];
    $updated_purchase_line_ids = [0];
    $exchange_rate = $transaction->exchange_rate;

    foreach ($input_data as $data) {
      $multiplier = 1;
      if (isset($data['sub_unit_id']) && $data['sub_unit_id'] == $data['product_unit_id']) {
        unset($data['sub_unit_id']);
      }

      if (!empty($data['sub_unit_id'])) {
        $unit = Unit::find($data['sub_unit_id']);
        $multiplier = !empty($unit->base_unit_multiplier) ? $unit->base_unit_multiplier : 1;
      }
      $new_quantity = $data['quantity'] * $multiplier;

      $new_quantity_f = $new_quantity;
      //update existing purchase line
      if (isset($data['purchase_line_id'])) {
        $purchase_line = PurchaseLine::findOrFail($data['purchase_line_id']);
        $updated_purchase_line_ids[] = $purchase_line->id;
        $old_qty = $purchase_line->quantity;

        //Update quantity for existing products
        $this->updateProductQuantity($transaction->location_id, $data['id'], $data['variantion_id'], $new_quantity_f, $old_qty);
      }
      else {
        //create newly added purchase lines
        $purchase_line = new PurchaseLine();
        $purchase_line->product_id = $data['id'];
        $purchase_line->variantion_id = $data['variantion_id'];

        if ($transaction->status == 'received') {
          $this->updateProductQuantity($transaction->location_id, $data['id'], $data['variantion_id'], $new_quantity_f, 0);
        }
      }

      $purchase_line->quantity = $new_quantity;
      $purchase_line->discount_percent = $data['discount_percent'] ?? 0;
      $purchase_line->purchase_price = $data['purchase_price'];
      $purchase_line->lot_number = !empty($data['lot_number']) ? $data['lot_number'] : null;
      $purchase_line->mfg_date = !empty($data['mfg_date']) ? $this->uf_date($data['mfg_date']) : null;
      $purchase_line->exp_date = !empty($data['exp_date']) ? $this->uf_date($data['exp_date']) : null;
      $purchase_line->sub_unit_id = !empty($data['sub_unit_id']) ? $data['sub_unit_id'] : null;

      $updated_purchase_lines[] = $purchase_line;
    }

    //unset deleted purchase lines
    $delete_purchase_line_ids = [];
    $delete_purchase_lines = null;
    if (!empty($updated_purchase_line_ids)) {
      $delete_purchase_lines = PurchaseLine::where('transaction_id', $transaction->id)->whereNotIn('id', $updated_purchase_line_ids)->get();

      if ($delete_purchase_lines->count()) {
        foreach ($delete_purchase_lines as $delete_purchase_line) {
          $delete_purchase_line_ids[] = $delete_purchase_line->id;
        }
        //Delete deleted purchase lines
        PurchaseLine::where('transaction_id', $transaction->id)->whereIn('id', $delete_purchase_line_ids)->delete();
      }
    }

    //update purchase lines
    if (!empty($updated_purchase_lines)) {
      $transaction->purchase_lines()->saveMany($updated_purchase_lines);
    }

    return $delete_purchase_lines;
  }
}
