<!DOCTYPE html>
<html lang="km">
<head>
  <title><?php echo e($generalSetting->site_title); ?> <?php if (! empty(trim($__env->yieldContent('title')))): ?> - <?php endif; ?> <?php echo $__env->yieldContent('title'); ?></title>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <meta name="description" content="បង់​រំលោះ​ទូរសព្ទ​ដៃ">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
  <link rel="icon" href="<?php echo e(asset($generalSetting->site_logo)); ?>" sizes="32x32">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Battambang">
  <link rel="stylesheet" href="<?php echo e(asset('css/font-awesome.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('plugins/daterangepicker/daterangepicker.css')); ?>">

  <link rel="stylesheet" href="<?php echo e(asset('css/listswap.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('css/normalize.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('css/planit.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('css/main.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('css/custom.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('css/sweetalert.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('plugins/venobox/venobox.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('plugins/datepicker/bootstrap-datepicker.min.css')); ?>">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>

  <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <style>
    .btn-sm { line-height: 1; }
    .select2 { width: 100%!important; }
    .img-thumbnail { padding: 1px; border-radius: 0; }
    .dropdown-menu.dropdown-menu-right { width: 196px; }
  </style>

  <?php echo $__env->yieldContent('css'); ?>
</head>
<body class="app sidebar-mini rtl">
  <!-- Navbar -->
  <header class="app-header">
    <a class="app-header__logo" href="<?php echo e(route('dashboard')); ?>" style="padding: 0; line-height: 50px;">
      
      <h4 class="app-header-title"><?php echo e($generalSetting->site_title); ?></h4>
    </a>

    <!-- Sidebar Toggle Button -->
    <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
    <!-- Navbar Right Menu -->
    <ul class="app-nav">
      <!-- User Menu -->
      <li class="dropdown">
        <a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu">
          <i class="fa fa-user fa-lg"></i>
        </a>
        <ul class="dropdown-menu settings-menu dropdown-menu-right">
          
          <li>
            <a class="dropdown-item <?php echo e(activeMenu('profile')); ?>" href="<?php echo e(route('user.show_profile', auth()->user()->id)); ?>">
              <i class="fa fa-user fa-lg"></i> <?php echo e(trans('app.profile')); ?>

            </a>
          </li>

          <?php if (\Entrust::can('app.setting')) : ?>
          
          <li>
            <a class="dropdown-item <?php echo e(activeMenu('general', 2)); ?>" href="<?php echo e(route('general_setting.index')); ?>">
              <i class="fa fa-gear fa-lg"></i> <?php echo e(trans('app.general_setting')); ?>

            </a>
          </li>
          <?php endif; // Entrust::can ?>

          
          <li>
            <a class="dropdown-item" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
              <i class="fa fa-sign-out fa-lg"></i> <?php echo e(trans('app.log_out')); ?>

            </a>
            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="post" class="d-none"><?php echo csrf_field(); ?></form>
          </li>
        </ul>
      </li>
      <!-- End User Menu -->
    </ul>
  </header>

  <!-- Sidebar Menu -->
  <div class="app-sidebar__overlay" data-toggle="sidebar"></div>

  
  <?php echo $__env->make('layouts.partials.aside', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

  <?php echo $__env->yieldContent('content'); ?>

  <script src="<?php echo e(asset('js/lang/en.js')); ?>" type="text/javascript" charset="utf-8" async defer></script>
  <script src="<?php echo e(asset('js/jquery.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/popper.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/bootstrap.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/jquery.validate.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/main.js')); ?>"></script>
  <script src="<?php echo e(asset('js/pace.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/jquery.listswap.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
  <script src="<?php echo e(asset('js/modernizr.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/moment.min.js')); ?>"></script>
  <script src="<?php echo e(asset('plugins/daterangepicker/daterangepicker.js')); ?>"></script>
  <script src="<?php echo e(asset('js/sweetalert.min.js')); ?>"></script>
  <script src="<?php echo e(asset('plugins/venobox/venobox.min.js')); ?>"></script>
  <script src="<?php echo e(asset('plugins/datepicker/bootstrap-datepicker.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/jQuery.print.js')); ?>"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
  <script>
    var emptyOptionElm = '<option value=""><?php echo e(trans('app.select_option')); ?></option>';
    var sweetAlertTitle = '<?php echo e(trans('app.confirmation')); ?>';
    var sweetAlertText = '<?php echo e(trans('message.confirm_perform_action')); ?>';

    $.ajaxSetup({
      type: 'POST',
      data: {
        _token: '<?php echo e(csrf_token()); ?>'
      }
    });

    $(document).ready(function() {
      $(".popup-img").venobox();
    });
  </script>
  <?php echo $__env->yieldContent('js'); ?>
</body>
</html>
