@extends('layouts/backend')
@section('title', trans('app.product_category'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.product_category') . ' - ' . $title }}</h3>
            <hr>
            @include('partial/flash-message')
            <form id="product-category-form" method="post" action="{{ route('product_category.save', $productCategory->id) }}">
                @csrf

                <div class="row">
                    <div class="col-md-10 col-lg-8">
                        <div class="form-group">
                            <label for="name" class="control-label">
                                {{ trans('app.name') }} <span class="required">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control"
                                   value="{{ old('name') ?? $productCategory->value }}" required>
                        </div>
                        @include('partial/button-save', [
                            'class' => 'pull-right'
                        ])
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection

@section('js')
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script>
        $(function () {
            $('#product-category-form').validate();
        });
    </script>
@endsection