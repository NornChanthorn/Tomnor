<div class="modal-dialog modal-sm modal-dialog-center" role="document">
    <div class="modal-content">

        <form action="<?php echo e(route('payments.savePaymentDate',$payment)); ?>" method="post">
            <?php echo e(csrf_field()); ?>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for=""><?php echo e(trans('app.payment_date')); ?></label>
                        <input type="text" class="form-control date-picker" name="payment_date" value="<?php echo e(displayDate($payment->payment_date)); ?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo e(trans('app.close')); ?></button>
                <?php echo $__env->make('partial.button-save', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
            
        </form>
    </div>
</div>