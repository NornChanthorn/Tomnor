<?php $__env->startSection('title', trans('app.loan')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.loan_detail')); ?></h3>
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php
        $isFormShowType = ($formType == FormType::SHOW_TYPE);
        $disabledFormType = ($isFormShowType ? 'disabled' : '');
        $requiredFormType = ($formType != FormType::SHOW_TYPE ? '<span class="required">*</span>' : '');
    ?>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 col-lg-7">
                    <h5>
                        <strong>
                            <?php echo e($loan->client->name); ?>

                        </strong>
                    </h5>
                   
                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <?php echo e(trans('app.address')); ?> <br>
                                <strong>
                                    <?php echo e(@$loan->client->address ?? @$loan->client->location); ?>

                                </strong>
                            </p>
                            <p>
                                <?php echo e(trans('app.account_number')); ?> <br>
                                <strong>
                                    <?php echo e($loan->account_number); ?> / <?php echo e(str_pad($loan->client_id,6, 0, STR_PAD_LEFT)); ?>

                                </strong>
                            </p>
                            <p>
                                <?php echo e(trans('app.reference_number')); ?> <br>
                                <strong>
                                    <?php echo e($loan->client_code); ?>

                                </strong>
                            </p>
                        </div>
                        <div class="col-md-6">
                           
                            <p>
                                <?php echo e(__('app.contact')); ?> <br>
                                <strong>
                                    <?php echo e(@$loan->client->first_phone); ?><?php echo e(@$loan->client->second_phone ? ', '.@$loan->client->second_phone : ''); ?>

                                </strong>
                            </p>
                        </div>
                        
                    </div>
                    <?php if($loan->client->sponsor_name): ?>
                        <h5>
                            <strong>
                                <?php echo e(__('app.sponsor_information')); ?>

                            </strong>
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                               
                                <h5>
                                    <strong>
                                        <?php echo e($loan->client->sponsor_name); ?>

                                    </strong>
                                </h5>
                                <p>
                                    <?php echo e(trans('app.address')); ?> <br>
                                    <strong>
                                        <?php echo e($loan->client->slocation); ?>

                                    </strong>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    <?php echo e(trans('app.contact')); ?> <br>
                                    <strong>
                                        <?php echo e($loan->client->sponsor_phone); ?><?php echo e($loan->client->sponsor_phone_2 ? ', '. $loan->client->sponsor_phone_2 : ''); ?> 
                                    </strong>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                   
                </div>
                <div class="col-md-4 col-lg-5">
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="pull-right">

                           
                                <?php if($loan->status == LoanStatus::PENDING): ?>
                                    
                                    <button type="button" id="reject_loan" class="btn btn-danger mb-1"
                                        data-url="<?php echo e(route('loan.change_status', [$loan->id, LoanStatus::REJECTED])); ?>">
                                        <i class="fa fa-times pr-1"></i> <?php echo e(trans('app.cancel')); ?>

                                    </button>

                                    
                                    <button type="button" id="approve_loan" class="btn btn-success mb-1"
                                        data-url="<?php echo e(route('loan.change_status', [$loan->id, LoanStatus::ACTIVE])); ?>">
                                        <i class="fa fa-check pr-1"></i> <?php echo e(trans('app.approve')); ?>

                                    </button>
                                <?php endif; ?>
                                <?php if(isAdmin() || Auth::user()->can('loan.delete') && !isPaidLoan($loan->id)): ?>
                                    
                                    <button type="button" id="delete_loan" class="btn btn-danger btn-delete mb-1"
                                        data-url="<?php echo e(route('loan.destroy', $loan->id)); ?>">
                                        <i class="fa fa-trash-o"></i> <?php echo e(trans('app.delete')); ?>

                                    </button>
                                <?php endif; ?>

                                <?php if(Auth::user()->can('loan.edit') && !isPaidLoan($loan->id)): ?>
                                    <a href="<?php echo e(route('loan.edit', $loan->id)); ?>" class="btn btn-primary mb-1">
                                        <i class="fa fa-pencil-square-o pr-1"></i> <?php echo e(trans('app.edit')); ?>

                                    </a>
                                <?php endif; ?>
                                <?php if(isAdmin() &&  $loan->status == LoanStatus::ACTIVE || Auth::user()->can('loan.pay') && $loan->status == LoanStatus::ACTIVE): ?>
                                    
                                    <a href="<?php echo e(route('repayment.show', [$loan->id, RepayType::REPAY])); ?>" class="btn btn-success mb-1">
                                        <i class="fa fa-money"></i>  <?php echo e(trans('app.repay')); ?>

                                    </a>

                                    
                                    <a href="<?php echo e(route('repayment.show', [$loan->id, RepayType::PAYOFF])); ?>" class="btn btn-success mb-1">
                                        <i class="fa fa-money"></i> <?php echo e(trans('app.pay_off')); ?>

                                    </a>
                                <?php endif; ?>
                                
                                <?php if(Auth::user()->can('loan.print') && in_array($loan->status, [LoanStatus::ACTIVE, LoanStatus::PAID])): ?>
                                    <a class="btn btn-success mb-1" target="_blank" href="<?php echo e(route('loan.print_contract', $loan)); ?>">
                                        <i class="fa fa-print pr-1"></i> <?php echo e(trans('app.print_contract')); ?>

                                    </a>
                                <?php endif; ?>
                                <?php if($loan->status=='ac'): ?>
                                    <a href="javascript::void(0);" class="btn btn-primary mb-1 btn-modal" title="<?php echo e(trans('app.delay_schedule')); ?>" data-href="<?php echo e(route('loan.delaySchedule', $loan)); ?>" data-container=".schedule_modal">
                                        <?php echo e(__('app.delay_schedule')); ?>

                                    </a>
                                <?php endif; ?>
                           
                            </div>
                        </div>
                        
                    </div>
                     
                    <div class="row">
                        <div class="col-6 text-right">
                            <p>
                                <?php echo e(__('app.created_date')); ?>

                            </p>
                            
                        </div>
                        <div class="col-6">
                            <p>
                                : &nbsp;
                                <strong>
                                    <?php echo e(displayDate($loan->created_at)); ?>

                                </strong>
                            </p>
                      
                        </div>
                        
                        <div class="col-6 text-right">
                            <p>
                                <?php echo e(__('app.sale_agency')); ?> 
                            </p>
                 
                        </div>
                        <div class="col-6">
                            <p>
                                : &nbsp;
                                <strong>
                                    <?php echo e($loan->staff->name); ?>

                                </strong>
                           
                            </p>
                        
                        </div>
                        <div class="col-6 text-right">
                            <p>
                                <?php echo e(__('app.status')); ?> 
                            </p>
                 
                        </div>
                        <div class="col-6">
                            <p>
                               : &nbsp; <?php echo $__env->make('partial.loan-status-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                           
                            </p>
                        
                        </div>
                        
                    </div>
                   
                </div>
                
            </div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body table-responsive">
            <table class="table table-head-fixed text-nowrap">
                <thead>
                    <tr>
                        <th><?php echo e(__('app.no_sign')); ?></th>
                        <th><?php echo e(trans('app.name')); ?></th>
                        <th><?php echo e(trans('app.code')); ?></th>
                        <th><?php echo e(trans('app.sale_quantity')); ?></th>
                        <th><?php echo e(trans('app.unit_price')); ?></th>
                        <th><?php echo e(trans('app.sub_total')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($loan->transaction)): ?>
                        <?php $__currentLoopData = $loan->transaction->sell_lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key =>  $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php echo e(no_f($key+1)); ?>

                                </td>
                                <td>
                                    <?php echo $__env->make('partial.product-detail-link', ['product' => @$item->product, 'variantion' => @$item->variations->name], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

                                    <a class="btn btn-sm btn-success" href="<?php echo e(route('product.ime-create',[
                                        'transaction_id'=>$loan->transaction_id,
                                        'location_id'=>$loan->branch_id,
                                        'product_id'=>$item->product_id,
                                        'variantion_id'=>$item->variantion_id,
                                        'qty'=> $item->quantity,
                                        'purchase_sell_id'=>$item->id,
                                        'type'=>'loan'
                                        ])); ?>"><?php echo e(trans('app.product_ime')); ?></a>
                                </td>
                                <td><?php echo e(@$item->product->code ?? trans('app.none')); ?></td>
                                <td>
                                    <?php echo e(no_f($item->quantity)); ?>

                                </td>
                                <td>
                                    <?php echo e(num_f($item->unit_price)); ?>

                                </td>
                                <td>
                                    <?php echo e(num_f($item->quantity * $item->unit_price)); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <?php $__currentLoopData = $loan->productDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key =>  $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <?php echo e(no_f($key+1)); ?>

                                </td>
                                <td>
                                    <?php echo $__env->make('partial.product-detail-link', ['product' => @$item->product, 'variantion' => $item->variantion->name], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                </td>
                                <td><?php echo e(@$item->product->code ?? trans('app.none')); ?></td>
                                <td>
                                    <?php echo e(no_f($item->qty)); ?>

                                </td>
                                <td>
                                    <?php echo e(num_f($item->unit_price)); ?>

                                </td>
                                <td>
                                    <?php echo e(num_f($item->qty * $item->unit_price)); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                
                   
                </tbody>
            </table>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                <a class="nav-link <?php if(request()->get('get')=='account-details' || request()->get('get')==''): ?>active <?php endif; ?>" href="<?php echo e(route('loan.show',[$loan,'get'=>'account-details'])); ?>"><?php echo e(__('app.account')); ?> <?php echo e(__('app.detail')); ?></a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?php if(request()->get('get')=='repayment-schedule'): ?>active <?php endif; ?>" href="<?php echo e(route('loan.show',[$loan,'get'=>'repayment-schedule'])); ?>"><?php echo e(__('app.payment_schedule')); ?></a>
                </li>
                <?php if($loan->scheduleReferences->count()>0 && isAdmin() || $loan->scheduleReferences->count()> 0 && Auth::user()->can('loan.delay-schedule')): ?>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?php if(request()->get('get')=='delay-schedule'): ?>active <?php endif; ?>" href="<?php echo e(route('loan.show',[$loan,'get'=>'delay-schedule'])); ?>"><?php echo e(__('app.delay_schedule')); ?></a>
                    </li>
                <?php endif; ?>
                
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content">
                <?php if(request()->get('get')=='account-details' || request()->get('get')==''): ?>
                    <?php echo $__env->make('loan.partials.account-details', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
                <?php endif; ?>
                <?php if(request()->get('get')=='repayment-schedule'): ?>
                    <?php echo $__env->make('loan.partials.repayment-schedule', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
                <?php endif; ?>
                <?php if(request()->get('get')=='delay-schedule' && $loan->scheduleReferences->count()>0): ?>
                    <?php echo $__env->make('loan.partials.delay', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
                <?php endif; ?>
                
            </div>
        </div>
    </div>
  </div>
</main>
<div class="modal fade schedule_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<script>
    
    $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE','/loan');
    });
    // Reject loan
    $('#reject_loan').click(function () {
        confirmPopup($(this).data('url'), 'warning');
    });

    // Approve loan
    $('#approve_loan').click(function () {
        confirmPopup($(this).data('url'), 'success');
    });

    $("#disbursed_loan").click(function() {
        let url = $(this).data('url');

        swal(defaultSwalOptions('success'), function(isConfirmed) {
        if(isConfirmed) {
            window.location.href = url;
        }
        });
    });

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>