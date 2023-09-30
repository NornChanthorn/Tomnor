@extends('layouts.backend')
@section('title', trans('app.commission_payment'))
@section('css')
    <link rel="stylesheet" href="{{ asset('css/bootstrap4-datetimepicker.min.css') }}">
@endsection
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.commission_payment') }}</h3>
            @include('partial.flash-message')
            <div class="row">
                <div class="col-md-10 col-lg-8">
                    <form method="post" id="form-commission" action="{{ route('commission-payment.save') }}">
                        @csrf
                        <div class="form-group">
                            <label for="agent_id">{{ trans('app.agent') }} <span class="required">*</span></label>
                            <select name="agent_id" id="agent_id" class="form-control select2" required>
                                <option value="">{{ trans('app.select_option') }}</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}">
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="commission-info-wrapper" style="display: none;">
                            <div class="table-responsive">
                                <table id="table-commission-info" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('app.total_commission') }}</th>
                                            <th>{{ trans('app.paid_commission') }}</th>
                                            <th>{{ trans('app.balance') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <hr>
                        </div>
                        <div class="form-group">
                            <label for="payment_date">
                                {{ trans('app.paid_date') }} <span class="required">*</span>
                            </label>
                            <input type="text" name="payment_date" id="payment_date" class="form-control date-picker"
                                   value="{{ old('payment_date') ?? date('d-m-Y') }}" placeholder="{{ trans('app.date_placeholder') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_amount">
                                {{ trans('app.payment_amount') }} ($) <span class="required">*</span>
                            </label>
                            <input type="text" name="payment_amount" id="payment_amount" class="form-control decimal-input"
                                   value="{{ old('payment_amount') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="reference_number">
                                {{ trans('app.reference_number') }}
                            </label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control"
                                   value="{{ old('reference_number') }}">
                        </div>
                        <div class="form-group">
                            <label for="note">
                                {{ trans('app.note') }}
                            </label>
                            <textarea name="note" id="note" class="form-control">{{ old('note') }}</textarea>
                        </div>
                        @include('partial.button-save', [
                            'class' => 'pull-right'
                        ])
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('js')
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap4-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
    <script src="{{ asset('js/date-time-picker.js') }}"></script>
    <script src="{{ asset('js/jquery-number.min.js') }}"></script>
    <script src="{{ asset('js/number.js') }}"></script>
    <script>
        $(function () {
            $('.select2').select2();
            $('#form-commission').validate({
                agent_id: { required: true },
                payment_date: { required: true },
                payment_amount: { required: true, min: 0 }
            });

            // When change agent
            $('#agent_id').change(function () {
                var agentId = $(this).val();
                if (agentId != '') {
                    var agentCommissionUrl = ('{{ route('commission-payment.get_agent_commission_info', ':agentId') }}').replace(':agentId', agentId);
                    $.ajax({
                        url: agentCommissionUrl,
                        success: function (result) {
                            var commissionDataElm =
                                '<tr>' +
                                    '<td><b>$ ' + result.totalCommission + '</b></td>' +
                                    '<td><b>$ ' + result.paidCommission + '</b></td>' +
                                    '<td><b>$ ' + result.balance + '</b></td>' +
                                '</tr>';
                            $('#table-commission-info tbody').html(commissionDataElm);
                        }
                    });
                }

                agentId == '' ? $('#commission-info-wrapper').hide() : $('#commission-info-wrapper').show();
            });
        });
    </script>
@endsection
