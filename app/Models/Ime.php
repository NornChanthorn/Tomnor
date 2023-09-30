<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ime extends Model
{
    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }
    public function variantion()
    {
        return $this->belongsTo(\App\Models\Variantion::class, 'variantion_id');
    }
    public function transaction_ime()
    {
        return $this->hasMany(\App\Models\TransactionIme::class, 'ime_id','id');
    }
}
