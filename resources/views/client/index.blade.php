@extends('layouts/backend')
@section('title', trans('app.client'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <p>It from client.index</p>
            <h3 class="page-heading">{{ trans('app.client') }}</h3>
            @include('partial/flash-message')
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            @include('partial/anchor-create', [
                                'href' => route('client.create')
                            ])
                        </div>
                        <div class="col-md-6 text-right">
                            <form method="get" action="{{ route('client.index') }}">
                                @include('partial.search-input-group')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            @include('partial.item-count-label')
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>{{ trans('app.no_sign') }}</th>
                            <th>@sortablelink('name', trans('app.name'))</th>
                            <th>{{ trans('app.profile_photo') }}</th>
                            <th>@sortablelink('gender', trans('app.gender'))</th>
                            <th>@sortablelink('id_card_number', trans('app.id_card_number'))</th>
                            <th>@sortablelink('first_phone', trans('app.first_phone'))</th>
                            <th>@sortablelink('sponsor_name', trans('app.sponsor_name'))</th>
                            <th>@sortablelink('sponsor_phone', trans('app.sponsor_phone'))</th>
                            <th>{{ trans('app.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clients as $client)
                            <tr>
                                <td>{{ $offset++ }}</td>
                                <td>{{ $client->name }}</td>
                                <td>@include('partial.client-profile-photo', ['client' => $client])</td>
                                <td>{{ genders($client->gender ?? '') }}</td>
                                <td>{{ $client->id_card_number }}</td>
                                <td>{{ $client->first_phone }}</td>
                                <td>{{ $client->sponsor_name }}</td>
                                <td>{{ $client->sponsor_phone }}</td>
                                <td>
                                    @include('partial/anchor-show', [
                                        'href' => route('client.show', $client->id),
                                    ])
                                    @if ($client->is_default == 0)
                                        @include('partial/anchor-edit', [
                                            'href' => route('client.edit', $client->id),
                                        ])
                                        @if (isAdmin())
                                            @include('partial/button-delete', [
                                                'url' => route('client.destroy', $client->id),
                                                'disabled' => $client->loans->count() ? 'disabled' : '',
                                            ])
                                        @endif
                                    @endif
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
