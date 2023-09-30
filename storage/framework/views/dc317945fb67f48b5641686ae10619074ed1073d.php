<?php $__env->startSection('title', trans('app.role')); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.role')); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $__env->make('partial/anchor-create', [
                                'href' => route('role.create')
                            ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                        <div class="col-md-6">
                            <form method="get" action="<?php echo e(route('role.index')); ?>">
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
                        <!-- <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('name', trans('app.name')));?></td> -->
                        <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('display_name', trans('app.name')));?></td>
                        <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('description', trans('app.description')));?></td>
                        <th><?php echo e(trans('app.action')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($offset++); ?></td>
                                <!-- <td><?php echo e($role->name); ?></td> -->
                                <td><?php echo e($role->display_name); ?></td>
                                <td><?php echo e($role->description); ?></td>
                                <td>
                                    <?php echo $__env->make('partial.anchor-edit', [
                                        'href' => route('role.edit', $role->id),
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php echo $roles->appends(Request::except('page'))->render(); ?>

            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>