<?php $__env->startSection('title', trans('app.purchase')); ?>

<?php $__env->startSection('content'); ?>
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(trans('app.purchase') . ' - ' . $title); ?></h3>
      <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

      <form method="post" id="purchase-form" class="validated-form no-auto-submit" action="<?php echo e(route('purchase.save')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="purchase_id" value="<?php echo e($purchase->id); ?>">
        <input type="hidden" name="form_type" value="<?php echo e($formType); ?>">

        <div class="row">
          <?php if(isAdmin() || empty(auth()->user()->staff)): ?>
            
            <div class="col-lg-4 form-group">
              <label for="warehouse" class="control-label">
                <?php echo e(trans('app.location')); ?> <span class="required">*</span>
              </label>
              <select name="warehouse" id="warehouse" class="form-control select2" required>
                <option value=""><?php echo e(trans('app.select_option')); ?></option>
                <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($warehouse->id); ?>" <?php echo e(selectedOption($warehouse->id, old('warehouse', $purchase->location_id))); ?>>
                    <?php echo e($warehouse->location); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
          <?php else: ?>
            <input type="hidden" name="warehouse" value="<?php echo e(auth()->user()->staff->branch_id); ?>">
          <?php endif; ?>

          
          <div class="col-lg-4 form-group">
            <label for="supplier" class="control-label">
              <?php echo e(trans('app.supplier')); ?> <span class="required">*</span>
            </label>
            <div class="input-group">
              <select name="supplier" id="supplier" class="form-control select2" required>
                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($supplier->id); ?>" <?php echo e(selectedOption($supplier->id, (old('supplier', $purchase->contact_id)))); ?> <?php if($supplier->is_default): ?> selected <?php endif; ?>>
                    <?php echo e($supplier->supplier_business_name); ?>, <?php echo e($supplier->name); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <div class="input-group-append">
                <a href="#" class="btn btn-block btn-primary" id="add-supplier" data-href="<?php echo e(route('contact.create', ['type'=>$contact->type])); ?>" data-container=".contact-modal"><i class="fa fa-plus"></i></a>
              </div>
            </div>
          </div>

          
          <div class="col-lg-4 form-group">
            <label for="invoice_id" class="control-label">
              <?php echo e(trans('app.invoice_id')); ?>

            </label>
            <input type="text" name="invoice_id" id="invoice_id" class="form-control" value="<?php echo e(old('invoice_id', $purchase->ref_no)); ?>">
          </div>

          
          <div class="col-lg-4 form-group">
            <label for="status" class="control-label">
              <?php echo e(trans('app.purchase_status')); ?> <span class="required">*</span>
            </label>
            <select name="status" id="status" class="form-control select2 select2-no-search" required>
              <?php $__currentLoopData = $purchaseStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusKey => $statusTitle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($statusKey); ?>" <?php echo e(selectedOption($statusKey, old('status', $purchase->status))); ?>>
                  <?php echo e($statusTitle); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>

          
          <div class="col-lg-4 form-group">
            <label for="purchase_date" class="control-label">
              <?php echo e(trans('app.purchase_date')); ?> <span class="required">*</span>
            </label>
            <input type="text" name="purchase_date" id="purchase_date" class="form-control datepicker" required placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(old('purchase_date', $purchase->transaction_date ? date('d-m-Y', strtotime($purchase->transaction_date)) : date('d-m-Y'))); ?>">
          </div>

          
          <div class="col-lg-4 form-group mt-4">
            <label for="document" class="control-label">
              <?php echo e(trans('app.document')); ?>

            </label>
            <input type="file" name="document" id="document" class="">
          </div>
        </div>

        
        <div class="card mb-4 mt-3">
          <div class="card-header">
            <h5 class="mb-0"><?php echo e(trans('app.product_table')); ?></h5>
          </div>
          <div class="card-body">
            <div class="row">
              
              <div class="col-lg-4 form-group">
                <label for="product" class="control-label"><?php echo e(trans('app.product')); ?></label>
                <input type="text" class="form-control" id="product" placeholder="<?php echo e(__('app.enter-product')); ?>">
              </div>
            </div>

            <div class="table-responsive">
              <table id="product-table" class="table table-bordered table-hover">
                <thead>
                  <tr class="bg-success text-white">
                    
                    <th><?php echo e(trans('app.name')); ?></th>
                    <th><?php echo e(trans('app.code')); ?></th>
                    <th class="text-center"><?php echo e(trans('app.quantity')); ?></th>
                    <th class="text-right"><?php echo e(trans('app.cost')); ?></th>
                    <th class="text-right"><?php echo e(trans('app.sub_total')); ?></th>
                    <th class="text-center"><?php echo e(trans('app.delete')); ?></th>
                  </tr>
                </thead>
                <tbody>
                  
                  <?php if($purchase->purchase_lines): ?>
                    <?php $__currentLoopData = $purchase->purchase_lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $purchase_line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php
                        $indexId = $purchase_line->product_id.$purchase_line->variantion_id;
                      ?>
                      <tr data-id="<?php echo e($indexId); ?>">
                        <input type="hidden" name="products[<?php echo e($indexId); ?>][purchase_line_id]" value="<?php echo e($purchase_line->id); ?>">
                        <input type="hidden" name="products[<?php echo e($indexId); ?>][id]" value="<?php echo e($purchase_line->product_id); ?>">
                        <input type="hidden" name="products[<?php echo e($indexId); ?>][name]" value="<?php echo e($purchase_line->product->name); ?>">
                        <input type="hidden" name="products[<?php echo e($indexId); ?>][code]" value="<?php echo e($purchase_line->product->code); ?>">
                        <input type="hidden" name="products[<?php echo e($indexId); ?>][variantion_id]" value="<?php echo e($purchase_line->variantion_id); ?>">
                        
                        <td><?php echo e($purchase_line->product->name.($purchase_line->variations->name != "DUMMY" ? '-'.$purchase_line->variations->name : '')); ?></td>
                        <td><?php echo e($purchase_line->variations->name != "DUMMY" ? $purchase_line->variations->sub_sku: $purchase_line->product->code ?? trans('app.none')); ?></td>
                        <td width="15%" class="text-center">
                          <input type="text" name="products[<?php echo e($indexId); ?>][quantity]" min="1" max="10000" class="form-control form-control-sm integer-input quantity" value="<?php echo e($purchase_line->quantity); ?>" required>
                        </td>
                        <td width="15%" class="text-right">
                          <input type="text" name="products[<?php echo e($indexId); ?>][purchase_price]" class="form-control form-control-sm decimal-input purchase-price" value="<?php echo e(decimalNumber($purchase_line->purchase_price, true)); ?>">
                        </td>
                        <td width="15%" class="text-right">
                          <input type="text" name="sub_total" class="form-control form-control-sm sub-total" value="<?php echo e(decimalNumber($purchase_line->purchase_price * $purchase_line->quantity, true)); ?>" readonly placeholder="">
                        </td>
                        <td class="text-center">
                          <button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(this)"><i class="fa fa-trash-o"></i></button>
                        </td>
                      </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  <?php endif; ?>
                </tbody>
                <tfoot>
                  <tr class="bg-light">
                    <td colspan="4" align="right"><b><?php echo e(trans('app.grand_total')); ?></b></td>
                    <td colspan="2">
                      <span class="shown_total_price">0.00</span>
                      <input type="hidden" name="total_price" class="total_price" value="<?php echo e($purchase->total_before_tax ?? 0); ?>">
                    </td>
                  </tr>
                  <tr class="bg-light">
                    <td colspan="4" align="right"><b><?php echo e(trans('app.discount')); ?> ($)</b></td>
                    <td colspan="2">
                      <input type="text" name="discount" class="form-control form-control-sm decimal-input discount" min="0" required value="<?php echo e($purchase->discount_amount ?? 0); ?>">
                    </td>
                  </tr>
                  <tr class="bg-light">
                    <td colspan="4" align="right"><b><?php echo e(trans('app.shipping_cost')); ?> ($)</b></td>
                    <td colspan="2">
                      <input type="text" name="shipping_cost" id="shipping_cost" class="form-control form-control-sm  decimal-input shipping-cost" min="0" value="<?php echo e($purchase->shipping_charges ?? 0); ?>">
                    </td>
                  </tr>
                  <tr class="bg-light">
                    <td colspan="4" align="right"><b><?php echo e(trans('app.balance')); ?></b></td>
                    <td colspan="2" align="left">
                      <span class="shown_balance_amount">0.00</span>
                      <input type="hidden" name="balance_amount" class="balance_amount" value="0">
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        <?php if($formType == FormType::CREATE_TYPE): ?>
          <hr>
          <div class="row">
            <fieldset class="col-lg-12">
              <legend><h5><?php echo e(trans('app.payment_information')); ?></h5></legend>
              <div class="row">
                
                <div class="col-lg-4 form-group">
                  <label for="total_cost" class="control-label">
                    <?php echo e(trans('app.paid_amount')); ?> ($) <span class="required">*</span>
                  </label>
                  <input type="text" name="total_cost" id="total_payable_amount" class="form-control decimal-input" min="0" value="<?php echo e(old('total_cost', (@$purchase->payment_lines[0]->amount ?? 0))); ?>" onclick="$(this).select();">
                </div>

                
                <div class="col-lg-4 form-group">
                  <label for="payment_method" class="control-label">
                    <?php echo e(trans('app.payment_method')); ?> <span class="required">*</span>
                  </label>
                  <select name="payment_method" id="payment_method" class="form-control select2 select2-no-search" required>
                    <?php $__currentLoopData = paymentMethods(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $methodKey => $methodValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($methodKey); ?>" <?php echo e($methodKey == old('payment_method') ? 'selected' : ''); ?>>
                        <?php echo e($methodValue); ?>

                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                </div>
              </div>

              
              <div class="form-group">
                <label for="note" class="control-label"><?php echo e(trans('app.note')); ?></label>
                <textarea name="note" id="note" rows="2" class="form-control" style="resize:none;"><?php echo e(old('note', $purchase->additional_notes)); ?></textarea>
              </div>
            </fieldset>
          </div>
        <?php endif; ?>

        
        <div class="row">
          <div class="col-lg-12 text-right">
            <?php echo $__env->make('partial/button-save', ['onClick' => 'confirmFormSubmission($("#purchase-form"))'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
          </div>
        </div>
      </form>
    </div>
  </main>

  <div class="modal fade contact_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel">
    <?php echo $__env->make('contact.form', ['quick_add' => true], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-fileinput.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('plugins/easyAutocomplete/easy-autocomplete.min.css')); ?>">
  <style>
    .input-group .select2 { width: 85%!important; }
    .input-group .input-group-append { width: 15%; }
  </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script>
    var noneLabel = '<?php echo e(trans('app.none')); ?>';
  </script>
  <script src="<?php echo e(asset('js/bootstrap-fileinput.js')); ?>"></script>
  <script src="<?php echo e(asset('js/bootstrap-fileinput-fa-theme.js')); ?>"></script>
  <script src="<?php echo e(asset('js/init-file-input.js')); ?>"></script>
  <script src="<?php echo e(asset('js/jquery-number.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/number.js')); ?>"></script>
  <script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/mask.js')); ?>"></script>
  <script src="<?php echo e(asset('js/date-time-picker.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
  <script src="<?php echo e(asset('js/jquery.validate.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/form-validation.js')); ?>"></script>
  <script src="<?php echo e(asset('plugins/easyAutocomplete/jquery.easy-autocomplete.js')); ?>"></script>
  <script src="<?php echo e(asset('js/purchase.js')); ?>"></script>
  <script>
    $(document).ready(function() {

      calculateTotal();

      $(".datepicker").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true
      });

      // if($("#warehouse").val() == '' || $("#status").val() == 'received') $("#product").attr('disabled', true);
      if($("#warehouse").val() == '') $("#product").attr('disabled', true);
      $("#warehouse").change(function(e) {
        e.preventDefault();
        if($(this).val() != '') {
          $("#product").attr('disabled', false);
        }
        else {
          $("#product").attr('disabled', true);
        }
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
            dataType: "json",
            type: 'purchase'
          }
        },
        preparePostData: function(resp) {
          resp.query = $("#product").val();
          resp.branch = $("#warehouse").val();
          resp.type = 'sale';
          return resp;
        },
        requestDelay: 100,
        list: {
          onLoadEvent: function() {
            var response = $("#product").getItems();
            console.log(response.length);
            if(response.length == 1 && response[0] != undefined) {
              addProduct($("#product").getItemData(0));

              $("#product").val('');
            }
          },
          onClickEvent: function() {
            var value = $("#product").getSelectedItemData();
            addProduct(value);

            $("#product").val('').focus();
          }
        }
      });

      $('form#quick_add_contact').submit(function(e) {
        e.preventDefault();
      }).validate({
        rules: {
          contact_id: {
            remote: {
              url: '<?php echo e(route('contact.check-contact')); ?>',
              type: 'post',
              data: {
                contact_id: function() {
                  return $('#contact_id').val();
                },
                hidden_id: function() {
                  if ($('#hidden_id').length) {
                    return $('#hidden_id').val();
                  }
                  else {
                    return '';
                  }
                },
              },
            },
          },
        },
        messages: {
          contact_id: {
            remote: "<?php echo e(trans('message.supplier_already_exist')); ?>",
          },
        },
        submitHandler: function(form) {
          $(form).find('button[type="submit"]').attr('disabled', true);
          var data = $(form).serialize();
          $.ajax({
            method: 'POST',
            url: $(form).attr('action'),
            dataType: 'json',
            data: data,
            success: function(result) {
              if (result.success == true) {
                $('select#supplier').append(
                  $('<option>', { value: result.data.id, text: [result.data.supplier_business_name, result.data.name ] })
                );
                $('select#supplier').val(result.data.id).trigger('change');
                $('div.contact_modal').modal('hide');
                // toastr.success(result.msg);
              }
            },
          });
        },
      });

      $('.contact_modal').on('hidden.bs.modal', function() {
        $('form#quick_add_contact').find('button[type="submit"]').removeAttr('disabled');
        $('form#quick_add_contact')[0].reset();
      });
    });

    $(document).on('click', '#add-supplier', function() {
      $('#supplier').select2('close');
      var name = $(this).data('name');
      $('.contact_modal').find('input#name').val(name);
      $('.contact_modal').find('select#contact_type').val('<?php echo e($contact->type); ?>').closest('div.contact_type_div').addClass('hide');
      $('.contact_modal').modal('show');
    });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>