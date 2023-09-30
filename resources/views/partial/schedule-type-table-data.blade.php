@if ($scheduleType == PaymentScheduleType::FLAT_INTEREST)

<td>{{ ($currencySign ?? '') . decimalNumber($schedule->principal, $decimalNumber) }}</td>
@else

<td >{{ ($currencySign ?? '') . decimalNumber($schedule->principal, $decimalNumber) }}</td>
<td>{{ ($currencySign ?? '') . decimalNumber($schedule->interest, $decimalNumber) }}</td>
<td class="bg-total" style="background-color: #ffe69b"><b>{{ ($currencySign ?? '') . decimalNumber($schedule->total, $decimalNumber) }}</b></td>
@endif
