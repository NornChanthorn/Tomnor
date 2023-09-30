<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductWarehouse extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public static function selectQuery($warehouseId, $productId)
    {
        return self::where('warehouse_id', $warehouseId)->where('product_id', $productId);
    }

    public function warehouse()
    {
        return $this->belongsTo(Branch::class, 'warehouse_id');
    }
}
