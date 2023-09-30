<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BranchRequest extends FormRequest
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
            'name' => 'required',
            'type' => ['required', Rule::in(array_keys(branchTypes()))],
            'location' => 'required',
            'first_phone' => 'required',
            'first_logo' => 'nullable|image|mimes:jpg,jpeg,png',
            'second_logo' => 'nullable|image|mimes:jpg,jpeg,png',
        ];
    }
}
