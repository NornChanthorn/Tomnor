<?php $__env->startSection('title', trans('app.loan')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.loan_detail')); ?>

        <div class="pull-right">

                           
            <?php if(Auth::user()->can('loan-cash.reject') && $loan->status == LoanStatus::PENDING): ?>
                
                <button type="button" id="reject_loan" class="btn btn-danger mb-1"
                    data-url="<?php echo e(route('loan-cash.change_status', [$loan->id, LoanStatus::REJECTED])); ?>">
                    <i class="fa fa-times pr-1"></i> <?php echo e(trans('app.cancel')); ?>

                </button>
            <?php endif; ?>
            <?php if(Auth::user()->can('loan-cash.approval') && $loan->status == LoanStatus::PENDING): ?>
                
                <button type="button" id="approve_loan" class="btn btn-success mb-1"
                    data-url="<?php echo e(route('loan-cash.change_status', [$loan->id, LoanStatus::ACTIVE])); ?>">
                    <i class="fa fa-check pr-1"></i> <?php echo e(trans('app.approve')); ?>

                </button>
            <?php endif; ?>
            <?php if(isAdmin() || Auth::user()->can('loan-cash.delete') && !isPaidLoan($loan->id)): ?>
                
                <button type="button" id="delete_loan" class="btn btn-danger btn-delete mb-1"
                    data-url="<?php echo e(route('loan.destroy', $loan->id)); ?>" data-redirect="<?php echo e(route('loan-cash.index')); ?>">
                    <i class="fa fa-trash-o"></i> <?php echo e(trans('app.delete')); ?>

                </button>
            <?php endif; ?>

            <?php if(Auth::user()->can('loan-cash.edit') && $loan->status==LoanStatus::PENDING): ?>
                <a href="<?php echo e(route('loan-cash.edit', $loan->id)); ?>" class="btn btn-primary mb-1">
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
        </div>
    </h3>
    
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <p>
                        <?php echo e(trans('app.client_name')); ?> : <br>
                        <strong> <a href="<?php echo e(route('client.show',$loan->client_id)); ?>"><?php echo e($loan->client->name); ?>   </a>   </strong>
                    </p>
                    <p>
                        <?php echo e(trans('app.address')); ?> <br>
                        <strong>
                            <?php echo e(@$loan->client->address ?? @$loan->client->location); ?>

                        </strong>
                    </p>

                    <p>
                        <?php echo e(__('app.contact')); ?> <br>
                        <strong>
                            <?php echo e(@$loan->client->first_phone); ?><?php echo e(@$loan->client->second_phone ? ', '.@$loan->client->second_phone : ''); ?>

                        </strong>
                    </p>
                    
                </div>
                <div class="col-md-2">
                    <?php if(@$loan->client->profile_photo): ?>
                        <img src="<?php echo e(asset($loan->client->profile_photo)); ?>" width="50%"  class="img-fluid">
                    <?php endif; ?>

                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-6">
                                    <p class="text-right">
                                        <?php echo e(trans('app.reference_number')); ?>

                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            <?php echo e($loan->client_code); ?>

                                        </strong>
                                    </p>
                                
                                </div>
                                <div class="col-6 text-right">
                                    <p>
                                        <?php echo e(trans('app.account_number')); ?>

                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            <?php echo e($loan->account_number); ?> / <?php echo e(str_pad($loan->client_id,6, 0, STR_PAD_LEFT)); ?>

                                        </strong>
                                    </p>
                                
                                </div>

                                <div class="col-6 text-right">
                                    <p>
                                        <?php echo e(trans('app.loan_amount')); ?>

                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            <?php echo e(num_f($loan->loan_amount)); ?>

                                        </strong>
                                    </p>
                                
                                </div>
                                
                                <div class="col-6 text-right">
                                    <p>
                                        <?php echo e(trans('app.installment')); ?>

                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            <?php echo e(numKhmer($loan->installment)); ?> <?php echo e(__('app.times')); ?>

                                        </strong>
                                    </p>
                                
                                </div>
                                <div class="col-6 text-right">
                                    <p>
                                        <?php echo e(trans('app.frequency')); ?>

                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            <?php echo e(numKhmer($loan->frequency)); ?> <?php echo e(__('app.day')); ?>

                                        </strong>
                                    </p>
                                
                                </div>
                                <div class="col-6 text-right">
                                    <p>
                                        <?php echo e(trans('app.interest')); ?>

                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            <?php echo e(numKhmer($loan->interest_rate)); ?> % / <?php echo e(__('app.day')); ?>

                                        </strong>
                                    </p>
                                
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">

                                <div class="col-6 text-right">
                                    <p>
                                        <?php echo e(trans('app.loan_start_date')); ?>

                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            <?php echo e(displayDate($loan->loan_start_date)); ?>

                                        </strong>
                                    </p>
                                
                                </div>
                                <div class="col-6 text-right">
                                    <p>
                                        <?php echo e(trans('app.first_payment_date')); ?>

                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            <?php echo e(displayDate($loan->first_payment_date)); ?>

                                        </strong>
                                    </p>
                                
                                </div>
                                <div class="col-6 text-right">
                                    <p>
                                        <?php echo e(trans('app.disbursement_date')); ?>

                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            <?php echo e(displayDate($loan->approved_date)); ?>

                                        </strong>
                                    </p>
                                
                                </div>
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
                                            <?php echo e(@$loan->staff->name); ?>

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
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                <a class="nav-link <?php if(request()->get('get')=='account-details' || request()->get('get')==''): ?>active <?php endif; ?>" href="<?php echo e(route('loan-cash.show',[$loan,'get'=>'account-details'])); ?>"><?php echo e(__('app.account')); ?> <?php echo e(__('app.detail')); ?></a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?php if(request()->get('get')=='repayment-schedule'): ?>active <?php endif; ?>" href="<?php echo e(route('loan-cash.show',[$loan,'get'=>'repayment-schedule'])); ?>"><?php echo e(__('app.payment_schedule')); ?></a>
                </li>
                <?php if($loan->scheduleReferences->count()>0 && isAdmin() || $loan->scheduleReferences->count()> 0 && Auth::user()->can('loan-cash.delay-schedule')): ?>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?php if(request()->get('get')=='delay-schedule'): ?>active <?php endif; ?>" href="<?php echo e(route('loan-cash.show',[$loan,'get'=>'delay-schedule'])); ?>"><?php echo e(__('app.delay_schedule')); ?></a>
                    </li>
                <?php endif; ?>
                <?php if(isAdmin() || Auth::user()->can('collateral.browse')): ?>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <?php if(request()->get('get')=='collaterals'): ?>active <?php endif; ?>" href="<?php echo e(route('loan-cash.show',[$loan,'get'=>'collaterals'])); ?>"><?php echo e(__('app.collateral')); ?></a>
                    </li>
                <?php endif; ?>
                
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content">
                <?php if(request()->get('get')=='account-details' || request()->get('get')==''): ?>
                    <?php echo $__env->make('loan-cash.partials.account-details', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
                <?php endif; ?>
                <?php if(request()->get('get')=='repayment-schedule'): ?>
                    <?php echo $__env->make('loan-cash.partials.repayment-schedule', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
                <?php endif; ?>
                <?php if(request()->get('get')=='delay-schedule' && $loan->scheduleReferences->count()>0): ?>
                    <?php echo $__env->make('loan-cash.partials.delay', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
                <?php endif; ?>
                <?php if(Auth::user()->can('collateral.browse') &&  request()->get('get')=='collaterals'): ?>
                    <?php echo $__env->make('loan-cash.partials.collateral', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
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
        confirmPopup($(this).data('url'), 'error', 'DELETE',$(this).data('redirect'));
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