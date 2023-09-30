<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
            <h3 class="page-heading"><?php echo e(trans('app.product_ime') . ' - '.$title); ?></h3>

            <form id="" method="post" action="<?php echo e(route('product.save-ime')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="form-group">
                    <input type="text" class="form-control" name="code" required>
                </div>
                <button class="btn btn-success" type="submit"><?php echo e(trans('app.save')); ?></button>
            </form>
        </div>

    </div>
</div>
