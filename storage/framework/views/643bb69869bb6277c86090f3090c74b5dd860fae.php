<?php if(isset($staff->profile_photo)): ?>
    <img src="<?php echo e(asset($staff->profile_photo)); ?>" alt="<?php echo e(trans('app.missing_image')); ?>"
         class="img-thumbnail" width="50">
<?php else: ?>
    <?php echo e(trans('app.none')); ?>

<?php endif; ?>
