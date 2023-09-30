<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Constants\ExtendedProperty as EPropertyType;

class ExtendedProperty extends Model
{
    use Sortable;

    public $sortable = ['value'];

    public static function allBrands()
    {
        return self::brand()->orderBy('value')->get();
    }

    public static function allPositions()
    {
        return self::position()->orderBy('value')->get();
    }

    public static function allProductCategories()
    {
        return self::productCategory()->orderBy('value')->get();
    }
    public static function allCollateralTypes()
    {
        return self::collateralType()->orderBy('value')->get();
    }
    public static function allmethodPayments()
    {
        return self::methodPayment()->orderBy('value')->get();
    }
    public static function brand()
    {
        return self::where('property_name', EPropertyType::BRAND)->where('group_name',EPropertyType::BRAND);
    }

    public static function position()
    {
        return self::where('property_name', EPropertyType::POSITION)->where('group_name', EPropertyType::POSITION);
    }
    public static function expense()
    {
        return self::where('property_name', EPropertyType::EXPENSE)->where('group_name', EPropertyType::EXPENSE);
    }
    public static function productCategory()
    {
        return self::where('property_name', EPropertyType::PRODUCT_CATEGORY)->where('group_name', EPropertyType::PRODUCT_CATEGORY);
    }
    public static function collateralType()
    {
        return self::where('property_name', EPropertyType::COLLATERAL_TYPE)->where('group_name', EPropertyType::COLLATERAL_TYPE);
    }

    public static function methodPayment()
    {
        return self::where('group_name', EPropertyType::METHOD_PAYMENT);
    }
    public function invoices()
    {
        return $this->hasMany('App\Models\Invoice', 'payment_method', 'property_name');
    }
}
