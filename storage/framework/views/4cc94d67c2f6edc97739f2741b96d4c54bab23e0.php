<?php $__env->startSection('title', trans('app.commission_payment')); ?>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap4-datetimepicker.min.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.commission_payment')); ?></h3>
            <?php echo $__env->make('partial.flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="row">
                <div class="col-md-10 col-lg-8">
                    <form method="post" id="form-commission" action="<?php echo e(route('commission-payment.save')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="form-group">
                            <label for="agent_id"><?php echo e(trans('app.agent')); ?> <span class="required">*</span></label>
                            <select name="agent_id" id="agent_id" class="form-control select2" required>
                                <option value=""><?php echo e(trans('app.select_option')); ?></option>
                                <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($agent->id); ?>">
                                        <?php echo e($agent->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div id="commission-info-wrapper" style="display: none;">
                            <div class="table-responsive">
                                <table id="table-commission-info" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(trans('app.total_commission')); ?></th>
                                            <th><?php echo e(trans('app.paid_commission')); ?></th>
                                            <th><?php echo e(trans('app.balance')); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <hr>
                        </div>
                        <div class="form-group">
                            <label for="payment_date">
                                <?php echo e(trans('app.paid_date')); ?> <span class="required">*</span>
                            </label>
                            <input type="text" name="payment_date" id="payment_date" class="form-control date-picker"
                                   value="<?php echo e(old('payment_date') ?? date('d-m-Y')); ?>" placeholder="<?php echo e(trans('app.date_placeholder')); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_amount">
                                <?php echo e(trans('app.payment_amount')); ?> ($) <span class="required">*</span>
                            </label>
                            <input type="text" name="payment_amount" id="payment_amount" class="form-control decimal-input"
                                   value="<?php echo e(old('payment_amount')); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="reference_number">
                                <?php echo e(trans('app.reference_number')); ?>

                            </label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control"
                                   value="<?php echo e(old('reference_number')); ?>">
                        </div>
                        <div class="form-group">
                            <label for="note">
                                <?php echo e(trans('app.note')); ?>

                            </label>
                            <textarea name="note" id="note" class="form-control"><?php echo e(old('note')); ?></textarea>
                        </div>
                        <?php echo $__env->make('partial.button-save', [
                            'class' => 'pull-right'
                        ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </form>
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/jquery.validate.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap4-datetimepicker.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/date-time-picker.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery-number.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/number.js')); ?>"></script>
    <script>
        $(function () {
            $('.select2').select2();
            $('#form-commission').validate({
                agent_id: { required: true },
                payment_date: { required: true },
                payment_amount: { required: true, min: 0 }
            });

            // When change agent
            $('#agent_id').change(function () {
                var agentId = $(this).val();
                if (agentId != '') {
                    var agentCommissionUrl = ('<?php echo e(route('commission-payment.get_agent_commission_info', ':agentId')); ?>').replace(':agentId', agentId);
                    $.ajax({
                        url: agentCommissionUrl,
                        success: function (result) {
                            var commissionDataElm =
                                '<tr>' +
                                    '<td><b>$ ' + result.totalCommission + '</b></td>' +
                                    '<td><b>$ ' + result.paidCommission + '</b></td>' +
                                    '<td><b>$ ' + result.balance + '</b></td>' +
                                '</tr>';
                            $('#table-commission-info tbody').html(commissionDataElm);
                        }
                    });
                }

                agentId == '' ? $('#commission-info-wrapper').hide() : $('#commission-info-wrapper').show();
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>