<?php $__env->startSection('title', trans('app.loan_disbursement')); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.loan_disbursement')); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <form id="form-search" method="get" action="<?php echo e(route('report.disbursed_loan')); ?>">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            
                            <div class="form-group col-sm-3 col-lg-2 pr-0">
                                <label for="start_date" class="control-label"><?php echo e(trans('app.start_date')); ?></label>
                                <input type="text" name="start_date" id="start_date" class="form-control date-picker"
                                       placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(displayDate($startDate)); ?>">
                            </div>

                            
                            <div class="form-group col-sm-3 col-lg-2 pr-0">
                                <label for="end_date" class="control-label"><?php echo e(trans('app.end_date')); ?></label>
                                <input type="text" name="end_date" id="end_date" class="form-control date-picker"
                                       placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(displayDate($endDate)); ?>">
                            </div>

                            
                            <div class="form-group col-sm-4 col-lg-3 pr-0">
                                <label for="branch" class="control-label"><?php echo e(trans('app.branch')); ?></label>
                                <select name="branch" id="branch" class="form-control select2">
                                    <option value=""><?php echo e(trans('app.all_branches')); ?></option>
                                    <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch') == $branch->id ? 'selected' : ''); ?>>
                                            <?php echo e($branch->location); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            
                            <div class="form-group col-sm-4 col-lg-3 pr-0">
                                <label for="agent" class="control-label"><?php echo e(trans('app.agent')); ?></label>
                                <select name="agent" id="agent" class="form-control select2">
                                    <option value=""><?php echo e(trans('app.all_agents')); ?></option>
                                    <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($agent->id); ?>" <?php echo e(request('agent') == $agent->id ? 'selected' : ''); ?>>
                                            <?php echo e($agent->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            
                            <div class="form-group col-sm-2">
                                <?php echo $__env->make('partial.button-search', ['class' => 'btn-lg btn-search-horizontal-2'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="card-body">
                        <h5>
                            <?php echo trans('app.disbursed_loans_from_date') . ' ' . displayDate($startDate)
                                . ' ' . trans('app.to') . ' ' . displayDate($endDate)
                                . ' (' . $branchTitle . ' - ' . $agentName . ')'; ?>

                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        <tr>
                                            <th><?php echo e(trans('app.number_of_disbursed_loans')); ?></th>
                                            <td><?php echo e($itemCount); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo e(trans('app.total_product_price')); ?></th>
                                            <td>$ <?php echo e(decimalNumber($totalLoanAmount, true)); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo e(trans('app.depreciation_amount')); ?></th>
                                            <td>$ <?php echo e(decimalNumber($totalDepreciation, true)); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo e(trans('app.down_payment_amount')); ?></th>
                                            <td>$ <?php echo e(decimalNumber($totalDownPayment, true)); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                <?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <div class="table-responsive resize-w">
                    <table class="table table-hover table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><?php echo e(trans('app.no_sign')); ?></th>
                                <th><?php echo e(trans('app.client')); ?></th>
                                <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('client_code', trans('app.client_code')));?></th>
                                <th><?php echo e(trans('app.branch')); ?></th>

                                <?php if(isAdmin()): ?>
                                    <th><?php echo e(trans('app.agent')); ?></th>
                                <?php endif; ?>

                                <th><?php echo e(trans('app.product')); ?></th>
                                <th><?php echo e(trans('app.product_price')); ?></th>
                                <th><?php echo e(trans('app.loan_amount')); ?></th>
                                <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('approved_date', trans('app.disbursement_date')));?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $disbursedLoans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($offset++); ?></td>
                                    <td><?php echo $__env->make('partial.client-detail-link', ['client' => $loan->client], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
                                    <td><?php echo $__env->make('partial.loan-detail-link', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
                                    <td><?php echo e($loan->branch->location ?? trans('app.n/a')); ?></td>

                                    <?php if(isAdmin()): ?>
                                        <td><?php echo $__env->make('partial.staff-detail-link', ['staff' => $loan->staff], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
                                    <?php endif; ?>

                                    <td>
                                        
                                        <?php $__currentLoopData = $loan->productDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo $__env->make('partial.product-detail-link', ['product' =>  $item->product,'variantion'=>$item->variantion->name], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        
                                    </td>
                                    <td>$ <?php echo e(decimalNumber($loan->loan_amount, true)); ?></td>
                                    <td>$ <?php echo e(decimalNumber($loan->down_payment_amount, true)); ?></td>
                                    <td><?php echo e(displayDate($loan->approved_date, true)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php echo $disbursedLoans->appends(Request::except('page'))->render(); ?>

                </div>
            </form>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        var agentSelectLabel = '<option value=""><?php echo e(trans('app.all_agents')); ?>';
        var agentRetrievalUrl = '<?php echo e(route('staff.get_agents', ':branchId')); ?>';
    </script>
    <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/date-time-picker.js')); ?>"></script>
    <script src="<?php echo e(asset('js/agent-retrieval.js')); ?>"></script>
    <script>
      $(document).ready(function() {
        $(".date-picker").datepicker({
          format: 'dd-mm-yyyy',
          autoclose: true,
          orientation: 'bottom right'
        });
      });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>