@extends('layouts/backend')

@section('title', trans('app.product'))

@section('css')
  <link rel="stylesheet" href="{{ asset('css/bootstrap-fileinput.css') }}">
@endsection

@section('content')
  @php
    $isFormShowType = ($formType == FormType::SHOW_TYPE);
    $disabledFormType = ($isFormShowType ? 'disabled' : '');
  @endphp

  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.product') . ' - ' . $title }}</h3>
      @include('partial/flash-message')
      <form id="form-product" method="post" action="{{ route('product.save', $product) }}" enctype="multipart/form-data">
        <input type="hidden" name="form_type" value="{{ $formType }}">
        @csrf

        {{-- Button save or edit --}}
        <div class="row">
          <div class="col-lg-12 text-right">
            @if ($isFormShowType)
              @include('partial/anchor-edit', [
                'href' => route('product.edit', $product->id)
              ])
            @else
              @include('partial/button-save')
            @endif
          </div>
        </div>

        <div class="row">
          {{-- Name --}}
          <div class="col-lg-6 form-group">
            <label for="name" class="control-label">
              {{ trans('app.name') }} <span class="required">*</span>
            </label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') ?? $product->name }}" required {{ $disabledFormType }}>
          </div>

          {{-- Product code/SKU --}}
          <div class="col-lg-3 form-group">
            <label for="product_code" class="control-label">
              {{ trans('app.product_code') }} <span class="required">*</span>
            </label>
            <div class="input-group">
              <input type="text" name="product_code" id="product_code" class="form-control" required placeholder="{{ trans('app.code') . ' *' }}" value="{{ old('product_code', ($product->code ?? $code)) }}">
              <button type="button" id="generate-code" class="btn btn-primary">{{ trans('app.generate') }}</button>
              <input type="hidden" name="product_sku" id="product_sku" class="form-control ml-2" placeholder="{{ trans('app.sku') }}" value="{{ old('product_sku', ($product->sku ?? $code)) }}">
            </div>
          </div>

          {{-- Brand --}}
          <div class="col-lg-3 form-group">
            <label for="brand" class="control-label">
              {{ trans('app.brand') }} <span class="required">*</span>
            </label>
            @if ($isFormShowType)
              <input type="text" class="form-control" value="{{ brands($product->brand ?? '') }}" disabled>
            @else
              <select name="brand" id="brand" class="form-control select2" required style="width:100%;">
                <option value="">{{ trans('app.select_option') }}</option>
                @foreach ($brands as $brand)
                  <option value="{{ $brand->id }}" {{ selectedOption($brand->id, old('brand'), $product->brand) }}>
                    {{ $brand->value }}
                  </option>
                @endforeach
              </select>
            @endif
          </div>

          {{-- unit --}}
          <div class="col-lg-3 form-group">
            <label for="unit" class="control-label">
              {{ trans('app.unit') }} <span class="required">*</span>
            </label>
            @if ($isFormShowType)
              <input type="text" class="form-control" value="{{ ($product->unit_id ?? '') }}" disabled>
            @else
              <select name="unit" id="unit" class="form-control select2" required style="width:100%;">
                <option value="">{{ trans('app.select_option') }}</option>
                @foreach ($units as $unit)
                  <option value="{{ $unit->id }}" {{ selectedOption($unit->id, old('unit'), $product->unit_id) }}>
                    {{ $unit->actual_name }}
                  </option>
                @endforeach
              </select>
            @endif
          </div>

          {{-- Product category --}}
          <div class="col-lg-3 form-group">
            <label for="category" class="control-label">
              {{ trans('app.product_category') }} <span class="required">*</span>
            </label>
            @if ($isFormShowType)
              <input type="text" class="form-control" value="{{ $product->category->value ?? trans('app.n/a') }}" disabled>
            @else
              <select name="category" id="category" class="form-control select2" required style="width:100%;">
                <option value="">{{ trans('app.select_option') }}</option>
                @foreach ($productCategories as $category)
                <option value="{{ $category->id }}" {{ selectedOption($category->id, old('category'), $product->category_id) }}>
                  {{ $category->value }}
                </option>
                @endforeach
              </select>
            @endif
          </div>

          {{-- Description --}}
          <div class="col-lg-6 form-group">
            <label for="description" class="control-label">
              {{ trans('app.description') }}
            </label>
            <input type="text" name="description" id="description" class="form-control" value="{{ old('description') ?? $product->description }}" {{ $disabledFormType }}>
          </div>
        </div>

        <div class="row">

            {{-- Brand --}}
            <div class="col-lg-12 form-group">
                <label for="brand" class="control-label">
                    {{ trans('app.location') }} <span class="required">*</span>
                </label>
                <select name="location_id[]" id="location_id" class="form-control-lg select2" required style="width:100%;" multiple="multiple">
                    @if(isset($locations))
                      @foreach ($locations as $location)
                        @if($formType == FormType::EDIT_TYPE)
                          <option value="{{ $location->id }}"
                                  @foreach($product->variation_location_detail as $varian_location)
                                    {{ $varian_location->location_id == $location->id ? 'selected' : '' }}
                                  @endforeach
                              >
                              {{ $location->location }}
                          </option>
                        @else
                          <option value="{{ $location->id }}" selected>
                              {{ $location->location }}
                          </option>
                        @endif
                      @endforeach
                    @endif
                </select>
            </div>

          <div class="col-lg-6">
            <div class="row">
              <div class="col-lg-12 form-group mt-4">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" name="enable_sr_no" {{ ($product->enable_sr_no==1 || old('enable_sr_no',1)==1) ? "checked" : '' }} value="1" class="custom-control-input" id="enable_imei">
                  <label class="custom-control-label" for="enable_imei">{{ __('app.enable_imei') }}</label>
                </div>
                {{-- <span class="text-muted">Enable stock management at product level</span> --}}
              </div>

              <div class="col-lg-6 form-group mt-4">
                <div class="custom-control custom-checkbox">
                  <input type="checkbox" name="enable_stock" {{ ($product->enable_stock==1 || old('enable_stock', 1)==1) ? "checked" : '' }} value="1" class="custom-control-input" id="enable-stock">
                  <label class="custom-control-label" for="enable-stock">{{ __('app.enable-stock') }}?</label>
                </div>
                <span class="text-muted">Enable stock management at product level</span>
              </div>

              {{-- Alert quantity --}}
              <div class="col-lg-6 form-group alert-quantity" style="{{ ($product->enable_stock==1 || old('enable_stock', 1)==1) ? "" : 'display:none;' }}">
                <label for="alert_quantity" class="control-label">
                  {{ trans('app.alert_quantity') }} <span class="required">*</span>
                </label>
                <input type="text" name="alert_quantity" id="alert_quantity" class="form-control integer-input" value="{{ old('alert_quantity', 1) ?? $product->alert_quantity }}">
              </div>
            </div>
            <div class="row">
              {{-- Product Type --}}
              <div class="col-lg-6 form-group">
                <label for="product-type">{{ __('app.type') }}</label>
                @if ($isFormShowType)
                  <input type="text" class="form-control" value="{{ ($product->type ?? '') }}" disabled>
                @else
                  @if($formType==FormType::EDIT_TYPE && $product->type=='variant')
                    <input type="hidden" name="product_type" value="{{ $product->type }}">
                  @endif
                  <select name="product_type" id="product-type" class="form-control" style="width:100%;" {{ ($formType==FormType::EDIT_TYPE && $product->type=='variant') ? "disabled" : '' }}>
                    <option value="variant" {{ ($product->type=='variant' || old('product_type')) ? 'selected' : '' }}>{{ __('app.variant') }}</option>
                    <option value="single" {{ ($product->type=='single' || old('product_type')) ? 'selected' : '' }}>{{ __('app.single') }}</option>
                  </select>
                @endif
              </div>
            </div>
            <div class="row hidden-field">
              {{-- Cost --}}
              <div class="col-lg-6 form-group">
                <label for="cost" class="control-label">{{ trans('app.cost') }} ($)</label>
                <input type="text" name="cost" id="cost" class="form-control decimal-input" value="{{ old('cost') ?? $product->cost }}" {{ $disabledFormType }}>
              </div>

              {{-- Price --}}
              <div class="col-lg-6 form-group">
                <label for="price" class="control-label">{{ trans('app.selling_price') }} ($) <span class="required">*</span></label>
                <input type="text" name="price" id="price" class="form-control decimal-input" value="{{ old('price') ?? $product->price }}" required {{ $disabledFormType }}>
              </div>
            </div>
          </div>

          <div class="col-lg-6 form-group">
            <label for="photo" class="control-label">
              {{ trans('app.photo') }}
            </label>
            @if ($isFormShowType)
              <div class="text-left">
                @if (isset($product->photo))
                  <img src="{{ asset($product->photo) }}" alt="" width="100%" class="img-responsive">
                @else
                  {{ trans('app.no_picture') }}
                @endif
              </div>
            @else
              <input type="file" name="photo" id="photo" class="form-control" accept=".jpg, .jpeg, .png">
            @endif
          </div>
        </div>

        <hr>
        <div class="row product-variantions" style="{{ ($product->type=='single' || old('product_type')=='single') ? 'display: none;' : '' }}">
          <fieldset class="col-lg-12">
            <legend>
              <h5>{{__('app.product_variantion')}}</h5>
            </legend>
            <div class="table-responsive">
              <table class="table table-bordered table-variantion">
                <thead>
                  <tr class="table-success">
                    <th>{{__('app.sku')}}</th>
                    <th>{{__('app.value')}}</th>
                    <th>{{__('app.purchase_price')}} ($)</th>
                    <th>{{__('app.selling_price')}} ($)</th>
                    <th class="text-center">
                      <button type="button" class="btn btn-sm btn-primary add-variant-row" id="" data-url="{{ url('product/get_variation_value_row') }}"><i class="fa fa-plus"></i></button>
                      {{-- <button type="button" class="btn btn-sm btn-danger reset-variant-row" id=""><i class="fa fa-times"></i></button> --}}
                    </th>
                  </tr>
                </thead>
                <tbody class="variant-row">
                  @if(!empty($product->variations) && count($product->variations) > 0)
                    @foreach($product->variations as $key => $variant)
                      <tr class="row_index_{{$key}}" data-index="{{$key}}">
                        <td>
                          <input type="hidden" name="variant[{{ $key }}][variantion_id]" value="{{ $variant->id }}">
                          <input type="text" name="variant[{{ $key }}][sku]" placeholder="" class="form-control form-control-sm variant-sku" id="variant-sku" value="{{ $variant->sub_sku }}">
                        </td>
                        <td>
                          <input type="text" name="variant[{{ $key }}][value]" placeholder="" class="form-control form-control-sm variant-value" id="variant-value" value="{{ $variant->name }}" required>
                        </td>
                        <td>
                          <input type="text" name="variant[{{ $key }}][purchase_price]" placeholder="" class="form-control form-control-sm decimal-input variant-purchase-price" id="variant-purchase-price" value="{{ $variant->default_purchase_price }}">
                        </td>
                        <td>
                          <input type="text" name="variant[{{ $key }}][selling_price]" placeholder="" class="form-control form-control-sm decimal-input variant-selling-price" id="variant-selling-price" value="{{ $variant->default_sell_price }}">
                        </td>
                        <td class="text-center">
                          <button type="button" class="btn btn-sm btn-danger remove-variant-row" id=""><i class="fa fa-minus"></i></button>
                          <input type="hidden" class="variant-row-index" value="{{ $key }}">
                        </td>
                      </tr>
                    @endforeach
                  @else
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
                  @endif
                </tbody>
              </table>
            </div>
          </fieldset>
        </div>

        {{-- Button save or edit --}}
        <div class="row">
          <div class="col-lg-12 text-right">
            <input type="hidden" name="submit_type" id="submit_type">
            @if ($isFormShowType)
              @include('partial/anchor-edit', [
                'href' => route('product.edit', $product->id)
              ])
            @else
              <button id="opening_stock_button" type="submit" value="with_opening_stock" class="btn bg-purple submit_product_form">{{__('app.save_n_opening_stock')}}</button>

              <button type="submit" value="with_adding_another" class="btn bg-maroon submit_product_form">{{__('app.save_n_adding_another')}}</button>

              @include('partial/button-save', ['class' => 'submit_product_form'])
            @endif
          </div>
        </div>
      </form>
    </div>
  </main>
@endsection

@section('js')
  <script>
    var variantions = [];
    // localStorage.setItem('variantions', variantions);
  </script>
  <script src="{{ asset('js/bootstrap-fileinput.js') }}"></script>
  <script src="{{ asset('js/bootstrap-fileinput-fa-theme.js') }}"></script>
  <script src="{{ asset('js/init-file-input.js') }}"></script>
  <script src="{{ asset('js/jquery-number.min.js') }}"></script>
  <script src="{{ asset('js/number.js') }}"></script>
  <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
  <script src="{{ asset('js/mask.js') }}"></script>
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>
  <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('js/product.js') }}"></script>

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
@endsection
