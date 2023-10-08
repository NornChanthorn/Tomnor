@extends('layouts/backend')

@section('title', trans('app.loan'))

@section('css')
<link rel="stylesheet" href="{{ asset('css/bootstrap4-datetimepicker.min.css') }}">
<link rel="stylesheet" href="{{asset('plugins/easyAutocomplete/easy-autocomplete.min.css')}}">
<style>
  .tabulator-print-header, tabulator-print-footer{
    text-align:center;
  }
</style>
@endsection

@section('content')
@php
$isFormShowType = ($formType == FormType::SHOW_TYPE);
$disabledFormType = ($isFormShowType ? 'disabled' : '');
$requiredFormType = ($formType != FormType::SHOW_TYPE ? '<span class="required">*</span>' : '');
@endphp

<main class="app-content">
  <div class="tile">
   <h1>Calculation loan</h1>
    <h3 class="page-heading">{{ trans('app.calculate_loan') }}</h3>
    @include('partial/flash-message')
    <form id="calculator-form" action="{{ route('loan.get_payment_schedule') }}">
      @csrf
      {{-- Payment info --}}
      <div class="row">
          <fieldset class="col-lg-12">
            <legend>
              <h5>{{ trans('app.payment_information') }}</h5>
            </legend>
            <div class="row">
              <div class="col-lg-4 form-group">
                  <label for="schedule_type" class="control-label">
                    {{ trans('app.payment_schedule_type') }} {!! $requiredFormType !!}
                  </label>
                  <select name="schedule_type" id="schedule_type" class="form-control select2 select2-no-search" required
                    {{ $disabledFormType }} disabled>
                    @foreach (paymentScheduleTypes() as $key => $paymentScheduleType)
                      <option value="{{ $key }}">
                        {{ $paymentScheduleType }}
                      </option>
                    @endforeach

                  </select>
              </div>

              <div class="col-lg-4 form-group">
                  <label for="loan_amount" class="control-label">
                    {{ trans('app.loan_amount') }} ($) {!! $requiredFormType !!}
                  </label>
                  <input type="text" name="loan_amount" id="loan_amount" class="form-control decimal-input" required
                    value="{{ old('loan_amount') ?? 0 }}">
              </div>
              {{-- Depreciation amount --}}
              <div class="col-lg-4 form-group">
                <label for="depreciation_amount" class="control-label">
                  {{ trans('app.depreciation_amount') }} ($)
                </label>
                <input type="text" name="depreciation_amount" id="depreciation_amount" class="form-control decimal-input"
                  value="{{ old('depreciation_amount') ?? 0}}" required >
              </div>
              {{-- Down payment amount --}}
              <div class="col-lg-4 form-group">
                <label for="down_payment_amount" class="control-label">
                  {{ trans('app.down_payment_amount') ?? 0 }} ($)
                </label>
                <input type="text" name="down_payment_amount" id="down_payment_amount" class="form-control decimal-input"
                  value="{{ old('down_payment_amount') ?? 0 }}" readonly disabled>
              </div>
               {{-- test --}}
              <div class="col-lg-4 form-group">
                <label for="depreciation_percentaget" class="control-label">
                  {{ trans('app.depreciation_percentage') ?? 0 }} ($)
                </label>
                <input type="text" name="depreciation_percentage" id="depreciation_percentage" class="form-control decimal-input"
                  value="{{ old('depreciation_percentage') ?? 0 }}" readonly disabled>
              </div>

              {{-- Down payment amount --}}
              {{-- <div class="col-lg-4 form-group">
                <label for="depreciation_amount" class="control-label">
                  {{ trans('app.down_payment_amount') ?? 0 }} ($)
                </label>
                <input type="text" name="depreciation_amount" id="depreciation_amount" class="form-control decimal-input"
                  value="{{ old('depreciation_amount') ?? 0 }}" readonly disabled>
              </div> --}}

              {{-- Interest rate --}}
              <div class="col-lg-4 form-group">
                <label for="interest_rate" class="control-label">
                  <span id="rate_text">{{ trans('app.interest_rate') }}</span> (%)
                  <span id="rate_sign" class="required"></span>
                </label>
                <input type="text" name="interest_rate" id="interest_rate" class="form-control decimal-input"
                  value="{{ old('interest_rate') ?? 0 }}" required min="0" >
              </div>

              {{-- Installment --}}
              <div class="col-lg-4 form-group">
                <label for="installment" class="control-label">
                  {{ trans('app.installment') }}
                </label>
                <input type="text" name="installment" id="installment" class="form-control integer-input"
                  value="{{ old('installment') }}" required >
              </div>
              <div class="col-lg-4 form-group">
                  <label for="payment_per_month" class="control-label">
                    {{ trans('app.number_payment_per_month') }} {!! $requiredFormType !!}
                  </label>
                  <select name="payment_per_month" id="payment_per_month" class="form-control" required disabled>
                    <option value="1">{{ trans('app.once') }}</option>
                    <option value="2"
                      {{ old('payment_per_month') == 2 ? 'selected' : '' }}>
                      {{ trans('app.twice') }}
                    </option>
                  </select>
                  <input type="hidden" name="payment_per_month" id="payment_per_month"  value="1">
              </div>
              {{-- Loan start date --}}
              <div class="col-lg-4 form-group">
                <label for="loan_start_date" class="control-label">
                  {{ trans('app.loan_start_date') }}
                </label>
                <input type="text" name="loan_start_date" id="loan_start_date" class="form-control date-picker"
                  placeholder="{{ trans('app.date_placeholder') }}" required
                  value="{{ old('loan_start_date') ?? date('d-m-Y') }}">
              </div>

              {{-- First payment date --}}
              <div class="col-lg-4 form-group">
                <label for="first_payment_date" class="control-label">
                  {{ trans('app.first_payment_date') }}
                </label>
                <input type="text" name="first_payment_date" id="first_payment_date" class="form-control date-picker"
                  placeholder="{{ trans('app.date_placeholder') }}"
                  value="{{ old('first_payment_date') ?? date('d-m-Y',strtotime("+30 days")) }}">
              </div>
            </div>

            <div class="row" {{ $isFormShowType ? 'style=display:none;' : '' }}>
              {{-- Message to display when there is error in data validation --}}
              <div class="col-lg-12 text-center">
                <h6 id="error-msg" class="text-danger"></h6>
              </div>

              {{-- Calculate payment schedule button --}}
              <div class="col-lg-12 text-center">
                <button type="button calculate_payment_schedule" type="submit" id="calculate-payment" class="btn btn-info">
                  {{ trans('app.') }}
                </button>
              </div>
            </div>
            <br>

            {{-- Payment schedule table --}}
            <div class="row">
                <h1>Schedule</h1>
              <div class="col-lg-12 table-responsive" id="print-table">
                <table style="display: none;" id="schedule-table" class="table table-bordered table-hover table-striped">
                </table>
              </div>
              <div class="col-lg-12">
                <button type="button" style="display: none;" id="print" class="btn btn-info">
                  {{ trans('app.print') }}
                </button>
              </div>

            </div>
          </fieldset>
        </div>
      </form>
  </div>
