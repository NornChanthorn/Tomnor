<?php $__env->startSection('title', $title); ?>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap4-datetimepicker.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/sweetalert.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-fileinput.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e($title); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <form method="post" id="payment-form" class="no-auto-submit" action="<?php echo e(route('repayment.save', $loan->id)); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="repay_type" value="<?php echo e($repayType); ?>">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5><?php echo e(trans('app.client_information')); ?></h5>
                        
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <p><?php echo e(trans('app.client_name')); ?> : <?php echo $__env->make('partial.client-detail-link', ['client' => $loan->client], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></p>
                                <p><?php echo e(trans('app.client_code')); ?> : <?php echo e($loan->client_code); ?></p>
                                <p><?php echo e(trans('app.loan_code')); ?> : <?php echo e($loan->account_number); ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><?php echo e(trans('app.first_phone')); ?> : <?php echo e($loan->client->first_phone); ?></p>
                                <p><?php echo e(trans('app.second_phone')); ?> : <?php echo e($loan->second_phone); ?></p>
                                <p><?php echo e(trans('app.id_card_number')); ?> : <?php echo e($loan->client->id_card_number); ?></p>
                            </div>
                            <div class="col-md-4">
                                <img src="<?php echo e(asset($loan->client->profile_photo)); ?>" width="50%" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                         
                         <h5><?php echo e(trans('app.payment_schedule')); ?></h5>
                         <br>
                         <?php $isFlatInterestSchedule = ($loan->schedule_type == PaymentScheduleType::FLAT_INTEREST) ?>
                         <div class="table-responsive">
                             <table class="table table-bordered table-hover table-striped">
                                 <thead>
                                     <tr>
                                         <?php if($repayType == RepayType::ADVANCE_PAY): ?>
                                             <th width="10%"><?php echo e(trans('app.advance_pay')); ?></th>
                                         <?php endif; ?>
                                         <th><?php echo e(trans('app.payment_date')); ?></th>
                                         <?php if($isFlatInterestSchedule): ?>
                                             <th><?php echo e(trans('app.payment_amount')); ?></th>
                                         <?php else: ?>
                                             <th><?php echo e(trans('app.total')); ?></th>
                                             <th><?php echo e(trans('app.principal')); ?></th>
                                             <th><?php echo e(trans('app.interest')); ?></th>
                                         <?php endif; ?>
                                         <th><?php echo e(trans('app.outstanding')); ?></th>
                                         <th><?php echo e(trans('app.paid_date')); ?></th>
                                         <th><?php echo e(trans('app.paid_principal')); ?></th>
                                         <th><?php echo e(trans('app.paid_interest')); ?></th>
                                         <th><?php echo e(trans('app.penalty_amount')); ?></th>
                                         <th><?php echo e(trans('app.paid_amount')); ?></th>
                                         <th><?php echo e(trans('app.action')); ?></th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     <?php $__currentLoopData = $loan->schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                         <?php $decimalNumber = ($schedule->interest == 0 ? 2 : 0) ?>
                                         <tr>
                                             <?php if($repayType == RepayType::ADVANCE_PAY): ?>
                                                 <td>
                                                     <?php if($schedule->paid_interest == null || $schedule->paid_interest == 0): ?>
                                                         <div class="custom-control custom-checkbox text-center">
                                                             <input type="checkbox" name="schedules[]" id="schedule<?php echo e($schedule->id); ?>"
                                                                    class="custom-control-input schedule" data-principal="<?php echo e($schedule->principal); ?>"
                                                                    data-schedule-id="<?php echo e($schedule->id); ?>">
                                                             <label for="schedule<?php echo e($schedule->id); ?>" class="custom-control-label"></label>
                                                         </div>
                                                     <?php endif; ?>
                                                 </td>
                                             <?php endif; ?>
                                             <td><?php echo e(displayDate($schedule->payment_date)); ?></td>
                                             <?php if($isFlatInterestSchedule): ?>
                                                 <td>$ <?php echo e(decimalNumber($schedule->principal, $decimalNumber)); ?></td>
                                             <?php else: ?>
                                                 <td>$ <?php echo e(decimalNumber($schedule->total, $decimalNumber)); ?></td>
                                                 <td>$ <?php echo e(decimalNumber($schedule->principal, $decimalNumber)); ?></td>
                                                 <td>$ <?php echo e(decimalNumber($schedule->interest, $decimalNumber)); ?></td>
                                             <?php endif; ?>
                                             <td>$ <?php echo e(decimalNumber($schedule->outstanding)); ?></td>
                                             <td><?php echo e(displayDate($schedule->paid_date)); ?></td>
                                             <td><?php echo e($schedule->paid_principal ? '$ ' . decimalNumber($schedule->paid_principal, $decimalNumber) : ''); ?></td>
                                             <td><?php echo e($schedule->paid_interest ? '$ ' . decimalNumber($schedule->paid_interest, $decimalNumber) : ''); ?></td>
                                             <td><?php echo e($schedule->paid_penalty ? '$ ' . decimalNumber($schedule->paid_penalty, $decimalNumber) : ''); ?></td>
                                             <td><?php echo e($schedule->paid_total ? '$ ' . decimalNumber($schedule->paid_total, $decimalNumber) : ''); ?></td>
                                             <td>
                                                 <?php if(isAdmin() || Auth::user()->can('loan.edit-schedule')): ?>
                                                     <a href="<?php echo e(route('loan.edit_payment_schedule',$schedule)); ?>" class="btn btn-sm btn-primary" title="<?php echo e(trans('app.edit')); ?>">
                                                         <i class="fa fa-edit"></i>
                                                     </a>
                                                 <?php endif; ?>
                                             </td>
                                         </tr>
                                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                 </tbody>
                             </table>
                         </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5><?php echo e(trans('app.payment_method')); ?></h5>
                        <br>
                        <?php if($repayType==1): ?>
                            <div class="row">
                                <div class="col-lg-4 form-group">
                                    <label for="payment_date" class="control-label">
                                        <?php echo e(trans('app.paid_date')); ?> <span class="required">*</span>
                                    </label>
                                    <input type="text" name="payment_date" id="date-picker" class="form-control"
                                        value="<?php echo e(old('payment_date') ?? date('d-m-Y')); ?>" placeholder="<?php echo e(trans('app.date_placeholder')); ?>" required>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label for="payment_amount" class="control-label">
                                        <?php echo e(trans('app.payment_amount')); ?> ($) <span class="required">*</span>
                                    </label>
                                    <input type="text" name="payment_amount" id="payment_amount" class="form-control decimal-input"
                                        value="<?php echo e($remainingAmount ?? old('payment_amount')); ?>" required <?php echo e(Config::get('app.remain_payment')==true ? "readonly":""); ?>>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label for="payment_method" class="control-label">
                                        <?php echo e(trans('app.payment_method')); ?> <span class="required">*</span>
                                    </label>
                                    <select name="payment_method" id="payment_method" class="form-control select2 select2-no-search" required>
                                        <?php $__currentLoopData = paymentMethods(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $methodKey => $methodValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($methodKey); ?>" <?php echo e($methodKey == (old('payment_method') ?? 'dp') ? 'selected' : ''); ?>>
                                                <?php echo e($methodValue); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label for="penalty_amount" class="control-label">
                                        <?php echo e(trans('app.penalty_amount')); ?> ($)
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><?php echo e(decimalNumber($penaltyAmount)); ?></span>
                                        </div>
                                        <input type="text" name="penalty_amount" id="penalty_amount" class="form-control decimal-input"
                                            value="<?php echo e(old('penalty_amount')); ?>">
                                    </div>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label for="reference_number" class="control-label">
                                        <?php echo e(trans('app.reference_number')); ?>

                                    </label>
                                    <input type="text" name="reference_number" id="reference_number" class="form-control"
                                        value="<?php echo e(old('reference_number')); ?>">
                                </div>
                                <div class="col-lg-6 form-group">
                                    <label for="note" class="control-label">
                                        <?php echo e(trans('app.note')); ?>

                                    </label>
                                    <textarea name="note" id="note" class="form-control" rows="16"><?php echo e(old('note')); ?></textarea>
                                </div>
                                <div class="col-lg-6 form-group">
                                    <label for="photo" class="control-label">
                                    <?php echo e(trans('app.document')); ?>

                                    </label>
                                    <input type="file" name="receipt_photo" id="photo" class="form-control" accept=".jpg, .jpeg, .png">
                                </div>
                                <div class="col-lg-12 text-right">
                                    <button type="submit" class="btn btn-success" onclick="confirmFormSubmission($('#payment-form'))">
                                        <?php echo e($repayLabel); ?>

                                    </button>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label for=""><?php echo e(trans('app.principal')); ?></label>
                                            <input type="text" class="form-control decimal-input" name="principal" id="principal" value="<?php echo e($payoffPrincipal); ?>" readonly>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for=""><?php echo e(trans('app.interest')); ?></label>
                                            <input type="text" class="form-control decimal-input" name="interest" id="interest" value="<?php echo e($payoffInterest); ?>">
                                        </div>
                                     
                                        <div class="col-md-4 form-group">
                                            <label for=""><?php echo e(trans('app.discount')); ?><?php echo e(trans('app.interest')); ?> %</label>
                                            <input type="text" class="form-control decimal-input" name="discount_interest" id="discount_interest" value="0">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for=""><?php echo e(trans('app.interest_after_discount')); ?></label>
                                            <input type="text" class="form-control decimal-input" name="interest_after_discount" id="interest_after_discount" value="<?php echo e($payoffInterest); ?>" readonly>
                                        </div>
                                      
                                       
                                        <div class="col-md-4 form-group">
                                            <label for=""><?php echo e(trans('app.penalty_amount')); ?></label>
                                            <input type="text" class="form-control decimal-input" name="penalty_amount" id="penalty_amount" value="0">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for=""><?php echo e(trans('app.wave')); ?></label>
                                            <input type="text" class="form-control decimal-input" name="wave" id="wave" value="0">
                                        </div>
                                        <div class="col-lg-4 form-group">
                                            <label for="payment_amount" class="control-label">
                                                <?php echo e(trans('app.payment_amount')); ?> ($) <span class="required">*</span>
                                            </label>
                                            <input type="text" name="payment_amount" id="payment_amount" class="form-control decimal-input"
                                                value="<?php echo e($payoffInterest +  $payoffPrincipal ?? old('payment_amount')); ?>" required
                                                <?php echo e(in_array($repayType, [RepayType::PAYOFF, RepayType::ADVANCE_PAY]) ? 'readonly' : ''); ?>>
                                        </div>
                                        <div class="col-lg-4 form-group">
                                            <label for="payment_date" class="control-label">
                                                <?php echo e(trans('app.paid_date')); ?> <span class="required">*</span>
                                            </label>
                                            <input type="text" name="payment_date" id="payment_date" class="form-control"
                                                value="<?php echo e(old('payment_date') ?? date('d-m-Y')); ?>" placeholder="<?php echo e(trans('app.date_placeholder')); ?>" required>
                                        </div>

                                        <div class="col-lg-4 form-group">
                                            <label for="payment_method" class="control-label">
                                                <?php echo e(trans('app.payment_method')); ?> <span class="required">*</span>
                                            </label>
                                            <select name="payment_method" id="payment_method" class="form-control select2 select2-no-search" required>
                                                <?php $__currentLoopData = paymentMethods(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $methodKey => $methodValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($methodKey); ?>" <?php echo e($methodKey == (old('payment_method') ?? 'dp') ? 'selected' : ''); ?>>
                                                        <?php echo e($methodValue); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-4 form-group">
                                            <label for="reference_number" class="control-label">
                                                <?php echo e(trans('app.reference_number')); ?>

                                            </label>
                                            <input type="text" name="reference_number" id="reference_number" class="form-control" value="<?php echo e(old('reference_number')); ?>">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="note" class="control-label">
                                                <?php echo e(trans('app.note')); ?>

                                            </label>
                                            <input type="text" class="form-control" name="note" id="note" value="<?php echo e(old('note')); ?>">
                                        </div>
                                    </div>
                                   
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="photo" class="control-label">
                                        <?php echo e(trans('app.document')); ?>

                                    </label>
                                    <input type="file" name="receipt_photo" id="photo" class="form-control" accept=".jpg, .jpeg, .png">
                                </div>
                                
                                <div class="col-lg-12 text-right">
                                    <button type="submit" class="btn btn-success" onclick="confirmFormSubmission($('#payment-form'))">
                                        <?php echo e($repayLabel); ?>

                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
            </form>
            <div class="card mb-4">
                <div class="card-body">
                    <h5><?php echo e(trans('app.payment_received')); ?></h5>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <?php echo e(trans('app.no_sign')); ?>

                                    </th>
                                    <th>
                                        <?php echo e(trans('app.reference_number')); ?>

                                    </th>
                                    <th>
                                        <?php echo e(trans('app.payment_method')); ?>

                                    </th>
                                    <th>
                                        <?php echo e(trans('app.payment_date')); ?>

                                    </th>
                        
                                    <th>
                                        <?php echo e(trans('app.payment_amount')); ?>

                                    </th>
                                    <th style="width: 20%">
                                        <?php echo e(trans('app.note')); ?>

                                    </th>
                                    <th style="width: 5%">
                                        <?php echo e(trans('app.document')); ?>

                                    </th>
                                    <th>
                                        <?php echo e(trans('app.action')); ?>

                                    </th>
                                </tr>

                            </thead>
                            <tbody>

                                <?php $__currentLoopData = $loan->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key =>  $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <?php echo e($key+1); ?>

                                        </td>
                                        <td>
                                            <?php echo e($item->invoice_number); ?>

                                        </td>
                                        <td>
                                            <?php echo e(paymentMethods($item->payment_method)); ?>

                                        </td>
                                        <td>
                                            <?php echo e(displayDate($item->payment_date)); ?>

                                        </td>
                                        <td>
                                            $ <?php echo e(decimalNumber($item->total)); ?>

                                        </td>
                                        <td>
                                            <?php echo $item->note; ?>

                                        </td>
                                        <td>
                                            <?php if($item->document): ?>
                                                <img src="<?php echo e(asset($item->document)); ?>" class="img-fluid" alt="">
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(isAdmin()): ?>
                                                <a href="#" class="btn btn-sm btn-primary mb-1 btn-modal" title="<?php echo e(trans('app.edit')); ?>" data-href="<?php echo e(route('payments.editPaymentDate',$item)); ?>" data-container=".payment-date-modal">
                                                    <i class="fa fa-edit"></i>
                                                </a>
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
    </main>
    <div class="modal fade payment-date-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery.validate.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-fileinput.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-fileinput-fa-theme.js')); ?>"></script>
    <script src="<?php echo e(asset('/js/init-file-input.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap4-datetimepicker.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/date-time-picker.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery-number.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/number.js')); ?>"></script>
    <script src="<?php echo e(asset('js/mask.js')); ?>"></script>
    <script src="<?php echo e(asset('js/sweetalert.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/repayment.js')); ?>"></script>
    <script>
        var repayment_type = "<?php echo e($repayType); ?>";
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>