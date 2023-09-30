<?php $__env->startSection('title', trans('app.role')); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.role') . ' - ' . $title); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php if($role->id): ?>
                    <form id="role-frm" method="post" action="<?php echo e(route('role.update', $role->id)); ?>">
                    <input type="hidden" name="id" value="<?php echo e($role->id); ?>">
                    <?php echo e(method_field('PUT')); ?>

                <?php else: ?>
                    <form id="role-frm" method="post" action="<?php echo e(route('role.store')); ?>">
                <?php endif; ?>
                <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="display_name" class="control-label"><?php echo e(trans('app.name')); ?><span class="red-star">*</span></label>
                                    <input type="text" class="form-control" value="<?php echo e(old('display_name', $role->display_name)); ?>" name="display_name" required >
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="description" class="control-label"><?php echo e(trans('app.description')); ?></label>
                                    <textarea class="form-control" id="description" name="description" rows="5"><?php echo old('description', $role->description); ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group row">
                                <label for="product" class="ml-4 form-control-label"><h5><?php echo e(trans('app.permission')); ?></h5></label>
                                <div class="col-sm-12">
                                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $pms_arr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="row">
                                            <div class="col-md-3 mb-2"><strong><?php echo e($key); ?></strong></div>
                                            <div class="col-md-9">
                                                <ul class="permission list-unstyled">
                                                    <?php $__currentLoopData = $pms_arr; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li class="mb-2">
                                                            <label>
                                                                <input type="checkbox" name="permissions[]" class="" value="<?php echo e($pms->id); ?>" <?php if(in_array($pms->id, $rolePermissions)): ?> checked <?php endif; ?>>
                                                                <?php echo e($pms->display_name); ?>

                                                            </label>
                                                        </li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <?php echo $__env->make('partial/button-save', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            <a href="javascript: window.history.go(-1)" class="btn btn-secondary"> Close</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery.validate.min.js')); ?>"></script>
    <script>
        $(function () {
            $('#role-frm').validate();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>