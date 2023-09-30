<?php $__env->startSection('title', trans('app.product_sell')); ?>

<?php $__env->startSection('content'); ?>
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(trans('app.product_sell')); ?></h3>
      <form method="get" action="<?php echo e(route('report.product-sell')); ?>">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-sm-3 col-lg-2 form-group">
                <label for="type" class="control-label"><?php echo e(trans('app.type')); ?></label>
                <select name="type" id="type" class="form-control select2">
                  <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type); ?>" <?php echo e(request('type') == $type ? 'selected' : ''); ?>>
                      <?php echo e(trans('app.'.$type)); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div class="col-sm-6 col-lg-3 form-group">
                <label for="branch" class="control-label"><?php echo e(trans('app.branch')); ?></label>
                <select name="branch" id="branch" class="form-control select2">
                  <option value=""><?php echo e(trans('app.all_branches')); ?></option>
                  <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch') == $branch->id ? 'selected' : ''); ?>>
                      <?php echo e($branch->location); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div class="col-sm-3 col-lg-2">
                <label for="start_date"><?php echo e(trans('app.start_date')); ?></label>
                <input type="text" name="start_date" id="start_date" class="form-control date-picker" value="<?php echo e(request('start_date')); ?>" placeholder="<?php echo e(trans('app.date_placeholder')); ?>" readonly>
              </div>
              <div class="col-sm-3 col-lg-2">
                <label for="end_date"><?php echo e(trans('app.end_date')); ?></label>
                <input type="text" name="end_date" id="end_date" class="form-control date-picker" value="<?php echo e(request('end_date')); ?>" placeholder="<?php echo e(trans('app.date_placeholder')); ?>" readonly>
              </div>
              <div class="col-sm-6 col-lg-3">
                <label for=""><?php echo e(trans('app.product_name')); ?>/<?php echo e(trans('app.product_code/sku')); ?></label>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control" id="q" placeholder="<?php echo e(trans('app.search')); ?> ...">
              </div>
            </div>
            <div class="text-right">
              <?php echo $__env->make('partial.button-search', ['class' => 'mt-4'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-sm-6">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <th><?php echo e(trans('app.total_product')); ?></th>
                      <th><?php echo e(number_format($totalProduct)); ?></th>
                    </tr>
                    <tr>
                      <th><?php echo e(trans('app.total_product_price')); ?></th>
                      <th>$ <?php echo e(decimalNumber($totalProductPrice, true)); ?></th>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </form>
      <br>
      
      <div class="table-responsive resize-w">
        <table class="table table-hover table-striped table-bordered">
          <thead>
            <tr>
              <th class="text-center"><?php echo e(trans('app.no_sign')); ?></th>
              <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('branch_id', trans('app.branch')));?></th>
              <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('product_id', trans('app.product')));?></th>
              <th class="text-center"><?php echo e(trans('app.type')); ?></th>
              <th class="text-left"><?php echo e(trans('app.sale_date')); ?></th>
              <th class="text-right"><?php echo e(trans('app.product_price')); ?></th>
              <th class="text-center"><?php echo e(trans('app.quantity')); ?></th>
              <th class="text-right"><?php echo e(trans('app.total')); ?></th>
              <th><?php echo e(trans('app.note')); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td class="text-center"><?php echo e($offset++); ?></td>
                <td><?php echo e($loan->transaction->warehouse->location); ?></td>
                <td><?php echo e($loan->product->name.($loan->variations&&$loan->variations->name!='DUMMY' ? ' - '.$loan->variations->name : '')); ?></td>
                <td class="text-center"><?php echo e(trans('app.'.$loan->transaction->type)); ?></td>
                <td class="text-left"><?php echo e(displayDate($loan->transaction->transaction_date)); ?></td>
                <td class="text-right"><b>$ <?php echo e(decimalNumber($loan->product->price, true)); ?></b></td>
                <td class="text-center"><?php echo e($loan->quantity); ?></td>
                <td class="text-right"><b>$ <?php echo e(decimalNumber(($loan->unit_price*$loan->quantity), true)); ?></b></td>
                <td><?php echo e($loan->transaction->additional_note); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
      <?php echo $loans->appends(Request::except('page'))->render(); ?>

    </div>
  </main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/date-time-picker.js')); ?>"></script>
  <script>
    $(document).ready(function() {
      $(".date-picker").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        orientation: 'bottom right'
      });
    });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>