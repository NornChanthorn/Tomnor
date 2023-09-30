@extends('layouts/backend')

@section('title', trans('app.stock_adjustment'))

@section('css')
  <link rel="stylesheet" href="{{ asset('css/bootstrap-fileinput.css') }}">
  <link rel="stylesheet" href="{{asset('plugins/easyAutocomplete/easy-autocomplete.min.css')}}">
@endsection

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.stock_adjustment') . ' - ' . $title }}</h3>
    @include('partial/flash-message')

    <form method="post" id="adjustment-form" class="validated-form no-auto-submit" action="{{ route('adjustment.save') }}" enctype="multipart/form-data">
      @csrf

      <div class="row">
        {{-- Warehouse --}}
        <div class="col-lg-4 form-group">
          <label for="warehouse" class="control-label">
            {{ trans('app.location') }} <span class="required">*</span>
          </label>
          <select name="warehouse" id="warehouse" class="form-control select2" required>
            <option value="">{{ trans('app.select_option') }}</option>
            @foreach ($warehouses as $warehouse)
              <option value="{{ $warehouse->id }}" {{ selectedOption($warehouse->id, old('warehouse')) }}>
                {{ $warehouse->location }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Reference No --}}
        <div class="col-lg-4 form-group">
          <label for="ref_no" class="control-label">
            {{ trans('app.reference_number') }}
          </label>
          <input type="text" name="ref_no" id="ref_no" class="form-control" placeholder="{{ trans('app.reference_number') }}" value="{{ old('ref_no') }}">
        </div>

        {{-- Adjustment date --}}
        <div class="col-lg-4 form-group">
          <label for="adjustment_date" class="control-label">
            {{ trans('app.adjustment_date') }} <span class="required">*</span>
          </label>
          <input type="text" name="adjustment_date" id="adjustment_date" class="form-control datepicker" required placeholder="{{ trans('app.date_placeholder') }}" value="{{ old('adjustment_date') ?? date('d-m-Y') }}">
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
              <label for="product" class="control-label">
                {{ trans('app.product') }} <span class="required">*</span>
              </label>
              <input type="text" placeholder="" class="form-control product" id="product" disabled>
            </div>

            <div class="col-lg-12">
              <div class="table-responsive">
                <table id="product-table" class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      {{-- <th>#</th> --}}
                      <th>{{ trans('app.code') }}</th>
                      <th>{{ trans('app.name') }}</th>
                      <th class="text-center">{{ trans('app.action') }}</th>
                      <th class="text-center">{{ trans('app.quantity') }}</th>
                      <th class="text-center">{{ trans('app.delete') }}</th>
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
        {{-- Reason --}}
        <div class="col-lg-4 form-group">
          <label for="note" class="control-label">
            {{ trans('app.reason') }} <span class="required">*</span>
          </label>
          <textarea name="reason" id="reason" class="form-control" required rows="4" style="resize:none;">{{ old('reason') }}</textarea>
        </div>
      </div>

      {{-- Button save or edit --}}
      <div class="row">
        <div class="col-lg-12 text-right">
          @include('partial.button-save', ['onClick' => 'confirmFormSubmission($("#adjustment-form"))'])
        </div>
      </div>
    </form>
  </div>
</main>
@endsection

@section('js')
  <script>
    var stockQtyRetrievalUrl = '{{ route('adjustment.get_stock_quantity', [':warehouseId', ':productId']) }}';
    var NALabel = '{{ trans('app.n/a') }}';
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
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>
  <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('js/form-validation.js') }}"></script>
  <script src="{{ asset('js/adjustment.js') }}"></script>
  <script src="{{ asset('plugins/easyAutocomplete/jquery.easy-autocomplete.js') }}"></script>
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
          return "{{ route('product.product-variantion') }}";
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
                swal(value.label, "{{trans('message.product_out_of_stock_content')}}", 'info');
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
              swal(value.label, "{{trans('message.product_out_of_stock_content')}}", 'info');
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
                @foreach ($stockTypes as $typeKey => $typeTitle)
                  '<option value="{{ $typeKey }}" {{ selectedOption($typeKey, old('action')) }}>' +
                    '{{ $typeTitle }}' + 
                  '</option>' +
                @endforeach
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
@endsection
