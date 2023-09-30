<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseRequest extends FormRequest
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
            'warehouse' => 'required|integer',
            'purchase_date' => 'required|date',
            // 'invoice_id' => ['nullable', Rule::unique('purchases', 'reference_no')],
            'status' => ['required', Rule::in(array_keys(purchaseStatuses()))],
            'total_cost' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'document' => 'nullable|file',
            'products' => 'required|array',
            'products.*.id' => 'required|integer',
            'products.*.quantity' => 'required|integer|min:1|max:10000',
        ];
    }
}
