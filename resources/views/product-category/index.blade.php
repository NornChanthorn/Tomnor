@extends('layouts/backend')
@section('title', trans('app.product_category'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.product_category') }}</h3>
            @include('partial/flash-message')
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-6">
                            @include('partial/anchor-create', [
                                'href' => route('product_category.create')
                            ])
                        </div>
                        <div class="col-lg-6">
                            <form method="get" action="{{ route('product_category.index') }}">
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
                            <td>@sortablelink('value', trans('app.name'))</td>
                            <th>{{ trans('app.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productCategories as $category)
                            <tr>
                                <td>{{ $offset++ }}</td>
                                <td>{{ $category->value }}</td>
                                <td class="text-center">
                                    @include('partial.anchor-edit', [
                                        'href' => route('product_category.edit', $category->id),
                                    ])
                                    @include('partial.button-delete', [
                                        'url' => route('product_category.destroy', $category->id),
                                        'disabled' => 'disabled',
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $productCategories->appends(Request::except('page'))->render() !!}
            </div>
        </div>
    </main>
@endsection
