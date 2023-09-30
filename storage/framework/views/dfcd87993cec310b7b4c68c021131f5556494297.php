<?php $__env->startSection('title', trans('app.loan')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.loan')); ?></h3>
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="card">
      <div class="card-header">
        <form method="get" action="">
          <div class="row">
            <div class="col-lg-2 col-md-4">
              <?php if(Auth::user()->can('loan-cash.add')): ?>
                <?php echo $__env->make('partial/anchor-create', ['href' => route('loan-cash.create')], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
              <?php endif; ?>
            </div>
            <?php echo $__env->make('partial.loan-search-fields', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
          </div>
        </form>
      </div>
    </div>
    <br>

    <?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="table-responsive resize-w" style="min-height: 500px">
      <table class="table table-hover table-bordered" >
        <thead>
          <tr>
            <th><?php echo e(trans('app.no_sign')); ?></th>
            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('account_number', trans('app.loan_code')));?></th>
            <th><?php echo e(trans('app.client')); ?></th>
            <th><?php echo e(trans('app.gender')); ?></th>
            <th><?php echo e(trans('app.phone_number')); ?></th>
            <th><?php echo e(trans('app.request')); ?><?php echo e(trans('app.amount')); ?> </th>
            
            <th><?php echo e(trans('app.installment')); ?> </th>
            <th><?php echo e(trans('app.frequency')); ?> </th>
            <th> <?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('created_at', trans('app.request_date')));?> </th>

            <th><?php echo e(trans('app.loan_disbursement')); ?> </th>
            <th><?php echo e(trans('app.next_payment_date')); ?> </th>
            <th><?php echo e(trans('app.loan_status')); ?> </th>
            <th><?php echo e(trans('app.action')); ?> </th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td><?php echo e($offset++); ?></td>
              <td><?php echo $__env->make('partial.loan-detail-link', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
              <td><?php echo $__env->make('partial.client-detail-link', ['client' => $loan->client], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
              <td><?php echo e(@$loan->client->gender? genders(@$loan->client->gender) : __('app.n/a')); ?></td>
              <td><?php echo e(@$loan->client->first_phone); ?> <?php echo e(@$loan->client->second_phone ? ', '.@$loan->client->second_phone : ''); ?></td>
              <td><?php echo e(num_f(@$loan->loan_amount)); ?></td>
              
              <td><?php echo e($loan->installment ?? __('app.n/a')); ?></td>
              <td><?php echo e(@$loan->frequency ? frequencies(@$loan->frequency,false) : trans('app.n/a')); ?></td>
              <td><?php echo e(displayDate($loan->loan_start_date ?? $loan->created_at)); ?></td>
              <td><?php echo e(displayDate($loan->disbursed_date)); ?></td>
              <td><?php echo e(displayDate($loan->payment_date)); ?></td>
              <td><?php echo e(loanStatuses($loan->status)); ?></td>
              <td>
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                  <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-tasks"></i></button>
                  <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-primary dropdown-toggle" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu dropdown-menu-right">

                      <?php if( Auth::user()->can('loan-cash.edit') && $loan->status!='ac'): ?>
                        <a href="<?php echo e(route('loan-cash.edit', $loan->id)); ?>" class="dropdown-item" title="<?php echo e(__('app.edit')); ?>"><i class="fa fa-edit"></i> <?php echo e(__('app.edit')); ?></a>
                      <?php endif; ?>
                      <a href="<?php echo e(route('loan-cash.show', $loan->id)); ?>" class="dropdown-item" title="<?php echo e(__('app.view_detail')); ?>"><i class="fa fa-eye"></i> <?php echo e(__('app.detail')); ?></a>
                      <?php if(isAdmin() || Auth::user()->can('loan-cash.delete') && !isPaidLoan($loan->id)): ?>
                        <a href="javascript:void(0);" title="<?php echo e(__('app.delete')); ?>" data-url="<?php echo e(route('loan.destroy', $loan->id)); ?>" class="dropdown-item btn-delete"><i class="fa fa-trash-o"></i> <?php echo e(__('app.delete')); ?></a>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
      <?php echo $loans->appends(Request::except('page'))->render(); ?>

    </div>
  </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script>
    var agentSelectLabel = '<option value=""><?php echo e(trans('app.agent')); ?>';
    var agentRetrievalUrl = '<?php echo e(route('staff.get_agents', ':branchId')); ?>';
  </script>
  <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
  <script src="<?php echo e(asset('js/agent-retrieval.js')); ?>"></script>
  <script>
    $(document).ready(function() {
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });
    });
  </script>
    
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