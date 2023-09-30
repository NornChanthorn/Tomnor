<?php $__env->startSection('title', trans('app.purchase_report')); ?>

<?php $__env->startSection('content'); ?>
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(trans('app.report').trans('app.purchase_report')); ?></h3>
      <form method="get" action="<?php echo e(route('report.purchase')); ?>" class="mb-4" id="sale_search_f">
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
              <div class="col-sm-4 col-lg-2 form-group">
                <label for="status" class="control-label"><?php echo e(trans('app.purchase_status')); ?></label>
                <select name="group" id="group" class="form-control select2 select2-no-search">
                  <option value=""><?php echo e(trans('app.select_option')); ?></option>
                  <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($item->id); ?>" <?php echo e(selectedOption($item->name, request('group'))); ?>>
                      <?php echo e($item->name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div class="col-sm-4 col-lg-2 form-group">
                <label for="status" class="control-label"><?php echo e(trans('app.purchase_status')); ?></label>
                <select name="status" id="status" class="form-control select2 select2-no-search">
                  <option value=""><?php echo e(trans('app.select_option')); ?></option>
                  <?php $__currentLoopData = purchaseStatuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusKey => $statusTitle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($statusKey); ?>" <?php echo e(selectedOption($statusKey, request('status'))); ?>>
                      <?php echo e($statusTitle); ?>

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
            <h5><?php echo trans('app.purchase') . trans('app.between') . ' ' . displayDate($startDate) . ' ' . trans('app.to') . ' ' . displayDate($endDate)
              . ' (' . $selectedBranch . ')'; ?></h5>
            <div class="row">
              <div class="col-md-6">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <th><?php echo e(trans('app.total_purchase_product')); ?></th>
                      <td><span class="total_items"><?php echo e($summeries->items); ?></span></td>
                    </tr>
                    <tr>
                      <th><?php echo e(trans('app.total_sale_amount')); ?></th>
                      <td>$ <span class="total_amount"><?php echo e(decimalNumber($summeries->total_amount, true)); ?></span></td>
                    </tr>
                    <tr>
                      <th><?php echo e(trans('app.total_sale_paid_amount')); ?></th>
                      <td>$ <span class="total_paid"><?php echo e(decimalNumber($summeries->paid_amount, true)); ?></span></td>
                    </tr>
                    <tr>
                      <th><?php echo e(trans('app.total_sale_due_amount')); ?></th>
                      <td>$ <span class="total_due"><?php echo e(decimalNumber($summeries->due_amount, true)); ?></span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </form>

      <div class="table-responsive resize-w">
        <table class="table table-hover table-striped table-bordered">
          <thead>
            <tr>
              <th class="text-center"><?php echo e(trans('app.no_sign')); ?></th>
              <th class="text-left"><?php echo e(trans('app.purchase_date')); ?></th>
              <th class="text-left"><?php echo e(trans('app.invoice_number')); ?></th>
              <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('branch_id', trans('app.branch')));?></th>
              <th class="text-left"><?php echo e(trans('app.supplier')); ?></th>
              <th class="text-center"><?php echo e(trans('app.quantity')); ?></th>
              <th class="text-right"><?php echo e(trans('app.payment_amount')); ?></th>
              <th class="text-right"><?php echo e(trans('app.paid_amount')); ?></th>
              <th class="text-right"><?php echo e(trans('app.due_amount')); ?></th>
              <th class="text-center"><?php echo e(trans('app.purchase_status')); ?></th>
              <th class="text-center"><?php echo e(trans('app.payment_status')); ?></th>
              <th class="text-center"><?php echo e(trans('app.note')); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
              $due_amount = $total_due = $total_amount = $total_paid = $total_items = 0;
            ?>
            <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
                $items = $loan->purchase_lines->sum('quantity') ?? 0;
                $paid_amount = $loan->invoices->sum('payment_amount') ?? 0;
                $due_amount = $loan->final_total - $paid_amount;
              ?>
              <tr>
                <td class="text-center"><?php echo e($offset++); ?></td>
                <td class="text-left"><?php echo e(displayDate($loan->transaction_date)); ?></td>
                <td class="text-left">
                  <a href="<?php echo e(route('purchase.show', $loan->id)); ?>" target="_blank"><?php echo e($loan->ref_no); ?></a>
                  <?php if(@$loan->return_parent->id): ?>
                    <span class="text-danger">
                      <i class="fa fa-undo" aria-hidden="true"></i>
                    </span>
                    
                  <?php endif; ?>
                </td>
                <td class="text-left">
                  <?php if($loan->warehouse): ?>
                    <?php echo $__env->make('partial.branch-detail-link', ['branch' => $loan->warehouse], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  <?php else: ?>
                    <?php echo e(trans('app.none')); ?>

                  <?php endif; ?>
                </td>
                <td class="text-left"><?php echo e($loan->client->supplier_business_name ?? $loan->client->name); ?></td>
                <td align="center"><?php echo e($items); ?></td>
                <td class="text-right">$ <?php echo e(decimalNumber($loan->final_total, true)); ?></td>
                <td class="text-right">$ <?php echo e(decimalNumber($paid_amount, true)); ?></td>
                <td class="text-right">$ <?php echo e(decimalNumber($due_amount, true)); ?></td>
                <td class="text-center"><?php echo e(purchaseStatuses($loan->status)); ?></td>
                <td class="text-center"><?php echo e(paymentStatus($loan->payment_status)); ?></td>
                <td class="text-center"><?php echo e($loan->additional_note); ?></td>
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