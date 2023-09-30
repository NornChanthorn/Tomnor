<?php $__env->startSection('title', trans('app.staff')); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.staff')); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="card">
                <div class="card-header">
                    <form method="get" action="">
                        <div class="row">
                            <div class="col-lg-4">
                                <?php echo $__env->make('partial/anchor-create', [
                                    'href' => route('staff.create')
                                ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            </div>
                            <div class="col-lg-2 pl-1 pr-0">
                                <select name="branch" id="branch" class="form-control select2">
                                    <option value=""><?php echo e(trans('app.branch')); ?></option>
                                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch') == $branch->id ? 'selected' : ''); ?>>
                                            <?php echo e($branch->location); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-lg-2 pl-1 pr-0">
                                <select name="position" id="position" class="form-control select2">
                                    <option value=""><?php echo e(trans('app.position')); ?></option>
                                    <?php $__currentLoopData = positions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($position->id); ?>" <?php echo e(request('position') == $position->id ? 'selected' : ''); ?>>
                                            <?php echo e($position->value); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-lg-4 pl-1">
                                <?php echo $__env->make('partial.search-input-group', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            </div>
                        </div>
                    </form>
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
                            <th><?php echo e(trans('app.profile_photo')); ?></th>
                            <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('gender', trans('app.gender')));?></td>
                            <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('id_card_number', trans('app.id_card_number')));?></td>
                            <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('first_phone', trans('app.first_phone')));?></td>
                            <th><?php echo e(trans('app.branch')); ?></th>
                            <th><?php echo e(trans('app.position')); ?></th>
                            <th><?php echo e(trans('app.username')); ?></th>
                            <th><?php echo e(trans('app.action')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $singleStaff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($offset++); ?></td>
                                <td><?php echo e($singleStaff->name); ?></td>
                                <td><?php echo $__env->make('partial.staff-profile-photo', ['staff' => $singleStaff], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
                                <td><?php echo e(genders($singleStaff->gender)); ?></td>
                                <td><?php echo e($singleStaff->id_card_number); ?></td>
                                <td><?php echo e($singleStaff->first_phone); ?></td>
                                <td><?php echo e($singleStaff->branch->location ?? trans('app.n/a')); ?></td>
                                <td><?php echo e(positions($singleStaff->position)); ?></td>
                                <td><?php echo e($singleStaff->user->username ?? ''); ?></td>
                                <td class="text-center">
                                    <a href="<?php echo e(route('staff.commission', $singleStaff->id)); ?>" class="btn btn-success btn-sm mb-1">
                                        <?php echo e(trans('app.commission')); ?>

                                    </a>
                                    <br>
                                    <?php echo $__env->make('partial.anchor-show', [
                                        'href' => route('staff.show', $singleStaff->id),
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                    <?php echo $__env->make('partial.anchor-edit', [
                                        'href' => route('staff.edit', $singleStaff->id),
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                    <?php echo $__env->make('partial.button-delete', [
                                        'url' => route('staff.destroy', $singleStaff->id),
                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php echo $staff->appends(Request::except('page'))->render(); ?>

            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="text/javascript">
        function deleteAction(argument) {
            console.log($(this).attr("data-url"));
        }
        $(document).ready(function(){
            let root_url= window.location.href;
            $(".btn-delete").click(function(evt){
                evt.preventDefault();
                let url = $(this).data('url');
                Swal.fire({
                  title: 'Are you sure?',
                  text: 'You will not be able to recover this record!',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonText: 'Yes, delete it!',
                  cancelButtonText: 'No, keep it'
                }).then((result) => {
                  if (result.value) {
                    axios.delete(url)
                        .then(res => {
                            if(200 ===res.status){
                                window.location.href=root_url;
                            }
                        }).catch(err => {
                            console.log(err);
                        });
                    
                  }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>