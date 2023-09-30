<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form method="post" id="add_form" class="no-auto-submit" action="<?php echo e(route('loan.saveDelaySchedule', $loan)); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="schedule_reference_id"  value="<?php echo e($schedule_reference->id); ?>">
        <div class="modal-header">
          <h4 class="modal-title"><?php echo e(trans('app.delay_schedule')); ?></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
  
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4 form-group">
              <label for=""><?php echo e(__("app.frequency")); ?></label>
              <select name="frequency" id="" class="form-control" required disabled>
                <?php $__currentLoopData = frequencies(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($key); ?>">
                    <?php echo e($item); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  
              </select>
              <input type="hidden" name="frequency" value="30">
            </div>
            <div class="col-md-4 form-group">
              <label for=""><?php echo e(__("app.installment")); ?></label>
              <input type="text" name="installment" class="form-control" value="1" required>
            </div>
            <div class="col-md-4 form-group">
              <label for=""><?php echo e(__('app.type')); ?></label>
              <select name="type" id="" class="form-control" required>
                <?php $__currentLoopData = updatedSchedules(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($key); ?>">
                    <?php echo e($item); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  
              </select>
            </div>
            <div class="col-md-12 form-group">
              <label for=""><?php echo e(__('app.note')); ?></label>
              <textarea name="note" id="" class="form-control" cols="30" rows="10"></textarea>
            </div>
          </div>
        </div>
  
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo app('translator')->getFromJson('app.save'); ?></button>
          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo e(trans('app.close')); ?></button>
        </div>
      </form>
    </div>
</div>
  