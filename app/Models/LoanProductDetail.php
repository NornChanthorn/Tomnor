<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanProductDetail extends Model
{
    protected $table = 'loan_product_details';
    protected $primaryKey = 'id';
    protected $fillable = ['loan_id', 'product_id', 'variantion_id', 'qty', 'unit_price','product_ime'];

    public function loan()
    {
        return $this->belongsTo(Variantion::class, 'loan_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variantion()
    {
        return $this->belongsTo(Variantion::class, 'variantion_id');
    }
}
