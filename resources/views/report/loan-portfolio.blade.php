@extends('layouts.backend')
@section('title', trans('app.loan_portfolio'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.loan_portfolio') }}</h3>
            @include('partial.flash-message')
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ trans('app.client_information') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <tbody>
                                <tr>
                                    <th width="40%">{{ trans('app.name') }}</th>
                                    <th>
                                        @include('partial.client-detail-link', [
                                            'client' => $client
                                        ])
                                    </th>
                                </tr>
                                <tr>
                                    <th>{{ trans('app.profile_photo') }}</th>
                                    <th>
                                        @if (isset($client->profile_photo))
                                            <img src="{{ asset($client->profile_photo) }}" alt="{{ trans('app.missing_image') }}" class="img-thumbnail" width="100">
                                        @else
                                            {{ trans('app.none') }}
                                        @endif
                                    </th>
                                </tr>
                                <tr>
                                    <th>{{ trans('app.id_card_number') }}</th>
                                    <th>{{ $client->id_card_number }}</th>
                                </tr>
                                <tr>
                                    <th>{{ trans('app.first_phone') }}</th>
                                    <th>{{ $client->first_phone }}</th>
                                </tr>
                                <tr>
                                    <th>{{ trans('app.sponsor_name') }}</th>
                                    <th>{{ $client->sponsor_name }}</th>
                                </tr>
                                <tr>
                                    <th>{{ trans('app.sponsor_phone') }}</th>
                                    <th>{{ $client->sponsor_phone }}</th>
                                </tr>
                                <tr>
                                    <th>{{ trans('app.number_of_loan') }}</th>
                                    <th>{{ $client->loans()->count() }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <h5>{{ trans('app.loan_and_payment_schedule_info') }}</h5>
            @foreach ($loans as $loan)
                @php
                    $unpaidSchedules = $loan->schedules()->where('paid_status', 0)->get();
                    $loanStatusTitle = trans(count($unpaidSchedules) > 0 ? 'app.progressing' : 'app.paid');
                @endphp
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <h6>{{ trans('app.loan') }}</h6>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <tbody>
                                    <tr>
                                        <th>{{ trans('app.account_number') }}</th>
                                        <th>@include('partial.loan-detail-link')</th>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('app.loan_status') }}</th>
                                        <th>{{ $loanStatusTitle }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('app.loan_start_date') }}</th>
                                        <th>{{ displayDate($loan->loan_start_date) }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('app.product') }}</th>
                                        <th>
                                            @foreach (@$loan->productDetails as $item)
                                                <a href="{{ route('product.show', $item->product) }}">
                                                    {{ $item->product->name }}
                                                </a>
                                            @endforeach
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('app.product_price') }}</th>
                                        <th>$ {{ decimalNumber($loan->loan_amount + $loan->depreciation_amount, true)  }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('app.depreciation_amount') }}</th>
                                        <th>$ {{ decimalNumber($loan->depreciation_amount, true)  }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('app.down_payment_amount') }}</th>
                                        <th>$ {{ decimalNumber($loan->loan_amount, true)  }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ trans($loan->schedule_type == PaymentScheduleType::AMORTIZATION ? 'app.loan_rate' : 'app.interest_rate') }}</th>
                                        <th>{{ $loan->interest_rate }} %</th>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('app.installment') }}</th>
                                        <th>{{ $loan->installment }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('app.payment_schedule_type') }}</th>
                                        <th>{{ paymentScheduleTypes($loan->schedule_type) }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6>{{ trans('app.payment_schedule') . ' (' . trans('app.cash_in_dollar') . ')' }}</h6>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ trans('app.no_sign') }}</th>
                                        <th>{{ trans('app.payment_date') }}</th>
                                        @include('partial.schedule-type-table-header', ['scheduleType' => $loan->schedule_type])
                                        <th></th>
                                        <th>{{ trans('app.paid_date') }}</th>
                                        <th>{{ trans('app.paid_amount') }}</th>
                                        <th>{{ trans('app.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loan->schedules as $schedule)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ displayDate($schedule->payment_date) }}</td>
                                            @include('partial.schedule-type-table-data', ['scheduleType' => $loan->schedule_type,'decimalNumber'=>2])
                                            <td></td>
                                            <td>{{ displayDate($schedule->paid_date) }}</td>
                                            <td>{{ isset($schedule->paid_total) ? decimalNumber($schedule->paid_total, true) : '' }}</td>
                                            <td>{{ trans($schedule->paid_status == 1 ? 'app.paid' : 'app.unpaid') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </main>
@endsection
