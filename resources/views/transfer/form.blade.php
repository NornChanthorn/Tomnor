@extends('layouts/backend')

@section('title', trans('app.stock_transfer'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.stock_transfer') . ' - ' . $title }}</h3>
    @include('partial/flash-message')

    <form method="post" id="transfer-form" class="validated-form no-auto-submit" action="{{ route('transfer.save') }}" enctype="multipart/form-data">
      @csrf

      <div class="row">
        {{-- Transfer date --}}
        <div class="col-lg-3 form-group">
          <label for="transaction_date" class="control-label">
            {{ trans('app.transfer_date') }} <span class="required">*</span>
          </label>
          <input type="text" name="transaction_date" id="transaction_date" class="form-control datepicker" readonly requiredz placeholder="{{ trans('app.date_placeholder') }}" value="{{ old('transaction_date') ?? date('d-m-Y') }}">
        </div>

        {{-- Invoice number --}}
        <div class="col-lg-3 form-group">
          <label for="ref_no" class="control-label">{{ trans('app.invoice_id') }}</label>
          <input type="text" name="ref_no" id="ref_no" class="form-control" value="{{ old('ref_no') }}">
        </div>

        {{-- Original warehouse --}}
        <div class="col-lg-3 form-group">
          <label for="original_warehouse" class="control-label">
            {{ trans('app.original_location') }} <span class="required">*</span>
          </label>
          <select name="original_warehouse" id="original_warehouse" class="form-control select2" required>
            <option value="">{{ trans('app.select_option') }}</option>
            @foreach ($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}" {{ selectedOption($warehouse->id, old('original_warehouse')) }}>
              {{ $warehouse->location }}
            </option>
            @endforeach
          </select>
        </div>

        {{-- Target warehouse --}}
        <div class="col-lg-3 form-group">
          <label for="target_warehouse" class="control-label">
            {{ trans('app.target_location') }} <span class="required">*</span>
          </label>
          <select name="target_warehouse" id="target_warehouse" class="form-control select2" required>
            <option value="">{{ trans('app.select_option') }}</option>
            @foreach ($warehouses as $warehouse)
            <option value="{{ $warehouse->id }}" {{ selectedOption($warehouse->id, old('target_warehouse')) }}>
              {{ $warehouse->location }}
            </option>
            @endforeach
          </select>
        </div>

        {{-- Transfer status --}}
        <div class="col-lg-3 form-group">
          <label for="status" class="control-label">
            {{ trans('app.transfer_status') }} <span class="required">*</span>
          </label>
          <select name="status" id="status" class="form-control select2 select2-no-search" required>
            @foreach ($transferStatuses as $statusKey => $statusTitle)
            <option value="{{ $statusKey }}" {{ selectedOption($statusKey, old('status')) }}>
              {{ $statusTitle }}
            </option>
            @endforeach
          </select>
        </div>

        {{-- Document --}}
        <div class="col-lg-3 form-group">
          <label for="document" class="control-label">
            {{ trans('app.document') }}
          </label>
          <input type="file" name="document" id="document" class="">
        </div>
      </div>

      {{-- Product list --}}
      <div class="card mb-4">
        <div class="card-header">
          <h5>{{ trans('app.product_table') }}</h5>
        </div>
        <div class="card-body">
          <div class="row">
            {{-- Product --}}
            <div class="col-lg-4 form-group">
              <label for="product" class="control-label">{{ trans('app.product') }}</label>
              <input type="text" id="product" class="form-control" placeholder="{{__('app.enter-product')}}" disabled>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="table-responsive">
                <table id="product-table" class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>{{ trans('app.name') }}</th>
                      <th>{{ trans('app.code') }}</th>
                      <th>{{ trans('app.in-stock_quantity') }}</th>
                      <th>{{ trans('app.transfer_quantity') }}</th>
                      <th>{{ trans('app.delete') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    {{-- When form validation has error (s) --}}
                    @foreach ($transferredProducts as $product)
                    <tr data-id="{{ $product['id'] }}">
                      <input type="hidden" name="products[{{ $product['id'] }}][id]" value="{{ $product['id'] }}">
                      <input type="hidden" name="products[{{ $product['id'] }}][name]" value="{{ $product['name'] }}">
                      <input type="hidden" name="products[{{ $product['id'] }}][code]" value="{{ $product['code'] }}">
                      <input type="hidden" name="products[{{ $product['id'] }}][stock_qty]" value="{{ $product['stock_qty'] }}">
                      <td>{{ $product['name'] }}</td>
                      <td>{{ $product['code'] ?? trans('app.none') }}</td>
                      <td>{{ $product['stock_qty'] }}</td>
                      <td width="25%">
                        <input type="text" name="products[{{ $product['id'] }}][quantity]" min="1" max="10000"
                        class="form-control integer-input" value="{{ $product['quantity'] }}" required>
                      </td>
                      <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(this)">
                          <i class="fa fa-trash-o"></i>
                        </button>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr class="bg-light">
                      <td colspan="3" align="right"><b>{{ trans('app.shipping_cost') }} ($)</b></td>
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
        {{-- Note --}}
        <div class="col-lg-6 form-group">
          <label for="note" class="control-label">
            {{ trans('app.note') }}
          </label>
          <input type="text" name="note" id="note" class="form-control" value="{{ old('note') }}">
        </div>
      </div>

      {{-- Button save or edit --}}
      <div class="row">
        <div class="col-lg-12 text-right">
          @include('partial.button-save', ['onClick' => 'confirmFormSubmission($("#transfer-form"))'])
        </div>
      </div>
    </form>
  </div>
</main>
@endsection

@section('css')
  <link rel="stylesheet" href="{{ asset('css/bootstrap-fileinput.css') }}">
  <link rel="stylesheet" href="{{asset('plugins/easyAutocomplete/easy-autocomplete.min.css')}}">
@endsection

@section('js')
  <script>
    var codeLabel = '{{ trans('app.code') }}';
    var noneLabel = '{{ trans('app.none') }}';
  </script>
  <script src="{{ asset('js/bootstrap-fileinput.js') }}"></script>
  <script src="{{ asset('js/bootstrap-fileinput-fa-theme.js') }}"></script>
  <script src="{{ asset('js/init-file-input.js') }}"></script>
  <script src="{{ asset('js/jquery-number.min.js') }}"></script>
  <script src="{{ asset('js/number.js') }}"></script>
  <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
  <script src="{{ asset('js/mask.js') }}"></script>
  <script src="{{ asset('js/date-time-picker.js') }}"></script>
  <script src="{{ asset('js/form-validation.js') }}"></script>
  <script src="{{ asset('plugins/easyAutocomplete/jquery.easy-autocomplete.js') }}"></script>
  <script src="{{ asset('js/transfer.js') }}"></script>
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
          return "{{ route('product.product-variantion') }}";
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
                swal(value.label, "{{trans('message.product_out_of_stock_content')}}", 'info');
              }

              $("#product").val('');
            }
          },
          onClickEvent: function() {
            var value = $("#product").getSelectedItemData();
            if(value.qty_available > 0){
              addProduct(value);
            }else{
              swal(value.label, "{{trans('message.product_out_of_stock_content')}}", 'info');
            }

            $("#product").val('').focus();
          }
        }
      });
    });
  </script>
@endsection
