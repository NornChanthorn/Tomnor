<a href="<?php echo e(route('product.show',$product)); ?>">
    <?php echo e((@$product->name).(empty($variantion)||$variantion=='DUMMY' ? '' : '-'.@$variantion)); ?>

</a>