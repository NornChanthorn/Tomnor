<?php $__env->startSection('title', trans('app.sale')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.sale') . ' - ' . $title); ?></h3>
    <?php echo $__env->make('partial.flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <form method="post" id="sale-form" class="validated-form no-auto-submit" action="<?php echo e(route('sale.save', SaleType::SALE)); ?>" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="sale_id" value="<?php echo e($sale->id); ?>">
      <input type="hidden" name="form_type" value="<?php echo e($formType); ?>">

      <div class="row">
        
        <div class="col-lg-4 form-group">
          <label for="client" class="control-label">
            <?php echo e(trans('app.client')); ?> <span class="required">*</span>
          </label>
          <div class="input-group">
            <select name="client" id="client" class="form-control select2" required>
              <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($client->id); ?>" <?php echo e(selectedOption($client->id, old('client', $sale->contact_id))); ?> <?php if($client->is_default): ?> selected <?php endif; ?>><?php echo e($client->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <div class="input-group-append">
              <a href="#" class="btn btn-block btn-primary" id="add-client" data-href="<?php echo e(route('contact.create', ['type'=>'customer'])); ?>" data-container=".contact-modal"><i class="fa fa-plus"></i></a>
            </div>
          </div>
        </div>

        <div class="col-lg-4 form-group client-addition hide">
          <label for="client_name" class="control-label"><?php echo e(trans('app.client_name')); ?></label>
          <input type="text" name="client_name" id="client_name" class="form-control" placeholder="<?php echo e(trans('app.client_name')); ?>" value="<?php echo e(old('client_name')); ?>">
        </div>

        <div class="col-lg-4 form-group client-addition hide">
          <label for="phone_number" class="control-label">
            <?php echo e(trans('app.phone_number')); ?> <span class="required">*</span>
          </label>
          <input type="text" name="phone_number" id="phone_number" class="form-control" required placeholder="<?php echo e(trans('app.phone_number')); ?>" value="<?php echo e(old('phone_number')); ?>">
        </div>

        <?php if(isAdmin() || empty(auth()->user()->staff)): ?>
          
          <div class="col-lg-4 form-group">
            <label for="branch" class="control-label">
              <?php echo e(trans('app.branch')); ?> <span class="required">*</span>
            </label>
            <select name="branch" id="branch" class="form-control select2" required>
              <option value=""><?php echo e(trans('app.select_option')); ?></option>
              <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($branch->id); ?>" <?php echo e(selectedOption($branch->id, old('branch', $sale->location_id))); ?>><?php echo e($branch->location); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>

          
          <div class="col-lg-4 form-group">
            <label for="agent" class="control-label">
              <?php echo e(trans('app.agent')); ?> <span class="required">*</span>
            </label>
            <select name="agent" id="agent" class="form-control select2" required>
              <option value=""><?php echo e(trans('app.select_option')); ?></option>
              <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($agent->user_id); ?>" <?php echo e(selectedOption($agent->user_id, old('agent', $sale->created_by))); ?>><?php echo e($agent->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
        <?php else: ?>
          <input type="hidden" name="branch" value="<?php echo e(auth()->user()->staff->branch_id); ?>">
        <?php endif; ?>

        
        <div class="col-lg-4 form-group">
          <label for="sale_date" class="control-label">
            <?php echo e(trans('app.sale_date')); ?> <span class="required">*</span>
          </label>
          <input type="text" name="sale_date" id="sale_date" class="form-control datepicker" required placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(old('sale_date', displayDate($sale->sale_date)) ?? date('d-m-Y')); ?>">
        </div>

        <div class="col-lg-4 form-group">
          <label for="sale_date" class="control-label">
            <?php echo e(trans('app.sale_status')); ?> <span class="required">*</span>
          </label>
          <select name="status" id="status" class="form-control" required>
            <option value=""><?php echo e(trans('app.select_option')); ?></option>
            <?php $__currentLoopData = saleStatuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $_sta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($k); ?>" <?php echo e(selectedOption($k, old('status', $sale->status))); ?>><?php echo e($_sta); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
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
              <input type="text" class="form-control" id="product" placeholder="<?php echo e(__('app.enter-product')); ?>" <?php echo e(old('branch', !empty(auth()->user()->staff) ? auth()->user()->staff->branch_id : null)=='' ? 'disabled' : ''); ?>>
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
                    <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                      $indexId = $item->product_id.$item->variantion_id;
                    ?>

                    <tr data-id="<?php echo e($indexId); ?>">
                      <input type="hidden" name="products[<?php echo e($indexId); ?>][transaction_sell_lines_id]" value="<?php echo e($item->id); ?>">
                      <input type="hidden" name="products[<?php echo e($indexId); ?>][id]" value="<?php echo e($item->product_id); ?>">
                      <input type="hidden" name="products[<?php echo e($indexId); ?>][name]" value="<?php echo e($item->product->name); ?>">
                      <input type="hidden" name="products[<?php echo e($indexId); ?>][code]" value="<?php echo e($item->product->code); ?>">
                      <input type="hidden" name="products[<?php echo e($indexId); ?>][variantion_id]" value="<?php echo e($item->variantion_id); ?>">
                      <input type="hidden" name="products[<?php echo e($indexId); ?>][enable_stock]" value="<?php echo e($item->product->enable_stock); ?>">
                      <td>
                        <?php echo e($item->product->name); ?><?php echo e($item->variations->name!='DUMMY' ? ' - '.$item->variations->name : ''); ?>

                      </td>
                      <td><?php echo e($item->product->code ?? trans('app.none')); ?></td>
                      <?php
                          $pro_id = $item->product_id;
                          $va_id = $item->variantion_id;
                          $lo_id = $sale->location_id;
                          $qty_available= App\Models\VariantionLocationDetails::where('location_id',$lo_id)->where('variantion_id',$va_id)->where('product_id', $pro_id)->first()->qty_available;
                      ?>
                      
                      <td><?php echo e(decimalNumber($qty_available)); ?></td>

                      <td width="15%">
                        <input type="text" name="products[<?php echo e($indexId); ?>][quantity]" class="form-control form-control-sm integer-input quantity" min="1" max="<?php echo e($qty_available + $item->quantity); ?>" required value="<?php echo e($item->quantity); ?>">
                      </td>

                      <td width="15%">
                        <input type="text" name="products[<?php echo e($indexId); ?>][price]" class="form-control form-control-sm decimal-input unit_price" min="1" required value="<?php echo e($item->unit_price); ?>">
                      </td>
                      <td width="15%">
                        <input type="text" name="products[<?php echo e($indexId); ?>][sub_total]" class="form-control form-control-sm decimal-input sub_total" min="1"  required value="<?php echo e($item->quantity * $item->unit_price); ?>" readonly>
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
                      <td colspan="5" align="right"><b><?php echo e(trans('app.discount')); ?> ($)</b></td>
                      <td colspan="2"><input type="text" name="discount" class="form-control form-control-sm decimal-input discount" placeholder="0.00"></td>
                    </tr>
                    <tr>
                      <td colspan="5" align="right"><b><?php echo e(trans('app.other_service')); ?> ($)</b></td>
                      <td colspan="2"><input type="text" name="other_service" class="form-control form-control-sm decimal-input other_service" placeholder="0.00"></td>
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
          </div>
        </div>
      </div>

      <hr>
      <div class="row">
        <fieldset class="col-lg-12">
          <legend><h5><?php echo e(trans('app.payment_information')); ?></h5></legend>
          <div class="row">
            
            <div class="col-lg-4 form-group">
              <label for="payment_method" class="control-label">
                <?php echo e(trans('app.paid_amount')); ?> <span class="required">*</span>
              </label>
              <input type="text" name="paid_amount" class="form-control decimal-input paid_amount" required value="0" onclick="$(this).select();">
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
            
            <div class="col-lg-12 form-group">
              <label for="note" class="control-label"><?php echo e(trans('app.note')); ?></label>
              <textarea name="note" id="note" rows="3" class="form-control" style="resize:none;"><?php echo e(old('note')); ?></textarea>
            </div>
          </div>
        </fieldset>
      </div>

      
      <div class="row">
        <div class="col-lg-12 text-right">
         <?php echo $__env->make('partial.button-save', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
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
    var codeLabel = '<?php echo e(trans('app.code')); ?>';
    var noneLabel = '<?php echo e(trans('app.none')); ?>';

    // When change branch
    var agentSelectLabel = emptyOptionElm;
    var agentRetrievalUrl = '<?php echo e(route('staff.get_agents', ':branchId')); ?>';
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
  <script src="<?php echo e(asset('js/agent-retrieval.js')); ?>"></script>
  <script src="<?php echo e(asset('js/sale.js')); ?>"></script>
  <script src="<?php echo e(asset('plugins/easyAutocomplete/jquery.easy-autocomplete.js')); ?>"></script>
  <script>
    $(document).ready(function() {
      $(".datepicker").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
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

      $('form#quick_add_contact').submit(function(e) {
        e.preventDefault();
      }).validate({
        rules: {
          contact_id: {
            remote: {
              url: '<?php echo e(route('contact.check-contact')); ?>',
              type: 'post',
              data: {
                type: function() {
                  return '<?php echo e($contact->type); ?>';
                },
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
            remote: "<?php echo e(trans('message.customer_already_exist')); ?>",
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
                $('select#client').append(
                  $('<option>', { value: result.data.id, text: result.data.name })
                );
                $('select#client').val(result.data.id).trigger('change');
                $('div.contact_modal').modal('hide');
                // toastr.success(result.msg);
              }
            },
          });
        },
      });
    });

    $(document).on('click', '#add-client', function() {
      $('#client').select2('close');
      var name = $(this).data('name');
      $('.contact_modal').find('input#name').val(name);
      $('.contact_modal').find('select#contact_type').val('customer').closest('div.contact_type_div').addClass('hide');
      $('.contact_modal').modal('show');
    });

    $('.contact_modal').on('hidden.bs.modal', function() {
      $('form#quick_add_contact').find('button[type="submit"]').removeAttr('disabled');
      $('form#quick_add_contact')[0].reset();
    });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>