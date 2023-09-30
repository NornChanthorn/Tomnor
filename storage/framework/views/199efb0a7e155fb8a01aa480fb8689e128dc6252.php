<?php $__env->startSection('title', trans('app.purchase')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.purchase')); ?></h3>
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-9">
            <form id="sale_search_f" method="get" action="">
              <input type="hidden" name="start" value="<?php echo e(request('start')); ?>" />
              <input type="hidden" name="end" value="<?php echo e(request('end')); ?>" />
              <div class="row">
                <?php if(!auth()->user()->staff): ?>
                  <div class="col-md-4">
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
                <div class="col-md-4">
                  <label for="supplier" class="control-label"><?php echo e(trans('app.supplier')); ?></label>
                  <div class="input-group">
                    <select name="supplier" id="supplier" class="form-control select2" required>
                      <option value=""><?php echo e(trans('app.all')); ?></option>
                      <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($supplier->id); ?>" <?php echo e(selectedOption($supplier->id, request('supplier'))); ?>><?php echo e($supplier->defualt_business_name ?? $supplier->name); ?></option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <label for="sale_date" class="control-label">
                    <?php echo e(trans('app.purchase_status')); ?>

                  </label>
                  <select name="status" id="status" class="form-control" required>
                    <option value=""><?php echo e(trans('app.all')); ?></option>
                    <?php $__currentLoopData = purchaseStatuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $_sta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k); ?>" <?php echo e(selectedOption($k, request('status'))); ?>>
                      <?php echo e($_sta); ?>

                    </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
              </div>
            </form>
          </div>
          <div class="col-md-3">
            <label for="sell_list_filter_date_range"><?php echo e(trans('app.sale_date')); ?></label>
            <input placeholder="<?php echo e(trans('app.select_date_range')); ?>" class="form-control" readonly="" name="sell_list_filter_date_range" type="text" id="sell_list_filter_date_range" value="<?php if(!empty(request('start'))): ?><?php echo e(dateIsoFormat(request('start'), 'd/m/Y')); ?><?php endif; ?> ~ <?php if(!empty(request('end'))): ?><?php echo e(dateIsoFormat(request('end'), 'd/m/Y')); ?><?php endif; ?>">
          </div>
        </div>
      </div>
    </div>
    <br>

    <div class="row">
      <div class="col-lg-6">
        <?php echo $__env->make('partial.anchor-create', ['href' => route('purchase.create')], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      </div>
      <div class="col-lg-6 text-right">
        <?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      </div>
    </div>

    <div class="table-responsive resize-w">
      <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr>
            <th><?php echo e(trans('app.no_sign')); ?></th>
            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('purchase_date', trans('app.purchase_date')));?></th>
            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('reference_no', trans('app.invoice_id')));?></th>
            <th><?php echo e(trans('app.location')); ?></th>
            <th><?php echo e(trans('app.supplier')); ?></th>
            <th class="text-center"><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('purchase_status', trans('app.status')));?></th>
            <th class="text-center"><?php echo e(trans('app.payment_status')); ?></th>
            <th class="text-right"><?php echo e(trans('app.payment_amount')); ?></th>
            <th class="text-right"><?php echo e(trans('app.paid_amount')); ?></th>
            <th class="text-right"><?php echo e(trans('app.due_amount')); ?></th>
            <th class="text-center"><?php echo e(trans('app.created_by')); ?></th>
            <th class="text-center"><?php echo e(trans('app.action')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
            $due_amount = $total_due = $total_amount = $total_paid = $total_items = 0;
          ?>
          <?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $paid_amount = $purchase->invoices->sum('payment_amount') ?? 0;
              $due_amount = $purchase->final_total - $paid_amount;

              $total_amount += $purchase->final_total;
              $total_paid += $paid_amount;
              $total_due += $due_amount;
            ?>

            <tr>
              <td><?php echo e($offset++); ?></td>
              <td><?php echo e(displayDate($purchase->transaction_date)); ?></td>
              <td>
                <?php echo e($purchase->ref_no); ?>

                <?php if(@$purchase->return_parent->id): ?>
                <span class="text-danger">
                  <i class="fa fa-undo" aria-hidden="true"></i>
                </span>
                
              <?php endif; ?> 
              </td>
              <td><?php echo $__env->make('partial.branch-detail-link', ['branch' => $purchase->warehouse], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
              <td><?php echo e($purchase->client->supplier_business_name ?? $purchase->client->name); ?></td>
              <td class="text-center"><?php echo $__env->make('partial.purchase-status-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
              <td class="text-center"><?php echo e(paymentStatus($purchase->payment_status)); ?></td>
              <td class="text-right">$ <?php echo e(decimalNumber($purchase->final_total,2)); ?></td>
              <td class="text-right">$ <?php echo e(decimalNumber($paid_amount,2)); ?></td>
              <td class="text-right">$ <?php echo e(decimalNumber($due_amount,2)); ?></td>
              <td><?php echo e(!empty($purchase->creator->staff) ? $purchase->creator->staff->name : $purchase->creator->name); ?></td>
              <td class="text-center">
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                  <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-tasks"></i></button>
                  <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-primary dropdown-toggle" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu dropdown-menu-right">
                      <?php if(Auth::user()->can('po.browse')): ?>
                        <a href="<?php echo e(route('purchase.invoice', $purchase->id)); ?>" title="<?php echo e(trans('app.invoice')); ?>" class="dropdown-item" target="_blank"><i class="fa fa-print"></i> <?php echo e(trans('app.invoice')); ?></a>
                      <?php endif; ?>
              
                      <div class="dropdown-divider"></div>
                      <?php if(Auth::user()->can('po.browse')): ?>
                        <a href="<?php echo e(route('purchase.show', $purchase->id)); ?>" class="dropdown-item" title="<?php echo e(__('app.view_detail')); ?>"><i class="fa fa-eye"></i> <?php echo e(__('app.view_detail')); ?></a>
                      <?php endif; ?>

                      
                      <?php if(Auth::user()->can('po.edit') && $purchase->status!='final'): ?>
                        <a href="<?php echo e(route('purchase.edit', $purchase->id)); ?>" class="dropdown-item" title="<?php echo e(__('app.edit')); ?>"><i class="fa fa-pencil-square-o"></i> <?php echo e(__('app.edit')); ?></a>
                      <?php endif; ?>
                      
                      
                      <?php if(Auth::user()->can('po.delete')): ?>
                        <a href="javascript:void(0);" title="<?php echo e(__('app.delete')); ?>" data-url="<?php echo e(route('purchase.destroy', $purchase->id)); ?>" class="dropdown-item btn-delete"><i class="fa fa-trash-o"></i> <?php echo e(__('app.delete')); ?></a>
                      <?php endif; ?>
                      <div class="dropdown-divider"></div>
                      <?php if($purchase->payment_status = 'paid'): ?>
                        <a href="<?php echo e(route('payments.create', $purchase->id)); ?>" class="dropdown-item add_payment_modal"><i class="fa fa-money"></i> <?php echo e(trans('app.add_payment')); ?></a>
                      <?php endif; ?>
                      <a href="<?php echo e(route('payments.show', $purchase->id)); ?>" class="dropdown-item view_payment_modal"><i class="fa fa-money"></i> <?php echo e(trans('app.view_payments')); ?></a>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
          <td colspan="7" align="right"><b><?php echo e(trans('app.total')); ?></b></td>
          <td align="right"><b>$ <?php echo e(decimalNumber($total_amount, true)); ?></b></td>
          <td align="right"><b>$ <?php echo e(decimalNumber($total_paid, true)); ?></b></td>
          <td align="right"><b>$ <?php echo e(decimalNumber($total_due, true)); ?></b></td>
          <td></td>
          <td></td>
        </tfoot>
      </table>
      <?php echo $purchases->appends(Request::except('page'))->render(); ?>

    </div>
  </div>
</main>

<div class="modal fade payment_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
  <script src="<?php echo e(asset('js/jquery-number.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/number.js')); ?>"></script>
  <script>
    $(document).ready(function() {
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });

      //Default settings for daterangePicker
      var ranges = {};
      var moment_date_format = 'DD/MM/YYYY';
      ranges['<?php echo e(trans('app.today')); ?>'] = [moment(), moment()];
      ranges['<?php echo e(trans('app.yesterday')); ?>'] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
      ranges['<?php echo e(trans('app.last_7_days')); ?>'] = [moment().subtract(6, 'days'), moment()];
      ranges['<?php echo e(trans('app.last_30_days')); ?>'] = [moment().subtract(29, 'days'), moment()];
      ranges['<?php echo e(trans('app.this_month')); ?>'] = [moment().startOf('month'), moment().endOf('month')];
      ranges['<?php echo e(trans('app.last_month')); ?>'] = [
        moment().subtract(1, 'month').startOf('month'),
        moment().subtract(1, 'month').endOf('month'),
      ];

      //Date range as a button
      $('#sell_list_filter_date_range').daterangepicker({
        ranges: ranges,
        startDate: '<?php echo e('01/01/'.date('Y')); ?>',
        endDate: '<?php echo e('31/12/'.date('Y')); ?>',
        locale: {
          cancelLabel: '<?php echo e(trans('app.clear')); ?>',
          applyLabel: '<?php echo e(trans('app.apply')); ?>',
          customRangeLabel: '<?php echo e(trans('app.custom_range')); ?>',
          format: moment_date_format,
          toLabel: '~',
        },
        opens: 'left',
        autoUpdateInput: false
      }, function (start, end) {
        $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
        submitSearchForm();
      });

      $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#sell_list_filter_date_range').val('');
        submitSearchForm();
      });

      $('#location, #client, #status').change(function () {
        submitSearchForm();
      });
    });

    $(document).on('click', '.add_payment_modal', function(e) {
      e.preventDefault();
      var container = $('.payment_modal');

      $.ajax({
        url: $(this).attr('href'),
        type: "GET",
        dataType: 'json',
        success: function(result) {
          if (result.status == 'due') {
            container.html(result.view).modal('show');
            $('#payment_date').datepicker({
              format: 'dd-mm-yyyy'
            });
            formatNumericFields();
            container.find('form#transaction_payment_add_form').validate();
          }
        },
      });
    });

    $(document).on('click', '.view_payment_modal', function(e) {
      e.preventDefault();
      var container = $('.payment_modal');

      $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        dataType: 'html',
        success: function(result) {
          $(container).html(result).modal('show');
        },
      });
    });

    function submitSearchForm() {
      var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
      $('input[name=start]').val(start);
      $('input[name=end]').val(end);
      $('#sale_search_f').submit();
    }
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>