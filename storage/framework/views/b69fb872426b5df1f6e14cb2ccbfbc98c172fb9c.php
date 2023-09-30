<?php $__env->startSection('title', $title); ?>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap4-datetimepicker.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/sweetalert.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e($title); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <form method="post" id="payment-form" class="no-auto-submit" action="<?php echo e(route('loan.update_payment_schedule', $schedule->id)); ?>">
                <?php echo csrf_field(); ?>
                
                <h5><?php echo e(trans('app.payment_schedule')); ?></h5>
                <br>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for=""><?php echo e(trans('app.paid_date')); ?></label>
                        <input type="text" class="form-control date-picker" name="paid_date" value="<?php echo e(old('paid_date', displayDate($schedule->paid_date))); ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for=""><?php echo e(trans('app.paid_principal')); ?></label>
                        <input type="text" class="form-control decimal-input" name="paid_principal" value="<?php echo e($schedule->paid_principal); ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for=""><?php echo e(trans('app.paid_interest')); ?></label>
                        <input type="text" class="form-control decimal-input" name="paid_interest" value="<?php echo e($schedule->paid_interest); ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for=""><?php echo e(trans('app.penalty_amount')); ?></label>
                        <input type="text" class="form-control decimal-input" name="paid_penalty" value="<?php echo e($schedule->paid_penalty); ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for=""><?php echo e(trans('app.payment_amount')); ?></label>
                        <input type="text" class="form-control decimal-input" name="paid_total" value="<?php echo e($schedule->paid_total); ?>">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for=""><?php echo e(trans('app.status')); ?></label>
                        <select name="paid_status" id="" class="form-control">
                            <option value="0" <?php if($schedule->paid_status==0): ?> selected  <?php endif; ?>><?php echo e(trans('app.partial')); ?></option>
                            <option value="1" <?php if($schedule->paid_status==1): ?> selected  <?php endif; ?>><?php echo e(trans('app.paid')); ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-success">
                        <?php echo e(trans('app.save')); ?>

                    </button>
                </div>
            </form>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery.validate.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap4-datetimepicker.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/date-time-picker.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery-number.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/number.js')); ?>"></script>
    <script src="<?php echo e(asset('js/sweetalert.min.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>