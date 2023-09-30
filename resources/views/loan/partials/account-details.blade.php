<div class="tab-pane active table-responsive" role="tabpanel">
    <table class="table table-hover table-bordered">
        <tbody>
            <tr>
                <td>
                    {{ __('app.loan_transaction_processing_strategy') }}
                </td>
                <td>
                    {{ __('app.principal') }}, {{ __('app.interest') }}, {{ __('app.penalty') }}, {{ __('app.fees_order') }}
                </td>
            </tr>
            <tr>
                <td>
                    {{ __('app.loan_amount') }}
                </td>
                <td>
                    {{ num_f($loan->loan_amount) }}
                </td>
            </tr>
            <tr>
                <td>
                    {{ __('app.installment') }}
                </td>
                <td>
                    {{ $loan->installment }} {{ __('app.times') }}
                </td>
            </tr>
            <tr>
                <td>
                    {{ __('app.repayment') }}
                </td>
                <td>
                    {{ $loan->payment_per_month }} {{ __('app.month') }}
                </td>
            </tr>
            <tr>
                <td>
                    {{ __('app.interest_methodology') }}
                </td>
                <td>
                    {{ __('app.flat_interest') }}
                </td>
            </tr>
            <tr>
                <td>
                    {{ __('app.interest_rate') }}
                </td>
                <td>
                    {{ $loan->interest_rate }}% / {{ __('app.month') }}
                </td>
            </tr>
            <tr>
                <td>
                    {{ __('app.request_loan') }}
                </td>
                <td>
                    {{ @$loan->staff->name }}
                </td>
            </tr>
            <tr>
                <td>
                    {{ __('app.approved_by') }}
                </td>
                <td>
                    {{ @$loan->changedBy->staff->name ?? @$loan->changedBy->name }}
                </td>
            </tr>
        </tbody>
    </table>
</div>