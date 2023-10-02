@extends('layouts/backend')

@section('title', trans('app.loan'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.loan_detail') }}</h3>
    @include('partial/flash-message')
    @php
        $isFormShowType = ($formType == FormType::SHOW_TYPE);
        $disabledFormType = ($isFormShowType ? 'disabled' : '');
        $requiredFormType = ($formType != FormType::SHOW_TYPE ? '<span class="required">*</span>' : '');
    @endphp
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8 col-lg-7">
                    <h5>
                        <strong>
                            {{ $loan->client->name }}
                        </strong>
                    </h5>

                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                {{ trans('app.address') }} <br>
                                <strong>
                                    {{ @$loan->client->address ?? @$loan->client->location }}
                                </strong>
                            </p>
                            <p>
                                {{ trans('app.account_number') }} <br>
                                <strong>
                                    {{ $loan->account_number }} / {{ str_pad($loan->client_id,6, 0, STR_PAD_LEFT) }}
                                </strong>
                            </p>
                            <p>
                                {{ trans('app.reference_number') }} <br>
                                <strong>
                                    {{ $loan->client_code }}
                                </strong>
                            </p>
                        </div>
                        <div class="col-md-6">

                            <p>
                                {{ __('app.contact') }} <br>
                                <strong>
                                    {{ @$loan->client->first_phone }}{{ @$loan->client->second_phone ? ', '.@$loan->client->second_phone : '' }}
                                </strong>
                            </p>
                        </div>

                    </div>
                    @if ($loan->client->sponsor_name)
                        <h5>
                            <strong>
                                {{ __('app.sponsor_information') }}
                            </strong>
                        </h5>
                        <div class="row">
                            <div class="col-md-6">

                                <h5>
                                    <strong>
                                        {{ $loan->client->sponsor_name }}
                                    </strong>
                                </h5>
                                <p>
                                    {{ trans('app.address') }} <br>
                                    <strong>
                                        {{ $loan->client->slocation }}
                                    </strong>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p>
                                    {{ trans('app.contact') }} <br>
                                    <strong>
                                        {{ $loan->client->sponsor_phone }}{{ $loan->client->sponsor_phone_2 ? ', '. $loan->client->sponsor_phone_2 : '' }}
                                    </strong>
                                </p>
                            </div>
                        </div>
                    @endif

                </div>
                <div class="col-md-4 col-lg-5">
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="pull-right">


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
                                @if(isAdmin() || Auth::user()->can('loan.delete') && !isPaidLoan($loan->id))
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
                                @if (isAdmin() &&  $loan->status == LoanStatus::ACTIVE || Auth::user()->can('loan.pay') && $loan->status == LoanStatus::ACTIVE)
                                    {{-- Pay Drepreciation --}}
                                    
                                    <a href="{{ route('payments.paydepreciation', [$loan->id, RepayType::PAY_DEPRECIATION])}}" class="btn btn-success mb-1">
                                        <i class="fa fa-money"></i>  {{ trans('app.pay_depreciation') }}
                                    </a>
                                    {{-- Simple repayment --}}
                                    <a href="{{ route('repayment.show', [$loan->id, RepayType::REPAY]) }}" class="btn btn-success mb-1">
                                        <i class="fa fa-money"></i>  {{ trans('app.repay') }}
                                    </a>

                                    {{-- Payoff --}}
                                    <a href="{{ route('repayment.show', [$loan->id, RepayType::PAYOFF]) }}" class="btn btn-success mb-1">
                                        <i class="fa fa-money"></i> {{ trans('app.pay_off') }}
                                    </a>
                                @endif
                                {{-- Print contract --}}
                                @if (Auth::user()->can('loan.print') && in_array($loan->status, [LoanStatus::ACTIVE, LoanStatus::PAID]))
                                    <a class="btn btn-success mb-1" target="_blank" href="{{ route('loan.print_contract', $loan) }}">
                                        <i class="fa fa-print pr-1"></i> {{ trans('app.print_contract') }}
                                    </a>
                                @endif
                                @if ($loan->status=='ac')
                                    <a href="javascript::void(0);" class="btn btn-primary mb-1 btn-modal" title="{{ trans('app.delay_schedule') }}" data-href="{{ route('loan.delaySchedule', $loan) }}" data-container=".schedule_modal">
                                        {{ __('app.delay_schedule') }}
                                    </a>
                                @endif

                            </div>
                        </div>

                    </div>

                    <div class="row">
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
                                    {{ $loan->staff->name }}
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
    <div class="card mb-4">
        <div class="card-body table-responsive">
            <table class="table table-head-fixed text-nowrap">
                <thead>
                    <tr>
                        <th>{{ __('app.no_sign') }}</th>
                        <th>{{ trans('app.name') }}</th>
                        <th>{{ trans('app.code') }}</th>
                        <th>{{ trans('app.sale_quantity') }}</th>
                        <th>{{ trans('app.unit_price') }}</th>
                        <th>{{ trans('app.sub_total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($loan->transaction))
                        @foreach ($loan->transaction->sell_lines as $key =>  $item)
                            <tr>
                                <td>
                                    {{ no_f($key+1) }}
                                </td>
                                <td>
                                    @include('partial.product-detail-link', ['product' => @$item->product, 'variantion' => @$item->variations->name])

                                    <a class="btn btn-sm btn-success" href="{{ route('product.ime-create',[
                                        'transaction_id'=>$loan->transaction_id,
                                        'location_id'=>$loan->branch_id,
                                        'product_id'=>$item->product_id,
                                        'variantion_id'=>$item->variantion_id,
                                        'qty'=> $item->quantity,
                                        'purchase_sell_id'=>$item->id,
                                        'type'=>'loan'
                                        ]) }}">{{ trans('app.product_ime') }}</a>
                                </td>
                                <td>{{@$item->product->code ?? trans('app.none')}}</td>
                                <td>
                                    {{no_f($item->quantity)}}
                                </td>
                                <td>
                                    {{num_f($item->unit_price)}}
                                </td>
                                <td>
                                    {{num_f($item->quantity * $item->unit_price)}}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        @foreach ($loan->productDetails as $key =>  $item)
                            <tr>
                                <td>
                                    {{ no_f($key+1) }}
                                </td>
                                <td>
                                    @include('partial.product-detail-link', ['product' => @$item->product, 'variantion' => $item->variantion->name])
                                </td>
                                <td>{{@$item->product->code ?? trans('app.none')}}</td>
                                <td>
                                    {{no_f($item->qty)}}
                                </td>
                                <td>
                                    {{num_f($item->unit_price)}}
                                </td>
                                <td>
                                    {{num_f($item->qty * $item->unit_price)}}
                                </td>
                            </tr>
                        @endforeach
                    @endif


                </tbody>
            </table>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                <a class="nav-link @if (request()->get('get')=='account-details' || request()->get('get')=='')active @endif" href="{{ route('loan.show',[$loan,'get'=>'account-details']) }}">{{ __('app.account') }} {{ __('app.detail') }}</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link @if (request()->get('get')=='repayment-schedule')active @endif" href="{{ route('loan.show',[$loan,'get'=>'repayment-schedule']) }}">{{ __('app.payment_schedule') }}</a>
                </li>
                @if ($loan->scheduleReferences->count()>0 && isAdmin() || $loan->scheduleReferences->count()> 0 && Auth::user()->can('loan.delay-schedule'))
                    <li class="nav-item" role="presentation">
                        <a class="nav-link @if (request()->get('get')=='delay-schedule')active @endif" href="{{ route('loan.show',[$loan,'get'=>'delay-schedule']) }}">{{ __('app.delay_schedule') }}</a>
                    </li>
                @endif

            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                @if (request()->get('get')=='account-details' || request()->get('get')=='')
                    @include('loan.partials.account-details')
                @endif
                @if (request()->get('get')=='repayment-schedule')
                    @include('loan.partials.repayment-schedule')
                @endif
                @if (request()->get('get')=='delay-schedule' && $loan->scheduleReferences->count()>0)
                    @include('loan.partials.delay')
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
        confirmPopup($(this).data('url'), 'error', 'DELETE','/loan');
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
