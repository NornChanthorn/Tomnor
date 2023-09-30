<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php echo e($generalSetting->site_title); ?> <?php if (! empty(trim($__env->yieldContent('title')))): ?> - <?php endif; ?> <?php echo $__env->yieldContent('title'); ?></title>
  <meta charset="utf-8">
  <meta name="keywords" content="">
  <meta name="description" content="">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo.png')); ?>" sizes="32x32">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="<?php echo e(asset('css/invoice.css')); ?>">
</head>

<body>
  <div class="invoice">
    <div class="header">
      <div class="row">
          <div class="col-12">
              <img src="<?php echo e(file_exists($invoice_head->logo) ? asset($invoice_head->logo) : asset('images/contract-phone-3.jpg')); ?>" alt="" class="img-fluid logo">
          </div>
          <div class="col-12 text-center">
              <div class="title">
                  <h3><?php echo e($invoice_head->location_kh); ?></h3>
                  <h3><b><?php echo e($invoice_head->location); ?></b></h3>
              </div>
             
          </div>
      </div>
    </div>

     <div class="content">
        <?php echo $__env->yieldContent('content'); ?>
     </div>
     <div class="row text-center mt-5">
      <div class="col-6">
        អ្នកទិញ / <b>BUYER</b> 
      </div>
      <div class="col-6">
        អ្នកលក់  / <b>SELLER</b> 
     </div>
    </div>
    <div class="footer">
      <div class="row box-footer">
        <div class="col-4 border-right">
          <img class="img-fluid" src="<?php echo e(asset('phone.png')); ?>" alt="" srcset="">
          <p><?php echo e($invoice_head->phone_1); ?></p>
          <p><?php echo e($invoice_head->phone_2); ?></p>
        </div>
        <div class="col-4 border-right">
          <img src="<?php echo e(asset('email.png')); ?>" alt="" srcset="">
          <p><?php echo e($invoice_head->email_1); ?></p>
          <p><?php echo e($invoice_head->email_2); ?></p>
        </div>
        <div class="col-4">
          <img class="img-fluid" src="<?php echo e(asset('map.png')); ?>" alt="" srcset="">
          <p><?php echo e($invoice_head->address); ?></p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
