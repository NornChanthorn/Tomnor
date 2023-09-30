<?php $__env->startSection('title', trans('app.loan_portfolio')); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.loan_portfolio')); ?></h3>
            <?php echo $__env->make('partial.flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo e(trans('app.client_information')); ?></h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <tbody>
                                <tr>
                                    <th width="40%"><?php echo e(trans('app.name')); ?></th>
                                    <th>
                                        <?php echo $__env->make('partial.client-detail-link', [
                                            'client' => $client
                                        ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th><?php echo e(trans('app.profile_photo')); ?></th>
                                    <th>
                                        <?php if(isset($client->profile_photo)): ?>
                                            <img src="<?php echo e(asset($client->profile_photo)); ?>" alt="<?php echo e(trans('app.missing_image')); ?>" class="img-thumbnail" width="100">
                                        <?php else: ?>
                                            <?php echo e(trans('app.none')); ?>

                                        <?php endif; ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th><?php echo e(trans('app.id_card_number')); ?></th>
                                    <th><?php echo e($client->id_card_number); ?></th>
                                </tr>
                                <tr>
                                    <th><?php echo e(trans('app.first_phone')); ?></th>
                                    <th><?php echo e($client->first_phone); ?></th>
                                </tr>
                                <tr>
                                    <th><?php echo e(trans('app.sponsor_name')); ?></th>
                                    <th><?php echo e($client->sponsor_name); ?></th>
                                </tr>
                                <tr>
                                    <th><?php echo e(trans('app.sponsor_phone')); ?></th>
                                    <th><?php echo e($client->sponsor_phone); ?></th>
                                </tr>
                                <tr>
                                    <th><?php echo e(trans('app.number_of_loan')); ?></th>
                                    <th><?php echo e($client->loans()->count()); ?></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <h5><?php echo e(trans('app.loan_and_payment_schedule_info')); ?></h5>
            <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $unpaidSchedules = $loan->schedules()->where('paid_status', 0)->get();
                    $loanStatusTitle = trans(count($unpaidSchedules) > 0 ? 'app.progressing' : 'app.paid');
                ?>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <h6><?php echo e(trans('app.loan')); ?></h6>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <tbody>
                                    <tr>
                                        <th><?php echo e(trans('app.account_number')); ?></th>
                                        <th><?php echo $__env->make('partial.loan-detail-link', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></th>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(trans('app.loan_status')); ?></th>
                                        <th><?php echo e($loanStatusTitle); ?></th>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(trans('app.loan_start_date')); ?></th>
                                        <th><?php echo e(displayDate($loan->loan_start_date)); ?></th>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(trans('app.product')); ?></th>
                                        <th>
                                            <?php $__currentLoopData = @$loan->productDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <a href="<?php echo e(route('product.show', $item->product)); ?>">
                                                    <?php echo e($item->product->name); ?>

                                                </a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(trans('app.product_price')); ?></th>
                                        <th>$ <?php echo e(decimalNumber($loan->loan_amount + $loan->depreciation_amount, true)); ?></th>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(trans('app.depreciation_amount')); ?></th>
                                        <th>$ <?php echo e(decimalNumber($loan->depreciation_amount, true)); ?></th>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(trans('app.down_payment_amount')); ?></th>
                                        <th>$ <?php echo e(decimalNumber($loan->loan_amount, true)); ?></th>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(trans($loan->schedule_type == PaymentScheduleType::EQUAL_PAYMENT ? 'app.loan_rate' : 'app.interest_rate')); ?></th>
                                        <th><?php echo e($loan->interest_rate); ?> %</th>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(trans('app.installment')); ?></th>
                                        <th><?php echo e($loan->installment); ?></th>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(trans('app.payment_schedule_type')); ?></th>
                                        <th><?php echo e(paymentScheduleTypes($loan->schedule_type)); ?></th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6><?php echo e(trans('app.payment_schedule') . ' (' . trans('app.cash_in_dollar') . ')'); ?></h6>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th><?php echo e(trans('app.no_sign')); ?></th>
                                        <th><?php echo e(trans('app.payment_date')); ?></th>
                                        <?php echo $__env->make('partial.schedule-type-table-header', ['scheduleType' => $loan->schedule_type], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                        <th></th>
                                        <th><?php echo e(trans('app.paid_date')); ?></th>
                                        <th><?php echo e(trans('app.paid_amount')); ?></th>
                                        <th><?php echo e(trans('app.status')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $loan->schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($loop->iteration); ?></td>
                                            <td><?php echo e(displayDate($schedule->payment_date)); ?></td>
                                            <?php echo $__env->make('partial.schedule-type-table-data', ['scheduleType' => $loan->schedule_type,'decimalNumber'=>2], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                            <td></td>
                                            <td><?php echo e(displayDate($schedule->paid_date)); ?></td>
                                            <td><?php echo e(isset($schedule->paid_total) ? decimalNumber($schedule->paid_total, true) : ''); ?></td>
                                            <td><?php echo e(trans($schedule->paid_status == 1 ? 'app.paid' : 'app.unpaid')); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>