<?php

namespace App\Models;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use App\Models\Loan;
class Depreciation extends Model
{
    //
    protected $fillable = [
        'loan_id', 'invoice_id', 'DepreciationAmount', 'payment_method',
    ];
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
}
