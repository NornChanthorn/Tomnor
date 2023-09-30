<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Adjustment extends Model
{
    use Sortable;

    public $sortable = [
        'action',
        'adjustment_date',
        'quantity',
        'reference_no',
        'reason',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Branch::class, 'warehouse_id');
    }
}
