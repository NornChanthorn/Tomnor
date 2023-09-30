<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdjustmentRequest extends FormRequest
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
            // 'action' => ['required', Rule::in(array_keys(stockTypes()))],
            'warehouse' => 'required|integer',
            // 'product' => 'required|integer',
            // 'quantity' => 'required|integer|min:1',
            'adjustment_date' => 'required|date',
            // 'reason' => 'required',
        ];
    }
}
