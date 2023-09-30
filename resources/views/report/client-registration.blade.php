@extends('layouts/backend')
@section('title', trans('app.client_registration'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.client_registration') }}</h3>
            @include('partial/flash-message')
            <div class="card">
                <div class="card-header">
                    <form method="get" action="{{ route('report.client_registration') }}">
                        <div class="row">
                            <div class="offset-md-6 col-md-2">
                                <select name="agent" class="form-control select2">
                                    <option value="">{{ trans('app.agent') }}</option>
                                    @foreach ($agents as $agent)
                                        <option value="{{ $agent->id }}" {{ request('agent') == $agent->id ? 'selected' : '' }}>
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                @include('partial.search-input-group')
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <br>
            @include('partial.item-count-label')
            <div class="table-responsive resize-w">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>{{ trans('app.no_sign') }}</th>
                            <th>@sortablelink('name', trans('app.name'))</th>
                            <th>{{ trans('app.profile_photo') }}</th>
                            <th>@sortablelink('id_card_number', trans('app.id_card_number'))</th>
                            <th>@sortablelink('first_phone', trans('app.first_phone'))</th>
                            <th>@sortablelink('sponsor_name', trans('app.sponsor_name'))</th>
                            <th>@sortablelink('sponsor_phone', trans('app.sponsor_phone'))</th>
                            <th>{{ trans('app.number_of_loan') }}</th>
                            <th>{{ trans('app.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clients as $client)
                            <tr>
                                <td>{{ $offset++ }}</td>
                                <td>@include('partial.client-detail-link', ['client' => $client])</td>
                                <td>@include('partial.client-profile-photo')</td>
                                <td>{{ $client->id_card_number }}</td>
                                <td>{{ $client->first_phone }}</td>
                                <td>{{ $client->sponsor_name }}</td>
                                <td>{{ $client->sponsor_phone }}</td>
                                <td>{{ $client->loans()->count() }}</td>
                                <td>
                                    <a href="{{ route('report.loan_portfolio', $client) }}" class="btn btn-info btn-sm mb-1">
                                        {{ trans('app.loan_portfolio') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $clients->appends(Request::except('page'))->render() !!}
            </div>
        </div>
    </main>
@endsection
@section('js')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/select-box.js') }}"></script>
@endsection
