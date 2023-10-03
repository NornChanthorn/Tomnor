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
    <!-- {{PaymentScheduleType::EQUAL_PAYMENT}} -->
    <h3 class="page-heading">{{ trans('app.loan') . ' - ' . $title }}</h3>
    @include('partial/flash-message')
    <form id="loan-form" method="post" class="no-auto-submit" action="{{ route('loan.save', $loan) }}">
      @csrf
      <input type="hidden" name="form_type" value="{{ $formType }}">
      @isset($loan->id)
      <input type="hidden" name="id" value="{{ $loan->id }}">
      @endisset

      {{-- Loan info --}}
      <div class="row">
        <fieldset class="col-lg-12">
          <legend>
            <h5>{{ trans('app.loan_information') }}</h5>
          </legend>
          <div class="row">
            @if (isAdmin() || empty(auth()->user()->staff))
            {{-- Branch --}}
            <div class="col-lg-4 form-group">
              <label for="branch" class="control-label">
                {{ trans('app.branch') }} {!! $requiredFormType !!}
              </label>
              @if ($isFormShowType)
              <input type="text" class="form-control" value="{{ $loan->branch->location }}" disabled>
              @else
              <select name="branch" id="branch" class="form-control select2" required {{ $disabledFormType }}>
                <option value="">{{ trans('app.select_option') }}</option>
                @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" {{ selectedOption($branch->id, old('branch'), $loan->branch_id) }}>
                  {{ $branch->location }}
                </option>
                @endforeach
              </select>
              @endif
            </div>
            @endif

            {{-- Account number --}}
            <div class="col-lg-8 form-group">
              <label for="account_number_append" class="control-label">
                {{ trans('app.account_number') }} <span class="required">*</span>
              </label>
              <div class="input-group">
                {{-- Loan code auto-generated --}}
                <input type="text" name="account_number" id="account_number" class="form-control"
                  value="{{ $loan->account_number ?? '' }}" placeholder="{{ trans('app.loan_code') }}" disabled>
                {{-- Wing code --}}
                {{-- <input type="text" name="wing_code" id="wing_code" class="form-control" value="{{ old('wing_code') ?? $loan->wing_code }}"
                required placeholder="{{ trans('app.wing_code') . ' *' }}" {{ $disabledFormType }}> --}}
                <input type="hidden" name="wing_code" id="wing_code" value="N/A">
                {{-- Client code --}}
                <input type="text" name="client_code" id="client_code" class="form-control"
                  value="{{ old('client_code') ?? $loan->client_code }}" required
                  placeholder="{{ trans('app.reference_code') . ' *' }}" {{ $disabledFormType }}>
              </div>
            </div>

            @if (isAdmin() || empty(auth()->user()->staff))
            {{-- Agent --}}
            <div class="col-lg-4 form-group">
              <label for="agent" class="control-label">
                {{ trans('app.agent') }} {!! $requiredFormType !!}
              </label>
              @if ($isFormShowType)
              <input type="text" class="form-control" value="{{ $loan->staff->name }}" disabled>
              @else
              <select name="agent" id="agent" class="form-control select2" required {{ $disabledFormType }}>
                <option value="">{{ trans('app.select_option') }}</option>
                @foreach ($agents as $agent)
                <option value="{{ $agent->user_id }}" {{ selectedOption($agent->user_id, old('agent'), $loan->staff_id) }}>
                  {{ $agent->name }}
                </option>
                @endforeach
              </select>
              @endif
            </div>
            @else
            <input type="hidden" name="branch" value="{{ auth()->user()->staff->branch_id }}">
            @endif

            {{-- Client --}}
            <div class="col-lg-4 form-group">
              <label for="client" class="control-label">
                {{ trans('app.client') }} {!! $requiredFormType !!}
              </label>
              @if ($isFormShowType)
              <input type="text" class="form-control" value="{{ $loan->client->name }}" disabled>
              @else
              <select name="client" id="client" class="form-control select2" required {{ $disabledFormType }}>
                <option value="">{{ trans('app.select_option') }}</option>
                @if($formType == FormType::EDIT_TYPE)
                <option value="{{ $loan->client_id }}" selected>{{ $loan->client->name }}</option>
                @endif
                {{-- @foreach ($clients as $client)
                        <option value="{{ $client->id }}"
                {{ selectedOption($client->id, old('client'), $loan->client_id) }}>
                {{ $client->name }}
                </option>
                @endforeach --}}
              </select>
              @endif
            </div>
            <div class="col-lg-12 form-group mt-4">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="allow_multi_loan"
                  {{ ($loan->allow_multi_loan==1 || old('allow_multi_loan')==1) ? "checked" : '' }} value="1"
                  class="custom-control-input" id="allow_multi_loan">
                <label class="custom-control-label" for="allow_multi_loan">{{ __('app.allow_multi_loan') }}</label>
              </div>
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
                  @if($isFormShowType)
                  <input type="text" class="form-control" id="product" placeholder="{{ __('app.enter-product') }}"
                    disabled>
                  @else
                  <input type="text" class="form-control" id="product" placeholder="{{ __('app.enter-product') }}"
                    {{ old('branch', !empty(auth()->user()->staff) ? auth()->user()->staff->branch_id : null)=='' ? 'disabled' : '' }}>
                  @endif
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
                        @foreach ($loan->productDetails as $item)
                        @php
                        $indexId = $item->product_id.$item->variantion_id;
                        @endphp

                        <tr data-id="{{ $indexId }}">
                          <input type="hidden" name="products[{{ $indexId }}][transaction_sell_lines_id]"
                            value="{{$item->id}}">
                          <input type="hidden" name="products[{{ $indexId }}][id]" value="{{@$item->product_id}}">
                          <input type="hidden" name="products[{{ $indexId }}][name]" value="{{@$item->product->name}}">
                          <input type="hidden" name="products[{{ $indexId }}][code]" value="{{@$item->product->code}}">
                          <input type="hidden" name="products[{{ $indexId }}][variantion_id]"
                            value="{{$item->variantion_id}}">
                          <input type="hidden" name="products[{{ $indexId }}][enable_stock]"
                            value="{{@$item->product->enable_stock}}">
                          <td>
                            {{ @$item->product->name }}{{ @$item->variantion->name!='DUMMY' ? ' - '.$item->variantion->name : '' }}
                            @if ($loan->transaction_id)
                              <a class="btn btn-sm btn-success" href="{{ route('product.ime-create',[
                                'transaction_id'=>$loan->transaction_id,
                                'location_id'=>$loan->branch_id,
                                'product_id'=>$item->product_id,
                                'variantion_id'=>$item->variantion_id,
                                'qty'=> $item->qty,
                                'purchase_sell_id'=>$loan->id,
                                'type'=>'loan'
                                ]) }}">{{ trans('app.product_ime') }}</a>
                            @endif
                          </td>
                          <td>{{@$item->product->code ?? trans('app.none')}}</td>
                          @php
                              $pro_id = $item->product_id;
                              $va_id = $item->variantion_id;
                              $lo_id = $loan->branch_id;
                              $qty_available= App\Models\VariantionLocationDetails::where('location_id',$lo_id)->where('variantion_id',$va_id)->where('product_id', $pro_id)->first()->qty_available;
                          @endphp
                            <td>{{ decimalNumber($qty_available) }}</td>
                          <td width="15%">
                            <input type="text" name="products[{{ $indexId }}][quantity]"
                              class="form-control form-control-sm integer-input quantity" min="1" required
                              value="{{$item->qty}}" max="{{ $qty_available }}" readonly>
                          </td>
                          <td width="15%">
                            <input type="text" name="products[{{ $indexId }}][price]"
                              class="form-control form-control-sm decimal-input unit_price" min="1" required
                              value="{{$item->unit_price}}" readonly>
                          </td>
                          <td width="15%">
                            <input type="text" name="products[{{ $indexId }}][sub_total]"
                              class="form-control form-control-sm decimal-input sub_total" min="1" required
                              value="{{$item->qty * $item->unit_price}}" readonly>
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
                        {{-- <tr>
                              <td colspan="5" align="right"><b>{{ trans('app.discount') }} ($)</b></td>
                        <td colspan="2"><input type="text" name="discount"
                            class="form-control form-control-sm decimal-input discount" placeholder="0.00"></td>
                        </tr>
                        <tr>
                          <td colspan="5" align="right"><b>{{ trans('app.other_service') }} ($)</b></td>
                          <td colspan="2"><input type="text" name="other_service"
                              class="form-control form-control-sm decimal-input other_service" placeholder="0.00"></td>
                        </tr> --}}
                        <tr>
                          <td colspan="5" align="right"><b>{{ trans('app.balance') }}</b></td>
                          <td colspan="2"><span class="shown_balance_amount"></span></td>
                          <input type="hidden" name="balance_amount" class="balance_amount" value="0">
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>

                {{-- Product price --}}
                <div class="col-lg-4 form-group">
                  <label for="product_price" class="control-label">
                    {{ trans('app.product_price') }} ($)
                  </label>
                  <input type="text" name="product_price" id="product_price" class="form-control currency-input"
                    value="{{ old('product_price') ?? $loan->product_price }}" {{ $disabledFormType }}>
                </div>

                {{-- Product IME --}}
                {{-- <div class="col-lg-4 form-group">
                  <label for="product_ime" class="control-label">
                    {{ trans('app.product_ime') }} <span class="required"></span>
                  </label>
                  <input type="text" name="product_ime" id="product_ime" class="form-control"
                    value="{{ old('product_ime') ?? $loan->product_ime }}" {{ $disabledFormType }}>
                </div> --}}

                {{-- Note --}}
                <div class="col-lg-4 form-group">
                  <label for="note" class="control-label">
                    {{ trans('app.icloud') }}
                  </label>
                  <input type="text" name="note" id="note" class="form-control" value="{{ old('note') ?? $loan->note }}"
                    {{ $disabledFormType }}>
                  {{--<textarea name="note" id="note" class="form-control" {{ $disabledFormType }} style="min-height:
                  50px;">{{ $loan->note ?? old('note') }}</textarea>--}}
                </div>
              </div>
            </div>
          </div>
        </fieldset>
      </div>
      <br>

      {{-- Payment info --}}
      <div class="row">
        <fieldset class="col-lg-12">
          <legend>
            <h5>{{ trans('app.payment_information') }}</h5>
          </legend>
          <div class="row">
            {{-- Payment schedule type --}}
            <div class="col-lg-4 form-group">
              <label for="schedule_type" class="control-label">
                {{ trans('app.payment_schedule_type') }} {!! $requiredFormType !!}
              </label>
              <select name="schedule_type" id="schedule_type" class="form-control select2 select2-no-search" required
                {{ $disabledFormType }}>
                <!-- <option value="{{ PaymentScheduleType::EQUAL_PAYMENT }}">
                  {{ trans('app.equal_payment') }}
                </option> -->
                <option value="{{PaymentScheduleType::DECLINE_INTEREST}}">
                  {{ trans('app.down_interest_payment') }}
                </option>
                <!-- <option value="{{PaymentScheduleType::FLAT_INTEREST}}">
                  {{ trans('app.flat_interest') }}
                </option> -->
                {{--<option value="">{{ trans('app.select_option') }}</option>
                @foreach (paymentScheduleTypes() as $typeKey => $typeTitle)
                <option value="{{ $typeKey }}"
                  {{ !is_null(old('schedule_type')) ? (old('schedule_type') == $typeKey ? 'selected' : '') : ($loan->schedule_type == $typeKey ? 'selected' : '') }}>
                  {{ $typeTitle }}
                </option>

                @endforeach--}}
              </select>
            </div>

            <!-- {{-- Loan amount --}}
            <div class="col-lg-4 form-group">
              <label for="loan_amount" class="control-label">
                {{ trans('app.loan_amount') }} ($) {!! $requiredFormType !!}
              </label>
              <input type="text" name="loan_amount" id="loan_amount" class="form-control decimal-input" required
                value="{{ old('loan_amount') ?? $loan->loan_amount }}" readonly>
            </div> -->
            {{-- Loan amount --}}
            <div class="col-lg-4 form-group">
              <label for="loan_amount" class="control-label">
                Loan amount ($) {!! $requiredFormType !!}
              </label>
              <input type="text" name="loan_amount" id="loan_amount" class="form-control decimal-input" required
                value="{{ old('loan_amount') ?? $loan->loan_amount }}" readonly>
            </div>

            <!-- {{-- Depreciation amount --}}
            <div class="col-lg-4 form-group">
              <label for="depreciation_amount" class="control-label">
                {{ trans('app.depreciation_amount') }} ($) {!! $requiredFormType !!}
              </label>
              <input type="text" name="depreciation_amount" id="depreciation_amount" class="form-control decimal-input"
                value="{{ old('depreciation_amount') ?? $loan->depreciation_amount }}" required {{ $disabledFormType }}>
            </div> -->
            {{-- Depreciation amount --}}
            <div class="col-lg-4 form-group">
              <label for="depreciation_amount" class="control-label">
                Depreciation amount ($) {!! $requiredFormType !!}
              </label>
              <input type="text" name="depreciation_amount" id="depreciation_amount" class="form-control decimal-input"
                value="{{ old('depreciation_amount') ?? $loan->depreciation_amount }}" required {{ $disabledFormType }}>
            </div>
          </div>
          <div class="row">
            {{-- Payment Method --}}
            <div class="col-lg-4 form-group">
              <label for="payment_method" class="control-label">
                {{ trans('app.payment_method') }} <span class="required">*</span>
              </label>
              <select name="payment_method" id="payment_method" class="form-control select2 select2-no-search" required {{ $disabledFormType }}>
                @foreach (paymentMethods() as $methodKey => $methodValue)
                  <option value="{{ $methodKey }}" {{ $methodKey == $loan->payment_method ? 'selected' : '' }} {{ $loan->payment_method ?? ($methodKey='dp'?'selected':'')  }} >
                    {{ $methodValue }}
                  </option>
                @endforeach
              </select>
            </div>
            <!-- {{-- Down payment amount --}}
            <div class="col-lg-4 form-group">
              <label for="down_payment_amount" class="control-label">
                {{ trans('app.down_payment_amount') }} ($)
              </label>
              <input type="text" name="down_payment_amount" id="down_payment_amount" class="form-control decimal-input"
                value="{{ old('down_payment_amount') ?? $loan->down_payment_amount }}" readonly {{ $disabledFormType }}>
            </div> -->
            {{-- Down payment amount --}}
            <div class="col-lg-4 form-group">
              <label for="down_payment_amount" class="control-label">
                Down payment amount ($)
              </label>
              <input type="text" name="down_payment_amount" id="down_payment_amount" class="form-control decimal-input"
                value="{{ old('down_payment_amount') ?? $loan->down_payment_amount }}" readonly {{ $disabledFormType }}>
            </div>
            
            {{-- Depreciation Percentage --}}
            <div class="col-lg-4 form-group">
              <label for="depreciation_percentage" class="depreciation_percentage">
                {{ trans('app.down_payment_amount') }} (%)
              </label>
              <input type="text" name="depreciation_percentage" id="depreciation_percentage" class="form-control decimal-input"
                value="{{ old('depreciation_percentage') ?? $loan->depreciation_percentage }}" readonly {{ $disabledFormType }}>
            </div>

            {{-- Interest rate --}}
            <div class="col-lg-4 form-group">
              <label for="interest_rate" class="control-label">
                <span id="rate_text">{{ trans('app.interest_rate') }}</span> (%)
                <span id="rate_sign" class="required"></span>
              </label>
              <input type="text" name="interest_rate" id="interest_rate" class="form-control decimal-input"
                value="{{ old('interest_rate') ?? $loan->interest_rate }}" required min="0" {{ $disabledFormType }}>
            </div>

            {{-- Installment --}}
            <div class="col-lg-4 form-group">
              <label for="installment" class="control-label">
                {{ trans('app.installment') }} {!! $requiredFormType !!}
              </label>
              <input type="text" name="installment" id="installment" class="form-control integer-input"
                value="{{ old('installment') ?? $loan->installment }}" required {{ $disabledFormType }}>
            </div>
          </div>
          <div class="row">
            {{-- Payment frequency --}}
            <div class="col-lg-4 form-group">
              <label for="payment_per_month" class="control-label">
                {{ trans('app.number_payment_per_month') }} {!! $requiredFormType !!}
              </label>
              <select name="payment_per_month" id="payment_per_month" class="form-control" required disabled>
                <option value="1">{{ trans('app.once') }}</option>
                <option value="2"
                  {{ $loan->payment_per_month == 2 || old('payment_per_month') == 2 ? 'selected' : '' }}>
                  {{ trans('app.twice') }}
                </option>
              </select>
              <input type="hidden" name="payment_per_month" value="1">
            </div>

            {{-- Loan start date --}}
            <div class="col-lg-4 form-group">
              <label for="loan_start_date" class="control-label">
                {{ trans('app.loan_start_date') }} {!! $requiredFormType !!}
              </label>
              <input type="text" name="loan_start_date" id="loan_start_date" class="form-control date-picker"
                placeholder="{{ trans('app.date_placeholder') }}" required {{ $disabledFormType }}
                value="{{ old('loan_start_date', displayDate($loan->loan_start_date ?? date('d-m-Y')))   }}">
            </div>

            {{-- First payment date --}}
            <div class="col-lg-4 form-group">
              <label for="first_payment_date" class="control-label">
                {{ trans('app.first_payment_date') }}
              </label>
              <input type="text" name="first_payment_date" id="first_payment_date" class="form-control date-picker"
                placeholder="{{ trans('app.date_placeholder') }}" {{ $disabledFormType }}
                value="{{ old('first_payment_date') ?? displayDate($loan->first_payment_date ?? oneMonthIncrement(date('Y-m-d'))) }}">
            </div>
          </div>

          <div class="row" {{ $isFormShowType ? 'style=display:none;' : '' }}>
            {{-- Message to display when there is error in data validation --}}
            <div class="col-lg-12 text-center">
              <h6 id="error-msg" class="text-danger"></h6>
            </div>

            {{-- Calculate payment schedule button --}}
            <div class="col-lg-12 text-center">
              <button type="button" id="calculate-payment" class="btn btn-info">
                {{ trans('app.calculate_payment_schedule') }}
              </button>
            </div>
          </div>
          <br>

          {{-- Payment schedule table --}}
          <div class="row">
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
      <br>

      {{-- Misc buttons --}}
      <div class="row">
        <div class="col-lg-12 text-center">
          @if ($isFormShowType)
          {{-- Pending loan --}}
          @if ($loan->status == LoanStatus::PENDING)
          {{-- Reject loan --}}
          <button type="button" id="reject_loan" class="btn btn-danger mb-1"
            data-url="{{ route('loan.change_status', [$loan->id, LoanStatus::REJECTED]) }}">
            <i class="fa fa-times pr-1"></i> {{ trans('app.cancel') }}
          </button>

          {{-- Approve loan --}}
          <button type="button" id="approve_loan" class="btn btn-success mb-1"
            data-url="{{ route('loan.change_status', [$loan->id, LoanStatus::ACTIVE]) }}">
            <i class="fa fa-check pr-1"></i> {{ trans('app.approve') }}
          </button>
          @endif
          @if ($loan->status == LoanStatus::ACTIVE)
            {{-- Show Sale --}}
            <a class="btn btn-success" href="{{ route('sale.show', $loan->transaction->id) }}">
                <i class="fa fa-eye"></i> {{ __('app.view_sell_detail') }}
            </a>

          @endif
          @if(Auth::user()->can('loan.delete') && !isPaidLoan($loan->id))
          {{-- Delete loan --}}
          <button type="button" id="delete_loan" class="btn btn-danger btn-delete mb-1"
            data-url="{{ route('loan.destroy', $loan->id) }}">
            <i class="fa fa-trash-o"></i> {{ trans('app.delete') }}
          </button>
          @endif

          @if (Auth::user()->can('loan.edit') && !isPaidLoan($loan->id))
          <a href="{{ route('loan.edit', $loan->id) }}" class="btn btn-primary mb-1">
            <i class="fa fa-pencil-square-o pr-1"></i> {{ trans('app.edit') }}
          </a>
          @endif

          {{-- @if (Auth::user()->can('loan.edit') && ($loan->status==LoanStatus::ACTIVE && $loan->disbursed_date==NULL))
                  <a href="javascript:void(0);" id="disbursed_loan" data-url="{{ route('loan.disburse', $loan->id) }}"
          class="btn btn-dark {{ $loan->disbursed_date!=NULL ? 'disabled' : '' }} mb-1">
          <i class="fa fa-shopping-cart pr-1"></i> {{ trans('app.disburse') }}
          </a>
          @endif --}}

          {{-- Print contract --}}
          @if (Auth::user()->can('loan.print') && in_array($loan->status, [LoanStatus::ACTIVE, LoanStatus::PAID]))
          <a class="btn btn-success mb-1" target="_blank" href="{{ route('loan.print_contract', $loan) }}">
            <i class="fa fa-print pr-1"></i> {{ trans('app.print_contract') }}
          </a>
          @endif
          @else
          @include('partial/button-save')
          @endif
        </div>
      </div>
    </form>
  </div>
</main>
@endsection

@section('js')
<script>
    var count = "{{ $loan->count }}";
    var formType = '{{ $formType }}';
    var codeLabel = '{{ trans('app.code') }}';
    var noneLabel = '{{ trans('app.none') }}';
    var formShowType = '{{ FormType::SHOW_TYPE }}';
    var equalPaymentSchedule = '{{ PaymentScheduleType::EQUAL_PAYMENT }}';
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

    // When change branch
    var agentSelectLabel = emptyOptionElm;
    var agentRetrievalUrl = '{{ route('staff.get_agents', ':branchId') }}';
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
<script src="{{ asset('js/loan.js') }}"></script>
<script src="{{ asset('plugins/easyAutocomplete/jquery.easy-autocomplete.js') }}"></script>
<script>
  $('#print').click(function(){
    var divToPrint=document.getElementById("print-table");
    newWin  = window.open('', '', 'height=800,width=800');
    newWin.document.write('<html><head><title>{{ trans('app.calculate_payment_schedule') }}</title><link rel="stylesheet" href="{{ asset('css/main.css') }}"> <style>@media print {body { width: 21cm; height: 29.7cm;}} </style></head><body><div class="container"><h5 class="mt-4 text-center">{{ trans('app.calculate_payment_schedule') }}</h5>',divToPrint.outerHTML,'<button class="ml-3 btn btn-success" id="print" onclick="window.print();">{{ trans('app.print') }}</button>','</div></body></html>');

    // newWin.print();
    // newWin.close();
  });
  $(document).ready(function() {
      $(".currency-input").on('keypress keyup blur', function(event) {
        // event.preventDefault();

        var key = window.event ? event.keyCode : event.which;
        if(event.keyCode === 8 || event.keyCode == 46) {
          return true;
        } else if(key < 48 || key > 57) {
          return false;
        } else {
          return true;
        }
      });

      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });

      $("#branch").change(function(e) {
        e.preventDefault();
        if($(this).val() != '') {
          $("#product").attr('disabled', false);
        }
        else {
          $("#product").attr('disabled', true);
        }
      });

      // $("#branch").on('change', function(e) {
      //   e.preventDefault();
      //   let branchId = $(this).find(':selected').data('code');

      //   $("#product").attr('disabled', ($(this).val()=='' ? true : false));
      // });

      $("#client").select2({
        ajax: {
          url: "{{ route('client.list') }}",
          dataType: 'json',
          data: function(params) {
            return {
              search: params.term,
              type: 'public',
            }
          },
          processResults: function(data) {
            return {
              results: data
            }
          }
        }
      });
      if('{{ $loan->client_id }}'!=""){
        var $value = '{{ $loan->client_id }}';
        var $account_number = $('#account_number').val();
        var  $tmp = $account_number.split('/');

        $('#account_number').val($tmp[0] + '/' +("000000" + $value).slice(-6));
      }

      $("#client").on('change', function(){
        $value = $(this).val();
        $account_number = $('#account_number').val();
        $tmp = $account_number.split('/');

        $('#account_number').val($tmp[0] + '/' +("000000" + $value).slice(-6));
        //$('#account_number').val($tmp[0] + '/' + zeroPad($value, 6));
        // console.log('>value', $value);
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
          resp.type = 'sale';
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
    });
</script>
@endsection
