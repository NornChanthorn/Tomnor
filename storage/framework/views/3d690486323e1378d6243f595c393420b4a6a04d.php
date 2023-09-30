<?php if(isset($product->photo)): ?>
    <img src="<?php echo e(asset($product->photo)); ?>" alt="<?php echo e(trans('app.missing_image')); ?>"
         class="img-thumbnail" width="50">
<?php else: ?>
    <?php echo e(trans('app.none')); ?>

<?php endif; ?>
