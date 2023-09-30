<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

use App\Models\User;

class Transaction extends Model
{

  use Sortable;

  /**
   * The attributes that aren't mass assignable.
   *
   * @var array
   */
  protected $guarded = ['id'];

  public function purchase_lines()
  {
    return $this->hasMany(\App\Models\PurchaseLine::class);
  }

  public function sell_lines()
  {
    return $this->hasMany(\App\Models\TransactionSellLine::class);
  }
  public function return_parent()
  {
      return $this->hasOne(\App\Models\Transaction::class, 'return_parent_id');
  }
  public function return_parent_sell()
  {
      return $this->belongsTo(\App\Models\Transaction::class, 'return_parent_id');
  }
  // public function payment_lines()
  // {
  //     return $this->hasMany(\App\Models\TransactionPayment::class);
  // }

  public function invoices()
  {
    return $this->hasMany(\App\Models\Invoice::class, 'transaction_id');
  }

  public function stock_adjustment_lines()
  {
      return $this->hasMany(\App\Models\StockAdjustmentLine::class);
  }
  public function transaction_ime()
  {
      return $this->hasMany(\App\Models\TransactionIme::class,'transaction_id');
  }
  public function creator()
  {
      return $this->belongsTo(User::class, 'created_by');
  }

  public function client()
  {
      return $this->belongsTo(\App\Models\Contact::class, 'contact_id');
  }

  public function customer()
  {
      return $this->belongsTo(\App\Models\Client::class, 'contact_id');
  }

  public function staff()
  {
      return $this->belongsTo(\App\Models\Staff::class, 'created_by','user_id');
  }

  public function agency()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'created_by','user_id');
    }

  public function warehouse()
  {
      return $this->belongsTo(Branch::class, 'location_id');
  }
  public function loan()
  {
      return $this->hasMany(\App\Models\Loan::class, 'transaction_id');
  }
}
