<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ProductWarehouse;
use App\Models\Variantion;
use App\Models\Unit;

class Product extends Model
{
  use Sortable;

  use SoftDeletes;

  protected $guarded = ['id'];
  public $sortable = [
    'name',
    'qty',
    'serial_number',
    'price',
    'quantity',
    'description',
  ];

  public function category()
  {
    return $this->belongsTo(ExtendedProperty::class, 'category_id');
  }

  /**
  * Get the unit associated with the product.
  */
  public function unit()
  {
      return $this->belongsTo(Unit::class);
  }

  /**
   * Get all records.
   *
   * @param array $fields
   *
   * @return mixed
   */
  public static function getAll($fields = ['*'])
  {
    return self::orderBy('name')->get($fields);
  }

  public function loans()
  {
    return $this->hasMany(Loan::class, 'product_id');
  }
  public function loanDetials()
  {
    return $this->hasMany(LoanProductDetail::class, 'product_id');
  }
  public function variantions()
  {
    return $this->hasMany(Variantion::class, 'product_id');
  }
  public function variantionLocationDetails()
  {
    return $this->hasMany(VariantionLocationDetails::class, 'product_id');
  }
  public function sellDetails()
  {
    return $this->hasMany(TransactionSellLine::class, 'product_id');
  }
  public function purchaseDetails()
  {
    return $this->hasMany(PurchaseLine::class, 'product_id');
  }

  public function Warehouse()
  {
    return $this->hasMany(ProductWarehouse::class);
  }


  public function getQty($product_id, $location)
  {
    // if($location > 0) {
    //   $qty = 0;
    //   foreach ($this->Warehouse as $product) {
    //     if($product_id == $product->product_id && $product->warehouse_id == $location):
    //       $qty += $product->quantity;
    //     endif;
    //   }
    //   return $qty;
    // }
    // else {
    //   $qty = 0;
    //   foreach ($this->Warehouse as $product) {
    //     $qty += $product->quantity;
    //   }
    //   return $qty;
    // }
    $current_stock = \App\Models\VariantionLocationDetails::select([DB::raw("SUM(qty_available) AS current_stock")])
    ->where('product_id', $product_id)
    ->groupBy('product_id')->first();
    return !empty($current_stock) ? (int)$current_stock->current_stock : 0;
  }

  public function getPrefixPriceAttribute()
  {
    $price = 0;

    $variantion = Variantion::where('product_id', $this->id);
    if($this->type == 'single') {
      return number_format($variantion->first()->default_sell_price ?? $this->price, 0);
    }
    else {
      $variantion = $variantion->get();
      $minPrice = number_format($variantion->min('default_sell_price'), 0);
      $maxPrice = number_format($variantion->max('default_sell_price'), 0);

      if($minPrice == $maxPrice) {
        return $minPrice;
      }
      else {
        return $minPrice.'-'.$maxPrice;
      }
    }

    return false;
  }

  /**
   * Get the variations associated with the product.
   */
  public function variations()
  {
    return $this->hasMany(Variantion::class, 'product_id', 'id');
  }

  /**
   * Get the variations associated with the product.
   */
  public function variation_location_detail()
  {
    return $this->hasMany(\App\Models\VariantionLocationDetails::class, 'product_id', 'id');
  }
}