<?php $__env->startSection('title', trans('app.loan_cash')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
    <div class="card">
        <div class="card-header">
            <h4 class="title">
                <?php echo e($title); ?>

            </h4>
        </div>
        <div class="card-body">
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <form id="loan-form" method="post" class="no-auto-submit" action="<?php echo e(route('loan-cash.save', $loan)); ?>">
              <?php echo csrf_field(); ?>
        
              <input type="hidden" name="form_type" value="<?php echo e($formType); ?>">
              <?php if(isset($loan->id)): ?>
                <input type="hidden" name="id" value="<?php echo e($loan->id); ?>">
              <?php endif; ?>
        
              
              <div class="row">
                <fieldset class="col-lg-12">
                    <div class="row">
                        <?php if(isAdmin() || empty(auth()->user()->staff)): ?>
                            
                            <div class="col-lg-4 form-group">
                                <label for="branch" class="control-label">
                                    <?php echo e(trans('app.branch')); ?>

                                    <span class="required">*</span>
                                </label>
                                <select name="branch_id" id="branch" class="form-control select2" required >
                                    <option value=""><?php echo e(trans('app.select_option')); ?></option>
                                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($branch->id); ?>" <?php echo e(selectedOption($branch->id, old('branch'), $loan->branch_id)); ?>>
                                                <?php echo e($branch->location); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        <?php endif; ?>
            
                        
                        <div class="col-lg-8 form-group">
                            <label for="account_number_append" class="control-label">
                                <?php echo e(trans('app.account_number')); ?> <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                
                                <input type="text" name="account_number" id="account_number" class="form-control"
                                value="<?php echo e($loan->account_number ?? nextLoanAccNum()); ?>" placeholder="<?php echo e(trans('app.loan_code')); ?>" disabled>
                                
                                <input type="text" name="client_code" id="client_code" class="form-control"
                                value="<?php echo e(old('client_code') ?? $loan->client_code); ?>" required
                                placeholder="<?php echo e(trans('app.reference_code') . ' *'); ?>" >
                            </div>
                        </div>
            
                        <?php if(isAdmin() || empty(auth()->user()->staff)): ?>
                            
                            <div class="col-lg-4 form-group">
                                <label for="agent" class="control-label">
                                    <?php echo e(trans('app.agent')); ?> <span class="required">*</span>
                                </label>
                                <select name="agent" id="agent" class="form-control select2" required >
                                    <option value=""><?php echo e(trans('app.select_option')); ?></option>
                                    <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($agent->user_id); ?>" <?php echo e(selectedOption($agent->id, old('agent'), $loan->staff_id)); ?>>
                                            <?php echo e($agent->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="branch_id" value="<?php echo e(auth()->user()->staff->branch_id); ?>">
                        <?php endif; ?>
            
                        
                        <div class="col-lg-4 form-group">
                            <label for="client" class="control-label">
                                <?php echo e(trans('app.client')); ?> <span class="required">*</span>
                            </label>
                            <select name="client_id" id="client" class="form-control select2" required>
                                <option value=""><?php echo e(trans('app.select_option')); ?></option>
                                <?php if($formType == FormType::EDIT_TYPE): ?>
                                    <option value="<?php echo e($loan->client_id); ?>" selected><?php echo e($loan->client->name); ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </fieldset>
              </div>
              <br>
        
              
              <div class="row">
                <fieldset class="col-lg-12">
                    <h5><?php echo e(trans('app.loan_information')); ?></h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                
                                <div class="col-lg-6 form-group">
                                    <label for="schedule_type" class="control-label">
                                        <?php echo e(trans('app.payment_schedule_type')); ?> <span class="required">*</span>
                                    </label>
                                    <select name="schedule_type" id="schedule_type" class="form-control select2 select2-no-search" required>
                                        <option value="<?php echo e(PaymentScheduleType::EQUAL_PAYMENT); ?>">
                                            <?php echo e(trans('app.equal_payment')); ?>

                                        </option>
                                    </select>
                                </div>
                    
                                
                                <div class="col-lg-6 form-group">
                                    <label for="loan_amount" class="control-label">
                                        <?php echo e(trans('app.loan_amount')); ?> ($) <span class="required">*</span>
                                    </label>
                                    <input type="text" name="loan_amount" id="loan_amount" class="form-control decimal-input" required
                                        value="<?php echo e(old('loan_amount',$loan->loan_amount) ?? 0); ?>" required>
                                </div>
                            
                                
                                <div class="col-lg-6 form-group">
                                    <label for="interest_rate" class="control-label">
                                        <span id="rate_text"><?php echo e(trans('app.interest_rate')); ?></span> (%)
                                        <span id="rate_sign" class="required"></span>
                                    </label>
                                    <input type="text" name="interest_rate" id="interest_rate" class="form-control decimal-input"
                                        value="<?php echo e(old('interest_rate',$loan->interest_rate) ?? 0); ?>" required min="0">
                                </div>
                    
                                
                                <div class="col-lg-6 form-group">
                                    <label for="installment" class="control-label">
                                        <?php echo e(trans('app.installment')); ?> <span class="required">*</span>
                                    </label>
                                    <input type="text" class="form-control integer-input" name="installment" value="<?php echo e(old('installment',$loan->installment) ?? 1); ?>" min="1">
                                    
                                </div>
                                
                                <div class="col-lg-6 form-group">
                                    <label for="frequency" class="control-label">
                                        <?php echo e(trans('app.frequency')); ?> <span class="required">*</span>
                                    </label>
                                    <select name="frequency" id="frequency" class="form-control" required>
                                        <option value=""><?php echo e(__('app.select_option')); ?></option>
                                        <?php $__currentLoopData = frequencies(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fkey => $fval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($fkey); ?>" <?php echo e(selectedOption( $fkey , old('frequency'), $loan->frequency)); ?>><?php echo e(numKhmer(no_f($fval))); ?> <?php echo e(__('app.day')); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                    
                                
                                <div class="col-lg-6 form-group">
                                    <label for="loan_start_date" class="control-label">
                                        <?php echo e(trans('app.loan_start_date')); ?> <span class="required">*</span>
                                    </label>
                                    <input type="text" name="loan_start_date" id="loan_start_date" class="form-control date-picker"
                                        placeholder="<?php echo e(trans('app.date_placeholder')); ?>" required 
                                        value="<?php echo e(old('loan_start_date') ?? displayDate($loan->loan_start_date ??  date('d-m-Y'))); ?>">
                                </div>
                    
                                
                                <div class="col-lg-6 form-group">
                                    <label for="first_payment_date" class="control-label">
                                        <?php echo e(trans('app.first_payment_date')); ?>

                                    </label>
                                    <input type="text" name="first_payment_date" id="first_payment_date" class="form-control"
                                        placeholder="<?php echo e(trans('app.date_placeholder')); ?>" 
                                        value="<?php echo e(old('first_payment_date') ?? displayDate($loan->first_payment_date ?? oneMonthIncrement(date('Y-m-d')))); ?>" readonly>
                                    <input type="hidden" name="first_payment_date" id="first_payment_date" class="form-control date-picker"
                                        value="<?php echo e(old('first_payment_date') ?? displayDate($loan->first_payment_date ?? oneMonthIncrement(date('Y-m-d')))); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="" class="control-label"><?php echo e(__('app.note')); ?></label>
                            <textarea class="form-control" name="note" id="" cols="30" rows="15">
                                <?php echo $loan->note; ?>

                            </textarea>
                        </div>
                    </div>
                   
                </fieldset>
              </div>
        
              
              <div class="row">
                <div class="col-lg-12">
                  <?php echo $__env->make('partial/button-save', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </div>
              </div>
            </form>
        </div>
    </div>
</main>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<script src="<?php echo e(asset('plugins/tinymce/tinymce.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/tinymce.js')); ?>"></script>
<script src="<?php echo e(asset('js/jquery.validate.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
<script src="<?php echo e(asset('js/bootstrap4-datetimepicker.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/date-time-picker.js')); ?>"></script>
<script src="<?php echo e(asset('js/jquery-number.min.js')); ?>"></script>
<script src="<?php echo e(asset('js/number.js')); ?>"></script>

<script>
    var agentSelectLabel = emptyOptionElm;
    var agentRetrievalUrl = '<?php echo e(route('staff.get_agents', ':branchId')); ?>';
    $(document).ready(function() {
        $("#client").select2({
            ajax: {
            url: "<?php echo e(route('client.list')); ?>",
            dataType: 'json',
            data: function(params) {
                return {
                search: params.term,
                type: 'public',
                }
            },
            processResults: function(data) {
                return {
                results: data
                }
            }
            }
        });
        if('<?php echo e($loan->client_id); ?>'!=""){
            var $value = '<?php echo e($loan->client_id); ?>';
            var $account_number = $('#account_number').val();
            var  $tmp = $account_number.split('/');

            $('#account_number').val($tmp[0] + '/' +("000000" + $value).slice(-6));
        }
        
        $("#client").on('change', function(){
            $value = $(this).val();
            $account_number = $('#account_number').val();
            $tmp = $account_number.split('/');

            $('#account_number').val($tmp[0] + '/' +("000000" + $value).slice(-6));
            //$('#account_number').val($tmp[0] + '/' + zeroPad($value, 6));
            // console.log('>value', $value);
        });
        $("#frequency, #loan_start_date").on('change', function(){
            var day  = $('#frequency').val();
            var now = $('#loan_start_date').val();
            var newdate= formatDate(now,day);
            $('#first_payment_date').val(newdate);
        });
    });
</script>
<script src="<?php echo e(asset('js/agent-retrieval.js')); ?>"></script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>