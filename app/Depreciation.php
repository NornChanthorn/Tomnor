<?php

namespace App;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsTo(Invoice::class, 'loan_id');
    }
}