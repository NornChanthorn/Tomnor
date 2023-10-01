<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseLine extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function transaction()
    {
        return $this->belongsTo(\App\Models\Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }

    public function variations()
    {
        return $this->belongsTo(\App\Models\Variantion::class, 'variantion_id');
    }
    public function transaction_ime()
    {
        return $this->hasMany(\App\Models\TransactionIme::class,'purchase_sell_id');
    }
    /**
     * Set the quantity.
     *
     * @param  string  $value
     * @return float $value
     */
    public function getQuantityAttribute($value)
    {
        return (float)$value;
    }

    /**
     * Get the unit associated with the purchase line.
     */
    public function sub_unit()
    {
        return $this->belongsTo(\App\Models\Unit::class, 'sub_unit_id');
    }

    /**
     * Give the quantity remaining for a particular
     * purchase line.
     *
     * @return float $value
     */
    public function getQuantityRemainingAttribute()
    {
        return (float)($this->quantity - $this->quantity_sold - $this->quantity_adjusted - $this->quantity_returned);
    }

    /**
     * Give the sum of quantity sold, adjusted, returned.
     *
     * @return float $value
     */
    public function getQuantityUsedAttribute()
    {
        return (float)($this->quantity_sold + $this->quantity_adjusted + $this->quantity_returned);
    }
}