<?php $__env->startSection('title', trans('app.collateral_type')); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.collateral_type') . ' - ' . $title); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <form id="form-position" method="post" action="<?php echo e(route('collateral-type.save')); ?>">
                <?php echo csrf_field(); ?>
                <?php if(isset($collateral_type->id)): ?>
                    <input type="hidden" name="id" value="<?php echo e($collateral_type->id); ?>">
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-10 col-lg-8">
                        <label for="title" class="control-label">
                            <?php echo e(trans('app.title')); ?> <span class="required">*</span>
                        </label>
                        <div class="input-group">

                            <input type="text" name="title" id="title" class="form-control"
                            value="<?php echo e($collateral_type->value ?? old('title')); ?>" required>
                            <div class="input-group-append">
                            
                                <?php echo $__env->make('partial/button-save', [
                                    'class' => 'pull-right'
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            </div>
                        </div>
                      
                    </div>
                </div>
            </form>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-fileinput.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('plugins/easyAutocomplete/easy-autocomplete.min.css')); ?>">
  <style>
    .input-group #input { width: 85%!important; }
    .input-group .input-group-append { width: 15%; }
  </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/jquery.validate.min.js')); ?>"></script>
    <script>
        $(function () {
            $('#form-position').validate();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>