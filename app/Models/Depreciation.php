<?php

namespace App\Models;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use App\Models\Loan;
class Depreciation extends Model
{
    //
    protected $fillable = [
        'loan_id', 'c_id', 'paid_amount', 'outstanding_amount',
    ];
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
}
