<?php $__env->startSection('title', trans('app.overdue_loan')); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.overdue_loan')); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <div class="card">
                <div class="card-header">
                    <form method="get" action="<?php echo e(route('report.overdue_loan')); ?>">
                        <div class="row">
                            
                            
                            <?php if(empty(auth()->user()->staff)): ?>
                            <div class="col-sm-6 col-lg-3 pl-1 pr-0">
                                <select name="branch" id="branch" class="form-control select2">
                                    <option value=""><?php echo e(trans('app.branch')); ?></option>
                                    <?php $__currentLoopData = allBranches(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch') == $branch->id ? 'selected' : ''); ?>><?php echo e($branch->location); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            
                            
                            <div class="col-sm-6 col-lg-3 pl-1 pr-0">
                                <select name="agent" id="agent" class="form-control select2">
                                <option value=""><?php echo e(trans('app.agent')); ?></option>
                                <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($agent->id); ?>" <?php echo e(request('agent') == $agent->id ? 'selected' : ''); ?>>
                                    <?php echo e($agent->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            
                            <?php endif; ?>
                            
                            <div class="col-md-3">
                                
                                <select name="sort" class="form-control" id="">
                                  <option value="asc" <?php echo e(request('sort')== 'asc' ? 'selected' : ''); ?>><?php echo e(trans('app.asc')); ?></option>
                                  <option value="desc" <?php echo e(request('sort')== 'desc' ? 'selected' : ''); ?>><?php echo e(trans('app.desc')); ?></option>
                                </select>
                            </div>
                            <div class="form-group col-sm-3 col-lg-3">
                            
                            <input type="text" name="date" id="date" class="form-control date-picker" readonly placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(request('date')); ?>">
                            </div>

                            
                            <div class="col-lg-3 pl-1">
                            <?php echo $__env->make('partial.search-input-group', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                            </div>

                            
                        </div>
                    </form>
                </div>
            </div>
            <br>
            <?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <a href="<?php echo e(url('repayment/list')); ?>" class="btn btn-sm btn-success pull-right mb-1"><?php echo e(trans('app.print')); ?></a>
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 5%"><?php echo e(trans('app.action')); ?></th>
                            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('client_code', trans('app.loan_code')));?></th>
                            <th style="width: 10%"><?php echo e(trans('app.client')); ?></th>
                            <th style="width: 10%"><?php echo e(trans('app.profile_photo')); ?></th>
                            <th style="width: 10%"><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('client_id', trans('app.client_code')));?></th>
                            <th style="width: 10%"><?php echo e(trans('app.phone_number')); ?></th>
                            <th style="width: 10%"><?php echo e(trans('app.branch')); ?></th>
                            <?php if(isAdmin()): ?>
                                <th><?php echo e(trans('app.agent')); ?></th>
                            <?php endif; ?>
                            <th style="width: 10%"><?php echo e(trans('app.next_payment_date')); ?></th>
                            <th><?php echo e(trans('app.payment_amount')); ?></th>
                            <th><?php echo e(trans('app.count_late_date')); ?></th>
                            <th style="width: 10%">
                                <?php echo e(trans('app.product_ime')); ?>

                            </th>
                            <th>
                                <?php echo e(trans('app.icloud')); ?>

                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $overdueLoans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php 
                                $amountToPay = $loan->total_amount - $loan->total_paid_amount
                            ?>
                            <tr>
                                <td class="text-center">
                                    
                                    <a href="<?php echo e(route('repayment.show', [$loan->id, RepayType::REPAY])); ?>" class="btn btn-success btn-sm mb-1">
                                    <?php echo e(trans('app.repay')); ?>

                                    </a>
                
                                    
                                    <a href="<?php echo e(route('repayment.show', [$loan->id, RepayType::PAYOFF])); ?>" class="btn btn-success btn-sm mb-1">
                                    <?php echo e(trans('app.pay_off')); ?>

                                    </a>
                
                                
                                
                                </td>
                                <td>
                                    <?php echo e($loan->client_code); ?>

                                </td>
                                <td><?php echo $__env->make('partial.client-detail-link', ['client' => $loan->client], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
                                <td><?php echo $__env->make('partial.client-profile-photo', ['client' => $loan->client], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
                                <td>
                                <?php echo $__env->make('partial.loan-detail-link', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                </td>
                                <td><?php echo e($loan->client->first_phone); ?> <?php echo e($loan->client->second_phone ? ', '.$loan->client->second_phone : ""); ?></td>
                                <td><?php echo e($loan->branch->location ?? trans('app.n/a')); ?></td>
                                <?php if(isAdmin()): ?>
                                <td><?php echo $__env->make('partial.staff-detail-link', ['staff' => $loan->staff], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
                                <?php endif; ?>
                
                                <td><?php echo e(displayDate($loan->schedules[0]->payment_date)); ?></td>
                                <td><b><?php echo e($amountToPay ?  num_f($amountToPay) : ''); ?></b></td>
                                <td>
                                <b>
                                    <?php echo e($loan->late_payment); ?>

                                </b>
                                </td>
                                <td>
                                <?php $__currentLoopData = $loan->transaction->sell_lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(@$item->product): ?>
                                    <?php echo $__env->make('partial.product-detail-link', ['product' => @$item->product, 'variantion' => @$item->variantion->name], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?><br>
                                    <?php endif; ?>
                                    <b><?php echo e(trans('app.quantity')); ?>:<?php echo e($item->quantity); ?></b>, <b>IME:</b>
                                    <?php if(@$item->transaction->transaction_ime): ?>
                                    <?php $__currentLoopData = $item->transaction->transaction_ime; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(!$loop->first): ?>
                                            ,
                                        <?php endif; ?>
                                        <?php echo e($ime->ime->code); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <?php echo e(trans('app.n/a')); ?>

                                    <?php endif; ?>
                                    
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td>
                                <?php echo $loan->note ?? trans('app.n/a'); ?>

                                </td>
            
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <?php echo $overdueLoans->appends(Request::except('page'))->render(); ?>

            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script>
        var agentSelectLabel = '<option value=""><?php echo e(trans('app.agent')); ?>';
        var agentRetrievalUrl = '<?php echo e(route('staff.get_agents', ':branchId')); ?>';
    </script>
    <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
    <script src="<?php echo e(asset('js/agent-retrieval.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>