<?php $__env->startSection('title', trans('app.client')); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.client')); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $__env->make('partial/anchor-create', [
                                'href' => route('client.create')
                            ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </div>
                        <div class="col-md-6 text-right">
                            <form method="get" action="<?php echo e(route('client.index')); ?>">
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
                            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('name', trans('app.name')));?></th>
                            <th><?php echo e(trans('app.profile_photo')); ?></th>
                            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('gender', trans('app.gender')));?></th>
                            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('id_card_number', trans('app.id_card_number')));?></th>
                            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('first_phone', trans('app.first_phone')));?></th>
                            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('sponsor_name', trans('app.sponsor_name')));?></th>
                            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('sponsor_phone', trans('app.sponsor_phone')));?></th>
                            <th><?php echo e(trans('app.action')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($offset++); ?></td>
                                <td><?php echo e($client->name); ?></td>
                                <td><?php echo $__env->make('partial.client-profile-photo', ['client' => $client], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
                                <td><?php echo e(genders($client->gender ?? '')); ?></td>
                                <td><?php echo e($client->id_card_number); ?></td>
                                <td><?php echo e($client->first_phone); ?></td>
                                <td><?php echo e($client->sponsor_name); ?></td>
                                <td><?php echo e($client->sponsor_phone); ?></td>
                                <td>
                                    <?php echo $__env->make('partial/anchor-show', [
                                        'href' => route('client.show', $client->id),
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                    <?php if($client->is_default == 0): ?>
                                        <?php echo $__env->make('partial/anchor-edit', [
                                            'href' => route('client.edit', $client->id),
                                        ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                        <?php if(isAdmin()): ?>
                                            <?php echo $__env->make('partial/button-delete', [
                                                'url' => route('client.destroy', $client->id),
                                                'disabled' => $client->loans->count() ? 'disabled' : '',
                                            ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php echo $clients->appends(Request::except('page'))->render(); ?>

            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>