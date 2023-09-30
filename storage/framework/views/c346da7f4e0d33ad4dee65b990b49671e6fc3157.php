<?php $__env->startSection('title', trans('app.branch')); ?>

<?php $__env->startSection('content'); ?>
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(trans('app.branch')); ?></h3>
      <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-6">
              <?php echo $__env->make('partial/anchor-create', [
                'href' => route('branch.create')
              ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </div>
            <div class="col-lg-6 text-right">
              <form method="get" action="<?php echo e(route('branch.index')); ?>">
                <?php echo $__env->make('partial.search-input-group', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
              </form>
            </div>
          </div>
        </div>
      </div>
      <br>
      <?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th><?php echo e(trans('app.no_sign')); ?></th>
              <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('name', trans('app.name')));?></td>
              <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('location', trans('app.location')));?></td>
              <td><?php echo e(trans('app.branch_type')); ?></td>
              <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('phone_1', trans('app.first_phone')));?></td>
              <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('phone_2', trans('app.second_phone')));?></td>
              <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('address', trans('app.address')));?></td>
              <th><?php echo e(trans('app.action')); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e($offset++); ?></td>
                <td><?php echo e($branch->name); ?></td>
                <td><?php echo e($branch->location); ?></td>
                <td><?php echo e(branchTypes($branch->type ?? '')); ?></td>
                <td><?php echo e($branch->phone_1); ?></td>
                <td><?php echo e($branch->phone_2); ?></td>
                <td><?php echo e($branch->address); ?></td>
                <td>
                  
                  <?php echo $__env->make('partial.anchor-show', [
                    'href' => route('branch.show', $branch->id)
                  ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  <?php echo $__env->make('partial.anchor-edit', [
                    'href' => route('branch.edit', $branch->id),
                  ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  <?php echo $__env->make('partial.button-delete', [
                    'url' => route('branch.destroy', $branch->id),
                    'disabled' => 'disabled',
                  ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
        <?php echo $branches->appends(Request::except('page'))->render(); ?>

      </div>
    </div>
  </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>