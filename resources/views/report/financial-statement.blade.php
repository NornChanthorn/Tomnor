@extends('layouts/backend')
@section('title', trans('app.financial_statement'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.financial_statement') }}</h3>
            {{-- Summary info --}}
            <div class="row">
                <div class="col-md-6 table-responsive">
                    {{-- Interest summary --}}
                    <h5>{{ trans('app.interest') }}</h5>
                    <table class="table table-hover table-bordered">
                        <tbody>
                            <tr>
                                <th>{{ trans('app.total_interest') }}</th>
                                <th>$ {{ decimalNumber($totalInterest, true) }}</th>
                            </tr>
                            <tr>
                                <th>{{ trans('app.paid_interest') }}</th>
                                <th>$ {{ decimalNumber($totalPaidInterest, true) }}</th>
                            </tr>
                            <tr>
                                <th>{{ trans('app.outstanding_interest') }}</th>
                                <th>$ {{ decimalNumber(($totalInterest - $totalPaidInterest), true) }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 table-responsive">
                    {{-- Principal summary --}}
                    <h5>{{ trans('app.principal') }}</h5>
                    <table class="table table-hover table-bordered">
                        <tbody>
                            <tr>
                                <th>{{ trans('app.total_product_price') }}</th>
                                <th>$ {{ decimalNumber($totalLoanAmount, true) }}</th>
                            </tr>
                            <tr>
                                <th>{{ trans('app.depreciation_amount') }}</th>
                                <th>$ {{ decimalNumber($totalDepreciation, true) }}</th>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <th>{{ trans('app.down_payment_amount') }}</th>
                                <th>$ {{ decimalNumber($totalDownPayment, true) }}</th>
                            </tr>
                            <tr>
                                <th>{{ trans('app.paid_principal') }}</th>
                                <th>$ {{ decimalNumber($totalPaidPrincipal, true) }}</th>
                            </tr>
                            <tr>
                                <th>{{ trans('app.outstanding') }}</th>
                                <th>$ {{ decimalNumber(($totalDownPayment - $totalPaidPrincipal), true) }}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>

            {{-- Detail info --}}
            <div class="card">
                <div class="card-header">
                    <form method="GET" action="{{ route('report.financial_statement') }}">
                        <div class="row">
                            {{-- Report type --}}
                            <div class="form-group col-sm-6 col-md-3 pr-0">
                                <label for="report_type" class="control-label">{{ trans('app.report_type') }}</label>
                                <select name="report_type" id="report_type" class="form-control select2 select2-no-search">
                                    @foreach (durationTypes() as $typeKey => $typeTitle)
                                        <option value="{{ $typeKey }}" {{ selectedOption($typeKey, request('report_type')) }}>
                                            {{ $typeTitle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Year --}}
                            <div class="form-group col-sm-5 col-md-2 pr-0">
                                <label for="year" class="control-label">{{ trans('app.year') }}</label>
                                <select name="year" id="year" class="form-control select2 select2-no-search">
                                    @foreach (range(date('Y'), 2019) as $year)
                                        <option value="{{ $year }}" {{ selectedOption($year, request('year')) }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Month --}}
                            <div class="form-group col-sm-5 col-md-2 pr-0">
                                <label for="month" class="control-label">{{ trans('app.month') }}</label>
                                <select name="month" id="month" class="form-control select2 select2-no-search"
                                    {{ request('report_type') != DurationType::MONTHLY ? 'disabled' : '' }}>
                                    @foreach (khmerMonths() as $monthTitle)
                                        <option value="{{ $loop->iteration }}" {{ selectedOption($loop->iteration, request('month')) }}>
                                            {{ $monthTitle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Branch --}}
                            <div class="form-group col-sm-6 col-md-3 pr-0">
                                <label for="branch" class="control-label">{{ trans('app.branch') }}</label>
                                <select name="branch" id="branch" class="form-control select2">
                                    <option value="">{{ trans('app.all_branches') }}</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->location }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Search button --}}
                            <div class="form-group col-sm-2 col-md-2 pr-0">
                            ​   @include('partial.button-search', ['class' => 'btn-lg btn-search-horizontal'])
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <h5>
                        @if ($reportType == DurationType::YEARLY)
                            {{ trans('app.payment_in_year') . ' ' . $filteredYear }}
                        @else
                            {{ trans('app.payment_in_month') . ' ' . khmerMonths(request('month')) . ' ' . trans('app.year') . ' ' . $filteredYear }}
                        @endif
                        ({{ $branchTitle }})
                    </h5>
                    @if ($reportType == DurationType::YEARLY)
                        <div class="table-responsive resize-w">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        @foreach (khmerMonths() as $key => $monthTitle)
                                            <th @if ($loop->iteration == date('m')) class="bg-success text-white" @endif>
                                                {{ $monthTitle }}
                                            </th>
                                        @endforeach
                                        <th>{{ trans('app.total_amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalPaidPrincipal = $totalPaidInterest = $totalPaidPenalty = $totalPaidAmount = 0;
                                        $paidPrincipalElm = $paidInterestElm =  $paidPenaltyElm = $paidTotalElm = '';

                                        foreach ($filteredData as $key => $monthlyPayment) {
                                            $isCurrentMonth = (($key + 1) == date('m'));
                                            $totalPaidPrincipal += $monthlyPayment['paid_principal'];
                                            $totalPaidInterest += $monthlyPayment['paid_interest'];
                                            $totalPaidPenalty += $monthlyPayment['paid_penalty'];
                                            $totalPaidAmount += $monthlyPayment['paid_total'];

                                            $paidPrincipalElm .=
                                                '<th' . ($isCurrentMonth ? ' class="bg-success text-white">' : '>') .
                                                    decimalNumber($monthlyPayment['paid_principal'], true) .
                                                 '</th>';
                                            $paidInterestElm .=
                                                '<th' . ($isCurrentMonth ? ' class="bg-success text-white">' : '>') .
                                                    decimalNumber($monthlyPayment['paid_interest'], true) .
                                                 '</th>';
                                            $paidPenaltyElm .=
                                                '<th' . ($isCurrentMonth ? ' class="bg-success text-white">' : '>') .
                                                    decimalNumber($monthlyPayment['paid_penalty'], true) .
                                                 '</th>';
                                            $paidTotalElm .=
                                                '<th' . ($isCurrentMonth ? ' class="bg-success text-white">' : '>') .
                                                    decimalNumber($monthlyPayment['paid_total'], true) .
                                                '</th>';
                                        }
                                    @endphp
                                    <tr>
                                        <th>{{ trans('app.total_paid_amount') }} ($)</th>
                                        {!!  $paidTotalElm !!}
                                        <th>{{ decimalNumber($totalPaidAmount, true) }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('app.paid_principal') }} ($)</th>
                                        {!! $paidPrincipalElm !!}
                                        <th>{{ decimalNumber($totalPaidPrincipal, true) }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('app.paid_penalty') }} ($)</th>
                                        {!! $paidPenaltyElm !!}
                                        <th>{{ decimalNumber($totalPaidPenalty, true) }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('app.paid_interest') }} ($)</th>
                                        {!! $paidInterestElm !!}
                                        <th>{{ decimalNumber($totalPaidInterest, true) }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-10 col-lg-8 table-responsive">
                                <table class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('app.day') }}</th>
                                            <th>{{ trans('app.total_paid_amount') }} ($)</th>
                                            <th>{{ trans('app.paid_principal') }} ($)</th>
                                            <th>{{ trans('app.paid_interest') }} ($)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalPaidPrincipal = $totalPaidInterest = $totalPaidAmount = 0; @endphp
                                        @foreach ($filteredData as $dailyPayment)
                                            @php
                                                $totalPaidPrincipal += $dailyPayment['paid_principal'];
                                                $totalPaidInterest += $dailyPayment['paid_interest'];
                                                $totalPaidAmount += $dailyPayment['paid_total'];
                                            @endphp
                                            <tr @if ($loop->iteration == date('d')) class="bg-success text-white" @endif>
                                                <th>{{ $loop->iteration }}</th>
                                                <th>{{ decimalNumber($dailyPayment['paid_total'], true) }}</th>
                                                <th>{{ decimalNumber($dailyPayment['paid_principal'], true) }}</th>
                                                <th>{{ decimalNumber($dailyPayment['paid_interest'], true) }}</th>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th>{{ trans('app.total') }}</th>
                                            <th>{{ decimalNumber($totalPaidAmount, true) }}</th>
                                            <th>{{ decimalNumber($totalPaidPrincipal, true) }}</th>
                                            <th>{{ decimalNumber($totalPaidInterest, true) }}</th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
@endsection
@section('js')
    <script>
        var monthlyDuration = '{{ DurationType::MONTHLY }}';
    </script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/select-box.js') }}"></script>
    <script src="{{ asset('js/financial-report.js') }}"></script>
@endsection
