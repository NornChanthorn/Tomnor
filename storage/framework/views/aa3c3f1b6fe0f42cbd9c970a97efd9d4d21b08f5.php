<?php $__env->startSection('title', trans('app.stock_transfer')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.stock_transfer')); ?></h3>
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <div class="card">
      <div class="card-header">
        <form method="get" action="">
          <div class="row">
            <div class="col-lg-6">
              <?php echo $__env->make('partial.anchor-create', ['href' => route('transfer.create')], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
            <div class="col-lg-6">
              <?php echo $__env->make('partial.search-input-group', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
          </div>
        </form>
      </div>
    </div>
    <br>

    <?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr>
            <th><?php echo e(trans('app.no_sign')); ?></th>
            <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('transfer_date', trans('app.transfer_date')));?></td>
            <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('reference_no', trans('app.invoice_id')));?></td>
            <th><?php echo e(trans('app.original_location')); ?></th>
            <th><?php echo e(trans('app.target_location')); ?></th>
            <th><?php echo e(trans('app.quantity')); ?></th>
            <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('note', trans('app.note')));?></td>
            <th><?php echo e(trans('app.creator')); ?></th>
            <th><?php echo e(trans('app.action')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $transfers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transfer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><?php echo e($offset++); ?></td>
            <td><?php echo e(displayDate($transfer->transaction_date)); ?></td>
            <td><?php echo e($transfer->ref_no); ?></td>
            <td><?php echo e($transfer->location_from); ?></td>
            <td><?php echo e($transfer->location_to); ?></td>
            <td><?php echo e(@$transfer->sell_lines->sum('quantity') ?? 0); ?></td>
            
            
            <td><?php echo e($transfer->note); ?></td>
            <td><?php echo e($transfer->creator->name ?? trans('app.n/a')); ?></td>
            <td class="text-center">
              <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-tasks"></i></button>
                <div class="btn-group" role="group">
                  <button class="btn btn-sm btn-primary dropdown-toggle" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a href="<?php echo e(route('transfer.show', $transfer->id)); ?>" class="dropdown-item" title="<?php echo e(__('app.view_detail')); ?>"><i class="fa fa-eye"></i> <?php echo e(__('app.view_detail')); ?></a>
                    
                  </div>
                </div>
              </div>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
        <?php echo $transfers->appends(Request::except('page'))->render(); ?>

      </div>
    </div>
  </main>
  <?php $__env->stopSection(); ?>

  <?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
    
  <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>