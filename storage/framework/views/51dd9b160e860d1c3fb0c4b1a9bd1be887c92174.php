<?php $__env->startSection('title', trans('app.user')); ?>
<?php $__env->startSection('content'); ?>
    <?php
        $isFormShowType = ($formType == FormType::SHOW_TYPE);
        $disabledFormType = ($isFormShowType ? 'disabled' : '');
    ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.user') . ' - ' . $title); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <form id="form-user" method="post" action="<?php echo e(route('user.save', $user->id)); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="form_type" value="<?php echo e($formType); ?>">
                
                <div class="row">
                    
                    
                    <div class="col-lg-6 form-group">
                        <label for="username" class="control-label">
                            <?php echo e(trans('app.username')); ?> <span class="required">*</span>
                        </label>
                        <input type="text" name="username" id="username" class="form-control"
                               value="<?php echo e(old('username') ?? $user->username); ?>" required
                               placeholder="<?php echo e(trans('app.at_least_6_char')); ?>" <?php echo e($disabledFormType); ?>>
                    </div>

                    
                    <div class="col-lg-6 form-group">
                        <label for="password" class="control-label">
                            <?php echo e(trans('app.password')); ?>

                            <?php if($formType == FormType::CREATE_TYPE): ?> <span class="required">*</span> <?php endif; ?>
                        </label>
                        <input type="password" name="password" id="password" class="form-control"
                               placeholder="<?php echo e(trans('app.at_least_6_char')); ?>" <?php echo e($disabledFormType); ?>

                               <?php if($formType == FormType::CREATE_TYPE): ?> value="amazon" required <?php endif; ?>>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-lg-6 form-group">
                        <label for="role" class="control-label">
                            <?php echo e(trans('app.role')); ?> <span class="required">*</span>
                        </label>
                        <?php if($isFormShowType): ?>
                            <input type="text" class="form-control" value="<?php echo e(count($user->roles)? $user->roles[0]->display_name:''); ?>" disabled>
                        <?php else: ?>
                            <select name="role" id="role" class="form-control select2 select2-no-search" required <?php echo e($disabledFormType); ?>>
                                <option value=""><?php echo e(trans('app.select_option')); ?></option>
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role->id); ?>" <?php echo e(selectedOption($role->id, old('role'), $user->roles[0]->id ?? null)); ?>>
                                        <?php echo e($role->display_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        <?php endif; ?>
                    </div>

                    
                    <div class="col-lg-6 form-group">
                        <label for="status" class="control-label">
                            <?php echo e(trans('app.status')); ?>

                        </label>
                        <select name="status" id="status" class="form-control select2 select2-no-search" required>
                            <option value="1"><?php echo e(trans('app.active')); ?></option>
                            <option value="0" <?php echo e(old('status') === 0 || $user->active === 0 ? 'selected' : ''); ?>>
                                <?php echo e(trans('app.inactive')); ?>

                            </option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-right">
                        <?php echo $__env->make('partial/button-save', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </div>
                </div>
            </form>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery.validate.min.js')); ?>"></script>
    <script>
        $(function () {
            $('#form-user').validate();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>