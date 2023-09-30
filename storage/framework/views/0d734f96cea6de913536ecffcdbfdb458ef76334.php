<button class="btn btn-danger btn-sm mb-1 btn-delete <?php echo e($class ?? ''); ?>" <?php echo e($disabled ?? ''); ?>

        title="<?php echo e(trans('app.delete')); ?>" data-url="<?php echo e($url ?? ''); ?>">
    <i class="fa fa-trash-o"></i>
</button>
 