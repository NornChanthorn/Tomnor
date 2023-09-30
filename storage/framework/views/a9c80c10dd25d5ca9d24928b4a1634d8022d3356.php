<?php $__env->startSection('title', trans('app.sale')); ?>

<?php $__env->startSection('content'); ?>
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(@$group_id ? groupContacts($group_id): trans('app.sale').trans('app.all')); ?></h3>
      <?php echo $__env->make('partial.flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

      <div class="card mb-2">
        <div class="card-header">
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
                <label for="sale_date" class="control-label">
                  <?php echo e(trans('app.sale_status')); ?>

                </label>
                <select name="status" id="status" class="form-control">
                  <option value=""><?php echo e(trans('app.all')); ?></option>
                  <?php $__currentLoopData = saleStatuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $_sta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($k); ?>" <?php echo e(selectedOption($k, request('status'))); ?>>
                    <?php echo e($_sta); ?>

                  </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div class="form-group col-sm-3 col-lg-2">
                <label for="start_date" class="control-label"><?php echo e(trans('app.start_date')); ?></label>
                <input type="text" name="start_date" id="start_date" class="form-control date-picker" readonly placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(displayDate(request('start_date'))); ?>">
              </div>
              <div class="form-group col-sm-3 col-lg-2">
                <label for="end_date" class="control-label"><?php echo e(trans('app.end_date')); ?></label>
                <input type="text" name="end_date" id="end_date" class="form-control date-picker" readonly placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(displayDate(request('end_date'))); ?>">
              </div>
              <div class="form-group col-sm-4 col-lg-4">
                    <label for=""><?php echo e(trans('app.search')); ?> <?php echo e(trans('app.invoice')); ?></label>
                    <input type="text" name="sale_code" value="<?php echo e(request('sale_code')); ?>" class="form-control" id="sale_code" placeholder="<?php echo e(trans('app.search')); ?> ...">
                </div>
                <div class="col-md-4">
                    <label for="sale_date" class="control-label">
                        <?php echo e(trans('app.payment_status')); ?>

                    </label>
                    <select name="payment_status" id="payment_status" class="form-control">
                        <option value=""><?php echo e(trans('app.all')); ?></option>
                        <?php $__currentLoopData = paymentStatus(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $_sta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k); ?>" <?php echo e(selectedOption($k, request('status'))); ?>>
                        <?php echo e($_sta); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-4">
                  <label for="client" class="control-label">
                    <?php echo e(trans('app.client')); ?>

                  </label>
                  <select name="client" id="client" class="form-control select2">
                    <option value=""><?php echo e(trans('app.select_option')); ?></option>
                  </select>
                </div>
            </div>
            <div class="text-right">
              <?php echo $__env->make('partial.button-search', ['class' => 'mt-4'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
          </form>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-6">
          <?php echo $__env->make('partial.anchor-create', ['href' => route('sale.create')], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
        <div class="col-lg-6 text-right"><?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></div>
      </div>

      <div class="table-responsive" style="min-height: 400px">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center"><?php echo e(trans('app.no_sign')); ?></th>
              <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('sale_date', trans('app.sale_date')));?></td>
              <th><?php echo e(trans('app.location')); ?></th>
              <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('sale_code', trans('app.sale_code')));?></td>
              <th><?php echo e(trans('app.client')); ?></th>
              <?php if(isAdmin() || empty(auth()->user()->staff)): ?>
                <th><?php echo e(trans('app.agent')); ?></th>
              <?php endif; ?>
              <th class="text-right"><?php echo e(trans('app.total_amount')); ?></th>
              <th class="text-right"><?php echo e(trans('app.paid_amount')); ?></th>
              <th class="text-right"><?php echo e(trans('app.due_amount')); ?></th>
              <th class="text-center"><?php echo e(trans('app.payment_status')); ?></th>
              <th class="text-center"><?php echo e(trans('app.sale_status')); ?></th>
              <th class="text-center"><?php echo e(trans('app.total_product')); ?></th>
              <th class="text-right"><?php echo e(trans('app.action')); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
              $due_amount = $total_due = $total_amount = $total_paid = $total_items = 0;
            ?>
            <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
                $items = $sale->sell_lines->count() ?? 0;
                $paid_amount = $sale->invoices->sum('payment_amount');
                $due_amount = $sale->final_total - $paid_amount;
                $total_due += $due_amount;
                $total_amount += $sale->final_total;
                $total_paid += $paid_amount;
                $total_items += $items;
              ?>
            <tr>
              <td align="center"><?php echo e($offset++); ?></td>
              <td><?php echo e(displayDate($sale->transaction_date)); ?></td>
              <td>
                <?php if($sale->warehouse): ?>
                  <?php echo $__env->make('partial.branch-detail-link', ['branch' => $sale->warehouse], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php else: ?>
                  <?php echo e(trans('app.none')); ?>

                <?php endif; ?>
                
              </td>
              <td>
                <?php echo e($sale->invoice_no); ?>

                <?php if(@$sale->return_parent->id): ?>
                  <span class="text-danger">
                    <i class="fa fa-undo" aria-hidden="true"></i>
                  </span>
                  
                <?php endif; ?>
                
              
              </td>
              <td>
                <?php echo e($sale->client->name); ?>

              </td>
              <?php if(isAdmin() || empty(auth()->user()->staff)): ?>
                <td>
                  <?php if($sale->staff): ?>
                    <?php echo $__env->make('partial.staff-detail-link', ['staff' => $sale->staff], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  <?php else: ?>
                    <?php echo e(trans('app.none')); ?>

                  <?php endif; ?>
                </td>
              <?php endif; ?>
              <td align="right">$ <?php echo e(decimalNumber($sale->final_total, true)); ?></td>
              <td align="right">$ <?php echo e(decimalNumber($paid_amount, true)); ?></td>
              <td align="right">$ <?php echo e(decimalNumber(($due_amount), true)); ?></td>
              <td align="center"><?php echo e(paymentStatus($sale->payment_status)); ?></td>
              <td align="center"><?php echo e(saleStatuses($sale->status)); ?></td>
              <td align="center"><?php echo e($items); ?></td>
              <td class="text-center">
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                  <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-tasks"></i></button>
                  <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-primary dropdown-toggle" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu dropdown-menu-right">
                      <?php if(Auth::user()->can('sale.browse')): ?>
                        <a href="<?php echo e(route('sale.invoice', $sale->id)); ?>" title="<?php echo e(trans('app.invoice')); ?>" class="dropdown-item" target="_blank"><i class="fa fa-print"></i> <?php echo e(trans('app.invoice')); ?></a>
                      <?php endif; ?>

                      <div class="dropdown-divider"></div>
                      <?php if(Auth::user()->can('sale.browse')): ?>
                        <a href="<?php echo e(route('sale.show', $sale->id)); ?>" class="dropdown-item" title="<?php echo e(__('app.view_detail')); ?>"><i class="fa fa-eye"></i> <?php echo e(__('app.view_detail')); ?></a>
                      <?php endif; ?>

                      <?php if(Auth::user()->can('sale.edit')): ?>
                        <a href="<?php echo e(route('sale.edit', $sale->id)); ?>" class="dropdown-item" title="<?php echo e(__('app.edit')); ?>"><i class="fa fa-pencil-square-o"></i> <?php echo e(__('app.edit')); ?></a>
                      <?php endif; ?>
                      <?php if(Auth::user()->can('sell-return.add') && $sale->payment_status = 'paid'): ?>
                        <a href="<?php echo e(route('sell-return.add', $sale->id)); ?>" class="dropdown-item" title="<?php echo e(__('app.sell-return')); ?>"><i class="fa fa-reply"></i> <?php echo e(__('app.sell-return')); ?></a>
                      <?php endif; ?>
                      <?php if(Auth::user()->can('sale.delete')): ?>
                        <a href="javascript:void(0);" title="<?php echo e(__('app.delete')); ?>" data-url="<?php echo e(route('sale.destroy', $sale->id)); ?>" class="dropdown-item btn-delete"><i class="fa fa-trash-o"></i> <?php echo e(__('app.delete')); ?></a>
                      <?php endif; ?>

                      <div class="dropdown-divider"></div>
                      <?php if($sale->payment_status ='paid'): ?>
                        <a href="<?php echo e(route('payments.create', $sale->id)); ?>" class="dropdown-item add_payment_modal"><i class="fa fa-money"></i> <?php echo e(trans('app.add_payment')); ?></a>
                      <?php endif; ?>

                      <a href="<?php echo e(route('payments.show', $sale->id)); ?>" class="dropdown-item view_payment_modal"><i class="fa fa-money"></i> <?php echo e(trans('app.view_payments')); ?></a>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
          <tfoot>
            <td colspan="<?php echo e(empty(auth()->user()->staff) ? 6 : 5); ?>" align="right"><b><?php echo e(trans('app.total')); ?></b></td>
            <td align="right"><b>$ <?php echo e(decimalNumber($total_amount, true)); ?></b></td>
            <td align="right"><b>$ <?php echo e(decimalNumber($total_paid, true)); ?></b></td>
            <td align="right"><b>$ <?php echo e(decimalNumber($total_due, true)); ?></b></td>
            <td colspan="2"></td>
            <td align="center"><?php echo e($total_items); ?></td>
            <td></td>
          </tfoot>
        </table>
        <?php echo $sales->appends(Request::except('page'))->render(); ?>

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
  <script type="text/javascript">
    $(document).ready( function() {
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });

      $(".date-picker").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        orientation: 'bottom right'
      });

      $("#client").select2({
        ajax: {
          url: "<?php echo e(route('contact.client-list')); ?>",
          dataType: 'json',
          data: function(params) {
            return {
              search: params.term,
              type: 'public',
            }
          },
          processResults: function(data) {
            return {
              results: data
            }
          }
        }
      });

      $('#client').change(function () {
        $('#client').select2('close');
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
      // var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
      // var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
      // $('input[name=start]').val(start);
      // $('input[name=end]').val(end);
      $('#sale_search_f').submit();
    }
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>