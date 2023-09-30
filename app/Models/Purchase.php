<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Purchase extends Model
{
    use Sortable;

    public $sortable = [
        'reference_no',
        'purchase_date',
        'total_quantity',
        'total_cost',
        'total_price',
        'total_discount',
        'total_tax',
        'shipping_cost',
        'paid_amount',
        'document',
        'note',
        'purchase_status',
        'payment_status',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Branch::class, 'warehouse_id');
    }
    public function transaction_ime()
    {
        return $this->hasMany(\App\Models\TransactionIme::class,'transaction_id');
    }
}
