<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Constants\FormType;
use App\Constants\PaymentScheduleType;

class LoanRequest extends FormRequest
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
            // Payment schedule info
            'schedule_type' => ['required', Rule::in(array_keys(paymentScheduleTypes()))],
            'down_payment_amount' => 'required|numeric',
            'interest_rate' => ['nullable', 'numeric', 'min:0', Rule::requiredIf(request('schedule_type') != PaymentScheduleType::FLAT_INTEREST)],
            'installment' => 'required|integer|min:1',
            'payment_per_month' => 'required|integer',
            'loan_start_date' => 'required|date',
            'first_payment_date' => 'nullable|date|after_or_equal:loan_start_date',
        ];
    }
}
