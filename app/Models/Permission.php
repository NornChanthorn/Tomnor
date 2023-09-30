<?php 

namespace App\Models;

use Zizaco\Entrust\EntrustPermission;
use Kyslik\ColumnSortable\Sortable;

class Permission extends EntrustPermission
{
	use Sortable;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
							'group',
							'name',
							'display_name',
							'description',
							'ordering'
						];

	public function roles()
    {
        return $this->belongsToMany('App\Models\Role');
    }
	
}