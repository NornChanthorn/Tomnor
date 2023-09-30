<div class="tab-pane active table-responsive" role="tabpanel">
    <?php if(Auth::user()->can('collateral.add')): ?>
        <?php echo $__env->make('partial/anchor-create', ['href' => route('collateral-create',$loan), 'class' => 'mb-2 mt-2 pull-right'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php endif; ?>
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th><?php echo e(__('app.no_sign')); ?></th>
                <th><?php echo e(__('app.name')); ?></th>
                <th><?php echo e(__('app.collateral_type')); ?></th>
                <th><?php echo e(__('app.value')); ?></th>
                <th><?php echo e(__('app.note')); ?></th>
                <th><?php echo e(__('app.action')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $loan->collaterals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $collateral): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td>
                        <?php echo e(no_f($loop->iteration)); ?>

                    </td>
                    <td>
                        <?php echo e($collateral->name); ?>

                    </td>
                    <td>
                        <?php echo e(collateralTypes($collateral->type_id)); ?>

                    </td>
                    <td>
                        <?php echo e(num_f($collateral->value)); ?>

                    </td>
                    <td>
                        <?php echo $collateral->note; ?>

                    </td>
                    <td>
                        <a class="btn btn-sm btn-success mb-1" href="<?php echo e(asset($collateral->files)); ?>" target="_blank">
                        <i class="fa fa-download"></i></a>
                        <?php if(Auth::user()->can('collateral_type.edit')): ?>
                            <?php echo $__env->make('partial.anchor-edit', [
                                'href' => route('collateral-edit', $collateral),
                            ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php endif; ?>
                        <?php if(Auth::user()->can('collateral.delete')): ?>
                            <a href="javascript:void(0);" title="<?php echo e(__('app.delete')); ?>" data-url="<?php echo e(route('collateral.destroy', $collateral)); ?>"  data-redirect="<?php echo e(route('loan-cash.show',[$loan,'get'=>'collaterals'])); ?>" class="btn btn-danger btn-sm mb-1 btn-delete"><i class="fa fa-trash-o"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>