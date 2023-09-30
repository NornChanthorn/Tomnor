<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
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
            'name' => ['required', Rule::unique('clients')->ignore(request('client'))],
            'id_card_number' => ['required', Rule::unique('clients')->ignore(request('client'))],
            'date_of_birth' => 'nullable|date',
            'first_phone' => 'required',
            'province' => 'nullable|integer',
            'district' => 'nullable|integer',
            'commune' => 'nullable|integer',
            'village' => 'nullable|integer',
            'profile_photo' => 'nullable|file|mimes:jpg,jpeg,png',
            'id_card_photo' => 'nullable|file|mimes:jpg,jpeg,png',

            // Sponsor info
            'sponsor_id_card_number' => ['nullable', Rule::unique('clients', 'sponsor_id_card')->ignore(request('client'))],
            'sponsor_province' => 'nullable|integer',
            'sponsor_district' => 'nullable|integer',
            'sponsor_commune' => 'nullable|integer',
            'sponsor_village' => 'nullable|integer',
        ];
    }
}
