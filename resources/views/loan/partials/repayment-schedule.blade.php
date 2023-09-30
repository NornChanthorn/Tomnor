<div class="tab-pane active table-responsive" role="tabpanel">
    <table class="table table-hover table-bordered">
        @php $isFlatInterestSchedule = ($loan->schedule_type == PaymentScheduleType::FLAT_INTEREST) @endphp
        <thead>
            <tr>
                <th>{{ trans('app.payment_date') }}</th>
                @if ($isFlatInterestSchedule)
                    <th>{{ trans('app.payment_amount') }}</th>
                @else
                    <th>{{ trans('app.total') }}</th>
                    <th>{{ trans('app.principal') }}</th>
                    <th>{{ trans('app.interest') }}</th>
                @endif
                <th>{{ trans('app.outstanding') }}</th>
                <th>{{ trans('app.paid_date') }}</th>
                <th>{{ trans('app.paid_principal') }}</th>
                <th>{{ trans('app.paid_interest') }}</th>
                <th>{{ trans('app.penalty_amount') }}</th>
                <th>{{ trans('app.paid_amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loan->schedules as $schedule)
                @php $decimalNumber = ($schedule->interest == 0 ? 2 : 0) @endphp
                <tr>
                    <td>{{ displayDate($schedule->payment_date) }}</td>
                    @if ($isFlatInterestSchedule)
                        <td>$ {{ decimalNumber($schedule->principal, $decimalNumber) }}</td>
                    @else
                        <td>$ {{ decimalNumber($schedule->total, $decimalNumber) }}</td>
                        <td>$ {{ decimalNumber($schedule->principal, $decimalNumber) }}</td>
                        <td>$ {{ decimalNumber($schedule->interest, $decimalNumber) }}</td>
                    @endif
                    <td>$ {{ decimalNumber($schedule->outstanding) }}</td>
                    <td>{{ displayDate($schedule->paid_date) }}</td>
                    <td>{{ $schedule->paid_principal ? '$ ' . decimalNumber($schedule->paid_principal, $decimalNumber) : '' }}</td>
                    <td>{{ $schedule->paid_interest ? '$ ' . decimalNumber($schedule->paid_interest, $decimalNumber) : '' }}</td>
                    <td>{{ $schedule->paid_penalty ? '$ ' . decimalNumber($schedule->paid_penalty, $decimalNumber) : '' }}</td>
                    <td>{{ $schedule->paid_total ? '$ ' . decimalNumber($schedule->paid_total, $decimalNumber) : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>