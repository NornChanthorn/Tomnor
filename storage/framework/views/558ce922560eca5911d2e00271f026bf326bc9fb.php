<?php $__env->startSection('title', trans('app.financial_statement')); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.financial_statement')); ?></h3>
            
            <div class="row">
                <div class="col-md-6 table-responsive">
                    
                    <h5><?php echo e(trans('app.interest')); ?></h5>
                    <table class="table table-hover table-bordered">
                        <tbody>
                            <tr>
                                <th><?php echo e(trans('app.total_interest')); ?></th>
                                <th>$ <?php echo e(decimalNumber($totalInterest, true)); ?></th>
                            </tr>
                            <tr>
                                <th><?php echo e(trans('app.paid_interest')); ?></th>
                                <th>$ <?php echo e(decimalNumber($totalPaidInterest, true)); ?></th>
                            </tr>
                            <tr>
                                <th><?php echo e(trans('app.outstanding_interest')); ?></th>
                                <th>$ <?php echo e(decimalNumber(($totalInterest - $totalPaidInterest), true)); ?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 table-responsive">
                    
                    <h5><?php echo e(trans('app.principal')); ?></h5>
                    <table class="table table-hover table-bordered">
                        <tbody>
                            <tr>
                                <th><?php echo e(trans('app.total_product_price')); ?></th>
                                <th>$ <?php echo e(decimalNumber($totalLoanAmount, true)); ?></th>
                            </tr>
                            <tr>
                                <th><?php echo e(trans('app.depreciation_amount')); ?></th>
                                <th>$ <?php echo e(decimalNumber($totalDepreciation, true)); ?></th>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <th><?php echo e(trans('app.down_payment_amount')); ?></th>
                                <th>$ <?php echo e(decimalNumber($totalDownPayment, true)); ?></th>
                            </tr>
                            <tr>
                                <th><?php echo e(trans('app.paid_principal')); ?></th>
                                <th>$ <?php echo e(decimalNumber($totalPaidPrincipal, true)); ?></th>
                            </tr>
                            <tr>
                                <th><?php echo e(trans('app.outstanding')); ?></th>
                                <th>$ <?php echo e(decimalNumber(($totalDownPayment - $totalPaidPrincipal), true)); ?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>

            
            <div class="card">
                <div class="card-header">
                    <form method="GET" action="<?php echo e(route('report.financial_statement')); ?>">
                        <div class="row">
                            
                            <div class="form-group col-sm-6 col-md-3 pr-0">
                                <label for="report_type" class="control-label"><?php echo e(trans('app.report_type')); ?></label>
                                <select name="report_type" id="report_type" class="form-control select2 select2-no-search">
                                    <?php $__currentLoopData = durationTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeKey => $typeTitle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($typeKey); ?>" <?php echo e(selectedOption($typeKey, request('report_type'))); ?>>
                                            <?php echo e($typeTitle); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            
                            <div class="form-group col-sm-5 col-md-2 pr-0">
                                <label for="year" class="control-label"><?php echo e(trans('app.year')); ?></label>
                                <select name="year" id="year" class="form-control select2 select2-no-search">
                                    <?php $__currentLoopData = range(date('Y'), 2019); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($year); ?>" <?php echo e(selectedOption($year, request('year'))); ?>>
                                            <?php echo e($year); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            
                            <div class="form-group col-sm-5 col-md-2 pr-0">
                                <label for="month" class="control-label"><?php echo e(trans('app.month')); ?></label>
                                <select name="month" id="month" class="form-control select2 select2-no-search"
                                    <?php echo e(request('report_type') != DurationType::MONTHLY ? 'disabled' : ''); ?>>
                                    <?php $__currentLoopData = khmerMonths(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monthTitle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($loop->iteration); ?>" <?php echo e(selectedOption($loop->iteration, request('month'))); ?>>
                                            <?php echo e($monthTitle); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            
                            <div class="form-group col-sm-6 col-md-3 pr-0">
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

                            
                            <div class="form-group col-sm-2 col-md-2 pr-0">
                            ​   <?php echo $__env->make('partial.button-search', ['class' => 'btn-lg btn-search-horizontal'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <h5>
                        <?php if($reportType == DurationType::YEARLY): ?>
                            <?php echo e(trans('app.payment_in_year') . ' ' . $filteredYear); ?>

                        <?php else: ?>
                            <?php echo e(trans('app.payment_in_month') . ' ' . khmerMonths(request('month')) . ' ' . trans('app.year') . ' ' . $filteredYear); ?>

                        <?php endif; ?>
                        (<?php echo e($branchTitle); ?>)
                    </h5>
                    <?php if($reportType == DurationType::YEARLY): ?>
                        <div class="table-responsive resize-w">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <?php $__currentLoopData = khmerMonths(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $monthTitle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <th <?php if($loop->iteration == date('m')): ?> class="bg-success text-white" <?php endif; ?>>
                                                <?php echo e($monthTitle); ?>

                                            </th>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <th><?php echo e(trans('app.total_amount')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $totalPaidPrincipal = $totalPaidInterest = $totalPaidPenalty = $totalPaidAmount = 0;
                                        $paidPrincipalElm = $paidInterestElm =  $paidPenaltyElm = $paidTotalElm = '';

                                        foreach ($filteredData as $key => $monthlyPayment) {
                                            $isCurrentMonth = (($key + 1) == date('m'));
                                            $totalPaidPrincipal += $monthlyPayment['paid_principal'];
                                            $totalPaidInterest += $monthlyPayment['paid_interest'];
                                            $totalPaidPenalty += $monthlyPayment['paid_penalty'];
                                            $totalPaidAmount += $monthlyPayment['paid_total'];

                                            $paidPrincipalElm .=
                                                '<th' . ($isCurrentMonth ? ' class="bg-success text-white">' : '>') .
                                                    decimalNumber($monthlyPayment['paid_principal'], true) .
                                                 '</th>';
                                            $paidInterestElm .=
                                                '<th' . ($isCurrentMonth ? ' class="bg-success text-white">' : '>') .
                                                    decimalNumber($monthlyPayment['paid_interest'], true) .
                                                 '</th>';
                                            $paidPenaltyElm .=
                                                '<th' . ($isCurrentMonth ? ' class="bg-success text-white">' : '>') .
                                                    decimalNumber($monthlyPayment['paid_penalty'], true) .
                                                 '</th>';
                                            $paidTotalElm .=
                                                '<th' . ($isCurrentMonth ? ' class="bg-success text-white">' : '>') .
                                                    decimalNumber($monthlyPayment['paid_total'], true) .
                                                '</th>';
                                        }
                                    ?>
                                    <tr>
                                        <th><?php echo e(trans('app.total_paid_amount')); ?> ($)</th>
                                        <?php echo $paidTotalElm; ?>

                                        <th><?php echo e(decimalNumber($totalPaidAmount, true)); ?></th>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(trans('app.paid_principal')); ?> ($)</th>
                                        <?php echo $paidPrincipalElm; ?>

                                        <th><?php echo e(decimalNumber($totalPaidPrincipal, true)); ?></th>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(trans('app.paid_penalty')); ?> ($)</th>
                                        <?php echo $paidPenaltyElm; ?>

                                        <th><?php echo e(decimalNumber($totalPaidPenalty, true)); ?></th>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(trans('app.paid_interest')); ?> ($)</th>
                                        <?php echo $paidInterestElm; ?>

                                        <th><?php echo e(decimalNumber($totalPaidInterest, true)); ?></th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-md-10 col-lg-8 table-responsive">
                                <table class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th><?php echo e(trans('app.day')); ?></th>
                                            <th><?php echo e(trans('app.total_paid_amount')); ?> ($)</th>
                                            <th><?php echo e(trans('app.paid_principal')); ?> ($)</th>
                                            <th><?php echo e(trans('app.paid_interest')); ?> ($)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $totalPaidPrincipal = $totalPaidInterest = $totalPaidAmount = 0; ?>
                                        <?php $__currentLoopData = $filteredData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dailyPayment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $totalPaidPrincipal += $dailyPayment['paid_principal'];
                                                $totalPaidInterest += $dailyPayment['paid_interest'];
                                                $totalPaidAmount += $dailyPayment['paid_total'];
                                            ?>
                                            <tr <?php if($loop->iteration == date('d')): ?> class="bg-success text-white" <?php endif; ?>>
                                                <th><?php echo e($loop->iteration); ?></th>
                                                <th><?php echo e(decimalNumber($dailyPayment['paid_total'], true)); ?></th>
                                                <th><?php echo e(decimalNumber($dailyPayment['paid_principal'], true)); ?></th>
                                                <th><?php echo e(decimalNumber($dailyPayment['paid_interest'], true)); ?></th>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <th><?php echo e(trans('app.total')); ?></th>
                                            <th><?php echo e(decimalNumber($totalPaidAmount, true)); ?></th>
                                            <th><?php echo e(decimalNumber($totalPaidPrincipal, true)); ?></th>
                                            <th><?php echo e(decimalNumber($totalPaidInterest, true)); ?></th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        var monthlyDuration = '<?php echo e(DurationType::MONTHLY); ?>';
    </script>
    <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
    <script src="<?php echo e(asset('js/financial-report.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>