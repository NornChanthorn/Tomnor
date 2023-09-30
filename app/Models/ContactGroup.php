<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ContactGroup extends Model
{
    use Sortable;
    public $sortable = [
        'name',
        'type'
    ];
    public function contacts()
    {
        return $this->hasMany(\App\Models\Contact::class, 'contact_group_id','id');
    }
    public function transactions()
    {
        return $this->hasMany(\App\Models\Transaction::class, 'contact_group_id','id');
    }
}
