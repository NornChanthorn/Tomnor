<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Contact extends Model
{
    use Sortable;

    public function province()
    {
        return $this->belongsTo(\App\Models\Address::class, 'city');
    }
    public function contact_group()
    {
        return $this->belongsTo(\App\Models\ContactGroup::class, 'contact_group_id','id');
    }
    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class, 'contact_id');
    }
    public function invoices()
    {
      return $this->hasMany(\App\Models\Invoice::class, 'client_id');
    }
}
