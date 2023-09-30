<?php $__env->startSection('title', trans('app.expense_type')); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.expense_type')); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <?php if(Auth::user()->can('collateral_type.add')): ?>
                                <?php echo $__env->make('partial/anchor-create', [
                                    'href' => route('collateral-type.create')
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <form method="get" action="">
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
                            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('value', trans('app.title')));?></th>
                            <th><?php echo e(trans('app.action')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($offset++); ?></td>
                                <td><?php echo e($position->value); ?></td>
                                <td>
                                    <?php if(Auth::user()->can('collateral_type.edit')): ?>
                                        <?php echo $__env->make('partial.anchor-edit', [
                                            'href' => route('collateral-type.edit', $position),
                                        ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php echo $positions->appends(Request::except('page'))->render(); ?>

            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>