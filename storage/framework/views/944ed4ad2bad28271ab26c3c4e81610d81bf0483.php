<?php $__env->startSection('title', trans('app.client')); ?>

<?php $__env->startSection('content'); ?>
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(trans('app.collateral') . ' - ' . $title); ?></h3>
      <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      <form id="form-client" method="post" action="<?php echo e(route('collateral-save', ['loan_id'=>$loan_id])); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php if(isset($collateral->id)): ?>
            <input type="hidden" name="id" value="<?php echo e($collateral->id); ?>">
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="" class="control-label">
                        <?php echo e(__('app.name')); ?>

                        <span class="required">*</span>
                    </label>
                    <input type="text"  name="name" class="form-control" value="<?php echo e($collateral->name); ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="" class="control-label">
                        <?php echo e(__('app.collateral_type')); ?>

                        <span class="required">*</span>
                    </label>
                    <select name="type_id"  class="form-control" id="">
                        <?php $__currentLoopData = collateralTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $collateralType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($collateralType->id); ?>"><?php echo e($collateralType->value); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
                    </select>
                </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                  <label for="" class="control-label">
                      <?php echo e(__('app.value')); ?>

                      <span class="required">*</span>
                  </label>
                  <input type="text"  name="value" class="form-control decimal-input" value="<?php echo e($collateral->value  ?? 0); ?>" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                  <label for="" class="control-label">
                      <?php echo e(__('app.file')); ?>

                  </label>
                  <input type="file"  name="files" class="form-control">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                  <label for="" class="control-label">
                      <?php echo e(__('app.note')); ?>

                  </label>
                  <textarea name="note"  class="form-control-feedback" id="" cols="30" rows="10"><?php echo $collateral->note; ?></textarea>
              </div>
              <?php echo $__env->make('partial/button-save', [
                  'class' => 'pull-right'
              ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>

        </div>
      </form>
    </div>
  </main>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/jquery-number.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/number.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>