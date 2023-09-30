@extends('layouts/backend')
@section('title', trans('app.unit'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.unit') . ' - ' . $title }}</h3>
            <hr>
            @include('partial/flash-message')
            <form id="product-unit-form" method="post" action="{{ route('product-units.save', $unit->id) }}">
                @csrf

                <div class="row">
                    <div class="col-md-10 col-lg-8">
                        <div class="form-group">
                            <label for="name" class="control-label">
                                {{ trans('app.name') }} <span class="required">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control"
                                   value="{{ old('name') ?? $unit->short_name }}" required>
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
            $('#product-unit-form').validate();
        });
    </script>
@endsection