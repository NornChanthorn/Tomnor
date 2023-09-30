<?php $__env->startSection('title', trans('app.expense')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(trans('app.expense')); ?></h3>
      <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-6">
              <a href="javascript::void(0);" class="btn btn-success mb-1 btn-modal" title="<?php echo e(trans('app.create')); ?>" data-href="<?php echo e(route('expense.create')); ?>" data-container=".expense-modal">
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
              <th class="text-center"><?php echo e(trans('app.reference_number')); ?></th>
              <th class="text-center"><?php echo e(trans('app.amount')); ?></th>
              <th class="text-center"><?php echo e(trans('app.note')); ?></th>
              <th class="text-center"><?php echo e(trans('app.category')); ?></th>
              <th class="text-center"><?php echo e(trans('app.date')); ?></th>
              <th class="text-right"><?php echo e(trans('app.action')); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td class="text-center"><?php echo e($offset++); ?></td>
                <td class="text-center"> <?php echo e($expense->refno); ?></td>
                <td class="text-center">$ <?php echo e(decimalNumber($expense->amount,2)); ?></td>
                <td><?php echo $expense->note; ?></td>
                <td class="text-center"> <?php echo e($expense->category->value); ?></td>
                <td class="text-center"><?php echo e(displayDate($expense->expense_date)); ?></td>
                <td>
                    <a href="javascript::void(0);" class="btn btn-sm btn-success mb-1 btn-modal" title="<?php echo e(trans('app.detail')); ?>" data-href="<?php echo e(route('expense.show',$expense->id)); ?>" data-container=".expense-modal">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a href="javascript::void(0);" class="btn btn-sm btn-primary mb-1 btn-modal" title="<?php echo e(trans('app.edit')); ?>" data-href="<?php echo e(route('expense.edit',$expense->id)); ?>" data-container=".expense-modal">
                      <i class="fa fa-edit"></i>
                    </a>
                    <?php echo $__env->make('partial/button-delete', ['url' => route('expense.destroy', $expense->id)], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
        <?php echo $expenses->appends(Request::except('page'))->render(); ?>

      </div>
    </div>
</main>

<div class="modal fade expense-modal" tabindex="-0" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/mask.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
    <script src="<?php echo e(asset('js/date-time-picker.js')); ?>"></script>
    <script>
      $(document).ready(function() {
        $(".date-picker").datepicker({
          format: 'dd-mm-yyyy',
          autoclose: true,
          orientation: 'bottom right'
        });
      });
    </script>
    <script type="text/javascript">
        var contactExist = "<?php echo e(trans('message.customer_already_exists')); ?>";

        $(document).ready( function() {
            $(".btn-delete").on('click', function() {
                confirmPopup($(this).data('url'), 'error', 'DELETE');
            });

            //On display of add contact modal
            $('.expense-modal').on('shown.bs.modal', function(e) {
                $(".date-picker").datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                orientation: 'bottom right'
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>