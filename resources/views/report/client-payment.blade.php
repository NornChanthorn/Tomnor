@extends('layouts/backend')

@section('title', trans('app.payment_report'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.payment_report') }}</h3>

    <form method="get" action="{{ route('report.client_payment') }}">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-sm-6 col-md-3 form-group">
              <label for="branch" class="control-label">{{ trans('app.branch') }}</label>
              <select name="branch" id="branch" class="form-control select2">
                <option value="">{{ trans('app.all_branches') }}</option>
                @foreach ($branches as $branch)
                  <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                    {{ $branch->location }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-sm-6 col-md-3 form-group">
              <label for="branch" class="control-label">{{ trans('app.agent') }}</label>
              <select name="agent" class="form-control select2">
                <option value="">{{ trans('app.agent') }}</option>
                @foreach ($agents as $agent)
                  <option value="{{ $agent->id }}" {{ request('agent') == $agent->id ? 'selected' : '' }}>
                    {{ $agent->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-sm-6 col-md-3 form-group">
              <label for="branch" class="control-label">{{ trans('app.type') }}</label>
              <select name="type" class="form-control select2">
                <option value="">{{ trans('app.select_option') }}</option>
                <option value="leasing-dp" {{ request('type') == 'leasing-dp' ? 'selected' : '' }}>
                    បង់ប្រាក់ដើម
                </option>
                <option value="leasing" {{ request('type') == 'leasing' ? 'selected' : '' }}>
                  បង់ប្រាក់ប្រចាំខែ
              </option>
              </select>
            </div>
            <div class="col-sm-6 col-md-3 form-group">
              <label>{{ trans('app.start_date') }}</label>
              <div class="input-group">
                <input type="text" name="start_date" id="start_date" class="form-control date-picker" autocomplete="off" value="{{ request('start_date') }}" placeholder="{{ trans('app.date_placeholder') }}">
                <span class="input-group-append"><i class="input-group-text fa fa-calendar"></i></span>
              </div>
            </div>
            <div class="col-sm-6 col-md-3 form-group">
              <label>{{ trans('app.end_date') }}</label>
              <div class="input-group">
                <input type="text" name="end_date" id="end_date" class="form-control date-picker" autocomplete="off" value="{{ request('end_date') }}" placeholder="{{ trans('app.date_placeholder') }}">
                <span class="input-group-append"><i for="start_date" class="input-group-text fa fa-calendar"></i></span>
              </div>
            </div>
            <div class="col-sm-6 col-md-3 form-group">
              <label for="branch" class="control-label">{{ trans('app.search') }}</label>
              <input type="text" name="q" class="form-control" value="{{ request('q') ?? '' }}" placeholder="{{__('app.search-account-number')}}">
            </div>
            <div class="col-sm-6 col-md-2">
              @include('partial.button-search', ['class' => 'btn-block mt-4'])
            </div>
          </div>
        </div>
      </div>
    </form>
    <br>

    <div class="row justify-content-end">
      <div class="col-md-6 table-responsive">
        <table class="table table-hover table-bordered">
          <tbody>
            <tr>
              <th>{{__('app.date')}}</th>
              <th>{{ $date }}</th>
            </tr>
            <tr>
              <th>{{__('app.total_invoice')}}</th>
              <th>{{ ($itemCount ?? trans('app.n/a')) }}</th>
            </tr>
            <tr>
              <th>{{__('app.total_amount')}}</th>
              <th>$ {{ $totalAmount }}</th>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    {{-- @include('partial.item-count-label') --}}

    <div class="table-responsive resize-w">
      <table class="table table-hover table-striped table-bordered">
        <thead>
          <tr>
            <th class="text-center">{{ trans('app.no_sign') }}</th>
            <th>@sortablelink('payment_date', trans('app.payment_date'))</th>
            <th class="tex-right">@sortablelink('payment_amount', trans('app.paid_amount'))</th>
            <th>@sortablelink('payment_method', trans('app.payment_method'))</th>
            <th>{{ trans('app.client_code') }}</th>
            <th>{{ trans('app.client') }}</th>
            <th>@sortablelink('reference_number', trans('app.reference_number'))</th>
            <th>{{ trans('app.receiver') }}</th>
            <th>{{ trans('app.note') }}</th>
            <th>{{ trans('app.action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($payments as $payment)
          <tr>
            <td class="text-center">{{ $offset++ }}</td>
            <td>{{ displayDate($payment->payment_date) }}</td>
            <td class="text-right"><b>$ {{ decimalNumber($payment->payment_amount, true) }}</b></td>
            <td>{{ paymentMethods($payment->payment_method) }}</td>
            <td>@include('partial.loan-detail-link', ['loan' => $payment->loan])</td>
            <td>@include('partial.client-detail-link', ['client' => $payment->client])</td>
            <td>{{ $payment->reference_number }}</td>
            <td>{{ $payment->user->name ?? trans('app.n/a') }}</td>
            <td>{{ $payment->note }}</td>
            <td class="text-center">
              {{--<a href="{{ route('report.client_payment_receipt', $payment) }}" class="btn btn-info btn-sm mb-1" target="_blank">
                {{ trans('app.print_receipt') }}
              </a>
              <br>--}}
              <a href="{{ route('report.loan_portfolio', $payment->client) }}" class="btn btn-info btn-sm mb-1">{{ trans('app.loan_portfolio') }}</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      {!! $payments->appends(Request::except('page'))->render() !!}
    </div>
  </div>
</main>
@endsection

@section('js')
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>
  <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
  {{-- <script src="{{ asset('js/date-time-picker.js') }}"></script> --}}
  <script>
    $(document).ready(function() {
      $("#start_date, #end_date").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        orientation: 'bottom right'
      });
    });
  </script>
@endsection
