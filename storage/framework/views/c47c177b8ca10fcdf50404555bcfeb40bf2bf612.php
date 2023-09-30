<?php $__env->startSection('title', trans('app.stock_adjustment')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.stock_adjustment')); ?></h3>
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <div class="card">
      <div class="card-header">
        <form method="get" action="">
          <div class="row">
            <div class="col-lg-6">
              <?php echo $__env->make('partial.anchor-create', ['href' => route('adjustment.create')], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
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
            <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('adjustment_date', trans('app.adjustment_date')));?></td>
            <th><?php echo e(trans('app.location')); ?></th>
            <th><?php echo e(trans('app.product')); ?></th>
            <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('reason', trans('app.reason')));?></td>
            <th><?php echo e(trans('app.creator')); ?></th>
            <th class="text-right"><?php echo e(trans('app.action')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $adjustments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adjustment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><?php echo e($offset++); ?></td>
            <td><?php echo e(displayDate($adjustment->transaction_date)); ?></td>
            <td><?php echo $__env->make('partial.branch-detail-link', ['branch' => $adjustment->warehouse], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
            <td>
              <?php if(!empty($adjustment->stock_adjustment_lines)): ?>
                <?php $__currentLoopData = $adjustment->stock_adjustment_lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stock_adjustment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php if($stock_adjustment->product): ?>
                  <li><?php echo e($stock_adjustment->product->name); ?><?php echo e($stock_adjustment->variantion->name!='DUMMY' ? ' - '.$stock_adjustment->variantion->name : ''); ?> (<?php echo e(($stock_adjustment->type=='stock_out' ? '-' : '').(int)($stock_adjustment->quantity)); ?>)</li>
                  <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php endif; ?>
            </td>
            <td><?php echo e($adjustment->additional_notes); ?></td>
            <td><?php echo e($adjustment->creator->name ?? trans('app.n/a')); ?></td>
            <td class="text-center">
              <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-tasks"></i></button>
                <div class="btn-group" role="group">
                  <button class="btn btn-sm btn-primary dropdown-toggle" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a href="javascript:void(0);" title="<?php echo e(__('app.delete')); ?>" data-url="<?php echo e(route('adjustment.destroy', $adjustment->id)); ?>" class="dropdown-item btn-delete"><i class="fa fa-trash-o"></i> <?php echo e(__('app.delete')); ?></a>
                  </div>
                </div>
              </div>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
      <?php echo $adjustments->appends(Request::except('page'))->render(); ?>

    </div>
  </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
  <script>
    $(document).ready(function() {
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });
    });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>