@extends('layouts/backend')
@section('title', trans('app.product_stock'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.product_stock') . ' - ' . $product->name }}</h3>
            @include('partial/flash-message')
            <br>
            @include('partial.item-count-label')
            <div class="row">
                <div class="col-lg-8">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ trans('app.no_sign') }}</th>
                                    <th>{{ trans('app.location') }}</th>
                                    <th>{{ trans('app.quantity') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stocks as $stock)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>@include('partial.branch-detail-link', ['branch' => $stock->warehouse])</td>
                                        <td>{{ $stock->quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
