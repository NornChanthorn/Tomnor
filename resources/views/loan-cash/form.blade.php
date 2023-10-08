@extends('layouts/backend')

@section('title', trans('app.loan_cash'))

@section('content')
<main class="app-content">
    <div class="card">
        <div class="card-header">
            <h4 class="title">
                {{ $title }}
            </h4>
        </div>
        <div class="card-body">
            @include('partial/flash-message')
            <form id="loan-form" method="post" class="no-auto-submit" action="{{ route('loan-cash.save', $loan) }}">
              @csrf

              <input type="hidden" name="form_type" value="{{ $formType }}">
              @isset($loan->id)
                <input type="hidden" name="id" value="{{ $loan->id }}">
              @endisset

              {{-- Loan info --}}
              <div class="row">
                <fieldset class="col-lg-12">
                    <div class="row">
                        @if (isAdmin() || empty(auth()->user()->staff))
                            {{-- Branch --}}
                            <div class="col-lg-4 form-group">
                                <label for="branch" class="control-label">
                                    {{ trans('app.branch') }}
                                    <span class="required">*</span>
                                </label>
                                <select name="branch_id" id="branch" class="form-control select2" required >
                                    <option value="">{{ trans('app.select_option') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ selectedOption($branch->id, old('branch'), $loan->branch_id) }}>
                                                {{ $branch->location }}
                                            </option>
                                        @endforeach
                                </select>
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
                                value="{{ $loan->account_number ?? nextLoanAccNum() }}" placeholder="{{ trans('app.loan_code') }}" disabled>
                                {{-- Client code --}}
                                <input type="text" name="client_code" id="client_code" class="form-control"
                                value="{{ old('client_code') ?? $loan->client_code }}" required
                                placeholder="{{ trans('app.reference_code') . ' *' }}" >
                            </div>
                        </div>

                        @if (isAdmin() || empty(auth()->user()->staff))
                            {{-- Agent --}}
                            <div class="col-lg-4 form-group">
                                <label for="agent" class="control-label">
                                    {{ trans('app.agent') }} <span class="required">*</span>
                                </label>
                                <select name="agent" id="agent" class="form-control select2" required >
                                    <option value="">{{ trans('app.select_option') }}</option>
                                    @foreach ($agents as $agent)
                                        <option value="{{ $agent->user_id }}" {{ selectedOption($agent->id, old('agent'), $loan->staff_id) }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" name="branch_id" value="{{ auth()->user()->staff->branch_id }}">
                        @endif

                        {{-- Client --}}
                        <div class="col-lg-4 form-group">
                            <label for="client" class="control-label">
                                {{ trans('app.client') }} <span class="required">*</span>
                            </label>
                            <select name="client_id" id="client" class="form-control select2" required>
                                <option value="">{{ trans('app.select_option') }}</option>
                                @if($formType == FormType::EDIT_TYPE)
                                    <option value="{{ $loan->client_id }}" selected>{{ $loan->client->name }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                </fieldset>
              </div>
              <br>

              {{-- Payment info --}}
              <div class="row">
                <fieldset class="col-lg-12">
                    <h5>{{ trans('app.loan_information') }}</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                {{-- Payment schedule type --}}
                                <div class="col-lg-6 form-group">
                                    <label for="schedule_type" class="control-label">
                                        {{ trans('app.payment_schedule_type') }} <span class="required">*</span>
                                    </label>
                                    <select name="schedule_type" id="schedule_type" class="form-control select2 select2-no-search" required>
                                        <option value="{{ PaymentScheduleType::AMORTIZATION }}">
                                            {{ trans('app.equal_payment') }}
                                        </option>
                                    </select>
                                </div>

                                {{-- Loan amount --}}
                                <div class="col-lg-6 form-group">
                                    <label for="loan_amount" class="control-label">
                                        {{ trans('app.loan_amount') }} ($) <span class="required">*</span>
                                    </label>
                                    <input type="text" name="loan_amount" id="loan_amount" class="form-control decimal-input" required
                                        value="{{ old('loan_amount',$loan->loan_amount) ?? 0  }}" required>
                                </div>

                                {{-- Interest rate --}}
                                <div class="col-lg-6 form-group">
                                    <label for="interest_rate" class="control-label">
                                        <span id="rate_text">{{ trans('app.interest_rate') }}</span> (%)
                                        <span id="rate_sign" class="required"></span>
                                    </label>
                                    <input type="text" name="interest_rate" id="interest_rate" class="form-control decimal-input"
                                        value="{{ old('interest_rate',$loan->interest_rate) ?? 0  }}" required min="0">
                                </div>

                                {{-- Installment --}}
                                <div class="col-lg-6 form-group">
                                    <label for="installment" class="control-label">
                                        {{ trans('app.installment') }} <span class="required">*</span>
                                    </label>
                                    <input type="text" class="form-control integer-input" name="installment" value="{{ old('installment',$loan->installment) ?? 1  }}" min="1">
                                    {{-- <select name="installment" id="installment" class="form-control" required>
                                        <option value="">{{ __('app.select_option') }}</option>
                                        @foreach (installments() as $inkey => $inval)
                                                <option value="{{ $inkey }}" {{ selectedOption($inkey, old('installment'), $loan->installment) }}>{{ numKhmer(no_f($inval)) }} {{ __('app.times') }}</option>
                                        @endforeach
                                    </select> --}}
                                </div>
                                {{-- Payment frequency --}}
                                <div class="col-lg-6 form-group">
                                    <label for="frequency" class="control-label">
                                        {{ trans('app.frequency') }} <span class="required">*</span>
                                    </label>
                                    <select name="frequency" id="frequency" class="form-control" required>
                                        <option value="">{{ __('app.select_option') }}</option>
                                        @foreach (frequencies() as $fkey => $fval)
                                                <option value="{{ $fkey }}" {{ selectedOption( $fkey , old('frequency'), $loan->frequency) }}>{{ numKhmer(no_f($fval)) }} {{ __('app.day') }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Loan start date --}}
                                <div class="col-lg-6 form-group">
                                    <label for="loan_start_date" class="control-label">
                                        {{ trans('app.loan_start_date') }} <span class="required">*</span>
                                    </label>
                                    <input type="text" name="loan_start_date" id="loan_start_date" class="form-control date-picker"
                                        placeholder="{{ trans('app.date_placeholder') }}" required
                                        value="{{ old('loan_start_date') ?? displayDate($loan->loan_start_date ??  date('d-m-Y')) }}">
                                </div>

                                {{-- First payment date --}}
                                <div class="col-lg-6 form-group">
                                    <label for="first_payment_date" class="control-label">
                                        {{ trans('app.first_payment_date') }}
                                    </label>
                                    <input type="text" name="first_payment_date" id="first_payment_date" class="form-control"
                                        placeholder="{{ trans('app.date_placeholder') }}"
                                        value="{{ old('first_payment_date') ?? displayDate($loan->first_payment_date ?? oneMonthIncrement(date('Y-m-d'))) }}" readonly>
                                    <input type="hidden" name="first_payment_date" id="first_payment_date" class="form-control date-picker"
                                        value="{{ old('first_payment_date') ?? displayDate($loan->first_payment_date ?? oneMonthIncrement(date('Y-m-d'))) }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="" class="control-label">{{ __('app.note') }}</label>
                            <textarea class="form-control" name="note" id="" cols="30" rows="15">
                                {!! $loan->note !!}
                            </textarea>
                        </div>
                    </div>

                </fieldset>
              </div>

              {{-- Misc buttons --}}
              <div class="row">
                <div class="col-lg-12">
                  @include('partial/button-save')
                </div>
              </div>
            </form>
        </div>
    </div>
</main>
@endsection
@section('js')
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

<script>
    var agentSelectLabel = emptyOptionElm;
    var agentRetrievalUrl = '{{ route('staff.get_agents', ':branchId') }}';
    $(document).ready(function() {
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
        $("#frequency, #loan_start_date").on('change', function(){
            var day  = $('#frequency').val();
            var now = $('#loan_start_date').val();
            var newdate= formatDate(now,day);
            $('#first_payment_date').val(newdate);
        });
    });
</script>
<script src="{{ asset('js/agent-retrieval.js') }}"></script>
@endsection

