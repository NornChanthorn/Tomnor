<?php $__env->startSection('title', trans('app.product')); ?>

<?php $__env->startSection('css'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-fileinput.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <?php
    $isFormShowType = ($formType == FormType::SHOW_TYPE);
    $disabledFormType = ($isFormShowType ? 'disabled' : '');
  ?>

  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(trans('app.product') . ' - ' . $title); ?></h3>
      <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      <form id="form-product" method="post" action="<?php echo e(route('product.save', $product)); ?>" enctype="multipart/form-data">
        <input type="hidden" name="form_type" value="<?php echo e($formType); ?>">
        <?php echo csrf_field(); ?>

        
        <div class="row">
          <div class="col-lg-12 text-right">
            <?php if($isFormShowType): ?>
              <?php echo $__env->make('partial/anchor-edit', [
                'href' => route('product.edit', $product->id)
              ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php else: ?>
              <?php echo $__env->make('partial/button-save', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php endif; ?>
          </div>
        </div>

        <div class="row">
          
          <div class="col-lg-6 form-group">
            <label for="name" class="control-label">
              <?php echo e(trans('app.name')); ?> <span class="required">*</span>
            </label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo e(old('name') ?? $product->name); ?>" required <?php echo e($disabledFormType); ?>>
          </div>

          
          <div class="col-lg-3 form-group">
            <label for="product_code" class="control-label">
              <?php echo e(trans('app.product_code')); ?> <span class="required">*</span>
            </label>
            <div class="input-group">
              <input type="text" name="product_code" id="product_code" class="form-control" required placeholder="<?php echo e(trans('app.code') . ' *'); ?>" value="<?php echo e(old('product_code', ($product->code ?? $code))); ?>">
              <button type="button" id="generate-code" class="btn btn-primary"><?php echo e(trans('app.generate')); ?></button>
              <input type="hidden" name="product_sku" id="product_sku" class="form-control ml-2" placeholder="<?php echo e(trans('app.sku')); ?>" value="<?php echo e(old('product_sku', ($product->sku ?? $code))); ?>">
            </div>
          </div>

          
          <div class="col-lg-3 form-group">
            <label for="brand" class="control-label">
              <?php echo e(trans('app.brand')); ?> <span class="required">*</span>
            </label>
            <?php if($isFormShowType): ?>
              <input type="text" class="form-control" value="<?php echo e(brands($product->brand ?? '')); ?>" disabled>
            <?php else: ?>
              <select name="brand" id="brand" class="form-control select2" required style="width:100%;">
                <option value=""><?php echo e(trans('app.select_option')); ?></option>
                <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($brand->id); ?>" <?php echo e(selectedOption($brand->id, old('brand'), $product->brand)); ?>>
                    <?php echo e($brand->value); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            <?php endif; ?>
          </div>

          
          <div class="col-lg-3 form-group">
            <label for="unit" class="control-label">
              <?php echo e(trans('app.unit')); ?> <span class="required">*</span>
            </label>
            <?php if($isFormShowType): ?>
              <input type="text" class="form-control" value="<?php echo e(($product->unit_id ?? '')); ?>" disabled>
            <?php else: ?>
              <select name="unit" id="unit" class="form-control select2" required style="width:100%;">
                <option value=""><?php echo e(trans('app.select_option')); ?></option>
                <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($unit->id); ?>" <?php echo e(selectedOption($unit->id, old('unit'), $product->unit_id)); ?>>
                    <?php echo e($unit->actual_name); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            <?php endif; ?>
          </div>

          
          <div class="col-lg-3 form-group">
            <label for="category" class="control-label">
              <?php echo e(trans('app.product_category')); ?> <span class="required">*</span>
            </label>
            <?php if($isFormShowType): ?>
              <input type="text" class="form-control" value="<?php echo e($product->category->value ?? trans('app.n/a')); ?>" disabled>
            <?php else: ?>
              <select name="category" id="category" class="form-control select2" required style="width:100%;">
                <option value=""><?php echo e(trans('app.select_option')); ?></option>
                <?php $__currentLoopData = $productCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($category->id); ?>" <?php echo e(selectedOption($category->id, old('category'), $product->category_id)); ?>>
                  <?php echo e($category->value); ?>

                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            <?php endif; ?>
          </div>

          
          <div class="col-lg-6 form-group">
            <label for="description" class="control-label">
              <?php echo e(trans('app.description')); ?>

            </label>
            <input type="text" name="description" id="description" class="form-control" value="<?php echo e(old('description') ?? $product->description); ?>" <?php echo e($disabledFormType); ?>>
          </div>
        </div>

        <div class="row">

            
            <div class="col-lg-12 form-group">
                <label for="brand" class="control-label">
                    <?php echo e(trans('app.location')); ?> <span class="required">*</span>
                </label>
                <select name="location_id[]" id="location_id" class="form-control-lg select2" required style="width:100%;" multiple="multiple">
                    <?php if(isset($locations)): ?>
                      <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($formType == FormType::EDIT_TYPE): ?>
                          <option value="<?php echo e($location->id); ?>"
                                  <?php $__currentLoopData = $product->variation_location_detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $varian_location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo e($varian_location->location_id == $location->id ? 'selected' : ''); ?>

                                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              >
                              <?php echo e($location->location); ?>

                          </option>
                        <?php else: ?>
                          <option value="<?php echo e($location->id); ?>" selected>
                              <?php echo e($location->location); ?>

                          </option>
                        <?php endif; ?>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </select>
            </div>

          <div class="col-lg-6">
            <div class="row">
              <div class="col-lg-12 form-group mt-4">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" name="enable_sr_no" <?php echo e(($product->enable_sr_no==1 || old('enable_sr_no',1)==1) ? "checked" : ''); ?> value="1" class="custom-control-input" id="enable_imei">
                  <label class="custom-control-label" for="enable_imei"><?php echo e(__('app.enable_imei')); ?></label>
                </div>
                
              </div>

              <div class="col-lg-6 form-group mt-4">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" name="enable_stock" <?php echo e(($product->enable_stock==1 || old('enable_stock', 1)==1) ? "checked" : ''); ?> value="1" class="custom-control-input" id="enable-stock">
                  <label class="custom-control-label" for="enable-stock"><?php echo e(__('app.enable-stock')); ?>?</label>
                </div>
                <span class="text-muted">Enable stock management at product level</span>
              </div>

              
              <div class="col-lg-6 form-group alert-quantity" style="<?php echo e(($product->enable_stock==1 || old('enable_stock', 1)==1) ? "" : 'display:none;'); ?>">
                <label for="alert_quantity" class="control-label">
                  <?php echo e(trans('app.alert_quantity')); ?> <span class="required">*</span>
                </label>
                <input type="text" name="alert_quantity" id="alert_quantity" class="form-control integer-input" value="<?php echo e(old('alert_quantity', 1) ?? $product->alert_quantity); ?>">
              </div>
            </div>
            <div class="row">
              
              <div class="col-lg-6 form-group">
                <label for="product-type"><?php echo e(__('app.type')); ?></label>
                <?php if($isFormShowType): ?>
                  <input type="text" class="form-control" value="<?php echo e(($product->type ?? '')); ?>" disabled>
                <?php else: ?>
                  <?php if($formType==FormType::EDIT_TYPE && $product->type=='variant'): ?>
                    <input type="hidden" name="product_type" value="<?php echo e($product->type); ?>">
                  <?php endif; ?>
                  <select name="product_type" id="product-type" class="form-control" style="width:100%;" <?php echo e(($formType==FormType::EDIT_TYPE && $product->type=='variant') ? "disabled" : ''); ?>>
                    <option value="variant" <?php echo e(($product->type=='variant' || old('product_type')) ? 'selected' : ''); ?>><?php echo e(__('app.variant')); ?></option>
                    <option value="single" <?php echo e(($product->type=='single' || old('product_type')) ? 'selected' : ''); ?>><?php echo e(__('app.single')); ?></option>
                  </select>
                <?php endif; ?>
              </div>
            </div>
            <div class="row hidden-field">
              
              <div class="col-lg-6 form-group">
                <label for="cost" class="control-label"><?php echo e(trans('app.cost')); ?> ($)</label>
                <input type="text" name="cost" id="cost" class="form-control decimal-input" value="<?php echo e(old('cost') ?? $product->cost); ?>" <?php echo e($disabledFormType); ?>>
              </div>

              
              <div class="col-lg-6 form-group">
                <label for="price" class="control-label"><?php echo e(trans('app.selling_price')); ?> ($) <span class="required">*</span></label>
                <input type="text" name="price" id="price" class="form-control decimal-input" value="<?php echo e(old('price') ?? $product->price); ?>" required <?php echo e($disabledFormType); ?>>
              </div>
            </div>
          </div>

          <div class="col-lg-6 form-group">
            <label for="photo" class="control-label">
              <?php echo e(trans('app.photo')); ?>

            </label>
            <?php if($isFormShowType): ?>
              <div class="text-left">
                <?php if(isset($product->photo)): ?>
                  <img src="<?php echo e(asset($product->photo)); ?>" alt="" width="100%" class="img-responsive">
                <?php else: ?>
                  <?php echo e(trans('app.no_picture')); ?>

                <?php endif; ?>
              </div>
            <?php else: ?>
              <input type="file" name="photo" id="photo" class="form-control" accept=".jpg, .jpeg, .png">
            <?php endif; ?>
          </div>
        </div>

        <hr>
        <div class="row product-variantions" style="<?php echo e(($product->type=='single' || old('product_type')=='single') ? 'display: none;' : ''); ?>">
          <fieldset class="col-lg-12">
            <legend>
              <h5><?php echo e(__('app.product_variantion')); ?></h5>
            </legend>
            <div class="table-responsive">
              <table class="table table-bordered table-variantion">
                <thead>
                  <tr class="table-success">
                    <th><?php echo e(__('app.sku')); ?></th>
                    <th><?php echo e(__('app.value')); ?></th>
                    <th><?php echo e(__('app.purchase_price')); ?> ($)</th>
                    <th><?php echo e(__('app.selling_price')); ?> ($)</th>
                    <th class="text-center">
                      <button type="button" class="btn btn-sm btn-primary add-variant-row" id="" data-url="<?php echo e(url('product/get_variation_value_row')); ?>"><i class="fa fa-plus"></i></button>
                      
                    </th>
                  </tr>
                </thead>
                <tbody class="variant-row">
                  <?php if(!empty($product->variations) && count($product->variations) > 0): ?>
                    <?php $__currentLoopData = $product->variations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <tr class="row_index_<?php echo e($key); ?>" data-index="<?php echo e($key); ?>">
                        <td>
                          <input type="hidden" name="variant[<?php echo e($key); ?>][variantion_id]" value="<?php echo e($variant->id); ?>">
                          <input type="text" name="variant[<?php echo e($key); ?>][sku]" placeholder="" class="form-control form-control-sm variant-sku" id="variant-sku" value="<?php echo e($variant->sub_sku); ?>">
                        </td>
                        <td>
                          <input type="text" name="variant[<?php echo e($key); ?>][value]" placeholder="" class="form-control form-control-sm variant-value" id="variant-value" value="<?php echo e($variant->name); ?>" required>
                        </td>
                        <td>
                          <input type="text" name="variant[<?php echo e($key); ?>][purchase_price]" placeholder="" class="form-control form-control-sm decimal-input variant-purchase-price" id="variant-purchase-price" value="<?php echo e($variant->default_purchase_price); ?>">
                        </td>
                        <td>
                          <input type="text" name="variant[<?php echo e($key); ?>][selling_price]" placeholder="" class="form-control form-control-sm decimal-input variant-selling-price" id="variant-selling-price" value="<?php echo e($variant->default_sell_price); ?>">
                        </td>
                        <td class="text-center">
                          <button type="button" class="btn btn-sm btn-danger remove-variant-row" id=""><i class="fa fa-minus"></i></button>
                          <input type="hidden" class="variant-row-index" value="<?php echo e($key); ?>">
                        </td>
                      </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  <?php else: ?>
                    <tr class="row_index_0" data-index="0">
                      <td>
                        <input type="hidden" name="variant[0][variantion_id]" value="">
                        <input type="text" name="variant[0][sku]" value="" placeholder="" class="form-control form-control-sm variant-sku" id="variant-sku">
                      </td>
                      <td>
                        <input type="text" name="variant[0][value]" value="" placeholder="" class="form-control form-control-sm variant-value" id="variant-value" required>
                      </td>
                      <td>
                        <input type="text" name="variant[0][purchase_price]" value="" placeholder="" class="form-control form-control-sm decimal-input variant-purchase-price" id="variant-purchase-price">
                      </td>
                      <td>
                        <input type="text" name="variant[0][selling_price]" value="" placeholder="" class="form-control form-control-sm decimal-input variant-selling-price" id="variant-selling-price">
                      </td>
                      <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-variant-row" id=""><i class="fa fa-minus"></i></button>
                        <input type="hidden" class="variant-row-index" value="0">
                      </td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </fieldset>
        </div>

        
        <div class="row">
          <div class="col-lg-12 text-right">
            <input type="hidden" name="submit_type" id="submit_type">
            <?php if($isFormShowType): ?>
              <?php echo $__env->make('partial/anchor-edit', [
                'href' => route('product.edit', $product->id)
              ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php else: ?>
              <button id="opening_stock_button" type="submit" value="with_opening_stock" class="btn bg-purple submit_product_form"><?php echo e(__('app.save_n_opening_stock')); ?></button>

              <button type="submit" value="with_adding_another" class="btn bg-maroon submit_product_form"><?php echo e(__('app.save_n_adding_another')); ?></button>

              <?php echo $__env->make('partial/button-save', ['class' => 'submit_product_form'], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            <?php endif; ?>
          </div>
        </div>
      </form>
    </div>
  </main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script>
    var variantions = [];
    // localStorage.setItem('variantions', variantions);
  </script>
  <script src="<?php echo e(asset('js/bootstrap-fileinput.js')); ?>"></script>
  <script src="<?php echo e(asset('js/bootstrap-fileinput-fa-theme.js')); ?>"></script>
  <script src="<?php echo e(asset('js/init-file-input.js')); ?>"></script>
  <script src="<?php echo e(asset('js/jquery-number.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/number.js')); ?>"></script>
  <script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/mask.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
  <script src="<?php echo e(asset('js/jquery.validate.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/product.js')); ?>"></script>

  <script>
    $(document).on('click', '#enable-stock', function() {
      if($(this).is(':checked')) {
        $(".alert-quantity").show();
      }
      else {
        $(".alert-quantity").hide();
      }
    });

    $(document).on('click', '#product-type', function() {
      if($(this).val() == 'single') {
        $(".product-variantions").hide();
        $("#cost").attr('readonly', false);
        $("#price").attr('readonly', false);

        $(".hidden-field").show();
      }
      else {
        $(".product-variantions").show();
        $("#cost").attr('readonly', true);
        $("#price").attr('readonly', true);

        $(".hidden-field").hide();
      }
    });

    $(document).ready(function() {
      const productType = $("#product-type").val();
      if(productType == 'single') {
        $('.product-variantions').hide();
        $(".hidden-field").show();
      }
      else {
        $('.product-variantions').show();
        $(".hidden-field").hide();
      }
    });
    $(document).ready(function() {
      let code = $("#product_code").val();
      if($(".variant-sku").val()=='') {
        $(".variant-sku").val(code+'-'+1);
      }
    });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>