@extends('layouts/backend')

@section('title', trans('app.sale'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.sale') . ' - ' . $title }}</h3>
      @include('partial.flash-message')

      <form method="post" id="sale-form" class="validated-form no-auto-submit" action="{{ route('sale.save', SaleType::SALE) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="sale_id" value="{{$sale->id}}">
        <input type="hidden" name="form_type" value="{{ $formType }}">

        <div class="row">
          {{-- Client --}}
          <div class="col-lg-4 form-group">
            <label for="client" class="control-label">
              {{ trans('app.client') }} <span class="required">*</span>
            </label>
            <div class="input-group">
              <select name="client" id="client" class="form-control select2" required>
                @foreach ($clients as $client)
                  <option value="{{ $client->id }}" {{ selectedOption($client->id, old('client', $sale->contact_id)) }} @if($client->is_default) selected @endif>{{ $client->name }}</option>
                @endforeach
              </select>
              <div class="input-group-append">
                <a href="#" class="btn btn-block btn-primary" id="add-client" data-href="{{ route('contact.create', ['type'=>'customer']) }}" data-container=".contact-modal"><i class="fa fa-plus"></i></a>
              </div>
            </div>
          </div>

          <div class="col-lg-4 form-group client-addition hide">
            <label for="client_name" class="control-label">{{ trans('app.client_name') }}</label>
            <input type="text" name="client_name" id="client_name" class="form-control" placeholder="{{ trans('app.client_name') }}" value="{{ old('client_name') }}">
          </div>

          <div class="col-lg-4 form-group client-addition hide">
            <label for="phone_number" class="control-label">
              {{ trans('app.phone_number') }} <span class="required">*</span>
            </label>
            <input type="text" name="phone_number" id="phone_number" class="form-control" required placeholder="{{ trans('app.phone_number') }}" value="{{ old('phone_number') }}">
          </div>

          @if (isAdmin() || empty(auth()->user()->staff))
            {{-- Branch --}}
            <div class="col-lg-4 form-group">
              <label for="branch" class="control-label">
                {{ trans('app.branch') }} <span class="required">*</span>
              </label>
              <select name="branch" id="branch" class="form-control select2" required>
                <option value="">{{ trans('app.select_option') }}</option>
                @foreach ($branches as $branch)
                  <option value="{{ $branch->id }}" {{ selectedOption($branch->id, old('branch', $sale->location_id)) }}>{{ $branch->location }}</option>
                @endforeach
              </select>
            </div>

            {{-- Agent --}}
            <div class="col-lg-4 form-group">
              <label for="agent" class="control-label">
                {{ trans('app.agent') }} <span class="required">*</span>
              </label>
              <select name="agent" id="agent" class="form-control select2" required>
                <option value="">{{ trans('app.select_option') }}</option>
                @foreach ($agents as $agent)
                  <option value="{{ $agent->user_id }}" {{ selectedOption($agent->user_id, old('agent', $sale->created_by)) }}>{{ $agent->name }}</option>
                @endforeach
              </select>
            </div>
          @endif

          {{-- Transfer date --}}
          <div class="col-lg-4 form-group">
            <label for="sale_date" class="control-label">
              {{ trans('app.sale_date') }} <span class="required">*</span>
            </label>
            <input type="text" name="sale_date" id="sale_date" class="form-control datepicker" required placeholder="{{ trans('app.date_placeholder') }}" value="{{ old('sale_date', displayDate($sale->transaction_date)) ?? date('d-m-Y') }}">
          </div>

          <div class="col-lg-4 form-group">
            <label for="sale_date" class="control-label">
              {{ trans('app.sale_status') }} <span class="required">*</span>
            </label>
            <select name="status" id="status" class="form-control" required>
              <option value="">{{ trans('app.select_option') }}</option>
              @foreach (saleStatuses() as $k => $_sta)
                <option value="{{ $k }}" {{ selectedOption($k, old('status', $sale->status)) }}>{{ $_sta }}</option>
              @endforeach
            </select>
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
                <input type="text" class="form-control" id="product" placeholder="{{ __('app.enter-product') }}">
              </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                <div class="table-responsive">
                  <table id="sale-product-table" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>{{ trans('app.name') }}</th>
                        <th>{{ trans('app.code') }}</th>
                        <th>{{ trans('app.in-stock_quantity') }}</th>
                        <th>{{ trans('app.sale_quantity') }}</th>
                        <th>{{ trans('app.unit_price') }}</th>
                        <th>{{ trans('app.sub_total') }}</th>
                        <th>{{ trans('app.delete') }}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($details as $item)
                        @php
                          $indexId = $item->product_id.$item->variantion_id;
                        @endphp

                        <tr data-id="{{ $indexId }}">
                          <input type="hidden" name="products[{{ $indexId }}][transaction_sell_lines_id]" value="{{$item->id}}">
                          <input type="hidden" name="products[{{ $indexId }}][id]" value="{{$item->product_id}}">
                          <input type="hidden" name="products[{{ $indexId }}][name]" value="{{$item->product->name}}">
                          <input type="hidden" name="products[{{ $indexId }}][code]" value="{{$item->product->code}}">
                          <input type="hidden" name="products[{{ $indexId }}][variantion_id]" value="{{$item->variantion_id}}">
                          <input type="hidden" name="products[{{ $indexId }}][enable_stock]" value="{{$item->product->enable_stock}}">
                          <td>
                            {{ $item->product->name }}{{ $item->variations->name!='DUMMY' ? ' - '.$item->variations->name : '' }}
                          </td>
                          <td>{{ $item->variations->sub_sku ?? trans('app.none') }}</td>
                          @php
                              $pro_id = $item->product_id;
                              $va_id = $item->variantion_id;
                              $lo_id = $sale->location_id;
                              $qty_available= App\Models\VariantionLocationDetails::where('location_id',$lo_id)->where('variantion_id',$va_id)->where('product_id', $pro_id)->first()->qty_available;
                          @endphp
                      
                          <td>{{ decimalNumber($qty_available) }}</td>
                          <td width="15%">
                            <input type="text" name="products[{{ $indexId }}][quantity]" class="form-control form-control-sm integer-input quantity" min="1" max="{{ $qty_available + $item->quantity }}" required value="{{$item->quantity}}">
                          </td>
                          <td width="15%">
                            <input type="text" name="products[{{ $indexId }}][price]" class="form-control form-control-sm decimal-input unit_price" min="1" required value="{{$item->unit_price}}">
                          </td>
                          <td width="15%">
                            <input type="text" name="products[{{ $indexId }}][sub_total]" class="form-control form-control-sm decimal-input sub_total" min="1"  required value="{{$item->quantity * $item->unit_price}}" readonly>
                          </td>
                          <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="rmProduct(this)">
                              <i class="fa fa-trash-o"></i>
                            </button>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="5" align="right"><b>{{ trans('app.grand_total') }}</b></td>
                        <td colspan="2"><span class="shown_total_price"></span></td>
                        <input type="hidden" name="total_price" class="total_price" value="0">
                      </tr>
                      <tr>
                        <td colspan="5" align="right"><b>{{ trans('app.discount') }} ($)</b></td>
                        <td colspan="2"><input type="text" name="discount" value="{{ $sale->discount_amount }}" class="form-control form-control-sm decimal-input discount"></td>
                      </tr>
                      <tr>
                        <td colspan="5" align="right"><b>{{ trans('app.other_service') }} ($)</b></td>
                        <td colspan="2"><input type="text" name="other_service" value="{{ $sale->others_charges }}" class="form-control form-control-sm decimal-input other_service"></td>
                      </tr>
                      <tr>
                        <td colspan="5" align="right"><b>{{ trans('app.balance') }}</b></td>
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

        {{-- Button save or edit --}}
        <div class="row">
          <div class="col-lg-12 text-right">
            @include('partial.button-save')
          </div>
        </div>
      </form>
    </div>
  </main>

<div class="modal fade contact_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel">
  @include('contact.form', ['quick_add' => true])
</div>
@endsection

@section('css')
  <link rel="stylesheet" href="{{ asset('css/bootstrap-fileinput.css') }}">
  <link rel="stylesheet" href="{{asset('plugins/easyAutocomplete/easy-autocomplete.min.css')}}">
  <style>
    .input-group .select2 { width: 85%!important; }
    .input-group .input-group-append { width: 15%; }
  </style>
@endsection

@section('js')
  <script>
    var codeLabel = '{{ trans('app.code') }}';
    var noneLabel = '{{ trans('app.none') }}';

    // When change branch
    var agentSelectLabel = emptyOptionElm;
    var agentRetrievalUrl = '{{ route('staff.get_agents', ':branchId') }}';
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
  <script src="{{ asset('js/sweetalert.min.js') }}"></script>
  <script src="{{ asset('js/agent-retrieval.js') }}"></script>
  <script src="{{ asset('js/sale.js') }}"></script>
  <script src="{{ asset('plugins/easyAutocomplete/jquery.easy-autocomplete.js') }}"></script>
  <script>
    $(document).ready(function() {
      $(".datepicker").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
      });

      $("#branch").change(function(e) {
        e.preventDefault();
        if($(this).val() != '') {
          $("#product").attr('disabled', false).trigger('change');
        }
        else {
          $("#product").attr('disabled', true).trigger('change');
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
          resp.branch = $("#branch").val();
          return resp;
        },
        requestDelay: 100,
        list: {
          onLoadEvent: function() {
            var response = $("#product").getItems();
            if(response.length == 1 && response[0] != undefined) {
              // addProduct($("#product").getItemData(0));
              var value = $("#product").getItemData(0);
              @if($setting->enable_over_sale == 0)
                if(value.qty_available > 0){
                  addProduct(value);
                }else{
                  swal(value.label, "{{trans('message.product_out_of_stock_content')}}", 'info');
                }
              @else
                addProduct(value);
              @endif
              $("#product").val('');
            }
          },
          onClickEvent: function() {
            var value = $("#product").getSelectedItemData();
            addProduct(value);
            @if($setting->enable_over_sale == 0)
              if(value.qty_available > 0){
                addProduct(value);
              }else{
                swal(value.label, "{{trans('message.product_out_of_stock_content')}}", 'info');
              }
            @else
              addProduct(value);
            @endif
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
              url: '{{ route('contact.check-contact') }}',
              type: 'post',
              data: {
                type: function() {
                  return '{{ $contact->type }}';
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
            remote: "{{ trans('message.customer_already_exist') }}",
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
@endsection
