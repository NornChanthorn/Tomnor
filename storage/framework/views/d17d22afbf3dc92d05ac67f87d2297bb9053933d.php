<?php if($scheduleType == PaymentScheduleType::FLAT_INTEREST): ?>
<th class="bg-header"><?php echo e(trans('app.payment_amount')); ?></th>
<?php else: ?>
<th class="bg-header"><?php echo e(trans('app.principal')); ?></th>
<th class="bg-header"><?php echo e(trans('app.interest')); ?></th>
<th class="bg-header"><?php echo e(trans('app.total')); ?></th>
<?php endif; ?>