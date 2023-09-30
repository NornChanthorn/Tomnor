<?php $__env->startSection('title', trans('app.profile')); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.profile')); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <form id="form-profile" method="post" action="<?php echo e(route('user.save_profile', $user)); ?>">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-md-10 col-lg-8">
                        
                        <div class="form-group">
                            <label for="username" class="control-label">
                                <?php echo e(trans('app.username')); ?> <span class="required">*</span>
                            </label>
                            <input type="text" name="username" id="username" class="form-control"
                                   value="<?php echo e(old('username') ?? $user->username); ?>" required>
                        </div>

                        
                        <div class="form-group">
                            <label for="current_password" class="control-label">
                                <?php echo e(trans('app.current_password')); ?>

                            </label>
                            <input type="password" name="current_password" id="current_password"
                                   class="form-control" value="<?php echo e(old('current_password')); ?>">
                        </div>

                        
                        <div class="form-group">
                            <label for="new_password" class="control-label">
                                <?php echo e(trans('app.new_password')); ?>

                            </label>
                            <input type="password" name="new_password" id="new_password"
                                   class="form-control" value="<?php echo e(old('new_password')); ?>">
                        </div>

                        
                        <div class="form-group">
                            <label for="confirmed_password" class="control-label">
                                <?php echo e(trans('app.confirm_password')); ?>

                            </label>
                            <input type="password" name="confirmed_password" id="confirmed_password"
                                   class="form-control" value="<?php echo e(old('confirmed_password')); ?>">
                        </div>

                        <?php echo $__env->make('partial/button-save', [
                            'class' => 'pull-right'
                        ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    </div>
                </div>
            </form>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/mask.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery.validate.min.js')); ?>"></script>
    <script>
        $(function () {
            $('#form-profile').validate();
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>