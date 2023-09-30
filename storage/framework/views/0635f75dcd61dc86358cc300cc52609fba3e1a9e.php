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
              <?php echo $__env->make('partial/anchor-create', ['href' => route('loan.create')], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
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
            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('client_code', trans('app.loan_code')));?></th>
            <th><?php echo e(trans('app.client')); ?></th>
            <th><?php echo e(trans('app.profile_photo')); ?></th>
            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('client_id', trans('app.client_code')));?></th>
            <th><?php echo e(trans('app.branch')); ?></th>

            <?php if(isAdmin()): ?>
            <th><?php echo e(trans('app.agent')); ?></th>
            <?php endif; ?>

            <th><?php echo e(trans('app.product')); ?></th>
            <th><?php echo e(trans('app.next_payment_date')); ?></th>
            <th><?php echo e(trans('app.payment_amount')); ?></th>
            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('status', trans('app.status')));?></th>
            <th><?php echo e(trans('app.action')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $dueSchedule = $loan->schedules()->first(); ?>
          <tr>
            <td><?php echo e($offset++); ?></td>
            <td>
              <?php echo e($loan->client_code); ?>

            </td>
            <td><?php echo $__env->make('partial.client-detail-link', ['client' => $loan->client], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
            <td><?php echo $__env->make('partial.client-profile-photo', ['client' => $loan->client], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
            <td>
              <?php echo $__env->make('partial.loan-detail-link', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </td>
            <td><?php echo e(@$loan->branch->location ?? trans('app.n/a')); ?></td>

            <?php if(isAdmin()): ?>
              <td><?php echo $__env->make('partial.staff-detail-link', ['staff' => $loan->staff], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
            <?php endif; ?>

            
            <td>
                <?php $__currentLoopData = $loan->productDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php if(@$item->product): ?>
                    <?php echo $__env->make('partial.product-detail-link', ['product' => @$item->product, 'variantion' => @$item->variantion->name], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?><br>
                  <?php endif; ?>
                 
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </td>
            <td><?php echo e(displayDate($loan->schedules[0]->payment_date ?? null)); ?></td>
            <td><b>$ <?php echo e(decimalNumber($dueSchedule['total'])); ?></b></td>
            <td class="text-center"><?php echo $__env->make('partial.loan-status-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
            <td class="text-center">
              <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-tasks"></i></button>
                <div class="btn-group" role="group">
                  <button class="btn btn-sm btn-primary dropdown-toggle" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a href="<?php echo e(route('loan.show', $loan->id)); ?>" class="dropdown-item" title="<?php echo e(__('app.view_detail')); ?>"><i class="fa fa-eye"></i> <?php echo e(__('app.detail')); ?></a>

                    <?php if(Auth::user()->can('loan.print') && in_array($loan->status, [LoanStatus::ACTIVE, LoanStatus::PAID])): ?>
                      <a href="<?php echo e(route('loan.print_contract', $loan)); ?>" title="<?php echo e(trans('app.print_contract')); ?>" class="dropdown-item" target="_blank"><i class="fa fa-print"></i> <?php echo e(trans('app.print')); ?></a>
                    <?php endif; ?>

                    <?php if(Auth::user()->can('loan.print') && $loan->disbursed_date != NULL): ?>
                      <a href="<?php echo e(route('loan.invoice', $loan->id)); ?>" title="<?php echo e(trans('app.invoice')); ?>" class="dropdown-item" target="_blank"><i class="fa fa-print"></i> <?php echo e(trans('app.invoice')); ?></a>
                    <?php endif; ?>

                    <div class="dropdown-divider"></div>
                    <?php if( Auth::user()->can('loan.edit') && !isPaidLoan($loan->id)): ?>
                      <a href="<?php echo e(route('loan.edit', $loan->id)); ?>" class="dropdown-item" title="<?php echo e(__('app.edit')); ?>"><i class="fa fa-edit"></i> <?php echo e(__('app.edit')); ?></a>
                    <?php endif; ?>

                    <?php if(isAdmin() || Auth::user()->can('loan.delete') && !isPaidLoan($loan->id)): ?>
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