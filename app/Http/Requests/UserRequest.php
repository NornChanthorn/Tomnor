<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Constants\FormType;

class UserRequest extends FormRequest
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
            'password' => ['nullable', Rule::requiredIf(request('form_type') == FormType::CREATE_TYPE), 'min:6', 'max:32'],
            //'role' => ['required', Rule::in(array_keys(userRoles()))],
            'status' => ['required', Rule::in([0, 1])],
        ];
    }
}
