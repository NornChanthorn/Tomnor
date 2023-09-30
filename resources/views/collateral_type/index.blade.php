@extends('layouts/backend')
@section('title', trans('app.expense_type'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.expense_type') }}</h3>
            @include('partial/flash-message')
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            @if(Auth::user()->can('collateral_type.add'))
                                @include('partial/anchor-create', [
                                    'href' => route('collateral-type.create')
                                ])
                            @endif
                        </div>
                        <div class="col-md-6">
                            <form method="get" action="">
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
                                    @if(Auth::user()->can('collateral_type.edit'))
                                        @include('partial.anchor-edit', [
                                            'href' => route('collateral-type.edit', $position),
                                        ])
                                    @endif
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
