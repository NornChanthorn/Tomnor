<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Constants\FormType;
class FormTypes implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return in_array($value, [FormType::CREATE_TYPE, FormType::EDIT_TYPE]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('message.invalid_form_type');
    }
}
