<div class="tab-pane active table-responsive" role="tabpanel">
    <table class="table table-hover table-bordered">
        <thead>
            <th>
                <?php echo e(__('app.installment')); ?>

            </th>
            <th>
                <?php echo e(__('app.frequency')); ?>

            </th>
            <th>
                <?php echo e(__('app.type')); ?>

            </th>
            <th>
                <?php echo e(__('app.note')); ?>

            </th>
            <th>
                <?php echo e(__('app.status')); ?>

            </th>
            <th>
                <?php echo e(__('app.approved_by')); ?>

            </th>
            <th>
                <?php echo e(__('app.approve')); ?><?php echo e(__('app.note')); ?>

            </th>
            <th>
                <?php echo e(__('app.created_date')); ?>

            </th>
            <th>
                <?php echo e(__('app.action')); ?>

            </th>
        </thead>
        <tbody>
            <?php $__currentLoopData = $loan->scheduleReferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>

                    <td>
                        <?php echo e($item->installment); ?> <?php echo e(__('app.times')); ?>

                    </td>
                    <td>
                        <?php echo e(frequencies($item->frequency)); ?>

                    </td>

                    <td>
                        <?php echo e(updatedSchedules($item->type)); ?>

                    </td>
                    <td>
                        <?php echo e($item->note); ?>

                    </td>
                    <td>
                        <?php echo e(@$item->is_approved ? __('app.approved') : __('app.pending')); ?> 
                    </td>
                    <td>
                        <?php echo e(@$item->approved_note); ?>

                    </td>
                    <td>
                        <?php echo e(@$item->approved_note); ?>

                    </td>
                    <td>
                        <?php echo e(displayDate($item->created_at)); ?>

                    </td>
                    <td>
                        <?php if(@$item->is_approved==false): ?>
                            <a href="javascript::void(0);" class="btn btn-primary mb-1 btn-modal" title="<?php echo e(trans('app.approve')); ?>" data-href="<?php echo e(route('loan.getDelayStatus', $item)); ?>" data-container=".schedule_modal">
                                <?php echo e(__('app.approve')); ?>

                            </a>
                        <?php endif; ?>
                        <?php if(@$item->is_approved==true): ?>
                            <a href="javascript::void(0);" class="btn btn-primary mb-1 btn-modal" title="<?php echo e(trans('app.schedule_history')); ?>" data-href="<?php echo e(route('loan.getScheduleHistory', $item)); ?>" data-container=".schedule_modal">
                                <?php echo e(__('app.schedule_history')); ?>

                            </a>
                        <?php endif; ?>
                        <?php if(isAdmin() && @$item->is_approved==false|| Auth::user()->can('loan.delete') && @$item->is_approved==false): ?>
                            
                            <button type="button" id="delete_loan" class="btn btn-danger btn-delete mb-1"
                                data-url="<?php echo e(route('loan.deleteDelaySchedule', $item->id)); ?>">
                                <i class="fa fa-trash-o"></i> <?php echo e(trans('app.delete')); ?>

                            </button>
                        <?php endif; ?>

                        
                    </td>
              
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>