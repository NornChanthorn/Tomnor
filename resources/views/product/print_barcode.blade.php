@extends('layouts/backend')

@section('title', __('app.product-barcode'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading print-hidden">{{ __('app.product-barcode') }}</h3>

    <div class="card mb-3 border-primary print-hidden">
      <div class="card-body">
        <form id="form-barcode" method="post" action="{{ route('product.barcode-generate') }}">
        <div class="row justify-content-center">
          <div class="col-md-12">
            <div class="form-group">
              <label style="font-size:16px;">{{ __('app.add-product-generate-label') }}</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon1">
                    <i class="fa fa-search"></i>
                  </span>
                </div>
                <input type="text" id="product" class="form-control" placeholder="{{__('app.enter-product')}}">
              </div>
            </div>

            <table class="table table-bordered" id="selected-products">
              <thead>
                <tr>
                  <th>{{ __('app.product') }}</th>
                  <th>{{ __('app.qty-labels') }}</th>
                </tr>
              </thead>
              <tbody id=""></tbody>
            </table>
          </div>
        </div>
        <hr>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="" style="font-size:16px;">{{ __('app.stylesheet') }}</label>
            </div>
            <div class="custom-control custom-checkbox custom-control-inline form-group mr-5">
              <input type="checkbox" name="product_name" value="1" class="custom-control-input" id="product-name">
              <label class="custom-control-label" for="product-name">{{__('app.product_name')}}</label>
            </div>

            <div class="custom-control custom-checkbox custom-control-inline form-group mr-5">
              <input type="checkbox" name="product_variantion" value="1" checked class="custom-control-input" id="product-variantion">
              <label class="custom-control-label" for="product-variantion">{{__('app.product_variantion')}} ({{__('app.recommended')}})</label>
            </div>

            <div class="custom-control custom-checkbox custom-control-inline form-group mr-5">
              <input type="checkbox" name="product_price" value="1" class="custom-control-input" id="product-price">
              <label class="custom-control-label" for="product-price">{{__('app.product_price')}}</label>
            </div>

            <div class="custom-control custom-checkbox custom-control-inline form-group mr-5">
              <input type="checkbox" name="product_unit" value="1" class="custom-control-input" id="product-unit">
              <label class="custom-control-label" for="product-unit">{{__('app.unit')}}</label>
            </div>

            <div class="custom-control custom-checkbox custom-control-inline form-group mr-5">
              <input type="checkbox" name="product_category" value="1" class="custom-control-input" id="product-category">
              <label class="custom-control-label" for="product-category">{{__('app.category')}}</label>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label for="">{{__('app.barcode-settings')}}:</label>
            <select class="custom-select" name="barcode_setting">
              @foreach($barcodes as $barcode)
                <option value="{{ $barcode->id }}">{{ $barcode->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <hr>

        <div class="row">
          <div class="col-md-12 text-right">
            <button type="reset" class="btn btn-warning">{{ __('app.reset') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('app.generate') }}</button>
          </div>
        </div>
        </form>
      </div>
    </div>

    <div class="card" id="barcode-print" style="display:none;">
      <div class="card-body">
        <button type="button" class="btn btn-block btn-primary print-hidden" id="print-label">{{ __('app.print') }}</button>
        <div class="" id="barcode-print-area"></div>
      </div>
    </div>
  </div>
</main>
@endsection

@section('css')
  <link rel="stylesheet" href="{{asset('plugins/easyAutocomplete/easy-autocomplete.min.css')}}">
  <style>

    .easy-autocomplete-container { z-index: 9; }

    
    .barcode .item { overflow: hidden; text-align: center; border: 1px dotted #ccc; font-size: 12px; }
    .barcode-name {}
    .barcode-price {}
    .barcode-unit {}
    
  </style>
@endsection

@section('js')
  <script src="{{ asset('plugins/easyAutocomplete/jquery.easy-autocomplete.js') }}"></script>
  
  <script>
    $(document).ready(function() {
      $("#product").easyAutocomplete({
        url: function(phrase) {
          return "{{ route('product.barcode-suggestion') }}";
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
          return resp;
        },
        requestDelay: 100,
        list: {
          onClickEvent: function() {
            var value = $("#product").getSelectedItemData();
            suggestedProduct(value);

            $("#product").val('').focus();
          }
        }
      });

      $("#form-barcode").on('submit', function(e) {
        e.preventDefault();
        $("#barcode-print").hide();
        const value = $(this).serializeArray();
        console.log(value);

        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{ route('product.barcode-generate') }}",
          dataType: 'json',
          method: 'POST',
          data: value,
          success: function(resp) {
            if(resp.status) {
              $("#barcode-print-area").html(resp.html);
              $("#barcode-print").show();
            } 
            else {
              alert(resp.message);
            }
          },
          error: function(err) {
            console.log(err);
          }
        });
      });
    });

    $(document).on('click', "button#print-label", function() {
      $("#barcode-print-area").print();
    });

    function suggestedProduct(item) {
      var output = '<tr>' +
        '<td>' +
          '<input type="hidden" name="item['+ item.id +'][product_id]" value="'+ item.id +'">' +
          '<label>'+ item.label +'</label>' +
        '</td>' +
        '<td>' +
          '<input type="number" name="item['+ item.id +'][quantity]" value="'+ item.quantity +'" class="form-control form-control-sm" placeholder="">' +
        '</td>' +
      '</tr>';
      console.log(output);

      $("table#selected-products tbody").prepend(output);
    }
  </script>
@endsection
