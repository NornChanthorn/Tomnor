@extends('layouts/backend')
@section('title', trans('app.agent_commission'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.agent_commission') }}</h3>
            @include('partial.item-count-label')
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>{{ trans('app.no_sign') }}</th>
                            <th>@sortablelink('name', trans('app.name'))</th>
                            <th>{{ trans('app.total_commission') }}</th>
                            <th>{{ trans('app.paid_commission') }}</th>
                            <th>{{ trans('app.balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($agents as $agent)
                            <tr>
                                <td>{{ $offset++ }}</td>
                                <td>
                                    @include('partial.staff-detail-link', [
                                        'staff' => $agent
                                    ])
                                </td>
                                <td><b>$ {{ decimalNumber($agent->total_commission, true) }}</b></td>
                                <td><b>$ {{ decimalNumber($agent->paid_commission, true) }}</b></td>
                                <td><b>$ {{ decimalNumber(($agent->total_commission - $agent->paid_commission), true) }}</b></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection
