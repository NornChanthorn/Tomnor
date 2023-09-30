<?php if(isset($client->profile_photo)): ?>
    <img src="<?php echo e(asset($client->profile_photo)); ?>" onerror="this.src='<?php echo e(asset('/user.png')); ?>';this.onerror='';" alt="<?php echo e(trans('app.missing_image')); ?>"
         class="img-thumbnail" width="50">
<?php else: ?>
    <?php echo e(trans('app.none')); ?>

<?php endif; ?>
