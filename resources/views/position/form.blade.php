@extends('layouts/backend')
@section('title', trans('app.position'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.position') . ' - ' . $title }}</h3>
            @include('partial/flash-message')
            <form id="form-position" method="post" action="{{ route('position.save') }}">
                @csrf
                @isset($position->id)
                    <input type="hidden" name="id" value="{{ $position->id }}">
                @endisset
                <div class="row">
                    <div class="col-md-10 col-lg-8">
                        <div class="form-group">
                            <label for="title" class="control-label">
                                {{ trans('app.title') }} <span class="required">*</span>
                            </label>
                            <input type="text" name="title" id="title" class="form-control"
                                   value="{{ $position->value ?? old('title') }}" required>
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
            $('#form-position').validate();
        });
    </script>
@endsection
