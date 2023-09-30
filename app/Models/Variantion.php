<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variantion extends Model
{

  use SoftDeletes;

  /**
   * The attributes that aren't mass assignable.
   *
   * @var array
   */
  protected $guarded = ['id'];

  public function product()
  {
    return $this->belongsTo(\App\Models\Product::class, 'product_id');
  }

  /**
   * Get the location wise details of the the variation.
   */
  public function variation_location_details()
  {
    return $this->hasMany(\App\Models\VariantionLocationDetails::class,'variantion_id', 'id');
  }

  /**
   * Get Selling price group prices.
   */
  public function group_prices()
  {
    return $this->hasMany(\App\Models\VariantionGroupPrice::class, 'variation_id');
  }
}
