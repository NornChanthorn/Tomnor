<?php $__env->startSection('title', trans('app.product_ime')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.product_ime')); ?></h3>
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-6">
             <a href="javascript::void(0);" class="btn btn-success mb-1 btn-modal" title="<?php echo e(trans('app.create')); ?>" data-href="<?php echo e(route('product.ime-single')); ?>" data-container=".ime-modal">
              <i class="fa fa-plus-circle pr-1"></i> <?php echo e(trans('app.create')); ?>

            </a>
          </div>
          <div class="col-md-6 text-right">
            <form method="get" action="">
              <?php echo $__env->make('partial.search-input-group', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </form>
          </div>
        </div>
      </div>
    </div>
    <br>
    <?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead>
          <tr>
            <th class="text-center"><?php echo e(trans('app.no_sign')); ?></th>
            <th><?php echo e(trans('app.product_ime')); ?></th>
            <th><?php echo e(trans('app.status')); ?></th>
            <th class="text-right"><?php echo e(trans('app.action')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $imes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr style="background: <?php echo e(@$ime->transaction_ime ? "rgba(144, 253, 144, 0.192)" : "rgb(253 154 144 / 19%)"); ?>">
                <td class="text-center"><?php echo e($offset++); ?></td>
                <td><?php echo e($ime->code); ?></td>
                <td><span class="badge <?php echo e($ime->status=='available' ? "badge-success" : "badge-danger"); ?>"><?php echo e(ucfirst($ime->status)); ?></span></td>
                <td>
                    <a href="javascript::void(0);" class="btn btn-sm btn-primary mb-1 btn-modal" title="<?php echo e(trans('app.view_detail')); ?>" data-href="<?php echo e(route('product.ime-show',$ime->id)); ?>" data-container=".ime-modal">
                        <i class="fa fa-eye"></i>
                    </a>
                    <?php echo $__env->make('partial/button-delete', ['url' => route('product.ime-destroy', $ime->id)], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
      <?php echo $imes->appends(Request::except('page'))->render(); ?>

    </div>
  </div>
</main>

<div class="modal fade ime-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/mask.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>

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