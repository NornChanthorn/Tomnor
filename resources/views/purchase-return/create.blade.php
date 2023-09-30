@extends('layouts/backend')

@section('title', trans('app.purchase'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.purchase') . ' - ' . $title }}</h3>
      @include('partial/flash-message')

      <form method="post" id="purchase-form" class="validated-form no-auto-submit" action="{{ route('purchase.save') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="purchase_id" value="{{$purchase->id}}">
        <input type="hidden" name="form_type" value="{{ $formType }}">

        <div class="row">
          @if (isAdmin() || empty(auth()->user()->staff))
            {{-- Warehouse --}}
            <div class="col-lg-4 form-group">
              <label for="warehouse" class="control-label">
                {{ trans('app.location') }} <span class="required">*</span>
              </label>
              <select name="warehouse" id="warehouse" class="form-control select2" required>
                <option value="">{{ trans('app.select_option') }}</option>
                @foreach ($warehouses as $warehouse)
                  <option value="{{ $warehouse->id }}" {{ selectedOption($warehouse->id, old('warehouse', $purchase->location_id)) }}>
                    {{ $warehouse->location }}
                  </option>
                @endforeach
              </select>
            </div>
          @else
            <input type="hidden" name="warehouse" value="{{ auth()->user()->staff->branch_id }}">
          @endif

          {{-- Suppliers --}}
          <div class="col-lg-4 form-group">
            <label for="supplier" class="control-label">
              {{ trans('app.supplier') }} <span class="required">*</span>
            </label>
            <div class="input-group">
              <select name="supplier" id="supplier" class="form-control select2" required>
                @foreach ($suppliers as $index => $supplier)
                  <option value="{{ $supplier->id }}" {{ selectedOption($supplier->id, old('supplier', $purchase->contact_id)) }}>
                    {{ $supplier->supplier_business_name ?? $supplier->name }}
                  </option>
                @endforeach
              </select>
              <div class="input-group-append">
                <a href="javascript::void(0);" class="btn btn-block btn-primary" id="add-supplier" data-href="{{ route('contact.create', ['type'=>$contact->type]) }}" data-container=".contact-modal"><i class="fa fa-plus"></i></a>
              </div>
            </div>
          </div>

          {{-- Invoice number --}}
          <div class="col-lg-4 form-group">
            <label for="invoice_id" class="control-label">
              {{ trans('app.invoice_id') }}
            </label>
            <input type="text" name="invoice_id" id="invoice_id" class="form-control" value="{{ old('invoice_id', $purchase->ref_no) }}">
          </div>

          {{-- Purchase status --}}
          <div class="col-lg-4 form-group">
            <label for="status" class="control-label">
              {{ trans('app.purchase_status') }} <span class="required">*</span>
            </label>
            <select name="status" id="status" class="form-control select2 select2-no-search" required>
              @foreach ($purchaseStatuses as $statusKey => $statusTitle)
                <option value="{{ $statusKey }}" {{ selectedOption($statusKey, old('status', $purchase->status)) }}>
                  {{ $statusTitle }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Purchase date --}}
          <div class="col-lg-4 form-group">
            <label for="purchase_date" class="control-label">
              {{ trans('app.purchase_date') }} <span class="required">*</span>
            </label>
            <input type="text" name="purchase_date" id="purchase_date" class="form-control datepicker" required placeholder="{{ trans('app.date_placeholder') }}" value="{{ old('purchase_date', $purchase->transaction_date ? date('d-m-Y', strtotime($purchase->transaction_date)) : date('d-m-Y')) }}">
          </div>

          {{-- Document --}}
          <div class="col-lg-4 form-group mt-4">
            <label for="document" class="control-label">
              {{ trans('app.document') }}
            </label>
            <input type="file" name="document" id="document" class="">
          </div>
        </div>

        {{-- Product list --}}
        <div class="card mb-4 mt-3">
          <div class="card-header">
            <h5 class="mb-0">{{ trans('app.product_table') }}</h5>
          </div>
          <div class="card-body">
            <div class="row">
              {{-- Product --}}
              <div class="col-lg-4 form-group">
                <label for="product" class="control-label">{{ trans('app.product') }}</label>
                <input type="text" class="form-control" id="product" placeholder="{{ __('app.enter-product') }}">
              </div>
            </div>

            <div class="table-responsive">
              <table id="product-table" class="table table-bordered table-hover">
                <thead>
                  <tr class="bg-success text-white">
                    {{-- <th>#</th> --}}
                    <th>{{ trans('app.name') }}</th>
                    <th>{{ trans('app.code') }}</th>
                    <th class="text-center">{{ trans('app.quantity') }}</th>
                    <th class="text-right">{{ trans('app.cost') }}</th>
                    <th class="text-right">{{ trans('app.sub_total') }}</th>
                    <th class="text-center">{{ trans('app.delete') }}</th>
                  </tr>
                </thead>
                <tbody>
                  {{-- When form validation has error (s) --}}
                  @if($purchase->purchase_lines)
                    @foreach ($purchase->purchase_lines as $index => $purchase_line)
                      @php
                        $indexId = $purchase_line->product_id.$purchase_line->variantion_id;
                      @endphp
                      <tr>
                        <input type="hidden" name="products[{{ $indexId }}][id]" value="{{ $purchase_line->product_id }}">
                        <input type="hidden" name="products[{{ $indexId }}][name]" value="{{ $purchase_line->product->name }}">
                        <input type="hidden" name="products[{{ $indexId }}][code]" value="{{ $purchase_line->product->code }}">
                        <input type="hidden" name="products[{{ $indexId }}][variantion_id]" value="{{ $purchase_line->variantion_id }}">
                        {{-- <th>{{ ($index+1) }}</th> --}}
                        <td>{{ $purchase_line->product->name.($purchase_line->variations->name != "DUMMY" ? '-'.$purchase_line->variations->name : '') }}</td>
                        <td>{{ $purchase_line->variations->name != "DUMMY" ? $purchase_line->variations->sub_sku: $purchase_line->product->code ?? trans('app.none') }}</td>
                        <td width="15%" class="text-center">
                          <input type="text" name="products[{{ $indexId }}][quantity]" min="1" max="10000" class="form-control form-control-sm integer-input quantity" value="{{ $purchase_line->quantity }}" required>
                        </td>
                        <td width="15%" class="text-right">
                          <input type="text" name="products[{{ $indexId }}][purchase_price]" class="form-control form-control-sm decimal-input purchase-price" value="{{ decimalNumber($purchase_line->purchase_price, true) }}">
                        </td>
                        <td width="15%" class="text-right">
                          <input type="text" name="sub_total" class="form-control form-control-sm sub-total" value="{{ decimalNumber($purchase_line->purchase_price * $purchase_line->quantity, true) }}" readonly placeholder="">
                        </td>
                        <td class="text-center">
                          <button type="button" class="btn btn-danger btn-sm" onclick="removeProduct(this)"><i class="fa fa-trash-o"></i></button>
                        </td>
                      </tr>
                    @endforeach
                  @endif
                </tbody>
                <tfoot>
                  <tr class="bg-light">
                    <td colspan="4" align="right"><b>{{ trans('app.grand_total') }}</b></td>
                    <td colspan="2">
                      <span class="shown_total_price">0.00</span>
                      <input type="hidden" name="total_price" class="total_price" value="{{ $purchase->total_before_tax ?? 0 }}">
                    </td>
                  </tr>
                  <tr class="bg-light">
                    <td colspan="4" align="right"><b>{{ trans('app.discount') }} ($)</b></td>
                    <td colspan="2">
                      <input type="text" name="discount" class="form-control form-control-sm decimal-input discount" min="0" required value="{{ $purchase->discount_amount ?? 0 }}">
                    </td>
                  </tr>
                  <tr class="bg-light">
                    <td colspan="4" align="right"><b>{{ trans('app.shipping_cost') }} ($)</b></td>
                    <td colspan="2">
                      <input type="text" name="shipping_cost" id="shipping_cost" class="form-control form-control-sm  decimal-input shipping-cost" min="0" value="{{ $purchase->shipping_charges ?? 0 }}">
                    </td>
                  </tr>
                  <tr class="bg-light">
                    <td colspan="4" align="right"><b>{{ trans('app.balance') }}</b></td>
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

        @if($formType == FormType::CREATE_TYPE)
          <hr>
          <div class="row">
            <fieldset class="col-lg-12">
              <legend><h5>{{ trans('app.payment_information') }}</h5></legend>
              <div class="row">
                {{-- Total cost --}}
                <div class="col-lg-4 form-group">
                  <label for="total_cost" class="control-label">
                    {{ trans('app.paid_amount') }} ($) <span class="required">*</span>
                  </label>
                  <input type="text" name="total_cost" id="total_payable_amount" class="form-control decimal-input" min="0" value="{{ old('total_cost', (@$purchase->payment_lines[0]->amount ?? 0)) }}" onclick="$(this).select();">
                </div>

                {{-- Payment Method --}}
                <div class="col-lg-4 form-group">
                  <label for="payment_method" class="control-label">
                    {{ trans('app.payment_method') }} <span class="required">*</span>
                  </label>
                  <select name="payment_method" id="payment_method" class="form-control select2 select2-no-search" required>
                    @foreach (paymentMethods() as $methodKey => $methodValue)
                      <option value="{{ $methodKey }}" {{ $methodKey == old('payment_method') ? 'selected' : '' }}>
                        {{ $methodValue }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              {{-- Note --}}
              <div class="form-group">
                <label for="note" class="control-label">{{ trans('app.note') }}</label>
                <textarea name="note" id="note" rows="2" class="form-control" style="resize:none;">{{ old('note', $purchase->additional_notes) }}</textarea>
              </div>
            </fieldset>
          </div>
        @endif

        {{-- Button save or edit --}}
        <div class="row">
          <div class="col-lg-12 text-right">
            @include('partial/button-save', ['onClick' => 'confirmFormSubmission($("#purchase-form"))'])
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
  <script src="{{ asset('plugins/easyAutocomplete/jquery.easy-autocomplete.js') }}"></script>
  <script src="{{ asset('js/purchase.js') }}"></script>
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
              url: '{{ route('contact.check-contact') }}',
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
            remote: "{{ trans('message.supplier_already_exist') }}",
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
                  $('<option>', { value: result.data.id, text: result.data.name })
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
      $('.contact_modal').find('select#contact_type').val('{{ $contact->type }}').closest('div.contact_type_div').addClass('hide');
      $('.contact_modal').modal('show');
    });
  </script>
@endsection
