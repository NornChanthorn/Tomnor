<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sale_date' => 'required|date',
            //'branch' => 'required|integer',
            'client' => 'required|integer',
            //'agent' => 'required|integer',
            // 'reference_no',
            // 'total_quantity',
            // 'total_price',
            // 'discount_type',
            // 'total_discount',
            // 'shipping_amount',
            // 'shipping_note',
            // 'total_tax',
            // 'grand_total',
            // 'paid_amount',
            // 'total_commission',
            // 'payment_installment',
            // 'payment_start_date',
            // 'payment_end_date',
            // 'payment_period',
            // 'note',
            // 'staff_note',
            // 'type',
            // 'status'
        ];
    }
}
