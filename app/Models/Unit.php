<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Unit extends Model
{
    use SoftDeletes;
    use Sortable;
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    public $sortable = ['short_name'];
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Return list of units for a business
     *
     * @param int $business_id
     * @param boolean $show_none = true
     *
     * @return array
     */
    public static function forDropdown($business_id, $show_none = false, $only_base = true)
    {
        $query = Unit::where('business_id', $business_id);
        if ($only_base) {
            $query->whereNull('base_unit_id');
        }

        $units = $query->pluck('actual_name', 'id');
        if ($show_none) {
            $units->prepend(__('messages.please_select'), '');
        }
        
        return $units;
    }

    public function sub_units()
    {
        return $this->hasMany(\App\Models\Unit::class, 'base_unit_id');
    }

    public function base_unit()
    {
        return $this->belongsTo(\App\Models\Unit::class, 'base_unit_id');
    }
}