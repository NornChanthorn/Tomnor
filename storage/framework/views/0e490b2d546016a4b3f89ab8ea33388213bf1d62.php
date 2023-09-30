<?php $__env->startSection('title', trans('app.product')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.product')); ?></h3>
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo $__env->make('product.partials.search', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <?php echo $__env->make('product.partials.tabable_list', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <div class="row">
      <div class="col-lg-6">
        <?php echo $__env->make('partial/anchor-create', ['href' => route('product.create')], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      </div>
      <div class="col-lg-6 text-right">
        <?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      </div>
    </div>

    <div class="table-responsive resize-w">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th><?php echo e(trans('app.no_sign')); ?></th>
            <th><?php echo e(trans('app.photo')); ?></th>
            <td> <?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('code', trans('app.product_code')));?></td>
            <td style="width: 23%"> <?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('name', trans('app.name')));?></td>
              <td><?php echo e(trans('app.location')); ?></td>
            
            <td><?php echo e(trans('app.type')); ?></td>
            <th><?php echo e(trans('app.product_category')); ?></th>
            <th><?php echo e(trans('app.brand')); ?></th>
            <td class="text-center"><?php echo e(trans('app.quantity')); ?></td>
            <th class="text-right"><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('price', trans('app.price')));?></th>
            <th class="text-right"><?php echo e(trans('app.action')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td><?php echo e($offset++); ?></td>
            <td><?php echo $__env->make('partial.product-photo', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
            <td><?php echo e(wordwrap($product->code, 4, ' ', true)); ?></td>
            <td><?php echo e($product->name); ?></td>
              <td>
                  <button type="button" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="
                    <?php
                        $str = "";
                        $exist = ' ';
                      foreach ($product->variations as $v){
                          foreach($v->variation_location_details as $vlc){
                              if(strpos($exist, strval($vlc->location_id))){
                                  continue;
                              }
                              $str = $str.' '.$vlc->location->location;
                              $exist = $exist.$vlc->location_id.' ';
                          }
                      }
                        echo $str;
                    ?>
                    ">
                      <?php
                      $str = 0;
                      $exist = ' ';
                      foreach ($product->variations as $v){
                          foreach($v->variation_location_details as $vlc){
                              if(strpos($exist, strval($vlc->location_id))){
                                  continue;
                              }
                              $str += 1;
                              $exist = $exist.$vlc->location_id.' ';
                          }
                      }
                      echo $str;
                      ?>
                  </button>
              </td>
            
            <th><?php echo e($product->type); ?></th>
            <td><?php echo e($product->category->value ?? trans('app.n/a')); ?></td>
            <td><?php echo e(brands($product->brand)); ?></td>
            <td class="text-center"><?php echo e($product->getQty($product->id, request('location'))); ?></td>
            <td class="text-right">$ <?php echo e($product->prefix_price); ?></td>
            <td class="text-right">
              <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-tasks"></i></button>
                <div class="btn-group" role="group">
                  <button class="btn btn-sm btn-primary dropdown-toggle" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                  <div class="dropdown-menu dropdown-menu-right">
                    
                    <a href="<?php echo e(route('product.show', $product->id)); ?>" title="<?php echo e(__('app.view_detail')); ?>" class="dropdown-item"><i class="fa fa-eye"></i> <?php echo e(__('app.view_detail')); ?></a>

                    <?php if(Auth()->user()->can('product.edit')): ?>
                      <a href="<?php echo e(route('product.edit', $product->id)); ?>" title="<?php echo e(__('app.edit')); ?>" class="dropdown-item"><i class="fa fa-edit"></i> <?php echo e(__('app.edit')); ?></a>
                    <?php endif; ?>

                    <?php if(Auth()->user()->can('product.delete')): ?>
                      <a href="javascript:void(0);" title="<?php echo e(__('app.delete')); ?>" data-url="<?php echo e(route('product.destroy', $product->id)); ?>" class="dropdown-item btn-delete"><i class="fa fa-trash-o"></i> <?php echo e(__('app.delete')); ?></a>
                    <?php endif; ?>

                    <?php if($product->variations->count() > 0): ?>
                      <div class="dropdown-divider"></div>
                      <a href="<?php echo e(route('opening-stock.add', $product->id)); ?>" title="<?php echo e(__('app.add_edit_opening_stock')); ?>" class="dropdown-item"><i class="fa fa-database"></i> <?php echo e(__('app.add_edit_opening_stock')); ?></a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
      <?php echo $products->appends(Request::except('page'))->render(); ?>

    </div>
  </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
  <script type="text/javascript">
    $(function () {
      $('#location, #prod_type, #brand').change(function () {
        $(this).parents('form').submit();
      });
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });
    });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>