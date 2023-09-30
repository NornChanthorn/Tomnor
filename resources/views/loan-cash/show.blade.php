@extends('layouts/backend')

@section('title', trans('app.loan'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.loan_detail') }}
        <div class="pull-right">

                           
            @if (Auth::user()->can('loan-cash.reject') && $loan->status == LoanStatus::PENDING)
                {{-- Reject loan --}}
                <button type="button" id="reject_loan" class="btn btn-danger mb-1"
                    data-url="{{ route('loan-cash.change_status', [$loan->id, LoanStatus::REJECTED]) }}">
                    <i class="fa fa-times pr-1"></i> {{ trans('app.cancel') }}
                </button>
            @endif
            @if (Auth::user()->can('loan-cash.approval') && $loan->status == LoanStatus::PENDING)
                {{-- Approve loan --}}
                <button type="button" id="approve_loan" class="btn btn-success mb-1"
                    data-url="{{ route('loan-cash.change_status', [$loan->id, LoanStatus::ACTIVE]) }}">
                    <i class="fa fa-check pr-1"></i> {{ trans('app.approve') }}
                </button>
            @endif
            @if(isAdmin() || Auth::user()->can('loan-cash.delete') && !isPaidLoan($loan->id))
                {{-- Delete loan --}}
                <button type="button" id="delete_loan" class="btn btn-danger btn-delete mb-1"
                    data-url="{{ route('loan.destroy', $loan->id) }}" data-redirect="{{ route('loan-cash.index') }}">
                    <i class="fa fa-trash-o"></i> {{ trans('app.delete') }}
                </button>
            @endif

            @if (Auth::user()->can('loan-cash.edit') && $loan->status==LoanStatus::PENDING)
                <a href="{{ route('loan-cash.edit', $loan->id) }}" class="btn btn-primary mb-1">
                    <i class="fa fa-pencil-square-o pr-1"></i> {{ trans('app.edit') }}
                </a>
            @endif
            @if (isAdmin() &&  $loan->status == LoanStatus::ACTIVE || Auth::user()->can('loan.pay') && $loan->status == LoanStatus::ACTIVE)
                {{-- Simple repayment --}}
                <a href="{{ route('repayment.show', [$loan->id, RepayType::REPAY]) }}" class="btn btn-success mb-1">
                    <i class="fa fa-money"></i>  {{ trans('app.repay') }}
                </a>

                {{-- Payoff --}}
                <a href="{{ route('repayment.show', [$loan->id, RepayType::PAYOFF]) }}" class="btn btn-success mb-1">
                    <i class="fa fa-money"></i> {{ trans('app.pay_off') }}
                </a>
            @endif
        </div>
    </h3>
    
    @include('partial/flash-message')
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <p>
                        {{ trans('app.client_name') }} : <br>
                        <strong> <a href="{{ route('client.show',$loan->client_id) }}">{{ $loan->client->name }}   </a>   </strong>
                    </p>
                    <p>
                        {{ trans('app.address') }} <br>
                        <strong>
                            {{ @$loan->client->address ?? @$loan->client->location }}
                        </strong>
                    </p>

                    <p>
                        {{ __('app.contact') }} <br>
                        <strong>
                            {{ @$loan->client->first_phone }}{{ @$loan->client->second_phone ? ', '.@$loan->client->second_phone : '' }}
                        </strong>
                    </p>
                    
                </div>
                <div class="col-md-2">
                    @if (@$loan->client->profile_photo)
                        <img src="{{ asset($loan->client->profile_photo) }}" width="50%"  class="img-fluid">
                    @endif

                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-6">
                                    <p class="text-right">
                                        {{ trans('app.reference_number') }}
                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            {{ $loan->client_code }}
                                        </strong>
                                    </p>
                                
                                </div>
                                <div class="col-6 text-right">
                                    <p>
                                        {{ trans('app.account_number') }}
                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            {{ $loan->account_number }} / {{ str_pad($loan->client_id,6, 0, STR_PAD_LEFT) }}
                                        </strong>
                                    </p>
                                
                                </div>

                                <div class="col-6 text-right">
                                    <p>
                                        {{ trans('app.loan_amount') }}
                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            {{ num_f($loan->loan_amount) }}
                                        </strong>
                                    </p>
                                
                                </div>
                                
                                <div class="col-6 text-right">
                                    <p>
                                        {{ trans('app.installment') }}
                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            {{ numKhmer($loan->installment) }} {{ __('app.times') }}
                                        </strong>
                                    </p>
                                
                                </div>
                                <div class="col-6 text-right">
                                    <p>
                                        {{ trans('app.frequency') }}
                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            {{ numKhmer($loan->frequency) }} {{ __('app.day') }}
                                        </strong>
                                    </p>
                                
                                </div>
                                <div class="col-6 text-right">
                                    <p>
                                        {{ trans('app.interest') }}
                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            {{ numKhmer($loan->interest_rate) }} % / {{ __('app.day') }}
                                        </strong>
                                    </p>
                                
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">

                                <div class="col-6 text-right">
                                    <p>
                                        {{ trans('app.loan_start_date') }}
                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            {{ displayDate($loan->loan_start_date) }}
                                        </strong>
                                    </p>
                                
                                </div>
                                <div class="col-6 text-right">
                                    <p>
                                        {{ trans('app.first_payment_date') }}
                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            {{ displayDate($loan->first_payment_date) }}
                                        </strong>
                                    </p>
                                
                                </div>
                                <div class="col-6 text-right">
                                    <p>
                                        {{ trans('app.disbursement_date') }}
                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            {{ displayDate($loan->approved_date) }}
                                        </strong>
                                    </p>
                                
                                </div>
                                <div class="col-6 text-right">
                                    <p>
                                        {{ __('app.created_date') }}
                                    </p>
                                    
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            {{ displayDate($loan->created_at) }}
                                        </strong>
                                    </p>
                                
                                </div>
                                <div class="col-6 text-right">
                                    <p>
                                        {{ __('app.sale_agency') }} 
                                    </p>
                            
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp;
                                        <strong>
                                            {{ @$loan->staff->name }}
                                        </strong>
                                    
                                    </p>
                                
                                </div>
                                <div class="col-6 text-right">
                                    <p>
                                        {{ __('app.status') }} 
                                    </p>
                            
                                </div>
                                <div class="col-6">
                                    <p>
                                        : &nbsp; @include('partial.loan-status-label')
                                    
                                    </p>
                                
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                <a class="nav-link @if (request()->get('get')=='account-details' || request()->get('get')=='')active @endif" href="{{ route('loan-cash.show',[$loan,'get'=>'account-details']) }}">{{ __('app.account') }} {{ __('app.detail') }}</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link @if (request()->get('get')=='repayment-schedule')active @endif" href="{{ route('loan-cash.show',[$loan,'get'=>'repayment-schedule']) }}">{{ __('app.payment_schedule') }}</a>
                </li>
                @if ($loan->scheduleReferences->count()>0 && isAdmin() || $loan->scheduleReferences->count()> 0 && Auth::user()->can('loan-cash.delay-schedule'))
                    <li class="nav-item" role="presentation">
                        <a class="nav-link @if (request()->get('get')=='delay-schedule')active @endif" href="{{ route('loan-cash.show',[$loan,'get'=>'delay-schedule']) }}">{{ __('app.delay_schedule') }}</a>
                    </li>
                @endif
                @if (isAdmin() || Auth::user()->can('collateral.browse'))
                    <li class="nav-item" role="presentation">
                        <a class="nav-link @if (request()->get('get')=='collaterals')active @endif" href="{{ route('loan-cash.show',[$loan,'get'=>'collaterals']) }}">{{ __('app.collateral') }}</a>
                    </li>
                @endif
                
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content">
                @if (request()->get('get')=='account-details' || request()->get('get')=='')
                    @include('loan-cash.partials.account-details') 
                @endif
                @if (request()->get('get')=='repayment-schedule')
                    @include('loan-cash.partials.repayment-schedule') 
                @endif
                @if (request()->get('get')=='delay-schedule' && $loan->scheduleReferences->count()>0)
                    @include('loan-cash.partials.delay') 
                @endif
                @if (Auth::user()->can('collateral.browse') &&  request()->get('get')=='collaterals')
                    @include('loan-cash.partials.collateral') 
                @endif
                
            </div>
        </div>
    </div>
  </div>
</main>
<div class="modal fade schedule_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>
@endsection
@section('js')
<script>
    
    $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE',$(this).data('redirect'));
    });
    // Reject loan
    $('#reject_loan').click(function () {
        confirmPopup($(this).data('url'), 'warning');
    });

    // Approve loan
    $('#approve_loan').click(function () {
        confirmPopup($(this).data('url'), 'success');
    });

    $("#disbursed_loan").click(function() {
        let url = $(this).data('url');

        swal(defaultSwalOptions('success'), function(isConfirmed) {
        if(isConfirmed) {
            window.location.href = url;
        }
        });
    });

</script>
@endsection
