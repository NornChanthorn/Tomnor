<?php if($scheduleType == PaymentScheduleType::FLAT_INTEREST): ?>

<td><?php echo e(($currencySign ?? '') . decimalNumber($schedule->principal, $decimalNumber)); ?></td>
<?php else: ?>

<td ><?php echo e(($currencySign ?? '') . decimalNumber($schedule->principal, $decimalNumber)); ?></td>
<td><?php echo e(($currencySign ?? '') . decimalNumber($schedule->interest, $decimalNumber)); ?></td>
<td class="bg-total" style="background-color: #ffe69b"><b><?php echo e(($currencySign ?? '') . decimalNumber($schedule->total, $decimalNumber)); ?></b></td>
<?php endif; ?>
