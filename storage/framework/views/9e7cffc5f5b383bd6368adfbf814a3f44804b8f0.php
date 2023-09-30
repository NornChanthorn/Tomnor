<?php $__env->startSection('title', trans('app.loan')); ?>

<?php $__env->startSection('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/bootstrap4-datetimepicker.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('plugins/easyAutocomplete/easy-autocomplete.min.css')); ?>">
<style>
  .tabulator-print-header, tabulator-print-footer{
    text-align:center;
  }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
$isFormShowType = ($formType == FormType::SHOW_TYPE);
$disabledFormType = ($isFormShowType ? 'disabled' : '');
$requiredFormType = ($formType != FormType::SHOW_TYPE ? '<span class="required">*</span>' : '');
?>

<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.calculate_loan')); ?></h3>
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <form id="calculator-form" action="<?php echo e(route('loan.get_payment_schedule')); ?>">
      <?php echo csrf_field(); ?>
      
      <div class="row">
          <fieldset class="col-lg-12">
            <legend>
              <h5><?php echo e(trans('app.payment_information')); ?></h5>
            </legend>
            <div class="row">
              <div class="col-lg-4 form-group">
                  <label for="schedule_type" class="control-label">
                    <?php echo e(trans('app.payment_schedule_type')); ?> <?php echo $requiredFormType; ?>

                  </label>
                  <select name="schedule_type" id="schedule_type" class="form-control select2 select2-no-search" required
                    <?php echo e($disabledFormType); ?> disabled>
                    <?php $__currentLoopData = paymentScheduleTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $paymentScheduleType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($key); ?>">
                        <?php echo e($paymentScheduleType); ?>

                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                  </select>
              </div>

              <div class="col-lg-4 form-group">
                  <label for="loan_amount" class="control-label">
                    <?php echo e(trans('app.loan_amount')); ?> ($) <?php echo $requiredFormType; ?>

                  </label>
                  <input type="text" name="loan_amount" id="loan_amount" class="form-control decimal-input" required
                    value="<?php echo e(old('loan_amount') ?? 0); ?>">
              </div>
              
              <div class="col-lg-4 form-group">
                <label for="depreciation_amount" class="control-label">
                  <?php echo e(trans('app.depreciation_amount')); ?> ($)
                </label>
                <input type="text" name="depreciation_amount" id="depreciation_amount" class="form-control decimal-input"
                  value="<?php echo e(old('depreciation_amount') ?? 0); ?>" required >
              </div>
              
              <div class="col-lg-4 form-group">
                <label for="down_payment_amount" class="control-label">
                  <?php echo e(trans('app.down_payment_amount') ?? 0); ?> ($)
                </label>
                <input type="text" name="down_payment_amount" id="down_payment_amount" class="form-control decimal-input"
                  value="<?php echo e(old('down_payment_amount') ?? 0); ?>" readonly disabled>
              </div>

              
              <div class="col-lg-4 form-group">
                <label for="interest_rate" class="control-label">
                  <span id="rate_text"><?php echo e(trans('app.interest_rate')); ?></span> (%)
                  <span id="rate_sign" class="required"></span>
                </label>
                <input type="text" name="interest_rate" id="interest_rate" class="form-control decimal-input"
                  value="<?php echo e(old('interest_rate') ?? 0); ?>" required min="0" >
              </div>

              
              <div class="col-lg-4 form-group">
                <label for="installment" class="control-label">
                  <?php echo e(trans('app.installment')); ?>

                </label>
                <input type="text" name="installment" id="installment" class="form-control integer-input"
                  value="<?php echo e(old('installment')); ?>" required >
              </div>
              <div class="col-lg-4 form-group">
                  <label for="payment_per_month" class="control-label">
                    <?php echo e(trans('app.number_payment_per_month')); ?> <?php echo $requiredFormType; ?>

                  </label>
                  <select name="payment_per_month" id="payment_per_month" class="form-control" required disabled>
                    <option value="1"><?php echo e(trans('app.once')); ?></option>
                    <option value="2"
                      <?php echo e(old('payment_per_month') == 2 ? 'selected' : ''); ?>>
                      <?php echo e(trans('app.twice')); ?>

                    </option>
                  </select>
                  <input type="hidden" name="payment_per_month" id="payment_per_month"  value="1">
              </div>
              
              <div class="col-lg-4 form-group">
                <label for="loan_start_date" class="control-label">
                  <?php echo e(trans('app.loan_start_date')); ?>

                </label>
                <input type="text" name="loan_start_date" id="loan_start_date" class="form-control date-picker"
                  placeholder="<?php echo e(trans('app.date_placeholder')); ?>" required
                  value="<?php echo e(old('loan_start_date') ?? date('d-m-Y')); ?>">
              </div>

              
              <div class="col-lg-4 form-group">
                <label for="first_payment_date" class="control-label">
                  <?php echo e(trans('app.first_payment_date')); ?>

                </label>
                <input type="text" name="first_payment_date" id="first_payment_date" class="form-control date-picker"
                  placeholder="<?php echo e(trans('app.date_placeholder')); ?>"
                  value="<?php echo e(old('first_payment_date') ?? date('d-m-Y',strtotime("+30 days"))); ?>">
              </div>
            </div>

            <div class="row" <?php echo e($isFormShowType ? 'style=display:none;' : ''); ?>>
              
              <div class="col-lg-12 text-center">
                <h6 id="error-msg" class="text-danger"></h6>
              </div>

              
              <div class="col-lg-12 text-center">
                <button type="button calculate_payment_schedule" type="submit" id="calculate-payment" class="btn btn-info">
                  <?php echo e(trans('app.')); ?>

                </button>
              </div>
            </div>
            <br>

            
            <div class="row">
              <div class="col-lg-12 table-responsive" id="print-table">
                <table style="display: none;" id="schedule-table" class="table table-bordered table-hover table-striped">
                </table>
              </div>
              <div class="col-lg-12">
                <button type="button" style="display: none;" id="print" class="btn btn-info">
                  <?php echo e(trans('app.print')); ?>

                </button>
              </div>

            </div>
          </fieldset>
        </div>
      </form>
  </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    var formType = '<?php echo e($formType); ?>';
    var codeLabel = '<?php echo e(trans('app.code')); ?>';
    var noneLabel = '<?php echo e(trans('app.none')); ?>';
    var formShowType = '<?php echo e(FormType::SHOW_TYPE); ?>';
    var equalPaymentSchedule = '<?php echo e(PaymentScheduleType::EQUAL_PAYMENT); ?>';
    var flatInterestSchedule = '<?php echo e(PaymentScheduleType::FLAT_INTEREST); ?>';
    var declineInterestSchedule = '<?php echo e(PaymentScheduleType::DECLINE_INTEREST); ?>';
    var scheduleRetrievalUrl = '<?php echo e(route('loan.get_payment_schedule')); ?>';

    var loanRateLabel = '<?php echo e(trans('app.loan_rate')); ?>';
    var interestRateLabel = '<?php echo e(trans('app.interest_rate')); ?>';
    var noLabel = '<?php echo e(trans('app.no_sign')); ?>';
    var paymentDateLabel = '<?php echo e(trans('app.payment_date')); ?>';
    var paymentAmountLabel = '<?php echo e(trans('app.payment_amount')); ?>';
    var totalLabel = '<?php echo e(trans('app.total')); ?>';
    var principalLabel = '<?php echo e(trans('app.principal')); ?>';
    var interestLabel = '<?php echo e(trans('app.interest')); ?>';
    var outstandingLabel = '<?php echo e(trans('app.outstanding')); ?>';
</script>
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
<script src="<?php echo e(asset('js/agent-retrieval.js')); ?>"></script>
<script src="<?php echo e(asset('js/calculator.js')); ?>"></script>
<script>
  $('#print').click(function(){
    var divToPrint=document.getElementById("print-table");
    newWin  = window.open('', '', 'height=800,width=800');
    newWin.document.write('<html><head><title><?php echo e(trans('app.calculate_payment_schedule')); ?></title><link rel="stylesheet" href="<?php echo e(asset('css/main.css')); ?>"> <style>@media  print {body { width: 21cm; height: 29.7cm;}} </style></head><body><div class="container"><h5 class="mt-4 text-center"><?php echo e(trans('app.calculate_payment_schedule')); ?></h5>',divToPrint.outerHTML,'<button class="ml-3 btn btn-success" id="print" onclick="window.print();"><?php echo e(trans('app.print')); ?></button>','</div></body></html>');

    // newWin.print();
    // newWin.close();
  });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>