<?php 

namespace App\Models;

use Zizaco\Entrust\EntrustRole;
use Kyslik\ColumnSortable\Sortable;

class Role extends EntrustRole
{
	use Sortable;

	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
							'name',
							'display_name',
							'description'
						];

	public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }
	
	public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission');
    }

    /**
     * Get all records.
     *
     * @param array $fields
     *
     * @return mixed
     */
    public static function getAll($fields = ['*'])
    {
        return self::orderBy('display_name')->get($fields);
    }
}