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
    <h3 class="page-heading"><?php echo e(trans('app.loan') . ' - ' . $title); ?></h3>
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <form id="loan-form" method="post" class="no-auto-submit" action="<?php echo e(route('loan.save', $loan)); ?>">
      <?php echo csrf_field(); ?>

      <input type="hidden" name="form_type" value="<?php echo e($formType); ?>">
      <?php if(isset($loan->id)): ?>
      <input type="hidden" name="id" value="<?php echo e($loan->id); ?>">
      <?php endif; ?>

      
      <div class="row">
        <fieldset class="col-lg-12">
          <legend>
            <h5><?php echo e(trans('app.loan_information')); ?></h5>
          </legend>
          <div class="row">
            <?php if(isAdmin() || empty(auth()->user()->staff)): ?>
            
            <div class="col-lg-4 form-group">
              <label for="branch" class="control-label">
                <?php echo e(trans('app.branch')); ?> <?php echo $requiredFormType; ?>

              </label>
              <?php if($isFormShowType): ?>
              <input type="text" class="form-control" value="<?php echo e($loan->branch->location); ?>" disabled>
              <?php else: ?>
              <select name="branch" id="branch" class="form-control select2" required <?php echo e($disabledFormType); ?>>
                <option value=""><?php echo e(trans('app.select_option')); ?></option>
                <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($branch->id); ?>" <?php echo e(selectedOption($branch->id, old('branch'), $loan->branch_id)); ?>>
                  <?php echo e($branch->location); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php endif; ?>
            </div>
            <?php endif; ?>

            
            <div class="col-lg-8 form-group">
              <label for="account_number_append" class="control-label">
                <?php echo e(trans('app.account_number')); ?> <span class="required">*</span>
              </label>
              <div class="input-group">
                
                <input type="text" name="account_number" id="account_number" class="form-control"
                  value="<?php echo e($loan->account_number ?? ''); ?>" placeholder="<?php echo e(trans('app.loan_code')); ?>" disabled>
                
                
                <input type="hidden" name="wing_code" id="wing_code" value="N/A">
                
                <input type="text" name="client_code" id="client_code" class="form-control"
                  value="<?php echo e(old('client_code') ?? $loan->client_code); ?>" required
                  placeholder="<?php echo e(trans('app.reference_code') . ' *'); ?>" <?php echo e($disabledFormType); ?>>
              </div>
            </div>

            <?php if(isAdmin() || empty(auth()->user()->staff)): ?>
            
            <div class="col-lg-4 form-group">
              <label for="agent" class="control-label">
                <?php echo e(trans('app.agent')); ?> <?php echo $requiredFormType; ?>

              </label>
              <?php if($isFormShowType): ?>
              <input type="text" class="form-control" value="<?php echo e($loan->staff->name); ?>" disabled>
              <?php else: ?>
              <select name="agent" id="agent" class="form-control select2" required <?php echo e($disabledFormType); ?>>
                <option value=""><?php echo e(trans('app.select_option')); ?></option>
                <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($agent->user_id); ?>" <?php echo e(selectedOption($agent->user_id, old('agent'), $loan->staff_id)); ?>>
                  <?php echo e($agent->name); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php endif; ?>
            </div>
            <?php else: ?>
            <input type="hidden" name="branch" value="<?php echo e(auth()->user()->staff->branch_id); ?>">
            <?php endif; ?>

            
            <div class="col-lg-4 form-group">
              <label for="client" class="control-label">
                <?php echo e(trans('app.client')); ?> <?php echo $requiredFormType; ?>

              </label>
              <?php if($isFormShowType): ?>
              <input type="text" class="form-control" value="<?php echo e($loan->client->name); ?>" disabled>
              <?php else: ?>
              <select name="client" id="client" class="form-control select2" required <?php echo e($disabledFormType); ?>>
                <option value=""><?php echo e(trans('app.select_option')); ?></option>
                <?php if($formType == FormType::EDIT_TYPE): ?>
                <option value="<?php echo e($loan->client_id); ?>" selected><?php echo e($loan->client->name); ?></option>
                <?php endif; ?>
                
              </select>
              <?php endif; ?>
            </div>
            <div class="col-lg-12 form-group mt-4">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="allow_multi_loan"
                  <?php echo e(($loan->allow_multi_loan==1 || old('allow_multi_loan')==1) ? "checked" : ''); ?> value="1"
                  class="custom-control-input" id="allow_multi_loan">
                <label class="custom-control-label" for="allow_multi_loan"><?php echo e(__('app.allow_multi_loan')); ?></label>
              </div>
            </div>
          </div>

          
          <div class="card mb-4">
            <div class="card-header">
              <h5><?php echo e(trans('app.product_table')); ?></h5>
            </div>
            <div class="card-body">
              <div class="row">
                
                <div class="col-lg-4 form-group">
                  <label for="product" class="control-label"><?php echo e(trans('app.product')); ?></label>
                  <?php if($isFormShowType): ?>
                  <input type="text" class="form-control" id="product" placeholder="<?php echo e(__('app.enter-product')); ?>"
                    disabled>
                  <?php else: ?>
                  <input type="text" class="form-control" id="product" placeholder="<?php echo e(__('app.enter-product')); ?>"
                    <?php echo e(old('branch', !empty(auth()->user()->staff) ? auth()->user()->staff->branch_id : null)=='' ? 'disabled' : ''); ?>>
                  <?php endif; ?>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12">
                  <div class="table-responsive">
                    <table id="sale-product-table" class="table table-bordered table-hover">
                      <thead>
                        <tr>
                          <th><?php echo e(trans('app.name')); ?></th>
                          <th><?php echo e(trans('app.code')); ?></th>
                          <th><?php echo e(trans('app.in-stock_quantity')); ?></th>
                          <th><?php echo e(trans('app.sale_quantity')); ?></th>
                          <th><?php echo e(trans('app.unit_price')); ?></th>
                          <th><?php echo e(trans('app.sub_total')); ?></th>
                          <th><?php echo e(trans('app.delete')); ?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $__currentLoopData = $loan->productDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                        $indexId = $item->product_id.$item->variantion_id;
                        ?>

                        <tr data-id="<?php echo e($indexId); ?>">
                          <input type="hidden" name="products[<?php echo e($indexId); ?>][transaction_sell_lines_id]"
                            value="<?php echo e($item->id); ?>">
                          <input type="hidden" name="products[<?php echo e($indexId); ?>][id]" value="<?php echo e(@$item->product_id); ?>">
                          <input type="hidden" name="products[<?php echo e($indexId); ?>][name]" value="<?php echo e(@$item->product->name); ?>">
                          <input type="hidden" name="products[<?php echo e($indexId); ?>][code]" value="<?php echo e(@$item->product->code); ?>">
                          <input type="hidden" name="products[<?php echo e($indexId); ?>][variantion_id]"
                            value="<?php echo e($item->variantion_id); ?>">
                          <input type="hidden" name="products[<?php echo e($indexId); ?>][enable_stock]"
                            value="<?php echo e(@$item->product->enable_stock); ?>">
                          <td>
                            <?php echo e(@$item->product->name); ?><?php echo e(@$item->variantion->name!='DUMMY' ? ' - '.$item->variantion->name : ''); ?>

                            <?php if($loan->transaction_id): ?>
                              <a class="btn btn-sm btn-success" href="<?php echo e(route('product.ime-create',[
                                'transaction_id'=>$loan->transaction_id,
                                'location_id'=>$loan->branch_id,
                                'product_id'=>$item->product_id,
                                'variantion_id'=>$item->variantion_id,
                                'qty'=> $item->qty,
                                'purchase_sell_id'=>$loan->id,
                                'type'=>'loan'
                                ])); ?>"><?php echo e(trans('app.product_ime')); ?></a>
                            <?php endif; ?>
                          </td>
                          <td><?php echo e(@$item->product->code ?? trans('app.none')); ?></td>
                          <?php
                              $pro_id = $item->product_id;
                              $va_id = $item->variantion_id;
                              $lo_id = $loan->branch_id;
                              $qty_available= App\Models\VariantionLocationDetails::where('location_id',$lo_id)->where('variantion_id',$va_id)->where('product_id', $pro_id)->first()->qty_available;
                          ?>
                            <td><?php echo e(decimalNumber($qty_available)); ?></td>
                          <td width="15%">
                            <input type="text" name="products[<?php echo e($indexId); ?>][quantity]"
                              class="form-control form-control-sm integer-input quantity" min="1" required
                              value="<?php echo e($item->qty); ?>" max="<?php echo e($qty_available); ?>" readonly>
                          </td>
                          <td width="15%">
                            <input type="text" name="products[<?php echo e($indexId); ?>][price]"
                              class="form-control form-control-sm decimal-input unit_price" min="1" required
                              value="<?php echo e($item->unit_price); ?>" readonly>
                          </td>
                          <td width="15%">
                            <input type="text" name="products[<?php echo e($indexId); ?>][sub_total]"
                              class="form-control form-control-sm decimal-input sub_total" min="1" required
                              value="<?php echo e($item->qty * $item->unit_price); ?>" readonly>
                          </td>
                          <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="rmProduct(this)">
                              <i class="fa fa-trash-o"></i>
                            </button>
                          </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="5" align="right"><b><?php echo e(trans('app.grand_total')); ?></b></td>
                          <td colspan="2"><span class="shown_total_price"></span></td>
                          <input type="hidden" name="total_price" class="total_price" value="0">
                        </tr>
                        
                        <tr>
                          <td colspan="5" align="right"><b><?php echo e(trans('app.balance')); ?></b></td>
                          <td colspan="2"><span class="shown_balance_amount"></span></td>
                          <input type="hidden" name="balance_amount" class="balance_amount" value="0">
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>

                
                <div class="col-lg-4 form-group">
                  <label for="product_price" class="control-label">
                    <?php echo e(trans('app.product_price')); ?> ($)
                  </label>
                  <input type="text" name="product_price" id="product_price" class="form-control currency-input"
                    value="<?php echo e(old('product_price') ?? $loan->product_price); ?>" <?php echo e($disabledFormType); ?>>
                </div>

                
                

                
                <div class="col-lg-4 form-group">
                  <label for="note" class="control-label">
                    <?php echo e(trans('app.icloud')); ?>

                  </label>
                  <input type="text" name="note" id="note" class="form-control" value="<?php echo e(old('note') ?? $loan->note); ?>"
                    <?php echo e($disabledFormType); ?>>
                  
                </div>
              </div>
            </div>
          </div>
        </fieldset>
      </div>
      <br>

      
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
                <?php echo e($disabledFormType); ?>>
                <option value="<?php echo e(PaymentScheduleType::EQUAL_PAYMENT); ?>">
                  <?php echo e(trans('app.equal_payment')); ?>

                </option>
                
              </select>
            </div>

            
            <div class="col-lg-4 form-group">
              <label for="loan_amount" class="control-label">
                <?php echo e(trans('app.loan_amount')); ?> ($) <?php echo $requiredFormType; ?>

              </label>
              <input type="text" name="loan_amount" id="loan_amount" class="form-control decimal-input" required
                value="<?php echo e(old('loan_amount') ?? $loan->loan_amount); ?>" readonly>
            </div>

            
            <div class="col-lg-4 form-group">
              <label for="depreciation_amount" class="control-label">
                <?php echo e(trans('app.depreciation_amount')); ?> ($) <?php echo $requiredFormType; ?>

              </label>
              <input type="text" name="depreciation_amount" id="depreciation_amount" class="form-control decimal-input"
                value="<?php echo e(old('depreciation_amount') ?? $loan->depreciation_amount); ?>" required <?php echo e($disabledFormType); ?>>
            </div>
          </div>
          <div class="row">
            
            <div class="col-lg-4 form-group">
              <label for="payment_method" class="control-label">
                <?php echo e(trans('app.payment_method')); ?> <span class="required">*</span>
              </label>
              <select name="payment_method" id="payment_method" class="form-control select2 select2-no-search" required <?php echo e($disabledFormType); ?>>
                <?php $__currentLoopData = paymentMethods(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $methodKey => $methodValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($methodKey); ?>" <?php echo e($methodKey == $loan->payment_method ? 'selected' : ''); ?> <?php echo e($loan->payment_method ?? ($methodKey='dp'?'selected':'')); ?> >
                    <?php echo e($methodValue); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            
            <div class="col-lg-4 form-group">
              <label for="down_payment_amount" class="control-label">
                <?php echo e(trans('app.down_payment_amount')); ?> ($)
              </label>
              <input type="text" name="down_payment_amount" id="down_payment_amount" class="form-control decimal-input"
                value="<?php echo e(old('down_payment_amount') ?? $loan->down_payment_amount); ?>" readonly <?php echo e($disabledFormType); ?>>
            </div>

            
            <div class="col-lg-4 form-group">
              <label for="interest_rate" class="control-label">
                <span id="rate_text"><?php echo e(trans('app.interest_rate')); ?></span> (%)
                <span id="rate_sign" class="required"></span>
              </label>
              <input type="text" name="interest_rate" id="interest_rate" class="form-control decimal-input"
                value="<?php echo e(old('interest_rate') ?? $loan->interest_rate); ?>" required min="0" <?php echo e($disabledFormType); ?>>
            </div>

            
            <div class="col-lg-4 form-group">
              <label for="installment" class="control-label">
                <?php echo e(trans('app.installment')); ?> <?php echo $requiredFormType; ?>

              </label>
              <input type="text" name="installment" id="installment" class="form-control integer-input"
                value="<?php echo e(old('installment') ?? $loan->installment); ?>" required <?php echo e($disabledFormType); ?>>
            </div>
          </div>
          <div class="row">
            
            <div class="col-lg-4 form-group">
              <label for="payment_per_month" class="control-label">
                <?php echo e(trans('app.number_payment_per_month')); ?> <?php echo $requiredFormType; ?>

              </label>
              <select name="payment_per_month" id="payment_per_month" class="form-control" required disabled>
                <option value="1"><?php echo e(trans('app.once')); ?></option>
                <option value="2"
                  <?php echo e($loan->payment_per_month == 2 || old('payment_per_month') == 2 ? 'selected' : ''); ?>>
                  <?php echo e(trans('app.twice')); ?>

                </option>
              </select>
              <input type="hidden" name="payment_per_month" value="1">
            </div>

            
            <div class="col-lg-4 form-group">
              <label for="loan_start_date" class="control-label">
                <?php echo e(trans('app.loan_start_date')); ?> <?php echo $requiredFormType; ?>

              </label>
              <input type="text" name="loan_start_date" id="loan_start_date" class="form-control date-picker"
                placeholder="<?php echo e(trans('app.date_placeholder')); ?>" required <?php echo e($disabledFormType); ?>

                value="<?php echo e(old('loan_start_date', displayDate($loan->loan_start_date ?? date('d-m-Y')))); ?>">
            </div>

            
            <div class="col-lg-4 form-group">
              <label for="first_payment_date" class="control-label">
                <?php echo e(trans('app.first_payment_date')); ?>

              </label>
              <input type="text" name="first_payment_date" id="first_payment_date" class="form-control date-picker"
                placeholder="<?php echo e(trans('app.date_placeholder')); ?>" <?php echo e($disabledFormType); ?>

                value="<?php echo e(old('first_payment_date') ?? displayDate($loan->first_payment_date ?? oneMonthIncrement(date('Y-m-d')))); ?>">
            </div>
          </div>

          <div class="row" <?php echo e($isFormShowType ? 'style=display:none;' : ''); ?>>
            
            <div class="col-lg-12 text-center">
              <h6 id="error-msg" class="text-danger"></h6>
            </div>

            
            <div class="col-lg-12 text-center">
              <button type="button" id="calculate-payment" class="btn btn-info">
                <?php echo e(trans('app.calculate_payment_schedule')); ?>

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
      <br>

      
      <div class="row">
        <div class="col-lg-12 text-center">
          <?php if($isFormShowType): ?>
          
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
          <?php if($loan->status == LoanStatus::ACTIVE): ?>
            
            <a class="btn btn-success" href="<?php echo e(route('sale.show', $loan->transaction->id)); ?>">
                <i class="fa fa-eye"></i> <?php echo e(__('app.view_sell_detail')); ?>

            </a>

          <?php endif; ?>
          <?php if(Auth::user()->can('loan.delete') && !isPaidLoan($loan->id)): ?>
          
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

          

          
          <?php if(Auth::user()->can('loan.print') && in_array($loan->status, [LoanStatus::ACTIVE, LoanStatus::PAID])): ?>
          <a class="btn btn-success mb-1" target="_blank" href="<?php echo e(route('loan.print_contract', $loan)); ?>">
            <i class="fa fa-print pr-1"></i> <?php echo e(trans('app.print_contract')); ?>

          </a>
          <?php endif; ?>
          <?php else: ?>
          <?php echo $__env->make('partial/button-save', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
          <?php endif; ?>
        </div>
      </div>
    </form>
  </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    var count = "<?php echo e($loan->count); ?>";
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

    // When change branch
    var agentSelectLabel = emptyOptionElm;
    var agentRetrievalUrl = '<?php echo e(route('staff.get_agents', ':branchId')); ?>';
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
<script src="<?php echo e(asset('js/loan.js')); ?>"></script>
<script src="<?php echo e(asset('plugins/easyAutocomplete/jquery.easy-autocomplete.js')); ?>"></script>
<script>
  $('#print').click(function(){
    var divToPrint=document.getElementById("print-table");
    newWin  = window.open('', '', 'height=800,width=800');
    newWin.document.write('<html><head><title><?php echo e(trans('app.calculate_payment_schedule')); ?></title><link rel="stylesheet" href="<?php echo e(asset('css/main.css')); ?>"> <style>@media  print {body { width: 21cm; height: 29.7cm;}} </style></head><body><div class="container"><h5 class="mt-4 text-center"><?php echo e(trans('app.calculate_payment_schedule')); ?></h5>',divToPrint.outerHTML,'<button class="ml-3 btn btn-success" id="print" onclick="window.print();"><?php echo e(trans('app.print')); ?></button>','</div></body></html>');

    // newWin.print();
    // newWin.close();
  });
  $(document).ready(function() {
      $(".currency-input").on('keypress keyup blur', function(event) {
        // event.preventDefault();

        var key = window.event ? event.keyCode : event.which;
        if(event.keyCode === 8 || event.keyCode == 46) {
          return true;
        } else if(key < 48 || key > 57) {
          return false;
        } else {
          return true;
        }
      });

      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });

      $("#branch").change(function(e) {
        e.preventDefault();
        if($(this).val() != '') {
          $("#product").attr('disabled', false);
        }
        else {
          $("#product").attr('disabled', true);
        }
      });

      // $("#branch").on('change', function(e) {
      //   e.preventDefault();
      //   let branchId = $(this).find(':selected').data('code');

      //   $("#product").attr('disabled', ($(this).val()=='' ? true : false));
      // });

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

      $("#product").easyAutocomplete({
        url: function(phrase) {
          return "<?php echo e(route('product.product-variantion')); ?>";
        },
        getValue: function(element) {
          return element.label;
        },
        ajaxSettings: {
          dataType: 'json',
          method: "GET",
          data: {
            dataType: "json"
          }
        },
        preparePostData: function(resp) {
          resp.query = $("#product").val();
          resp.branch = $("#branch").val();
          resp.type = 'sale';
          return resp;
        },
        requestDelay: 100,
        list: {
          onLoadEvent: function() {
            var response = $("#product").getItems();
            if(response.length == 1 && response[0] != undefined) {
              // addProduct($("#product").getItemData(0));
              var value = $("#product").getItemData(0);
              <?php if($setting->enable_over_sale == 0): ?>
                if(value.qty_available > 0){
                  addProduct(value);
                }else{
                  swal(value.label, "<?php echo e(trans('message.product_out_of_stock_content')); ?>", 'info');
                }
              <?php else: ?>
                addProduct(value);
              <?php endif; ?>
              $("#product").val('');
            }
          },
          onClickEvent: function() {
            var value = $("#product").getSelectedItemData();
            <?php if($setting->enable_over_sale == 0): ?>
              if(value.qty_available > 0){
                addProduct(value);
              }else{
                swal(value.label, "<?php echo e(trans('message.product_out_of_stock_content')); ?>", 'info');
              }
            <?php else: ?>
              addProduct(value);
            <?php endif; ?>

            $("#product").val('').focus();
          }
        }
      });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>