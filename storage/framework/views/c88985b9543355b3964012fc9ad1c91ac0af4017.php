<?php $__env->startSection('title', trans('app.product_stock')); ?>

<?php $__env->startSection('css'); ?>
  <style>
    .line-2 { line-height: 2.2; }
  </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(trans('app.product_stock_report')); ?></h3>
      <form method="get" action="<?php echo e(route('report.stock')); ?>" id="sale_search_f">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-md-6 col-lg-3 form-group">
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
              <div class="form-group col-md-6 col-lg-2">
                <label for="start_date" class="control-label"><?php echo e(trans('app.start_date')); ?></label>
                <input type="text" name="start_date" id="start_date" class="form-control date-picker" placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(displayDate($startDate)); ?>" readonly>
              </div>
              <div class="form-group col-md-6 col-lg-2">
                <label for="end_date" class="control-label"><?php echo e(trans('app.end_date')); ?></label>
                <input type="text" name="end_date" id="end_date" class="form-control date-picker" placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(displayDate($endDate)); ?>" readonly>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for=""><?php echo e(trans('app.product_name')); ?>/<?php echo e(trans('app.product_code/sku')); ?></label>
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control" id="q" placeholder="<?php echo e(trans('app.search')); ?> ...">
              </div>
              <div class="col-sm-12 text-right">
                <?php echo $__env->make('partial.button-search', ['class' => 'mt-4 line-2'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
              </div>
            </div>
          </div>

          <div class="card-body">
            <h5><?php echo trans('app.product_stock_report') .' '. trans('app.between') . ' ' . ($startDate ? displayDate($startDate) : '___') . ' ' . trans('app.to') . ' ' . ($endDate ? displayDate($endDate) : '___') . ' (' . $selectedBranch . ')'; ?></h5>
            <div class="row">
              <div class="col-md-6">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <th><?php echo e(trans('app.total_products_stock')); ?></th>
                      <td><?php echo e($report->total_stock); ?></td>
                    </tr>
                    <tr>
                      <th><?php echo e(trans('app.total_amount')); ?></th>
                      <td>$ <?php echo e(decimalNumber($report->total_stock_amount, true)); ?></td>
                    </tr>

                    <tr>
                      <th><?php echo e(trans('app.total_products_stock_oversale')); ?></th>
                      <td><?php echo e($report->total_stock_oversale); ?></td>
                    </tr>
                    <tr>
                      <th><?php echo e(trans('app.total_amount_oversale')); ?></th>
                      <td>$ <?php echo e(decimalNumber($report->total_stock_amount_oversale, true)); ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-md-6">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <th><?php echo e(trans('app.purchased_products')); ?></th>
                      <td><?php echo e($report->total_purchase); ?></td>
                    </tr>
                    <tr>
                      <th><?php echo e(trans('app.sold_products')); ?></th>
                      <td><?php echo e($report->total_sale); ?></td>
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
              <th><?php echo e(trans('app.product_code')); ?></th>
              <th><?php echo e(trans('app.product')); ?></th>
              <th class="text-right"><?php echo e(trans('app.product_price')); ?></th>
              <th class="text-center"><?php echo e(trans('app.current_stock')); ?></th>
              <th class="text-center"><?php echo e(trans('app.purchased_unit')); ?></th>
              <th class="text-center"><?php echo e(trans('app.sold_unit')); ?></th>
              <th class="text-center"><?php echo e(trans('app.transfered_unit_in')); ?></th>
              <th class="text-center"><?php echo e(trans('app.transfered_unit_out')); ?></th>
              <th class="text-center"><?php echo e(trans('app.adjusted_unit')); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
                $unit = $loan->unit;
                $stock = $loan->stock ?? 0;
              ?>
              <tr>
                <td class="text-center"><?php echo e($offset++); ?></td>
                <td>
                  <a href="<?php echo e(route('product.show', $loan->product_id)); ?>" target="_blank"><?php echo e(wordwrap(strlen($loan->variantion_sku)==0 ? $loan->sku : $loan->variantion_sku, 4, ' ', true)); ?></a>
                </td>
                <td><?php echo e($loan->product.($loan->variantion_name!='DUMMY' ? ' - '.$loan->variantion_name : '')); ?></td>
                <td class="text-right"><b>$ <?php echo e(decimalNumber($loan->unit_price, true)); ?></b></td>
                <td class="text-center"><?php echo e(number_format($loan->stock, 0) .' '. $unit); ?></td>
                <td class="text-center"><?php echo e(number_format($loan->total_purchased, 0) .' '. $unit); ?></td>
                <td class="text-center"><?php echo e(number_format($loan->total_sold, 0) .' '. $unit); ?></td>
                <td class="text-center"><?php echo e(number_format($loan->total_transfered_in, 0) .' '. $unit); ?></td>
                <td class="text-center"><?php echo e(number_format($loan->total_transfered_out, 0) .' '. $unit); ?></td>
                <td class="text-center"><?php echo e(number_format($loan->total_adjusted, 0) .' '. $unit); ?></td>
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

    function submitSearchForm() {
      $('#sale_search_f').submit();
    }
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>