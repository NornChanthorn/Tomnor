@extends('layouts/backend')

@section('title', trans('app.purchase_return'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.purchase_return') }}</h3>
      @include('partial/flash-message')

      <form method="post" id="purchase_return_form" class="validated-form no-auto-submit" action="{{ route('purchase-return.save') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="transaction_id" value="{{$purchase->id}}">

        <div class="row">
          <div class="col-lg-4 form-group">
            <label for="">{{ trans('app.location') }} : {{ $purchase->warehouse->location }}</label><br>
            <label for="">{{ trans('app.supplier') }} : {{ $purchase->client->supplier_business_name }} ({{ $purchase->client->name }})</label><br>
          </div>
          <div class="col-lg-4 form-group">
            <label for="">{{ trans('app.invoice_id') }} : {{ $purchase->invoice_no }}</label><br>
            <label for="">{{ trans('app.purchase_date') }} : {{ date('d-m-Y', strtotime($purchase->transaction_date)) }}</label>
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
                <label for="invoice_id" class="control-label">
                  {{ trans('app.invoice_id') }}
                </label>
                <input type="text" name="invoice_id" id="invoice_id" class="form-control" value="{{ old('invoice_id', $invoice_no) }}"placeholder="{{ trans('app.invoice_id') }}">
              </div>
            </div>
            <div class="table-responsive">
              <table id="purchase_return_table" class="table table-bordered table-hover">
                <thead>
                  <tr class="bg-success text-white">
                    <th>{{ trans('app.no_sign') }}</th>
                    <th>{{ trans('app.name') }}</th>
                    <th>{{ trans('app.unit_price') }}</th>
                    <th class="text-center">{{ trans('app.purchase_quantity') }}</th>
                    <th class="text-center">{{ trans('app.quantity_remaining') }}</th>
                    <th class="text-right">{{ trans('app.return_quantity') }}</th>
                    <th class="text-right">{{ trans('app.return_sub_total') }}</th>
                  </tr>
                </thead>
                <tbody>
                  {{-- When form validation has error (s) --}}
                  @if($purchase->purchase_lines)
                  @foreach ($purchase->purchase_lines as $index => $purchase_line)
                    @php
                      $indexId = $purchase_line->id;
                      $qty_available = $purchase_line->quantity - $purchase_line->quantity_sold - $purchase_line->quantity_adjusted;
                    @endphp
                    <tr>

                      <th>{{ ($index+1) }}</th>
                      <td>{{ $purchase_line->product->name.($purchase_line->variations->name != "DUMMY" ? '-'.$purchase_line->variations->name : '') }}</td>
                
                      <td width="15%" class="text-center">
                        <input type="hidden" class="form-control form-control-sm decimal-input purchase_price" value="{{ decimalNumber($purchase_line->purchase_price, true) }}" readonly>{{ decimalNumber($purchase_line->purchase_price, true) }}
                      </td>
                      <td width="15%" class="text-center">
                        <input type="hidden" min="1" max="10000" class="form-control form-control-sm integer-input quantity" value="{{ $purchase_line->quantity }}">{{ $purchase_line->quantity }}
                      </td>
                      <td width="15%" class="text-center">
                        {{ $qty_available }}
                      </td>
                      <td width="15%" class="text-center">
                        <input type="text" name="returns[{{$purchase_line->id}}]" min="1" max="{{ $qty_available }}" class="form-control form-control-sm integer-input return_quantity" value="{{ decimalNumber($purchase_line->quantity_returned,true) }}">
                      </td>
                      <td width="15%" class="text-right">
                        <span class="sub-total">{{ decimalNumber($purchase_line->purchase_price * $purchase_line->quantity_returned, true) }}</span>
                      </td>
                    </tr>
                  @endforeach
                @endif
                </tbody>
                <tfoot>
                  <tr class="bg-light">
                    <td colspan="5" align="right"><b>{{ trans('app.grand_total') }}</b></td>
                    <td colspan="2">
                      <span class="shown_total_price">0.00</span>
                      <input type="hidden" name="total_price" class="total_price" value="{{ $purchase->total_before_tax ?? 0 }}">
                    </td>
                  </tr>
                  <tr class="bg-light">
                    <td colspan="5" align="right"><b>{{ trans('app.balance') }}</b></td>
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
        {{-- Button save or edit --}}
        <div class="row">
          <div class="col-lg-12 text-right">
            @include('partial/button-save', ['onClick' => 'confirmFormSubmission($("#purchase_return_form"))'])
          </div>
        </div>
      </form>
    </div>
  </main>
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
  $(document).ready(function() {
    calculateTotal();
  })
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
<script src="{{ asset('js/purchase-return.js') }}"></script>
@endsection
