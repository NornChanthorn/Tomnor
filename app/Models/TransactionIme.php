<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class TransactionIme extends Model
{
    public function ime()
    {
        return $this->belongsTo(\App\Models\Ime::class, 'ime_id');
    }
    public function location()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'location_id');
    }
    public function transaction()
    {
        return $this->belongsTo(\App\Models\Transaction::class, 'transaction_id');
    }
    public function purchase()
    {
        return $this->belongsTo(\App\Models\PurchaseLine::class, 'purchase_sell_id');
    }
    public function sell()
    {
        return $this->belongsTo(\App\Models\TransactionSellLine::class, 'purchase_sell_id');
    }
}
