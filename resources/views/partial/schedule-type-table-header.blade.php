@if ($scheduleType == PaymentScheduleType::FLAT_INTEREST)
<th class="bg-header">{{ trans('app.payment_amount') }}</th>
@else
<th class="bg-header">{{ trans('app.principal') }}</th>
<th class="bg-header">{{ trans('app.interest') }}</th>
<th class="bg-header">{{ trans('app.total') }}</th>
@endif