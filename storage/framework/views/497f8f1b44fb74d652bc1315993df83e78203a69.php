<?php $__env->startSection('title', trans('app.payment_report')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.payment_report')); ?></h3>

    <form method="get" action="<?php echo e(route('report.client_payment')); ?>">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-sm-6 col-md-3 form-group">
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
            <div class="col-sm-6 col-md-3 form-group">
              <label for="branch" class="control-label"><?php echo e(trans('app.agent')); ?></label>
              <select name="agent" class="form-control select2">
                <option value=""><?php echo e(trans('app.agent')); ?></option>
                <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($agent->id); ?>" <?php echo e(request('agent') == $agent->id ? 'selected' : ''); ?>>
                    <?php echo e($agent->name); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div class="col-sm-6 col-md-3 form-group">
              <label for="branch" class="control-label"><?php echo e(trans('app.type')); ?></label>
              <select name="type" class="form-control select2">
                <option value=""><?php echo e(trans('app.select_option')); ?></option>
                <option value="leasing-dp" <?php echo e(request('type') == 'leasing-dp' ? 'selected' : ''); ?>>
                    បង់ប្រាក់ដើម
                </option>
                <option value="leasing" <?php echo e(request('type') == 'leasing' ? 'selected' : ''); ?>>
                  បង់ប្រាក់ប្រចាំខែ
              </option>
              </select>
            </div>
            <div class="col-sm-6 col-md-3 form-group">
              <label><?php echo e(trans('app.start_date')); ?></label>
              <div class="input-group">
                <input type="text" name="start_date" id="start_date" class="form-control date-picker" autocomplete="off" value="<?php echo e(request('start_date')); ?>" placeholder="<?php echo e(trans('app.date_placeholder')); ?>">
                <span class="input-group-append"><i class="input-group-text fa fa-calendar"></i></span>
              </div>
            </div>
            <div class="col-sm-6 col-md-3 form-group">
              <label><?php echo e(trans('app.end_date')); ?></label>
              <div class="input-group">
                <input type="text" name="end_date" id="end_date" class="form-control date-picker" autocomplete="off" value="<?php echo e(request('end_date')); ?>" placeholder="<?php echo e(trans('app.date_placeholder')); ?>">
                <span class="input-group-append"><i for="start_date" class="input-group-text fa fa-calendar"></i></span>
              </div>
            </div>
            <div class="col-sm-6 col-md-3 form-group">
              <label for="branch" class="control-label"><?php echo e(trans('app.search')); ?></label>
              <input type="text" name="q" class="form-control" value="<?php echo e(request('q') ?? ''); ?>" placeholder="<?php echo e(__('app.search-account-number')); ?>">
            </div>
            <div class="col-sm-6 col-md-2">
              <?php echo $__env->make('partial.button-search', ['class' => 'btn-block mt-4'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
          </div>
        </div>
      </div>
    </form>
    <br>

    <div class="row justify-content-end">
      <div class="col-md-6 table-responsive">
        <table class="table table-hover table-bordered">
          <tbody>
            <tr>
              <th><?php echo e(__('app.date')); ?></th>
              <th><?php echo e($date); ?></th>
            </tr>
            <tr>
              <th><?php echo e(__('app.total_invoice')); ?></th>
              <th><?php echo e(($itemCount ?? trans('app.n/a'))); ?></th>
            </tr>
            <tr>
              <th><?php echo e(__('app.total_amount')); ?></th>
              <th>$ <?php echo e($totalAmount); ?></th>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    

    <div class="table-responsive resize-w">
      <table class="table table-hover table-striped table-bordered">
        <thead>
          <tr>
            <th class="text-center"><?php echo e(trans('app.no_sign')); ?></th>
            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('payment_date', trans('app.payment_date')));?></th>
            <th class="tex-right"><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('payment_amount', trans('app.paid_amount')));?></th>
            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('payment_method', trans('app.payment_method')));?></th>
            <th><?php echo e(trans('app.client_code')); ?></th>
            <th><?php echo e(trans('app.client')); ?></th>
            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('reference_number', trans('app.reference_number')));?></th>
            <th><?php echo e(trans('app.receiver')); ?></th>
            <th><?php echo e(trans('app.note')); ?></th>
            <th><?php echo e(trans('app.action')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td class="text-center"><?php echo e($offset++); ?></td>
            <td><?php echo e(displayDate($payment->payment_date)); ?></td>
            <td class="text-right"><b>$ <?php echo e(decimalNumber($payment->payment_amount, true)); ?></b></td>
            <td><?php echo e(paymentMethods($payment->payment_method)); ?></td>
            <td><?php echo $__env->make('partial.loan-detail-link', ['loan' => $payment->loan], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
            <td><?php echo $__env->make('partial.client-detail-link', ['client' => $payment->client], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
            <td><?php echo e($payment->reference_number); ?></td>
            <td><?php echo e($payment->user->name ?? trans('app.n/a')); ?></td>
            <td><?php echo e($payment->note); ?></td>
            <td class="text-center">
              
              <a href="<?php echo e(route('report.loan_portfolio', $payment->client)); ?>" class="btn btn-info btn-sm mb-1"><?php echo e(trans('app.loan_portfolio')); ?></a>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
      <?php echo $payments->appends(Request::except('page'))->render(); ?>

    </div>
  </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
  <script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
  
  <script>
    $(document).ready(function() {
      $("#start_date, #end_date").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        orientation: 'bottom right'
      });
    });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>