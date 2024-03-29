<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
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
        'type' => 'required',
        'name' => 'required',
        'company' => 'nullable',
        'phone' => 'required',  
        // 'profile_photo' => 'nullable|file|mimes:jpg,jpeg,png',
        // 'id_card_photo' => 'nullable|file|mimes:jpg,jpeg,png',
      ];
    }
}
