@extends('layouts/backend')
@section('title', trans('app.collateral_type'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.collateral_type') . ' - ' . $title }}</h3>
            @include('partial/flash-message')
            <form id="form-position" method="post" action="{{ route('collateral-type.save') }}">
                @csrf
                @isset($collateral_type->id)
                    <input type="hidden" name="id" value="{{ $collateral_type->id }}">
                @endisset
                <div class="row">
                    <div class="col-md-10 col-lg-8">
                        <label for="title" class="control-label">
                            {{ trans('app.title') }} <span class="required">*</span>
                        </label>
                        <div class="input-group">

                            <input type="text" name="title" id="title" class="form-control"
                            value="{{ $collateral_type->value ?? old('title') }}" required>
                            <div class="input-group-append">
                            
                                @include('partial/button-save', [
                                    'class' => 'pull-right'
                                ])
                            </div>
                        </div>
                      
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection
@section('css')
  <link rel="stylesheet" href="{{ asset('css/bootstrap-fileinput.css') }}">
  <link rel="stylesheet" href="{{asset('plugins/easyAutocomplete/easy-autocomplete.min.css')}}">
  <style>
    .input-group #input { width: 85%!important; }
    .input-group .input-group-append { width: 15%; }
  </style>
@endsection
@section('js')
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script>
        $(function () {
            $('#form-position').validate();
        });
    </script>
@endsection
