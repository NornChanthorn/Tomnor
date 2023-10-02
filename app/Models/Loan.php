<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Loan extends Model
{
    use Sortable;

    public $sortable = [
        'product_price',
        'product_ime',
        'account_number',
        'account_number_append',
        'loan_amount',
        'commission_amount',
        'depreciation_amount',
        'depreciation_percentage',
        'down_payment_amount',
        'extra_fee',
        'interest_rate',
        'installment',
        'frequency',
        'loan_start_date',
        'payment_per_month',
        'first_payment_date',
        'second_payment_date',
        'wing_code',
        'client_code',
        'client_id',
        'note',
        'approved_date',
        'disbursed_date',
        'status',
        'created_at'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'loan_id');
    }

    public function productDetails()
    {
        return $this->hasMany(LoanProductDetail::class, 'loan_id');
    }


    public function variantion()
    {
        return $this->belongsTo(Variantion::class, 'variantion_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'loan_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function transaction()
    {
        return $this->belongsTo(\App\Models\Transaction::class, 'transaction_id');
    }
    public function payments()
    {
        return $this->hasMany(\App\Models\Invoice::class, 'loan_id')->where('type','leasing');
    }
    public function scheduleReferences()
    {
        return $this->hasMany(\App\Models\ScheduleReference::class, 'loan_id');
    }
    public function collaterals()
    {
        return $this->hasMany(Collateral::class, 'loan_id');
    }

    /**
     * Get user who approved or rejected loan.
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}