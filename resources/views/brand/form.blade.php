@extends('layouts/backend')
@section('title', trans('app.brand'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.brand') . ' - ' . $title }}</h3>
            <hr>
            @include('partial/flash-message')
            <form id="form-brand" method="post" action="{{ route('brand.save') }}">
                @csrf
                @isset($brand->id)
                    <input type="hidden" name="id" value="{{ $brand->id }}">
                @endisset
                <div class="row">
                    <div class="col-md-10 col-lg-8">
                        <div class="form-group">
                            <label for="name" class="control-label">{{ trans('app.name') }} <span class="required">*</span></label>
                            <input type="text" name="name" id="name" class="form-control"
                                   value="{{ $brand->value ?? old('name') }}" required>
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
            $('#form-brand').validate();
        });
    </script>
@endsection