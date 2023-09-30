<?php $__env->startSection('title', trans('app.cash_income_report')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <div class="row">
      <div class="col-sm-12">
        <h3 class="page-heading"><?php echo e(trans('app.cash_income_report')); ?></h3>
        <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <form action="" method="get" class="mb-4">
            <div class="card">
                <div class="card-header">
                  <div class="row">
                    <?php if(empty(auth()->user()->staff)): ?>
                      <div class="col-sm-6 col-md-4 form-group">
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
                      <div class="col-sm-3 col-lg-4 form-group">
                        <label for="start_date"><?php echo e(trans('app.start_date')); ?></label>
                        <input type="text" name="start_date" id="start_date" class="form-control date-picker" value="<?php echo e(request('start_date') ?? displayDate($startDate)); ?>" placeholder="<?php echo e(trans('app.date_placeholder')); ?>">
                      </div>
                      <div class="col-sm-3 col-lg-4 form-group">
                        <label for="end_date"><?php echo e(trans('app.end_date')); ?></label>
                        <input type="text" name="end_date" id="end_date" class="form-control date-picker" value="<?php echo e(request('end_date') ?? displayDate($startDate)); ?>" placeholder="<?php echo e(trans('app.date_placeholder')); ?>">
                      </div>
                    <?php endif; ?>
                    <div class="col-md-12 text-right">
                      <?php echo $__env->make('partial.button-search', ['class' => 'btn-lg'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </div>
                  </div>
                </div>
            </div>
        </form>
      </div>
     
    </div>
    <div class="row justify-content-center">
      <div class="col-md-10">
        <h5 class="pull-left"><?php echo e(trans('app.cash_income')); ?> (<?php echo e(displayDate($startDate)); ?> <?php echo e(trans('app.to')); ?> <?php echo e(displayDate($endDate)); ?>)</h5>
        <table class="table table-hover table-bordered">
            <tbody>
              <tr>
                  <th style="width: 40%"><?php echo e(trans('app.total').trans('app.depreciation_amount')); ?></th>
                  <th>$ <?php echo e(decimalNumber($total->downPayment, true)); ?></th>
                  <th><a href="<?php echo e(url('/report/client-payment?type=leasing-dp')); ?>">បង់ប្រាក់ដើម</a></th>
              </tr>
              <tr>
                  <th><?php echo e(trans('app.total_repayment_loan_amount')); ?></th>
                  <th>$ <?php echo e(decimalNumber($total->loanRepayment, true)); ?></th>
                  <th>
                    <a href="<?php echo e(url('/report/client-payment?type=leasing')); ?>">​បង់ប្រាក់ប្រចាំខែ</a>
                  </th>
              </tr>
              <tr>
                  <th><?php echo e(trans('app.total_sale_amount')); ?></th>
                  <th>$ <?php echo e(decimalNumber(($total->saleAmount - 0), true)); ?></th>
                  <th><a href="<?php echo e(url('/report/sell')); ?>"><?php echo e(trans('app.sell_report')); ?></a></th>
              </tr>
              <tr class="bg-success text-white">
                <?php
                  $totalAmount =$total->saleAmount + $total->loanRepayment + $total->downPayment;
                ?>
                  <th><?php echo e(trans('app.total_amount')); ?></th>
                  <th>$ <?php echo e(decimalNumber($totalAmount, true)); ?></th>
                  <th>
                  
                  </th>
              </tr>
              
                <tr>
                  <th>ទឹកប្រាក់ចំណាយទិញពីអតិថិជន</th>
                  <th>$ <?php echo e(decimalNumber($purchaseCustomer,true)); ?></th>
                  <th>
                    <a href="   <?php echo e(url('/report/purchase')); ?>"><?php echo e(trans('app.purchase_report')); ?></a>
                 
                  </th>
                </tr>
                <tr class="bg-success text-white">
                  <th>
                    សរុបទឹកប្រាក់នៅសល់
                  </th>
                  <th>
                    $ <?php echo e(decimalNumber(($totalAmount-$purchaseCustomer),true)); ?>

                  </th>
                  <th></th>
                </tr>
            </tbody>
        </table>
      </div>
    </div>
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