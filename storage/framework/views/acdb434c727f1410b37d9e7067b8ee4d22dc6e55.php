<ul class="nav nav-tabs mb-4">
  <li class="nav-item">
    <a class="nav-link <?php echo e(Route::current()->uri()=='product' ? 'active' : ''); ?>" href="<?php echo e(route('product.index')); ?>" title="" style="font-size:16px;"><i class="fa fa-cubes"></i> <?php echo e(__('app.all_products')); ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo e(Route::current()->uri()=='product/stock' ? 'active' : ''); ?>" href="<?php echo e(route('product.product_stock')); ?>" title="" style="font-size:16px;"><i class="fa fa-hourglass-half"></i> <?php echo e(__('app.stock_products')); ?></a>
  </li>
</ul>