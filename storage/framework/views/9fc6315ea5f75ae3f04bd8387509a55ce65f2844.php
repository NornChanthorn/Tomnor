<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
            <h3 class="page-heading"><?php echo e(trans('app.product_ime') . ' - '.$title); ?></h3>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>
                                <?php echo e(trans('app.no_sign')); ?>

                            </th>
                            <th>
                                <?php echo e(trans('app.product_name')); ?>

                            </th>
                            <th>
                                <?php echo e(trans('app.location')); ?>

                            </th>
                            <th>
                                <?php echo e(trans('app.name').trans('app.contact')); ?>

                            </th>
                            <th>
                                <?php echo e(trans('app.type')); ?>

                            </th>
                            <th>
                                <?php echo e(trans('app.price')); ?>

                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $ime->transaction_ime; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($offset++); ?></td>
                                <td>
                                     <?php echo e(@$ime->product->name); ?>

                                     <?php echo e(@$ime->variantion->name!='DUMMY' ? ' - '.@$ime->variantion->name : ''); ?>

                                </td>
                                <td><?php echo e(@$item->location->location); ?></td>
                                <td>
                                    <?php if(@$item->transaction->type=='leasing'): ?>
                                        <?php echo e(@$item->transaction->customer->name); ?>

                                    <?php else: ?>
                                        <?php echo e(@$item->transaction->client->name); ?>

                                    <?php endif; ?>
                                       </td>
                                <td><?php echo e(@$item->transaction->type); ?></td>
                                <td>
                                    <?php if(@$item->transaction->type=='purchase'): ?>
                                        <?php echo e(@$item->purchase->purchase_price); ?>

                                    <?php else: ?>
                                        <?php echo e(@$item->sell->unit_price); ?>

                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
