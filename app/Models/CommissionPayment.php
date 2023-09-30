<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class CommissionPayment extends Model
{
    use Sortable;

    public $sortable = [
        'paid_date',
        'amount',
        'receipt_number',
        'receipt_reference',
        'payment_method',
        'note',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
