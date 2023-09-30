<?php $__env->startSection('title', trans('app.client_registration')); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.client_registration')); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="card">
                <div class="card-header">
                    <form method="get" action="<?php echo e(route('report.client_registration')); ?>">
                        <div class="row">
                            <div class="offset-md-6 col-md-2">
                                <select name="agent" class="form-control select2">
                                    <option value=""><?php echo e(trans('app.agent')); ?></option>
                                    <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($agent->id); ?>" <?php echo e(request('agent') == $agent->id ? 'selected' : ''); ?>>
                                            <?php echo e($agent->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <?php echo $__env->make('partial.search-input-group', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <br>
            <?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="table-responsive resize-w">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo e(trans('app.no_sign')); ?></th>
                            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('name', trans('app.name')));?></th>
                            <th><?php echo e(trans('app.profile_photo')); ?></th>
                            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('id_card_number', trans('app.id_card_number')));?></th>
                            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('first_phone', trans('app.first_phone')));?></th>
                            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('sponsor_name', trans('app.sponsor_name')));?></th>
                            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('sponsor_phone', trans('app.sponsor_phone')));?></th>
                            <th><?php echo e(trans('app.number_of_loan')); ?></th>
                            <th><?php echo e(trans('app.action')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($offset++); ?></td>
                                <td><?php echo $__env->make('partial.client-detail-link', ['client' => $client], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
                                <td><?php echo $__env->make('partial.client-profile-photo', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
                                <td><?php echo e($client->id_card_number); ?></td>
                                <td><?php echo e($client->first_phone); ?></td>
                                <td><?php echo e($client->sponsor_name); ?></td>
                                <td><?php echo e($client->sponsor_phone); ?></td>
                                <td><?php echo e($client->loans()->count()); ?></td>
                                <td>
                                    <a href="<?php echo e(route('report.loan_portfolio', $client)); ?>" class="btn btn-info btn-sm mb-1">
                                        <?php echo e(trans('app.loan_portfolio')); ?>

                                    </a>
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
<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>