</main>
@endsection

@section('js')
<script>
    var formType = '{{ $formType }}';
    var codeLabel = '{{ trans('app.code') }}';
    var noneLabel = '{{ trans('app.none') }}';
    var formShowType = '{{ FormType::SHOW_TYPE }}';
    var equalPaymentSchedule = '{{ PaymentScheduleType::AMORTIZATION }}';
    var flatInterestSchedule = '{{ PaymentScheduleType::FLAT_INTEREST }}';
    var declineInterestSchedule = '{{ PaymentScheduleType::DECLINE_INTEREST }}';
    var scheduleRetrievalUrl = '{{ route('loan.get_payment_schedule') }}';

    var loanRateLabel = '{{ trans('app.loan_rate') }}';
    var interestRateLabel = '{{ trans('app.interest_rate') }}';
    var noLabel = '{{ trans('app.no_sign') }}';
    var paymentDateLabel = '{{ trans('app.payment_date') }}';
    var paymentAmountLabel = '{{ trans('app.payment_amount') }}';
    var totalLabel = '{{ trans('app.total') }}';
    var principalLabel = '{{ trans('app.principal') }}';
    var interestLabel = '{{ trans('app.interest') }}';
    var outstandingLabel = '{{ trans('app.outstanding') }}';
</script>
<script src="{{ asset('plugins/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('js/tinymce.js') }}"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="{{ asset('js/select-box.js') }}"></script>
<script src="{{ asset('js/bootstrap4-datetimepicker.min.js') }}"></script>
<script src="{{ asset('js/jquery-mask.min.js') }}"></script>
<script src="{{ asset('js/date-time-picker.js') }}"></script>
<script src="{{ asset('js/jquery-number.min.js') }}"></script>
<script src="{{ asset('js/number.js') }}"></script>
<script src="{{ asset('js/agent-retrieval.js') }}"></script>
<script src="{{ asset('js/calculator.js') }}"></script>
<script>
  $('#print').click(function(){
    var divToPrint=document.getElementById("print-table");
    newWin  = window.open('', '', 'height=800,width=800');
    newWin.document.write('<html><head><title>{{ trans('app.calculate_payment_schedule') }}</title><link rel="stylesheet" href="{{ asset('css/main.css') }}"> <style>@media print {body { width: 21cm; height: 29.7cm;}} </style></head><body><div class="container"><h5 class="mt-4 text-center">{{ trans('app.calculate_payment_schedule') }}</h5>',divToPrint.outerHTML,'<button class="ml-3 btn btn-success" id="print" onclick="window.print();">{{ trans('app.print') }}</button>','</div></body></html>');

    // newWin.print();
    // newWin.close();
  });
</script>
@endsection
