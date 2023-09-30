<?php

namespace App\Models;

use App\Constants\LoanStatus;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Events\InvoiceDeleted;
class Invoice extends Model
{
    use Sortable;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public $sortable = [
        'invoice_number',
        'payment_amount',
        'penalty',
        'total',
        'payment_method',
        'reference_number',
        'payment_date',
        'note',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get child payments
     */
    public function child_payments()
    {
        return $this->hasMany(\App\Models\Invoice::class, 'parent_id');
    }
   
}
