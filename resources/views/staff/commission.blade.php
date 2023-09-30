@extends('layouts/backend')
@section('title', trans('app.staff_commission'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.staff_commission') . ' - ' . $staff->name }}</h3>
            @include('partial/flash-message')
            <form id="form-commission" method="post" action="{{ route('staff.save_commission', $staff) }}">
                @csrf
                <div class="row">
                    <div class="col-lg-6 table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>{{ trans('app.no_sign') }}</th>
                                    <th>{{ trans('app.start_date') }}</th>
                                    <th>{{ trans('app.amount') }}</th>
                                    <th>{{ trans('app.created_date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($commissions as $commission)
                                    <tr>
                                        <td>{{ $offset++ }}</td>
                                        <td>{{ displayDate($commission->start_date) }}</td>
                                        <td>$ {{ decimalNumber($commission->amount, true) }}</td>
                                        <td>{{ displayDate($commission->created_at) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {!! $commissions->render() !!}
                    </div>

                    {{-- Fields to add new commission --}}
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-12 form-group">
                                <label for="start_date" class="control-label">
                                    {{ trans('app.start_date') }} <span class="required">*</span>
                                </label>
                                <input type="text" name="start_date" id="start_date" class="form-control date-picker" required
                                       placeholder="{{ trans('app.date_placeholder') }}" value="{{ old('start_date') }}">
                            </div>
                            <div class="col-lg-12 form-group">
                                <label for="start_date" class="control-label">
                                    {{ trans('app.amount') }} ($) <span class="required">*</span>
                                </label>
                                <input type="text" name="amount" id="amount" class="form-control decimal-input"
                                       value="{{ old('amount') }}" required>
                            </div>
                            <div class="col-lg-12 text-right">
                                @include('partial.button-save')
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection
@section('js')
    <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
    <script src="{{ asset('js/date-time-picker.js') }}"></script>
    <script src="{{ asset('js/jquery-number.min.js') }}"></script>
    <script src="{{ asset('js/number.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script>
        $(function() {
            $('#form-commission').validate();
        });
    </script>
@endsection
