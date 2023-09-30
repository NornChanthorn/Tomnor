<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Constants\FormType;

class StaffRequest extends FormRequest
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
        'form_type' => ['required', Rule::in([FormType::CREATE_TYPE, FormType::EDIT_TYPE])],
        'name' => ['required', Rule::unique('staff')->ignore(request('staff'))],
        'id_card_number' => ['nullable', Rule::unique('staff')->ignore(request('staff'))],
        'gender' => 'required',
        'date_of_birth' => 'nullable|date',
        'first_phone' => 'required',
        'branch' => ['nullable', 'integer', Rule::requiredIf(request('form_type') == FormType::CREATE_TYPE)],
        'position' => 'required|integer',
        'profile_photo' => 'nullable|file|mimes:jpg,jpeg,png',
        'id_card_photo' => 'nullable|file|mimes:jpg,jpeg,png',
        //'role' => ['required', Rule::in(array_keys(userRoles()))],
      ];
    }
}
