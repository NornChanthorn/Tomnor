@extends('layouts/backend')
@section('title', trans('app.unit'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.unit') }}</h3>
            @include('partial/flash-message')
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6">
                            @include('partial/anchor-create', [
                                'href' => route('product-units.create')
                            ])
                        </div>
                        <div class="col-lg-6">
                            <form method="get" action="{{ route('product-units.index') }}">
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
                            <td>@sortablelink('short_name', trans('app.name'))</td>
                            <th>{{ trans('app.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($units as $unit)
                            <tr>
                                <td>{{ $offset++ }}</td>
                                <td>{{ $unit->short_name }}</td>
                                <td class="text-center">
                                    @include('partial.anchor-edit', [
                                        'href' => route('product-units.edit', $unit->id),
                                    ])
                                    @include('partial.button-delete', [
                                        'url' => route('product-units.destroy', $unit->id),
                                        'disabled' => 'disabled',
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $units->appends(Request::except('page'))->render() !!}
            </div>
        </div>
    </main>
@endsection
