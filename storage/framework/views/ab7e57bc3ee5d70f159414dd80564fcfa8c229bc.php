<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form method="post" id="add_form" class="no-auto-submit" action="<?php echo e(route('loan.saveDelayStatus', $scheduleReference)); ?>">
        <?php echo csrf_field(); ?>
        <div class="modal-header">
          <h4 class="modal-title"><?php echo e(trans('app.delay_schedule')); ?> <?php echo e(__('app.approved')); ?></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
  
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 form-group">
              <label for=""><?php echo e(__('app.note')); ?></label>
              <textarea name="note" id="" class="form-control" cols="30" rows="10"></textarea>
            </div>
          </div>
        </div>
  
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo app('translator')->getFromJson('app.approved'); ?></button>
          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo e(trans('app.close')); ?></button>
        </div>
      </form>
    </div>
  </div>
  