<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class RoleRequest extends FormRequest
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
    public function rules(Request $request)
    {
        $id = $request->id ? ',' . $request->id : '';
        return [
            //'name' => 'required|max:255|unique:roles,name'.$id,
            'display_name' => 'required|max:255|unique:roles,display_name'.$id,
            'description' => 'max:255'
        ];
    }
}
