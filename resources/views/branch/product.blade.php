@extends('layouts/backend')
@section('title', trans('app.products_in_warehouse'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.products_in_warehouse') . ' - ' . $branch->location }}</h3>
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
                                    <th>{{ trans('app.product') }}</th>
                                    <th>{{ trans('app.quantity') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stocks as $stock)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>@include('partial.product-detail-link', ['product' => $stock->product])</td>
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
