<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\BranchType;
use Kyslik\ColumnSortable\Sortable;

class Branch extends Model
{
    use Sortable;

    public $sortable = [
        'name',
        'type',
        'location',
        'phone_1',
        'phone_2',
        'phone_3',
        'phone_4',
        'address',
    ];

    public static function allShops()
    {
        return self::shop()->orderBy('name')->get();
    }

    public static function allWarehouses()
    {
        return self::warehouse()->orderBy('name')->get();
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

    /**
     * Query branches with shop type.
     *
     * @return mixed
     */
    public static function shop()
    {
        return self::where('type', BranchType::SHOP);
    }

    /**
     * Query branches with warehouse type.
     *
     * @return mixed
     */
    public static function warehouse()
    {
        return self::where('type', BranchType::WAREHOUSE);
    }


}
