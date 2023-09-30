@extends('layouts/backend')

@section('title', trans('app.client'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.collateral') . ' - ' . $title }}</h3>
      @include('partial/flash-message')
      <form id="form-client" method="post" action="{{ route('collateral-save', ['loan_id'=>$loan_id]) }}" enctype="multipart/form-data">
        @csrf
        @isset($collateral->id)
            <input type="hidden" name="id" value="{{ $collateral->id }}">
        @endisset
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="" class="control-label">
                        {{ __('app.name') }}
                        <span class="required">*</span>
                    </label>
                    <input type="text"  name="name" class="form-control" value="{{ $collateral->name }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="" class="control-label">
                        {{ __('app.collateral_type') }}
                        <span class="required">*</span>
                    </label>
                    <select name="type_id"  class="form-control" id="">
                        @foreach (collateralTypes() as $collateralType)
                            <option value="{{ $collateralType->id }}">{{  $collateralType->value }}</option>
                        @endforeach
                        
                    </select>
                </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                  <label for="" class="control-label">
                      {{ __('app.value') }}
                      <span class="required">*</span>
                  </label>
                  <input type="text"  name="value" class="form-control decimal-input" value="{{ $collateral->value  ?? 0 }}" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                  <label for="" class="control-label">
                      {{ __('app.file') }}
                  </label>
                  <input type="file"  name="files" class="form-control">
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                  <label for="" class="control-label">
                      {{ __('app.note') }}
                  </label>
                  <textarea name="note"  class="form-control-feedback" id="" cols="30" rows="10">{!! $collateral->note !!}</textarea>
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
<script src="{{ asset('js/jquery-mask.min.js') }}"></script>
<script src="{{ asset('js/jquery-number.min.js') }}"></script>
<script src="{{ asset('js/number.js') }}"></script>
@endsection