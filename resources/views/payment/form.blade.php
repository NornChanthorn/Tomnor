@extends('layouts/backend')
@section('title', $title)
@section('css')
    <link rel="stylesheet" href="{{ asset('css/bootstrap4-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-fileinput.css') }}">
@endsection
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ $title  }}</h3>
            @include('partial/flash-message')
            <form method="post" id="payment-form" class="no-auto-submit" action="{{ route('repayment.save', $loan->id) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="repay_type" value="{{ $repayType }}">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>{{ trans('app.client_information') }}</h5>
                        
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <p>{{ trans('app.client_name') }} : @include('partial.client-detail-link', ['client' => $loan->client])</p>
                                <p>{{ trans('app.client_code') }} : {{ $loan->client_code }}</p>
                                <p>{{ trans('app.loan_code') }} : {{ $loan->account_number }}</p>
                            </div>
                            <div class="col-md-4">
                                <p>{{ trans('app.first_phone') }} : {{ $loan->client->first_phone }}</p>
                                <p>{{ trans('app.second_phone') }} : {{ $loan->second_phone }}</p>
                                <p>{{ trans('app.id_card_number') }} : {{ $loan->client->id_card_number }}</p>
                            </div>
                            <div class="col-md-4">
                                <img src="{{ asset($loan->client->profile_photo) }}" width="50%" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                         {{-- Payment schedule --}}
                         <h5>{{ trans('app.payment_schedule') }}</h5>
                         <br>
                         @php $isFlatInterestSchedule = ($loan->schedule_type == PaymentScheduleType::FLAT_INTEREST) @endphp
                         <div class="table-responsive">
                             <table class="table table-bordered table-hover table-striped">
                                 <thead>
                                     <tr>
                                         @if ($repayType == RepayType::ADVANCE_PAY)
                                             <th width="10%">{{ trans('app.advance_pay') }}</th>
                                         @endif
                                         <th>{{ trans('app.payment_date') }}</th>
                                         @if ($isFlatInterestSchedule)
                                             <th>{{ trans('app.payment_amount') }}</th>
                                         @else
                                             <th>{{ trans('app.total') }}</th>
                                             <th>{{ trans('app.principal') }}</th>
                                             <th>{{ trans('app.interest') }}</th>
                                         @endif
                                         <th>{{ trans('app.outstanding') }}</th>
                                         <th>{{ trans('app.paid_date') }}</th>
                                         <th>{{ trans('app.paid_principal') }}</th>
                                         <th>{{ trans('app.paid_interest') }}</th>
                                         <th>{{ trans('app.penalty_amount') }}</th>
                                         <th>{{ trans('app.paid_amount') }}</th>
                                         <th>{{ trans('app.action') }}</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     @foreach ($loan->schedules as $schedule)
                                         @php $decimalNumber = ($schedule->interest == 0 ? 2 : 0) @endphp
                                         <tr>
                                             @if ($repayType == RepayType::ADVANCE_PAY)
                                                 <td>
                                                     @if ($schedule->paid_interest == null || $schedule->paid_interest == 0)
                                                         <div class="custom-control custom-checkbox text-center">
                                                             <input type="checkbox" name="schedules[]" id="schedule{{ $schedule->id }}"
                                                                    class="custom-control-input schedule" data-principal="{{ $schedule->principal }}"
                                                                    data-schedule-id="{{ $schedule->id }}">
                                                             <label for="schedule{{ $schedule->id }}" class="custom-control-label"></label>
                                                         </div>
                                                     @endif
                                                 </td>
                                             @endif
                                             <td>{{ displayDate($schedule->payment_date) }}</td>
                                             @if ($isFlatInterestSchedule)
                                                 <td>$ {{ decimalNumber($schedule->principal, $decimalNumber) }}</td>
                                             @else
                                                 <td>$ {{ decimalNumber($schedule->total, $decimalNumber) }}</td>
                                                 <td>$ {{ decimalNumber($schedule->principal, $decimalNumber) }}</td>
                                                 <td>$ {{ decimalNumber($schedule->interest, $decimalNumber) }}</td>
                                             @endif
                                             <td>$ {{ decimalNumber($schedule->outstanding) }}</td>
                                             <td>{{ displayDate($schedule->paid_date) }}</td>
                                             <td>{{ $schedule->paid_principal ? '$ ' . decimalNumber($schedule->paid_principal, $decimalNumber) : '' }}</td>
                                             <td>{{ $schedule->paid_interest ? '$ ' . decimalNumber($schedule->paid_interest, $decimalNumber) : '' }}</td>
                                             <td>{{ $schedule->paid_penalty ? '$ ' . decimalNumber($schedule->paid_penalty, $decimalNumber) : '' }}</td>
                                             <td>{{ $schedule->paid_total ? '$ ' . decimalNumber($schedule->paid_total, $decimalNumber) : '' }}</td>
                                             <td>
                                                 @if (isAdmin() || Auth::user()->can('loan.edit-schedule'))
                                                     <a href="{{ route('loan.edit_payment_schedule',$schedule) }}" class="btn btn-sm btn-primary" title="{{ trans('app.edit') }}">
                                                         <i class="fa fa-edit"></i>
                                                     </a>
                                                 @endif
                                             </td>
                                         </tr>
                                     @endforeach
                                 </tbody>
                             </table>
                         </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>{{ trans('app.payment_method') }}</h5>
                        <br>
                        @if ($repayType==1)
                            <div class="row">
                                <div class="col-lg-4 form-group">
                                    <label for="payment_date" class="control-label">
                                        {{ trans('app.paid_date') }} <span class="required">*</span>
                                    </label>
                                    <input type="text" name="payment_date" id="date-picker" class="form-control"
                                        value="{{ old('payment_date') ?? date('d-m-Y') }}" placeholder="{{ trans('app.date_placeholder') }}" required>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label for="payment_amount" class="control-label">
                                        {{ trans('app.payment_amount') }} ($) <span class="required">*</span>
                                    </label>
                                    <input type="text" name="payment_amount" id="payment_amount" class="form-control decimal-input"
                                        value="{{ $remainingAmount ?? old('payment_amount') }}" required {{ Config::get('app.remain_payment')==true ? "readonly":""}}>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label for="payment_method" class="control-label">
                                        {{ trans('app.payment_method') }} <span class="required">*</span>
                                    </label>
                                    <select name="payment_method" id="payment_method" class="form-control select2 select2-no-search" required>
                                        @foreach (paymentMethods() as $methodKey => $methodValue)
                                            <option value="{{ $methodKey }}" {{ $methodKey == (old('payment_method') ?? 'dp') ? 'selected' : '' }}>
                                                {{ $methodValue }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label for="penalty_amount" class="control-label">
                                        {{ trans('app.penalty_amount') }} ($)
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">{{ decimalNumber($penaltyAmount) }}</span>
                                        </div>
                                        <input type="text" name="penalty_amount" id="penalty_amount" class="form-control decimal-input"
                                            value="{{ old('penalty_amount') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label for="reference_number" class="control-label">
                                        {{ trans('app.reference_number') }}
                                    </label>
                                    <input type="text" name="reference_number" id="reference_number" class="form-control"
                                        value="{{ old('reference_number') }}">
                                </div>
                                <div class="col-lg-6 form-group">
                                    <label for="note" class="control-label">
                                        {{ trans('app.note') }}
                                    </label>
                                    <textarea name="note" id="note" class="form-control" rows="16">{{ old('note') }}</textarea>
                                </div>
                                <div class="col-lg-6 form-group">
                                    <label for="photo" class="control-label">
                                    {{ trans('app.document') }}
                                    </label>
                                    <input type="file" name="receipt_photo" id="photo" class="form-control" accept=".jpg, .jpeg, .png">
                                </div>
                                <div class="col-lg-12 text-right">
                                    <button type="submit" class="btn btn-success" onclick="confirmFormSubmission($('#payment-form'))">
                                        {{ $repayLabel }}
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label for="">{{ trans('app.principal') }}</label>
                                            <input type="text" class="form-control decimal-input" name="principal" id="principal" value="{{ $payoffPrincipal }}" readonly>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="">{{ trans('app.interest') }}</label>
                                            <input type="text" class="form-control decimal-input" name="interest" id="interest" value="{{ $payoffInterest }}">
                                        </div>
                                     
                                        <div class="col-md-4 form-group">
                                            <label for="">{{ trans('app.discount') }}{{ trans('app.interest') }} %</label>
                                            <input type="text" class="form-control decimal-input" name="discount_interest" id="discount_interest" value="0">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="">{{ trans('app.interest_after_discount') }}</label>
                                            <input type="text" class="form-control decimal-input" name="interest_after_discount" id="interest_after_discount" value="{{ $payoffInterest  }}" readonly>
                                        </div>
                                      
                                       
                                        <div class="col-md-4 form-group">
                                            <label for="">{{ trans('app.penalty_amount') }}</label>
                                            <input type="text" class="form-control decimal-input" name="penalty_amount" id="penalty_amount" value="0">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="">{{ trans('app.wave') }}</label>
                                            <input type="text" class="form-control decimal-input" name="wave" id="wave" value="0">
                                        </div>
                                        <div class="col-lg-4 form-group">
                                            <label for="payment_amount" class="control-label">
                                                {{ trans('app.payment_amount') }} ($) <span class="required">*</span>
                                            </label>
                                            <input type="text" name="payment_amount" id="payment_amount" class="form-control decimal-input"
                                                value="{{ $payoffInterest +  $payoffPrincipal ?? old('payment_amount') }}" required
                                                {{ in_array($repayType, [RepayType::PAYOFF, RepayType::ADVANCE_PAY]) ? 'readonly' : '' }}>
                                        </div>
                                        <div class="col-lg-4 form-group">
                                            <label for="payment_date" class="control-label">
                                                {{ trans('app.paid_date') }} <span class="required">*</span>
                                            </label>
                                            <input type="text" name="payment_date" id="payment_date" class="form-control"
                                                value="{{ old('payment_date') ?? date('d-m-Y') }}" placeholder="{{ trans('app.date_placeholder') }}" required>
                                        </div>

                                        <div class="col-lg-4 form-group">
                                            <label for="payment_method" class="control-label">
                                                {{ trans('app.payment_method') }} <span class="required">*</span>
                                            </label>
                                            <select name="payment_method" id="payment_method" class="form-control select2 select2-no-search" required>
                                                @foreach (paymentMethods() as $methodKey => $methodValue)
                                                    <option value="{{ $methodKey }}" {{ $methodKey == (old('payment_method') ?? 'dp') ? 'selected' : '' }}>
                                                        {{ $methodValue }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-4 form-group">
                                            <label for="reference_number" class="control-label">
                                                {{ trans('app.reference_number') }}
                                            </label>
                                            <input type="text" name="reference_number" id="reference_number" class="form-control" value="{{ old('reference_number') }}">
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="note" class="control-label">
                                                {{ trans('app.note') }}
                                            </label>
                                            <input type="text" class="form-control" name="note" id="note" value="{{ old('note') }}">
                                        </div>
                                    </div>
                                   
                                </div>
                                <div class="col-md-4 form-group">
                                    <label for="photo" class="control-label">
                                        {{ trans('app.document') }}
                                    </label>
                                    <input type="file" name="receipt_photo" id="photo" class="form-control" accept=".jpg, .jpeg, .png">
                                </div>
                                
                                <div class="col-lg-12 text-right">
                                    <button type="submit" class="btn btn-success" onclick="confirmFormSubmission($('#payment-form'))">
                                        {{ $repayLabel }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
            </form>
            <div class="card mb-4">
                <div class="card-body">
                    <h5>{{ trans('app.payment_received') }}</h5>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        {{ trans('app.no_sign') }}
                                    </th>
                                    <th>
                                        {{ trans('app.reference_number') }}
                                    </th>
                                    <th>
                                        {{ trans('app.payment_method') }}
                                    </th>
                                    <th>
                                        {{ trans('app.payment_date') }}
                                    </th>
                        
                                    <th>
                                        {{ trans('app.payment_amount') }}
                                    </th>
                                    <th style="width: 20%">
                                        {{ trans('app.note') }}
                                    </th>
                                    <th style="width: 5%">
                                        {{ trans('app.document') }}
                                    </th>
                                    <th>
                                        {{ trans('app.action') }}
                                    </th>
                                </tr>

                            </thead>
                            <tbody>

                                @foreach ($loan->payments as $key =>  $item)
                                    <tr>
                                        <td>
                                            {{ $key+1 }}
                                        </td>
                                        <td>
                                            {{ $item->invoice_number }}
                                        </td>
                                        <td>
                                            {{ paymentMethods($item->payment_method) }}
                                        </td>
                                        <td>
                                            {{ displayDate($item->payment_date) }}
                                        </td>
                                        <td>
                                            $ {{ decimalNumber($item->total) }}
                                        </td>
                                        <td>
                                            {!! $item->note !!}
                                        </td>
                                        <td>
                                            @if ($item->document)
                                                <img src="{{ asset($item->document) }}" class="img-fluid" alt="">
                                            @endif
                                        </td>
                                        <td>
                                            @if(isAdmin())
                                                <a href="#" class="btn btn-sm btn-primary mb-1 btn-modal" title="{{ trans('app.edit') }}" data-href="{{ route('payments.editPaymentDate',$item) }}" data-container=".payment-date-modal">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div class="modal fade payment-date-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>
@endsection
@section('js')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/select-box.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-fileinput.js') }}"></script>
    <script src="{{ asset('js/bootstrap-fileinput-fa-theme.js') }}"></script>
    <script src="{{ asset('/js/init-file-input.js') }}"></script>
    <script src="{{ asset('js/bootstrap4-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
    <script src="{{ asset('js/date-time-picker.js') }}"></script>
    <script src="{{ asset('js/jquery-number.min.js') }}"></script>
    <script src="{{ asset('js/number.js') }}"></script>
    <script src="{{ asset('js/mask.js') }}"></script>
    <script src="{{ asset('js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/repayment.js') }}"></script>
    <script>
        var repayment_type = "{{ $repayType }}";
    </script>
@endsection
