<!DOCTYPE html>
<html>
<head>
    <title><?php echo e($generalSetting->site_title . ' - ' . trans('app.login_form')); ?></title>
    <meta charset="utf-8">
    <meta name="description" content="Leasing System">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php echo e(asset($generalSetting->site_logo)); ?>" sizes="32x32">
    <link rel="stylesheet" href="<?php echo e(asset('css/main.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/font-awesome.css')); ?>">
</head>
<body>
    <section class="material-half-bg">
        <div class="cover"></div>
    </section>
    <section class="login-content">
        <div class="text-center mt-3 mb-2">
            <img src="<?php echo e(asset($generalSetting->site_logo)); ?>" alt="" class="img-responsive" width="20%">
        </div>

        <h1 class="login-title"><?php echo e($generalSetting->site_title); ?></h1>
        <div class="login-box rounded">
            <!-- Login Form -->
            <form method="POST" action="<?php echo e(route('login')); ?>" class="login-form form-material">
                <?php echo csrf_field(); ?>
                <h4 class="login-head text-center pb-4"><?php echo e(trans('app.login_form')); ?></h4>

                <?php if($errors->has('errorMsg')): ?>
                    <h6 class="text-danger"><?php echo e($errors->first('errorMsg')); ?></h6>
                <?php endif; ?>

                <div class="form-group pt-2">
                    <label for="login" class="control-label text-uppercase"><?php echo e(trans('app.email_or_username')); ?></label>
                    <div class="d-block">
                        <input type="text" name="login" id="login" value="<?php echo e(old('login')); ?>" required autofocus
                               class="form-control p-0<?php echo e($errors->has('login') ? ' is-invalid' : ''); ?>">
                        <?php if($errors->has('login')): ?>
                            <span class="invalid-feedback">
                                <strong><?php echo e($errors->first('login')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="control-label text-uppercase"><?php echo e(trans('app.password')); ?></label>
                    <div class="d-block">
                        <input type="password" name="password" id="password" value="<?php echo e(old('password')); ?>"
                               class="form-control p-0<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" required>
                        <?php if($errors->has('password')): ?>
                            <span class="invalid-feedback">
                                <strong><?php echo e($errors->first('password')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="btn-container">
                    <button class="btn btn-success btn-block">
                        <i class="fa fa-sign-in fa-lg fa-fw"></i><?php echo e(trans('app.log_in')); ?>

                    </button>
                </div>
            </form>
            <!-- End Login Form -->
        </div>
    </section>
    <script src="<?php echo e(asset('js/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/popper.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/main.js')); ?>"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="<?php echo e(asset('js/pace.min.js')); ?>"></script>
    <script>
        // Login Page Flipbox control
        $('.login-content [data-toggle="flip"]').click(function() {
            $('.login-box').toggleClass('flipped');
            return false;
        });
    </script>
</body>
</html>
