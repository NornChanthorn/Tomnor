<?php $__env->startSection('title', trans('app.product')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.product')); ?></h3>
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo $__env->make('product.partials.search', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo $__env->make('product.partials.tabable_list', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <div class="row">
      <div class="col-lg-6"></div>
      <div class="col-lg-6 text-right">
        <?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead>
          <tr>
            <td> <?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('code', trans('app.product_code')));?></td>
            <td> <?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('name', trans('app.product_name')));?></td>
            <th class="text-right"> <?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('price', trans('app.price')));?></th>
            <th class="text-center"><?php echo e(trans('app.current_stock')); ?></th>
            <th class="text-center"><?php echo e(trans('app.purchased_unit')); ?></th>
            <th class="text-center"><?php echo e(trans('app.sold_unit')); ?></th>
            <th class="text-center"><?php echo e(trans('app.transfered_unit_in')); ?></th>
            <th class="text-center"><?php echo e(trans('app.transfered_unit_out')); ?></th>
            <th class="text-center"><?php echo e(trans('app.adjusted_unit')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php
            $totalCurrentStock = 0;
            $totalSold = 0;
            $totalPurchased = 0;
            $totalTransfered_in = 0;
            $totalTransfered_out = 0;
            $totalAdjusted = 0;
            $unit = '';
          ?>
     
          <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $unit = $product->unit;
              $stock = $product->stock;
              $totalCurrentStock += $stock;
              $totalSold += $product->total_sold;
              $totalPurchased += $product->total_purchased;
              $totalTransfered_in += $product->total_transfered_in;
              $totalTransfered_out += $product->total_transfered_out;
              $totalAdjusted += $product->total_adjusted;
            ?>

          <tr>
            <td><?php echo e(wordwrap(strlen($product->variantion_sku)==0 ? $product->code : $product->variantion_sku, 4, ' ', true)); ?></td>
            <td>
              <?php echo e($product->name . ($product->variantion_name!='DUMMY' ? ' - '.$product->variantion_name : '')); ?>

                <a class="btn btn-sm btn-success btn-modal"  href="#" data-container=".ime-modal"  data-href="<?php echo e(route('product.show_ime',['product_id'=>$product->product_id,'variantion_id'=>$product->variantion_id])); ?>">
                  <?php echo e(trans('app.product_ime')); ?>

                </a>
  
            </td>
            <td class="text-right">$ <?php echo e($product->unit_price ?? $product->price); ?></td>
            <td class="text-center">
              <?php if(Config::get('app.WRONG_STOCK')==true): ?>
                <?php if(($product->total_purchased + $product->total_transfered_in) - ($product->total_sold + $product->total_transfered_out + $product->total_adjusted) == $stock): ?>
                  <?php else: ?>

                    <?php if(!empty(Request::get('location'))): ?>
                      <a class="btn btn-sm btn-danger" href="<?php echo e(route('updated-qty',[
                        'id'=>$product->product_id,
                        'location_id'=>Request::get('location'),
                        'variantion_id'=>$product->variantion_id,
                        'qty_available'=> ($product->total_purchased + $product->total_transfered_in) - ($product->total_sold + $product->total_transfered_out + $product->total_adjusted) ])); ?>">Wrong Stock</a>
                    <?php endif; ?>
                <?php endif; ?>
              <?php endif; ?>
               
            <?php echo e((number_format($stock, 0)).' '.$unit); ?></td>
            <td class="text-center"><?php echo e((number_format($product->total_purchased) ?? 0).' '.$unit); ?></td>
            <td class="text-center"><?php echo e((number_format($product->total_sold) ?? 0).' '.$unit); ?></td>
            <td class="text-center"><?php echo e((number_format($product->total_transfered_in) ?? 0).' '.$unit); ?></td>
            <td class="text-center"><?php echo e((number_format($product->total_transfered_out) ?? 0).' '.$unit); ?></td>
            <td class="text-center"><?php echo e((number_format($product->total_adjusted) ?? 0).' '.$unit); ?></td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" class="text-right"><?php echo e(__('app.total')); ?></td>
            <td class="text-center"><?php echo e($totalCurrentStock.' '.$unit); ?></td>
            <td class="text-center"><?php echo e($totalPurchased.' '.$unit); ?></td>
            <td class="text-center"><?php echo e($totalSold.' '.$unit); ?></td>
            <td class="text-center"><?php echo e($totalTransfered_in.' '.$unit); ?></td>
            <td class="text-center"><?php echo e($totalTransfered_out.' '.$unit); ?></td>
            <td class="text-center"><?php echo e($totalAdjusted.' '.$unit); ?></td>
          </tr>
        </tfoot>
      </table>
      <?php echo $products->appends(Request::except('page'))->render(); ?>

    </div>
  </div>
</main>
<div class="modal fade ime-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
  <script type="text/javascript">
    $(function () {
      $('#location,#prod_variant, #prod_type, #brand').change(function () {
        $(this).parents('form').submit();
      });
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'danger', 'DELETE');
      });
    });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>