@extends('layouts/backend')
@section('title', trans('app.position'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.position') }}</h3>
            @include('partial/flash-message')
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            @include('partial/anchor-create', [
                                'href' => route('position.create')
                            ])
                        </div>
                        <div class="col-md-6">
                            <form method="get" action="{{ route('position.index') }}">
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
                            <th>@sortablelink('value', trans('app.title'))</th>
                            <th>{{ trans('app.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($positions as $position)
                            <tr>
                                <td>{{ $offset++ }}</td>
                                <td>{{ $position->value }}</td>
                                <td>
                                    @include('partial.anchor-edit', [
                                        'href' => route('position.edit', $position->id),
                                    ])
                                    @include('partial.button-delete', [
                                        'url' => route('position.destroy', $position->id),
                                        'disabled' => 'disabled',
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $positions->appends(Request::except('page'))->render() !!}
            </div>
        </div>
    </main>
@endsection
