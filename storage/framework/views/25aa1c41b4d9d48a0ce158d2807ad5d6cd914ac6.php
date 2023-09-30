<?php $__env->startSection('title', trans('app.general_setting')); ?>

<?php $__env->startSection('css'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-fileinput.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <div class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(trans('app.general_setting')); ?></h3>
      <?php echo $__env->make('partial.flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

      <form action="<?php echo e(route('general_setting.save')); ?>" class="validated-form" method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <div class="row">
          
          <div class="col-md-6">
            <div class="form-group">
              <label for="site_title" class="control-label">
                <?php echo e(trans('app.site_title')); ?> <span class="required">*</span>
              </label>
              <input type="text" name="site_title" id="site_title" class="form-control" value="<?php echo e(old('site_title') ?? $setting->site_title); ?>" required>
            </div>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" name="enable_over_sale" value="1" <?php echo e(($setting->enable_over_sale==1) ? 'checked' : ''); ?> class="custom-control-input" id="enable-over-sale">
              <label class="custom-control-label" for="enable-over-sale"><?php echo e(trans('app.enable_over_sale')); ?></label>
            </div>
          </div>

          
          <div class="col-lg-6 form-group">
            <label for="site_logo" class="control-label">
              <?php echo e(trans('app.site_logo')); ?>

            </label>
            <input type="file" name="site_logo" id="site_logo" class="form-control" accept=".jpg, .jpeg, .png">
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12 text-right">
            <?php echo $__env->make('partial.button-save', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
          </div>
        </div>
      </form>
    </div>
  </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/bootstrap-fileinput.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-fileinput-fa-theme.js')); ?>"></script>
    <script src="<?php echo e(asset('js/init-file-input.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery.validate.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/form-validation.js')); ?>"></script>
    <script src="<?php echo e(asset('js/general-setting.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>