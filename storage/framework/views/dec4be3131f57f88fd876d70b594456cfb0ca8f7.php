<?php $__env->startSection('title', trans('app.purchase_sale')); ?>

<?php $__env->startSection('content'); ?>
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(trans('app.report').trans('app.purchase_sale')); ?></h3>
      <form method="get" action="<?php echo e(route('report.purchase-sale')); ?>" class="mb-4" id="sale_search_f">
        <div class="card">
          <div class="card-header">
            <div class="row">
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
              
              <div class="form-group col-sm-3 col-lg-2 pr-0">
                <label for="start_date" class="control-label"><?php echo e(trans('app.start_date')); ?></label>
                <input type="text" name="start_date" id="start_date" class="form-control date-picker" placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(displayDate($startDate)); ?>">
              </div>

              
              <div class="form-group col-sm-3 col-lg-2 pr-0">
                <label for="end_date" class="control-label"><?php echo e(trans('app.end_date')); ?></label>
                <input type="text" name="end_date" id="end_date" class="form-control date-picker" placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(displayDate($endDate)); ?>">
              </div>
            </div>
            <div class="text-right">
              <?php echo $__env->make('partial.button-search', ['class' => 'mt-4'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
          </div>
          
          <div class="card-body">
            <h5><?php echo trans('app.purchase_sale') .' '. trans('app.between') . ' ' . displayDate($startDate) . ' ' . trans('app.to') . ' ' . displayDate($endDate)
              . ' (' . $selectedBranch . ')'; ?></h5>
            <div class="row">
              <div class="col-md-6">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr class="bg-success text-white">
                      <td colspan="2"><?php echo e(trans('app.purchase')); ?></td>
                    </tr>
                    <tr>
                      <th><?php echo e(trans('app.total_purchase_amount')); ?></th>
                      <td>$ <span class="total_items"><?php echo e(decimalNumber($report->total_purchase, true)); ?></span></td>
                    </tr>
                    <tr>
                      <th><?php echo e(trans('app.total_due_purchase_amount')); ?></th>
                      <td>$ <span class="total_amount"><?php echo e(decimalNumber($report->total_due_purchase, true)); ?></span></td>
                    </tr>
                    <tr class="bg-success text-white">
                      <td colspan="2"><?php echo e(trans('app.sale')); ?></td>
                    </tr>
                    <tr>
                      <th><?php echo e(trans('app.total_sale_amount')); ?></th>
                      <td>$ <span class="total_paid"><?php echo e(decimalNumber($report->total_sale, true)); ?></span></td>
                    </tr>
                    <tr>
                      <th><?php echo e(trans('app.total_due_sale_amount')); ?></th>
                      <td>$ <span class="total_due"><?php echo e(decimalNumber($report->total_due_sale, true)); ?></span></td>
                    </tr>
                    <tr class="bg-success text-white">
                      <td colspan="2">ប្រាក់ចំនេញ</td>
                    </tr>
                    <tr>
                      <th><?php echo e(trans('app.total_purchase_amount')); ?> - <?php echo e(trans('app.total_sale_amount')); ?></th>
                      <td>$ <span class="total_summary"><?php echo e(decimalNumber($report->summary, true)); ?></span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </form>
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