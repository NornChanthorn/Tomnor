@extends('layouts/backend')
@section('title', $title)
@section('css')
    <link rel="stylesheet" href="{{ asset('css/bootstrap4-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-fileinput.css') }}">
@endsection
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{$title}}</h3>
            @include('partial/flash-message')
            <form method="post" id="payment-form" class="no-auto-submit" action="" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="repay_type" value="">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>{{ trans('app.payment_method') }}</h5>
                        <br>
                            <div class="row">
                                <div class="col-lg-4 form-group">
                                    <label for="payment_date" class="control-label">
                                        {{ trans('app.paid_date') }} <span class="required">*</span>
                                    </label>
                                    <input type="text" name="payment_date" id="date-picker" class="form-control"
                                        value="{{ old('payment_date') ?? date('d-m-Y') }}" placeholder="{{ trans('app.date_placeholder') }}" required>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label for="payment_amount" class="control-label">
                                        {{ trans('app.payment_amount') }} ($) <span class="required">*</span>
                                    </label>
                                    <input type="text" name="payment_amount" id="payment_amount" class="form-control decimal-input"
                                        value="" required {{ Config::get('app.remain_payment')==true ? "readonly":""}}>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label for="payment_method" class="control-label">
                                        {{ trans('app.payment_method') }} <span class="required">*</span>
                                    </label>
                                    <select name="payment_method" id="payment_method" class="form-control select2 select2-no-search" required>
                                            <option value="">
                                                Payment Method
                                            </option>
                                    </select>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label for="penalty_amount" class="control-label">
                                        {{ trans('app.penalty_amount') }} ($)
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"></span>
                                        </div>
                                        <input type="text" name="penalty_amount" id="penalty_amount" class="form-control decimal-input"
                                            value="{{ old('penalty_amount') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label for="reference_number" class="control-label">
                                        {{ trans('app.reference_number') }}
                                    </label>
                                    <input type="text" name="reference_number" id="reference_number" class="form-control"
                                        value="{{ old('reference_number') }}">
                                </div>
                                <div class="col-lg-6 form-group">
                                    <label for="note" class="control-label">
                                        {{ trans('app.note') }}
                                    </label>
                                    <textarea name="note" id="note" class="form-control" rows="16">{{ old('note') }}</textarea>
                                </div>
                                <div class="col-lg-6 form-group">
                                    <label for="photo" class="control-label">
                                    {{ trans('app.document') }}
                                    </label>
                                    <input type="file" name="receipt_photo" id="photo" class="form-control" accept=".jpg, .jpeg, .png">
                                </div>
                                <div class="col-lg-12 text-right">
                                <button type="submit" class="btn btn-success" onclick="confirmFormSubmission($('#payment-form'))">
                                        {{ $repayLabel }}
                                    </button>
                                </div>
                            </div>



                    </div>
                </div>

            </form>

        </div>
    </main>
    <div class="modal fade payment-date-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>
@endsection
@section('js')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/select-box.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-fileinput.js') }}"></script>
    <script src="{{ asset('js/bootstrap-fileinput-fa-theme.js') }}"></script>
    <script src="{{ asset('/js/init-file-input.js') }}"></script>
    <script src="{{ asset('js/bootstrap4-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
    <script src="{{ asset('js/date-time-picker.js') }}"></script>
    <script src="{{ asset('js/jquery-number.min.js') }}"></script>
    <script src="{{ asset('js/number.js') }}"></script>
    <script src="{{ asset('js/mask.js') }}"></script>
    <script src="{{ asset('js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/repayment.js') }}"></script>

@endsection
