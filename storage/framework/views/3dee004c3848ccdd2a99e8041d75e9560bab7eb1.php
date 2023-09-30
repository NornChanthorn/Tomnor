<button type="submit" class="btn btn-success <?php echo e($class ?? ''); ?>"
    <?php if(isset($onClick)): ?> onclick="<?php echo e($onClick); ?>" <?php endif; ?>>
    <i class="fa fa-save pr-1"></i> <?php echo e(trans('app.save')); ?>

</button>
