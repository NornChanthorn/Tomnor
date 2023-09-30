<?php

namespace App\Models;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Expense extends Model
{
    use SoftDeletes;
    use Sortable;
    public function category()
    {
      return $this->belongsTo(ExtendedProperty::class, 'category_id')->where('property_name','ex');
    }
}
