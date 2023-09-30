<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferRequest extends FormRequest
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
            'original_warehouse' => 'required|integer',
            'target_warehouse' => 'required|integer|different:original_warehouse',
            'status' => ['required', Rule::in(array_keys(stockTransferStatuses()))],
            'transaction_date' => 'required|date',
            'invoice_id' => ['nullable', Rule::unique('transfers', 'reference_no')],
            'shipping_cost' => 'nullable|numeric|min:0',
            'document' => 'nullable|file',
            'products' => 'required|array',
            'products.*.variantion_id' => 'required|integer|distinct',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }
}
