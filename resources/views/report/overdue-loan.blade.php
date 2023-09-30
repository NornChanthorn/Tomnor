@extends('layouts/backend')
@section('title', trans('app.overdue_loan'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.overdue_loan') }}</h3>
            @include('partial/flash-message')
            <div class="card">
                <div class="card-header">
                    <form method="get" action="{{ route('report.overdue_loan') }}">
                        <div class="row">
                            
                            {{-- Branch --}}
                            @if(empty(auth()->user()->staff))
                            <div class="col-sm-6 col-lg-3 pl-1 pr-0">
                                <select name="branch" id="branch" class="form-control select2">
                                    <option value="">{{ trans('app.branch') }}</option>
                                    @foreach (allBranches() as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>{{ $branch->location }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- @if (isAdmin()) --}}
                            {{-- Agent --}}
                            <div class="col-sm-6 col-lg-3 pl-1 pr-0">
                                <select name="agent" id="agent" class="form-control select2">
                                <option value="">{{ trans('app.agent') }}</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ request('agent') == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                    </option>
                                @endforeach
                                </select>
                            </div>

                            {{-- @endif --}}
                            @endif
                            {{-- End date --}}
                            <div class="col-md-3">
                                {{-- <label for="sort">{{ trans('app.sort') }}</label> --}}
                                <select name="sort" class="form-control" id="">
                                  <option value="asc" {{ request('sort')== 'asc' ? 'selected' : '' }}>{{ trans('app.asc') }}</option>
                                  <option value="desc" {{ request('sort')== 'desc' ? 'selected' : '' }}>{{ trans('app.desc') }}</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-3 col-lg-3">
                            {{-- <label for="start_date" class="control-label">{{ trans('app.start_date') }}</label> --}}
                            <input type="text" name="date" id="date" class="form-control date-picker" readonly placeholder="{{ trans('app.date_placeholder') }}" value="{{ request('date') }}">
                            </div>

                            {{-- Text search --}}
                            <div class="col-lg-3 pl-1">
                            @include('partial.search-input-group')
                            </div>

                            
                        </div>
                    </form>
                </div>
            </div>
            <br>
            @include('partial.item-count-label')
            <a href="{{ url('repayment/list') }}" class="btn btn-sm btn-success pull-right mb-1">{{ trans('app.print') }}</a>
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 5%">{{ trans('app.action') }}</th>
                            <th>@sortablelink('client_code', trans('app.loan_code'))</th>
                            <th style="width: 10%">{{ trans('app.client') }}</th>
                            <th style="width: 10%">{{ trans('app.profile_photo') }}</th>
                            <th style="width: 10%">@sortablelink('client_id', trans('app.client_code'))</th>
                            <th style="width: 10%">{{ trans('app.phone_number') }}</th>
                            <th style="width: 10%">{{ trans('app.branch') }}</th>
                            @if (isAdmin())
                                <th>{{ trans('app.agent') }}</th>
                            @endif
                            <th style="width: 10%">{{ trans('app.next_payment_date') }}</th>
                            <th>{{ trans('app.payment_amount') }}</th>
                            <th>{{ trans('app.count_late_date') }}</th>
                            <th style="width: 10%">
                                {{ trans('app.product_ime') }}
                            </th>
                            <th>
                                {{ trans('app.icloud') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($overdueLoans as $loan)
                            @php 
                                $amountToPay = $loan->total_amount - $loan->total_paid_amount
                            @endphp
                            <tr>
                                <td class="text-center">
                                    {{-- Simple repayment --}}
                                    <a href="{{ route('repayment.show', [$loan->id, RepayType::REPAY]) }}" class="btn btn-success btn-sm mb-1">
                                    {{ trans('app.repay') }}
                                    </a>
                
                                    {{-- Payoff --}}
                                    <a href="{{ route('repayment.show', [$loan->id, RepayType::PAYOFF]) }}" class="btn btn-success btn-sm mb-1">
                                    {{ trans('app.pay_off') }}
                                    </a>
                
                                {{-- Advance payment --}}
                                {{--<a href="{{ route('repayment.show', [$loan->id, RepayType::ADVANCE_PAY]) }}" class="btn btn-success btn-sm mb-1">
                                    {{ trans('app.advance_pay') }}
                                </a>--}}
                                </td>
                                <td>
                                    {{ $loan->client_code }}
                                </td>
                                <td>@include('partial.client-detail-link', ['client' => $loan->client])</td>
                                <td>@include('partial.client-profile-photo', ['client' => $loan->client])</td>
                                <td>
                                @include('partial.loan-detail-link')
                                </td>
                                <td>{{ $loan->client->first_phone }} {{ $loan->client->second_phone ? ', '.$loan->client->second_phone : "" }}</td>
                                <td>{{ $loan->branch->location ?? trans('app.n/a') }}</td>
                                @if (isAdmin())
                                <td>@include('partial.staff-detail-link', ['staff' => $loan->staff])</td>
                                @endif
                
                                <td>{{ displayDate($loan->schedules[0]->payment_date) }}</td>
                                <td><b>{{ $amountToPay ?  num_f($amountToPay) : '' }}</b></td>
                                <td>
                                <b>
                                    {{ $loan->late_payment }}
                                </b>
                                </td>
                                <td>
                                @foreach ($loan->transaction->sell_lines as $item)
                                    @if (@$item->product)
                                    @include('partial.product-detail-link', ['product' => @$item->product, 'variantion' => @$item->variantion->name])<br>
                                    @endif
                                    <b>{{ trans('app.quantity') }}:{{ $item->quantity }}</b>, <b>IME:</b>
                                    @if (@$item->transaction->transaction_ime)
                                    @foreach ($item->transaction->transaction_ime as $ime)
                                        @if (!$loop->first)
                                            ,
                                        @endif
                                        {{ $ime->ime->code }}
                                    @endforeach
                                    @else
                                        {{ trans('app.n/a') }}
                                    @endif
                                    
                                @endforeach
                                </td>
                                <td>
                                {!! $loan->note ?? trans('app.n/a') !!}
                                </td>
            
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $overdueLoans->appends(Request::except('page'))->render() !!}
            </div>
        </div>
    </main>
@endsection
@section('js')
    <script>
        var agentSelectLabel = '<option value="">{{ trans('app.agent') }}';
        var agentRetrievalUrl = '{{ route('staff.get_agents', ':branchId') }}';
    </script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/select-box.js') }}"></script>
    <script src="{{ asset('js/agent-retrieval.js') }}"></script>
@endsection
