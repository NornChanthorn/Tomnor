@extends('layouts/backend')

@section('title', trans('app.sell-return'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.sell-return') }}</h3>
      @include('partial/flash-message')

      <form method="post" id="sell_return_form" class="validated-form no-auto-submit" action="{{ route('sell-return.save') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="transaction_id" value="{{$sell->id}}">

        <div class="row">
          <div class="col-lg-4 form-group">
            <label for="">{{ trans('app.location') }} : {{ $sell->warehouse->location }}</label><br>
            <label for="">{{ trans('app.supplier') }} : {{ $sell->client->supplier_business_name }} ({{ $sell->client->name }})</label><br>
          </div>
          <div class="col-lg-4 form-group">
            <label for="">{{ trans('app.invoice_id') }} : {{ $sell->invoice_no }}</label><br>
            <label for="">{{ trans('app.sell_date') }} : {{ date('d-m-Y', strtotime($sell->transaction_date)) }}</label>
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
              <div class="col-lg-4 form-group">
                <label for="invoice_id" class="control-label">
                  {{ trans('app.date') }}
                </label>
                <input type="text" name="return_date" id="return_date" class="form-control datepicker" required placeholder="{{ trans('app.date_placeholder') }}" value="{{ date('d-m-Y') }}">
              </div>
            </div>
            <div class="table-responsive">
              <table id="sell_return_table" class="table table-bordered table-hover">
                <thead>
                  <tr class="bg-success text-white">
                    <th>{{ trans('app.no_sign') }}</th>
                    <th>{{ trans('app.name') }}</th>
                    <th>{{ trans('app.unit_price') }}</th>
                    <th class="text-center">{{ trans('app.sell_quantity') }}</th>
                    <th class="text-center">{{ trans('app.quantity_remaining') }}</th>
                    <th class="text-right">{{ trans('app.return_quantity') }}</th>
                    <th class="text-right">{{ trans('app.return_sub_total') }}</th>
                  </tr>
                </thead>
                <tbody>
                  {{-- When form validation has error (s) --}}
                  @if($sell->sell_lines)
                  @foreach ($sell->sell_lines as $index => $sell_line)
                    @php
                      $indexId = $sell_line->id;
                      $qty_available = $sell_line->quantity - $sell_line->quantity_sold - $sell_line->quantity_adjusted;
                    @endphp
                    <tr>

                      <th>{{ ($index+1) }}</th>
                      <td>{{ $sell_line->product->name.($sell_line->variations->name != "DUMMY" ? '-'.$sell_line->variations->name : '') }}</td>
                
                      <td width="15%" class="text-center">
                        <input type="hidden" class="form-control form-control-sm decimal-input sell_price" value="{{ decimalNumber($sell_line->unit_price, true) }}" readonly>{{ decimalNumber($sell_line->unit_price, true) }}
                      </td>
                      <td width="15%" class="text-center">
                        <input type="hidden" min="1" max="10000" class="form-control form-control-sm integer-input quantity" value="{{ $sell_line->quantity }}">{{ $sell_line->quantity }}
                      </td>
                      <td width="15%" class="text-center">
                        {{ $qty_available }}
                      </td>
                      <td width="15%" class="text-center">
                        <input type="text" name="returns[{{$sell_line->id}}]" class="form-control form-control-sm integer-input return_quantity" value="{{ decimalNumber($sell_line->quantity_returned,true) }}">
                      </td>
                      <td width="15%" class="text-right">
                        <span class="sub-total">{{ decimalNumber($sell_line->unit_price * $sell_line->quantity_returned, true) }}</span>
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
                      <input type="hidden" name="total_price" class="total_price" value="{{ $sell->total_before_tax ?? 0 }}">
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
            @include('partial/button-save', ['onClick' => 'confirmFormSubmission($("#sell_return_form"))'])
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
    $(".datepicker").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
    });
  });

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
<script src="{{ asset('js/sell-return.js') }}"></script>
@endsection
