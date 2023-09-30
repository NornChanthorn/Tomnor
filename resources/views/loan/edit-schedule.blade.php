@extends('layouts/backend')
@section('title', $title)
@section('css')
    <link rel="stylesheet" href="{{ asset('css/bootstrap4-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">
@endsection
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ $title  }}</h3>
            @include('partial/flash-message')
            <form method="post" id="payment-form" class="no-auto-submit" action="{{ route('loan.update_payment_schedule', $schedule->id) }}">
                @csrf
                {{-- Payment schedule --}}
                <h5>{{ trans('app.payment_schedule') }}</h5>
                <br>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="">{{ trans('app.paid_date') }}</label>
                        <input type="text" class="form-control date-picker" name="paid_date" value="{{ old('paid_date', displayDate($schedule->paid_date)) }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="">{{ trans('app.paid_principal') }}</label>
                        <input type="text" class="form-control decimal-input" name="paid_principal" value="{{ $schedule->paid_principal }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="">{{ trans('app.paid_interest') }}</label>
                        <input type="text" class="form-control decimal-input" name="paid_interest" value="{{ $schedule->paid_interest }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="">{{ trans('app.penalty_amount') }}</label>
                        <input type="text" class="form-control decimal-input" name="paid_penalty" value="{{ $schedule->paid_penalty }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="">{{ trans('app.payment_amount') }}</label>
                        <input type="text" class="form-control decimal-input" name="paid_total" value="{{ $schedule->paid_total }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="">{{ trans('app.status') }}</label>
                        <select name="paid_status" id="" class="form-control">
                            <option value="0" @if ($schedule->paid_status==0) selected  @endif>{{ trans('app.partial') }}</option>
                            <option value="1" @if ($schedule->paid_status==1) selected  @endif>{{ trans('app.paid') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-success">
                        {{ trans('app.save') }}
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection
@section('js')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/select-box.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap4-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
    <script src="{{ asset('js/date-time-picker.js') }}"></script>
    <script src="{{ asset('js/jquery-number.min.js') }}"></script>
    <script src="{{ asset('js/number.js') }}"></script>
    <script src="{{ asset('js/sweetalert.min.js') }}"></script>
@endsection
