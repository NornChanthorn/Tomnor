@extends('layouts/backend')
@section('title', trans('app.commission_payment'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.commission_payment') }}</h3>
            <form method="get" action="{{ route('report.commission_payment') }}">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6 col-md-4">
                                <label for="start_date">{{ trans('app.start_date') }}</label>
                                <input type="text" name="start_date" id="start_date" class="form-control date-picker"
                                       value="{{ request('start_date') }}" placeholder="{{ trans('app.date_placeholder') }}">
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <label for="end_date">{{ trans('app.end_date') }}</label>
                                <input type="text" name="end_date" id="end_date" class="form-control date-picker"
                                       value="{{ request('end_date') }}" placeholder="{{ trans('app.date_placeholder') }}">
                            </div>
                            <div class="col-sm-6 col-md-4">
                                @include('partial.button-search', [
                                    'class' => 'mt-4'
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <br>
            @include('partial.item-count-label')
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>{{ trans('app.no_sign') }}</th>
                            <th>@sortablelink('paid_date', trans('app.paid_date'))</th>
                            <th>@sortablelink('amount', trans('app.paid_amount'))</th>
                            <th>{{ trans('app.agent') }}</th>
                            <th>@sortablelink('receipt_reference', trans('app.reference_number'))</th>
                            <th>{{ trans('app.note') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commissionPayments as $payment)
                            <tr>
                                <td>{{ $offset++ }}</td>
                                <td>{{ displayDate($payment->paid_date) }}</td>
                                <td><b>$ {{ decimalNumber($payment->amount, true) }}</b></td>
                                <td>@include('partial.staff-detail-link', ['agent' => $payment->staff])</td>
                                <td>{{ $payment->reference_number }}</td>
                                <td>{{ $payment->note }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {!! $commissionPayments->appends(Request::except('page'))->render() !!}
        </div>
    </main>
@endsection

@section('js')
    <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
    <script src="{{ asset('js/date-time-picker.js') }}"></script>
@endsection
