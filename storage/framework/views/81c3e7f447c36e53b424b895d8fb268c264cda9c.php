<?php $__env->startSection('title', trans('app.purchase')); ?>

<?php $__env->startSection('content'); ?>
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(trans('app.product_ime') . ' - '.$title); ?></h3>

      <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      <br>
      <div class="row">
      <?php if($qty > $transaction_imes->count()): ?>
        <div class="col-6">
          <a href="javascript::void(0);" class="btn btn-success mb-1 btn-modal" title="<?php echo e(trans('app.create')); ?>" data-href="<?php echo e(route('product.ime-single')); ?>" data-container=".ime-modal">
            <i class="fa fa-plus-circle pr-1"></i> <?php echo e(trans('app.create')); ?>

          </a>
          <form method="post" id="purchase-form" class="validated-form no-auto-submit" action="<?php echo e(route('product.ime-save')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
              <div class="form-group">
                <label for=""><?php echo e($product->name); ?>  <?php echo e(@$variantion->name!='DUMMY' ? ' - '.@$variantion->name : ''); ?>  <?php echo e(trans('app.quantity'). $qty); ?></label>
                <input type="text" class="form-control" name="code" required>
                <input type="hidden" name="transaction_id" value="<?php echo e($transaction_id); ?>">
                <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                <input type="hidden" name="variantion_id" value="<?php echo e($variantion->id); ?>">
                <input type="hidden" name="location_id" value="<?php echo e($location_id); ?>">
                <input type="hidden" name="purchase_sell_id" value="<?php echo e($purchase_sell_id); ?>">
                <input type="hidden" name="type" value="<?php echo e($type); ?>">
              </div>
            <button class="btn btn-success" type="submit"><?php echo e(trans('app.save')); ?></button>
  
          </form>
        </div>
        <div class="col-6">
      <?php else: ?>
      <div class="col-12">
      <?php endif; ?>
          <?php if($type=='loan'): ?>
              <?php
                $transaction_id = App\Models\Loan::where('transaction_id',$transaction_id)->first()->id;
              ?>
          <?php endif; ?>
          <a class="btn btn-sm btn-success mb-4" href="<?php echo e(url($type,$transaction_id)); ?>"><?php echo e(trans('app.back')); ?></a>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th><?php echo e(trans('app.no_sign')); ?></th>
                  <th><?php echo e(trans('app.product_name')); ?></th>
                  <th><?php echo e(trans('app.product_ime')); ?></th>
                  <th><?php echo e(trans('app.action')); ?></th>
                </tr>
              </thead>
              <?php if($transaction_imes->count()>0): ?>
                <?php $__currentLoopData = $transaction_imes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tbody>
                      <tr>
                        <td><?php echo e($offset++); ?></td>
                        <td><?php echo e(@$item->ime->product->name); ?></td>
                        <td><?php echo e(@$item->ime->code); ?></td>
                        <td>
                          <?php echo $__env->make('partial/button-delete', ['url' => route('product.ime-destroy', $item->ime->id)], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        </td>
                      </tr>
                    </tbody>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php endif; ?>
              
            </table>
          </div>

        </div>
      </div>
    </div>
  </main>

  <div class="modal fade ime-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script type="text/javascript">
    $(document).ready( function() {
        $(".btn-delete").on('click', function() {
            confirmPopup($(this).data('url'), 'error', 'GET');
        });
        //On display of add contact modal
        $('.ime-modal').on('shown.bs.modal', function(e) {

        });
    });

  </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>