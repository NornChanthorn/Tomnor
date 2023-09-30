@extends('layouts/backend')
@section('title', trans('app.loan_disbursement'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.loan_disbursement') }}</h3>
            @include('partial/flash-message')
            <form id="form-search" method="get" action="{{ route('report.disbursed_loan') }}">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            {{-- Start date --}}
                            <div class="form-group col-sm-3 col-lg-2 pr-0">
                                <label for="start_date" class="control-label">{{ trans('app.start_date') }}</label>
                                <input type="text" name="start_date" id="start_date" class="form-control date-picker"
                                       placeholder="{{ trans('app.date_placeholder') }}" value="{{ displayDate($startDate) }}">
                            </div>

                            {{-- End date --}}
                            <div class="form-group col-sm-3 col-lg-2 pr-0">
                                <label for="end_date" class="control-label">{{ trans('app.end_date') }}</label>
                                <input type="text" name="end_date" id="end_date" class="form-control date-picker"
                                       placeholder="{{ trans('app.date_placeholder') }}" value="{{ displayDate($endDate) }}">
                            </div>

                            {{-- Branch --}}
                            <div class="form-group col-sm-4 col-lg-3 pr-0">
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

                            {{-- Agent --}}
                            <div class="form-group col-sm-4 col-lg-3 pr-0">
                                <label for="agent" class="control-label">{{ trans('app.agent') }}</label>
                                <select name="agent" id="agent" class="form-control select2">
                                    <option value="">{{ trans('app.all_agents') }}</option>
                                    @foreach ($agents as $agent)
                                        <option value="{{ $agent->id }}" {{ request('agent') == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Search button --}}
                            <div class="form-group col-sm-2">
                                @include('partial.button-search', ['class' => 'btn-lg btn-search-horizontal-2'])
                            </div>
                        </div>
                    </div>

                    {{-- Summary info --}}
                    <div class="card-body">
                        <h5>
                            {!! trans('app.disbursed_loans_from_date') . ' ' . displayDate($startDate)
                                . ' ' . trans('app.to') . ' ' . displayDate($endDate)
                                . ' (' . $branchTitle . ' - ' . $agentName . ')' !!}
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered table-hover">
                                    <tbody>
                                        <tr>
                                            <th>{{ trans('app.number_of_disbursed_loans') }}</th>
                                            <td>{{ $itemCount }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ trans('app.total_product_price') }}</th>
                                            <td>$ {{ decimalNumber($totalLoanAmount, true) }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ trans('app.depreciation_amount') }}</th>
                                            <td>$ {{ decimalNumber($totalDepreciation, true) }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ trans('app.down_payment_amount') }}</th>
                                            <td>$ {{ decimalNumber($totalDownPayment, true) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                @include('partial.item-count-label')
                <div class="table-responsive resize-w">
                    <table class="table table-hover table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ trans('app.no_sign') }}</th>
                                <th>{{ trans('app.client') }}</th>
                                <th>@sortablelink('client_code', trans('app.client_code'))</th>
                                <th>{{ trans('app.branch') }}</th>

                                @if (isAdmin())
                                    <th>{{ trans('app.agent') }}</th>
                                @endif

                                <th>{{ trans('app.product') }}</th>
                                <th>{{ trans('app.product_price') }}</th>
                                <th>{{ trans('app.loan_amount') }}</th>
                                <th>@sortablelink('approved_date', trans('app.disbursement_date'))</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($disbursedLoans as $loan)
                                <tr>
                                    <td>{{ $offset++ }}</td>
                                    <td>@include('partial.client-detail-link', ['client' => $loan->client])</td>
                                    <td>@include('partial.loan-detail-link')</td>
                                    <td>{{ $loan->branch->location ?? trans('app.n/a') }}</td>

                                    @if (isAdmin())
                                        <td>@include('partial.staff-detail-link', ['staff' => $loan->staff])</td>
                                    @endif

                                    <td>
                                        
                                        @foreach ($loan->productDetails as $item)
                                            @include('partial.product-detail-link', ['product' =>  $item->product,'variantion'=>$item->variantion->name])
                                        @endforeach
                                        
                                    </td>
                                    <td>$ {{ decimalNumber($loan->loan_amount, true) }}</td>
                                    <td>$ {{ decimalNumber($loan->down_payment_amount, true) }}</td>
                                    <td>{{ displayDate($loan->approved_date, true) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {!! $disbursedLoans->appends(Request::except('page'))->render() !!}
                </div>
            </form>
        </div>
    </main>
@endsection
@section('js')
    <script>
        var agentSelectLabel = '<option value="">{{ trans('app.all_agents') }}';
        var agentRetrievalUrl = '{{ route('staff.get_agents', ':branchId') }}';
    </script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/select-box.js') }}"></script>
    <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
    <script src="{{ asset('js/date-time-picker.js') }}"></script>
    <script src="{{ asset('js/agent-retrieval.js') }}"></script>
    <script>
      $(document).ready(function() {
        $(".date-picker").datepicker({
          format: 'dd-mm-yyyy',
          autoclose: true,
          orientation: 'bottom right'
        });
      });
    </script>
@endsection
