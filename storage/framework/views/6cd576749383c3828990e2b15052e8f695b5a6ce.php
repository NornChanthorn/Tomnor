<?php $__env->startSection('title', trans('app.stock_adjustment')); ?>

<?php $__env->startSection('css'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-fileinput.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('plugins/easyAutocomplete/easy-autocomplete.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.stock_adjustment') . ' - ' . $title); ?></h3>
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

    <form method="post" id="adjustment-form" class="validated-form no-auto-submit" action="<?php echo e(route('adjustment.save')); ?>" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>

      <div class="row">
        
        <div class="col-lg-4 form-group">
          <label for="warehouse" class="control-label">
            <?php echo e(trans('app.location')); ?> <span class="required">*</span>
          </label>
          <select name="warehouse" id="warehouse" class="form-control select2" required>
            <option value=""><?php echo e(trans('app.select_option')); ?></option>
            <?php $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($warehouse->id); ?>" <?php echo e(selectedOption($warehouse->id, old('warehouse'))); ?>>
                <?php echo e($warehouse->location); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>

        
        <div class="col-lg-4 form-group">
          <label for="ref_no" class="control-label">
            <?php echo e(trans('app.reference_number')); ?>

          </label>
          <input type="text" name="ref_no" id="ref_no" class="form-control" placeholder="<?php echo e(trans('app.reference_number')); ?>" value="<?php echo e(old('ref_no')); ?>">
        </div>

        
        <div class="col-lg-4 form-group">
          <label for="adjustment_date" class="control-label">
            <?php echo e(trans('app.adjustment_date')); ?> <span class="required">*</span>
          </label>
          <input type="text" name="adjustment_date" id="adjustment_date" class="form-control datepicker" required placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(old('adjustment_date') ?? date('d-m-Y')); ?>">
        </div>

      </div>

      
      <div class="card mb-4">
        <div class="card-header">
          <h5><?php echo e(trans('app.product_table')); ?></h5>
        </div>
        <div class="card-body">
          <div class="row">
            
            <div class="col-lg-4 form-group">
              <label for="product" class="control-label">
                <?php echo e(trans('app.product')); ?> <span class="required">*</span>
              </label>
              <input type="text" placeholder="" class="form-control product" id="product" disabled>
            </div>

            <div class="col-lg-12">
              <div class="table-responsive">
                <table id="product-table" class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      
                      <th><?php echo e(trans('app.code')); ?></th>
                      <th><?php echo e(trans('app.name')); ?></th>
                      <th class="text-center"><?php echo e(trans('app.action')); ?></th>
                      <th class="text-center"><?php echo e(trans('app.quantity')); ?></th>
                      <th class="text-center"><?php echo e(trans('app.delete')); ?></th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        
        <div class="col-lg-4 form-group">
          <label for="note" class="control-label">
            <?php echo e(trans('app.reason')); ?> <span class="required">*</span>
          </label>
          <textarea name="reason" id="reason" class="form-control" required rows="4" style="resize:none;"><?php echo e(old('reason')); ?></textarea>
        </div>
      </div>

      
      <div class="row">
        <div class="col-lg-12 text-right">
          <?php echo $__env->make('partial.button-save', ['onClick' => 'confirmFormSubmission($("#adjustment-form"))'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        </div>
      </div>
    </form>
  </div>
</main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script>
    var stockQtyRetrievalUrl = '<?php echo e(route('adjustment.get_stock_quantity', [':warehouseId', ':productId'])); ?>';
    var NALabel = '<?php echo e(trans('app.n/a')); ?>';
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
  <script src="<?php echo e(asset('js/adjustment.js')); ?>"></script>
  <script src="<?php echo e(asset('plugins/easyAutocomplete/jquery.easy-autocomplete.js')); ?>"></script>
  <script>
    $(document).ready(function() {
      $(".datepicker").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true
      });

      $("#warehouse").on('change', function(e) {
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
          resp.branch = $("#warehouse").val();
          return resp;
        },
        requestDelay: 100,
        list: {
          onLoadEvent: function() {
            var response = $("#product").getItems();
            console.log(response.length);
            if(response.length == 1 && response[0] != undefined) {
              // addProduct($("#product").getItemData(0));
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
            // var value = $("#product").getSelectedItemData();
            // addProduct(value);
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

    function addProduct(productElm) {
      let indexId = productElm.id+productElm.variantion_id;
      let isProductAdded = ($('#product-table tbody').find('tr[data-id="' + indexId + '"]').length > 0);

      if (!isProductAdded) {
        let productRow =
          '<tr data-id="' + indexId + '">' +
            '<input type="hidden" name="products[' + indexId + '][id]" value="' + productElm.id + '">' +
            '<input type="hidden" name="products[' + indexId + '][name]" value="' + productElm.label + '">' +
            '<input type="hidden" name="products[' + indexId + '][code]" value="' + productElm.code + '">' +
            '<input type="hidden" name="products[' + indexId + '][variantion_id]" value="' + productElm.variantion_id + '">' +
            '<td>' + (productElm.code || noneLabel) + '</td>' +
            '<td>' + productElm.label + '</td>' +
            '<td width="15%" class="text-right">' +
              '<select name="products[' + indexId + '][action]" id="action" class="form-control form-control-sm" required>' +
                <?php $__currentLoopData = $stockTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeKey => $typeTitle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  '<option value="<?php echo e($typeKey); ?>" <?php echo e(selectedOption($typeKey, old('action'))); ?>>' +
                    '<?php echo e($typeTitle); ?>' + 
                  '</option>' +
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              '</select>' +
            '</td>' +
            '<td width="15%">' +
              '<input type="text" name="products[' + indexId + '][quantity]" value="1" class="form-control form-control-sm integer-input quantity" min="1" max="'+ productElm.qty_available +'" required>' +
            '</td>' +
            '<td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(this)"><i class="fa fa-trash-o"></i></button></td>' +
          '</tr>';

        $('#product-table tbody').append(productRow);
      }
    }

    function removeProduct(buttonElm) {
      $(buttonElm).parents('#product-table tbody tr').remove();
    }
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>