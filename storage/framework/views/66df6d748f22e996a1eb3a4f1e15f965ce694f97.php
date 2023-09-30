<?php $__env->startSection('title', trans('app.brand')); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.brand')); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6">
                            <?php echo $__env->make('partial/anchor-create', [
                                'href' => route('brand.create')
                            ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                        <div class="col-lg-6">
                            <form method="get" action="<?php echo e(route('brand.index')); ?>">
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
                            <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('value', trans('app.name')));?></td>
                            <th><?php echo e(trans('app.action')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($offset++); ?></td>
                                <td><?php echo e($brand->value); ?></td>
                                <td class="text-center">
                                    <?php echo $__env->make('partial.anchor-edit', [
                                        'href' => route('brand.edit', $brand->id),
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                    <?php echo $__env->make('partial.button-delete', [
                                        'url' => route('brand.destroy', $brand->id),
                                        'disabled' => 'disabled',
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php echo $brands->appends(Request::except('page'))->render(); ?>

            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>