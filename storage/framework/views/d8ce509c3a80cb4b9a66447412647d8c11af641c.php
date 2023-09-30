<div class="tab-pane active table-responsive" role="tabpanel">
    <table class="table table-hover table-bordered">
        <?php $isFlatInterestSchedule = ($loan->schedule_type == PaymentScheduleType::FLAT_INTEREST) ?>
        <thead>
            <tr>
                <th><?php echo e(trans('app.payment_date')); ?></th>
                <?php if($isFlatInterestSchedule): ?>
                    <th><?php echo e(trans('app.payment_amount')); ?></th>
                <?php else: ?>
                    <th><?php echo e(trans('app.total')); ?></th>
                    <th><?php echo e(trans('app.principal')); ?></th>
                    <th><?php echo e(trans('app.interest')); ?></th>
                <?php endif; ?>
                <th><?php echo e(trans('app.outstanding')); ?></th>
                <th><?php echo e(trans('app.paid_date')); ?></th>
                <th><?php echo e(trans('app.paid_principal')); ?></th>
                <th><?php echo e(trans('app.paid_interest')); ?></th>
                <th><?php echo e(trans('app.penalty_amount')); ?></th>
                <th><?php echo e(trans('app.paid_amount')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $loan->schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $decimalNumber = ($schedule->interest == 0 ? 2 : 0) ?>
                <tr>
                    <td><?php echo e(displayDate($schedule->payment_date)); ?></td>
                    <?php if($isFlatInterestSchedule): ?>
                        <td>$ <?php echo e(decimalNumber($schedule->principal, $decimalNumber)); ?></td>
                    <?php else: ?>
                        <td>$ <?php echo e(decimalNumber($schedule->total, $decimalNumber)); ?></td>
                        <td>$ <?php echo e(decimalNumber($schedule->principal, $decimalNumber)); ?></td>
                        <td>$ <?php echo e(decimalNumber($schedule->interest, $decimalNumber)); ?></td>
                    <?php endif; ?>
                    <td>$ <?php echo e(decimalNumber($schedule->outstanding)); ?></td>
                    <td><?php echo e($schedule->paid_date ? displayDate($schedule->paid_date) : ''); ?></td>
                    <td><?php echo e($schedule->paid_principal ? '$ ' . decimalNumber($schedule->paid_principal, $decimalNumber) : ''); ?></td>
                    <td><?php echo e($schedule->paid_interest ? '$ ' . decimalNumber($schedule->paid_interest, $decimalNumber) : ''); ?></td>
                    <td><?php echo e($schedule->paid_penalty ? '$ ' . decimalNumber($schedule->paid_penalty, $decimalNumber) : ''); ?></td>
                    <td><?php echo e($schedule->paid_total ? '$ ' . decimalNumber($schedule->paid_total, $decimalNumber) : ''); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>