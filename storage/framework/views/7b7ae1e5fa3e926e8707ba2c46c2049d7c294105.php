<?php $__env->startSection('title', trans('app.stock_transfer')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.stock_transfer') . ' - ' . $title); ?></h3>
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <form method="post" id="transfer-form" class="validated-form no-auto-submit" action="<?php echo e(route('transfer.save')); ?>" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>

      <div class="row">
        
        <div class="col-lg-3 form-group">
          <label for="transaction_date" class="control-label">
            <?php echo e(trans('app.transfer_date')); ?> <span class="required">*</span>
          </label>
          <input type="text" name="transaction_date" id="transaction_date" class="form-control datepicker" readonly requiredz placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(old('transaction_date') ?? date('d-m-Y')); ?>">
        </div>

        
        <div class="col-lg-3 form-group">
          <label for="ref_no" class="control-label"><?php echo e(trans('app.invoice_id')); ?></label>
          <input type="text" name="ref_no" id="ref_no" class="form-control" value="<?php echo e(old('ref_no')); ?>">
        </div>

        
        <div class="col-lg-3 form-group">
          <label for="original_warehouse" class="control-label">
            <?php echo e(trans('app.original_location')); ?> <span class="required">*</span>
          </label>
          <select name="original_warehouse" id="original_warehouse" class="form-control select2" required>
            <option value=""><?php echo e(trans('app.select_option')); ?></option>
            <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($warehouse->id); ?>" <?php echo e(selectedOption($warehouse->id, old('original_warehouse'))); ?>>
              <?php echo e($warehouse->location); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        
        <div class="col-lg-3 form-group">
          <label for="target_warehouse" class="control-label">
            <?php echo e(trans('app.target_location')); ?> <span class="required">*</span>
          </label>
          <select name="target_warehouse" id="target_warehouse" class="form-control select2" required>
            <option value=""><?php echo e(trans('app.select_option')); ?></option>
            <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($warehouse->id); ?>" <?php echo e(selectedOption($warehouse->id, old('target_warehouse'))); ?>>
              <?php echo e($warehouse->location); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        
        <div class="col-lg-3 form-group">
          <label for="status" class="control-label">
            <?php echo e(trans('app.transfer_status')); ?> <span class="required">*</span>
          </label>
          <select name="status" id="status" class="form-control select2 select2-no-search" required>
            <?php $__currentLoopData = $transferStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusKey => $statusTitle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($statusKey); ?>" <?php echo e(selectedOption($statusKey, old('status'))); ?>>
              <?php echo e($statusTitle); ?>

            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        
        <div class="col-lg-3 form-group">
          <label for="document" class="control-label">
            <?php echo e(trans('app.document')); ?>

          </label>
          <input type="file" name="document" id="document" class="">
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
              <input type="text" id="product" class="form-control" placeholder="<?php echo e(__('app.enter-product')); ?>" disabled>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="table-responsive">
                <table id="product-table" class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th><?php echo e(trans('app.name')); ?></th>
                      <th><?php echo e(trans('app.code')); ?></th>
                      <th><?php echo e(trans('app.in-stock_quantity')); ?></th>
                      <th><?php echo e(trans('app.transfer_quantity')); ?></th>
                      <th><?php echo e(trans('app.delete')); ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    <?php $__currentLoopData = $transferredProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr data-id="<?php echo e($product['id']); ?>">
                      <input type="hidden" name="products[<?php echo e($product['id']); ?>][id]" value="<?php echo e($product['id']); ?>">
                      <input type="hidden" name="products[<?php echo e($product['id']); ?>][name]" value="<?php echo e($product['name']); ?>">
                      <input type="hidden" name="products[<?php echo e($product['id']); ?>][code]" value="<?php echo e($product['code']); ?>">
                      <input type="hidden" name="products[<?php echo e($product['id']); ?>][stock_qty]" value="<?php echo e($product['stock_qty']); ?>">
                      <td><?php echo e($product['name']); ?></td>
                      <td><?php echo e($product['code'] ?? trans('app.none')); ?></td>
                      <td><?php echo e($product['stock_qty']); ?></td>
                      <td width="25%">
                        <input type="text" name="products[<?php echo e($product['id']); ?>][quantity]" min="1" max="10000"
                        class="form-control integer-input" value="<?php echo e($product['quantity']); ?>" required>
                      </td>
                      <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(this)">
                          <i class="fa fa-trash-o"></i>
                        </button>
                      </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </tbody>
                  <tfoot>
                    <tr class="bg-light">
                      <td colspan="3" align="right"><b><?php echo e(trans('app.shipping_cost')); ?> ($)</b></td>
                      <td colspan="2">
                        <input type="text" name="shipping_charges" id="shipping_charges" class="form-control form-control-sm  decimal-input shipping-charges" min="0" value="0">
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        
        <div class="col-lg-6 form-group">
          <label for="note" class="control-label">
            <?php echo e(trans('app.note')); ?>

          </label>
          <input type="text" name="note" id="note" class="form-control" value="<?php echo e(old('note')); ?>">
        </div>
      </div>

      
      <div class="row">
        <div class="col-lg-12 text-right">
          <?php echo $__env->make('partial.button-save', ['onClick' => 'confirmFormSubmission($("#transfer-form"))'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
      </div>
    </form>
  </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-fileinput.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('plugins/easyAutocomplete/easy-autocomplete.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script>
    var codeLabel = '<?php echo e(trans('app.code')); ?>';
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
  <script src="<?php echo e(asset('js/form-validation.js')); ?>"></script>
  <script src="<?php echo e(asset('plugins/easyAutocomplete/jquery.easy-autocomplete.js')); ?>"></script>
  <script src="<?php echo e(asset('js/transfer.js')); ?>"></script>
  <script>
    $(document).ready(function() {
      $(".datepicker").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true
      });

      $("#original_warehouse").on('change', function(e) {
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
            type: 'transfer'
          }
        },
        preparePostData: function(resp) {
          resp.query = $("#product").val();
          resp.branch = $("#original_warehouse").val();
          resp.type = 'transfer';
          return resp;
        },
        requestDelay: 100,
        list: {
          onLoadEvent: function() {
            var response = $("#product").getItems();
            if(response.length == 1 && response[0] != undefined) {
              var value = $("#product").getItemData(0);
              if(value.qty_available > 0){
                addProduct(value);
              }else{
                swal(value.label, "<?php echo e(trans('message.product_out_of_stock_content')); ?>", 'info');
              }

              $("#product").val('');
            }
          },
          onClickEvent: function() {
            var value = $("#product").getSelectedItemData();
            if(value.qty_available > 0){
              addProduct(value);
            }else{
              swal(value.label, "<?php echo e(trans('message.product_out_of_stock_content')); ?>", 'info');
            }

            $("#product").val('').focus();
          }
        }
      });
    });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>