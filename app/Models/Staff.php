<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Staff extends Model
{
    use Sortable;

    public $sortable = [
        'name',
        'gender',
        'id_card_number',
        'first_phone',
        'second_phone',
        'id_card_number',
        'email',
        'address',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
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
        return self::orderBy('name')->get($fields);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'staff_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
