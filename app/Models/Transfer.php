<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Transfer extends Model
{
    use Sortable;

    public $sortable = [
        'from_warehouse_id',
        'to_warehouse_id',
        'reference_no',
        'transfer_date',
        'total_quantity',
        'total_cost',
        'total_discount',
        'total_tax',
        'shipping_cost',
        'grand_total',
        'note',
        'status',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function details()
    {
        return $this->hasMany(TransferDetail::class, 'transfer_id');
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Branch::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Branch::class, 'to_warehouse_id');
    }
}
