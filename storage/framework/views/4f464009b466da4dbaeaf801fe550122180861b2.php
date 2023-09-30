<div class="card mb-2">
  <div class="card-header">
    <form method="get" action="">
      <div class="row">
        <div class="col-lg-12 pl-1 pr-0">
          <div class="row">
            <?php if(Route::current()->uri() == 'product/stock'): ?>
              <div class="col-md-3">
                <label for="location"><?php echo e(trans('app.warehouse')); ?></label>
                <select name="location" id="location" class="form-control select2">
                  <option value=""><?php echo e(trans('app.all')); ?></option>
                  <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($location->id); ?>" <?php echo e(request('location') == $location->id ? 'selected' : ''); ?>>
                      <?php echo e($location->location); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            <?php endif; ?>

            <div class="col-md-3">
              <label for=""><?php echo e(trans('app.product_type')); ?></label>
              <select name="type" class="form-control">
                <option value=""><?php echo e(trans('app.all')); ?></option>
                <?php $__currentLoopData = ['single', 'variant']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $productType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($productType); ?>" <?php echo e($productType==request('type') ? 'selected' : ''); ?>><?php echo e(ucfirst($productType)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="prod_type"><?php echo e(trans('app.product_category')); ?></label>
              <select name="prod_type" id="prod_type" class="form-control select2">
                <option value=""><?php echo e(trans('app.all')); ?></option>
                <?php $__currentLoopData = $productCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($t->id); ?>" <?php echo e(request('prod_type') == $t->id ? 'selected' : ''); ?>>
                    <?php echo e($t->value); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="brand"><?php echo e(trans('app.brand')); ?></label>
              <select name="brand" id="brand" class="form-control select2">
                <option value=""><?php echo e(trans('app.all')); ?></option>
                <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($brand->id); ?>" <?php echo e(request('brand') == $brand->id ? 'selected' : ''); ?>>
                    <?php echo e($brand->value); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-md-3">
              <label for="brand"><?php echo e(trans('app.name')); ?>/<?php echo e(trans('app.product_code/sku')); ?>/<?php echo e(trans('app.selling_price')); ?></label>
              <?php echo $__env->make('partial.search-input-group', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>