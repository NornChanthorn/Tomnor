@extends('layouts/backend')

@section('title', trans('app.purchase_return'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.purchase_return') }}</h3>
    @include('partial/flash-message')

    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-9">
            <form id="sale_search_f" method="get" action="">
              <input type="hidden" name="start" value="{{request('start')}}" />
              <input type="hidden" name="end" value="{{request('end')}}" />
              <div class="row">
                @if(!auth()->user()->staff)
                  <div class="col-md-4">
                    <label for="location">{{ trans('app.warehouse') }}</label>
                    <select name="location" id="location" class="form-control select2">
                      <option value="">{{ trans('app.all') }}</option>
                      @foreach($locations as $location)
                      <option value="{{ $location->id }}" {{ request('location') == $location->id ? 'selected' : '' }}>
                        {{ $location->location }}
                      </option>
                      @endforeach
                    </select>
                  </div>
                @endif
                <div class="col-md-4">
                  <label for="supplier" class="control-label">{{ trans('app.supplier') }}</label>
                  <div class="input-group">
                    <select name="supplier" id="supplier" class="form-control select2" required>
                      <option value="">{{ trans('app.all') }}</option>
                      @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ selectedOption($supplier->id, request('supplier')) }}>{{ $supplier->defualt_business_name ?? $supplier->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <label for="sale_date" class="control-label">
                    {{ trans('app.purchase_status') }}
                  </label>
                  <select name="status" id="status" class="form-control" required>
                    <option value="">{{ trans('app.all') }}</option>
                    @foreach (purchaseStatuses() as $k => $_sta)
                    <option value="{{ $k }}" {{ selectedOption($k, request('status')) }}>
                      {{ $_sta }}
                    </option>
                    @endforeach
                  </select>
                </div>
              </div>
            </form>
          </div>
          <div class="col-md-3">
            <label for="sell_list_filter_date_range">{{ trans('app.sale_date') }}</label>
            <input placeholder="{{ trans('app.select_date_range') }}" class="form-control" readonly="" name="sell_list_filter_date_range" type="text" id="sell_list_filter_date_range" value="@if(!empty(request('start'))){{dateIsoFormat(request('start'), 'd/m/Y')}}@endif ~ @if(!empty(request('end'))){{dateIsoFormat(request('end'), 'd/m/Y')}}@endif">
          </div>
        </div>
      </div>
    </div>
    <br>

    <div class="row">
      <div class="col-lg-6">
        @include('partial.item-count-label')
      </div>
    </div>

    <div class="table-responsive resize-w">
      <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr>
            <th>{{ trans('app.no_sign') }}</th>
            <th>@sortablelink('purchase_date', trans('app.purchase_date'))</th>
            <th>@sortablelink('reference_no', trans('app.invoice_id'))</th>
            <th>{{ trans('app.location') }}</th>
            <th>{{ trans('app.supplier') }}</th>
            <th class="text-center">{{ trans('app.payment_status') }}</th>
            <th class="text-right">{{ trans('app.payment_amount') }}</th>
            <th class="text-right">{{ trans('app.due_amount') }}</th>
            <th class="text-center">{{ trans('app.created_by') }}</th>
            <th class="text-center">{{ trans('app.action') }}</th>
          </tr>
        </thead>
        <tbody>
          @php
            $due_amount = $total_due = $total_amount = $total_paid = $total_items = 0;
          @endphp
          @foreach ($purchases as $purchase)
            @php
              $paid_amount = $purchase->invoices->sum('payment_amount') ?? 0;
              $due_amount = $purchase->final_total - $paid_amount;

              $total_amount += $purchase->final_total;
              $total_paid += $paid_amount;
              $total_due += $due_amount;
            @endphp

            <tr>
              <td>{{ $offset++ }}</td>
              <td>{{ displayDate($purchase->transaction_date) }}</td>
              <td>{{ $purchase->ref_no }}</td>
              <td>@include('partial.branch-detail-link', ['branch' => $purchase->warehouse])</td>
              <td>{{ $purchase->client->supplier_business_name ?? $purchase->client->name }}</td>
              <td class="text-center">{{ paymentStatus($purchase->payment_status) }}</td>
              <td class="text-right">$ {{ decimalNumber($purchase->final_total,2) }}</td>
              <td class="text-right">$ {{ decimalNumber($due_amount,2) }}</td>
              <td>{{ !empty($purchase->creator->staff) ? $purchase->creator->staff->name : $purchase->creator->name }}</td>
              <td class="text-center">
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                  <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-tasks"></i></button>
                  <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-primary dropdown-toggle" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu dropdown-menu-right">
                  
                      @if($purchase->payment_status != 'paid')
                        <a href="{{ route('payments.create', $purchase->id) }}" class="dropdown-item add_payment_modal"><i class="fa fa-money"></i> {{ trans('app.add_payment') }}</a>
                      @endif
                      <a href="{{ route('payments.show', $purchase->id) }}" class="dropdown-item view_payment_modal"><i class="fa fa-money"></i> {{ trans('app.view_payments') }}</a>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <td colspan="5" align="right"><b>{{ trans('app.total') }}</b></td>
          <td align="right"><b>$ {{ decimalNumber($total_amount, true) }}</b></td>
          <td align="right"><b>$ {{ decimalNumber($total_paid, true) }}</b></td>
          <td align="right"><b>$ {{ decimalNumber($total_due, true) }}</b></td>
          <td></td>
          <td></td>
        </tfoot>
      </table>
      {!! $purchases->appends(Request::except('page'))->render() !!}
    </div>
  </div>
</main>

<div class="modal fade payment_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>
@endsection

@section('js')
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>
  <script src="{{ asset('js/jquery-number.min.js') }}"></script>
  <script src="{{ asset('js/number.js') }}"></script>
  <script>
    $(document).ready(function() {
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });

      //Default settings for daterangePicker
      var ranges = {};
      var moment_date_format = 'DD/MM/YYYY';
      ranges['{{ trans('app.today')}}'] = [moment(), moment()];
      ranges['{{ trans('app.yesterday')}}'] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
      ranges['{{ trans('app.last_7_days')}}'] = [moment().subtract(6, 'days'), moment()];
      ranges['{{ trans('app.last_30_days')}}'] = [moment().subtract(29, 'days'), moment()];
      ranges['{{ trans('app.this_month')}}'] = [moment().startOf('month'), moment().endOf('month')];
      ranges['{{ trans('app.last_month')}}'] = [
        moment().subtract(1, 'month').startOf('month'),
        moment().subtract(1, 'month').endOf('month'),
      ];

      //Date range as a button
      $('#sell_list_filter_date_range').daterangepicker({
        ranges: ranges,
        startDate: '{{ '01/01/'.date('Y') }}',
        endDate: '{{ '31/12/'.date('Y') }}',
        locale: {
          cancelLabel: '{{ trans('app.clear')}}',
          applyLabel: '{{ trans('app.apply')}}',
          customRangeLabel: '{{ trans('app.custom_range')}}',
          format: moment_date_format,
          toLabel: '~',
        },
        opens: 'left',
        autoUpdateInput: false
      }, function (start, end) {
        $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
        submitSearchForm();
      });

      $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#sell_list_filter_date_range').val('');
        submitSearchForm();
      });

      $('#location, #client, #status').change(function () {
        submitSearchForm();
      });
    });

    $(document).on('click', '.add_payment_modal', function(e) {
      e.preventDefault();
      var container = $('.payment_modal');

      $.ajax({
        url: $(this).attr('href'),
        type: "GET",
        dataType: 'json',
        success: function(result) {
          if (result.status == 'due') {
            container.html(result.view).modal('show');
            $('#payment_date').datepicker({
              format: 'dd-mm-yyyy'
            });
            formatNumericFields();
            container.find('form#transaction_payment_add_form').validate();
          }
        },
      });
    });

    $(document).on('click', '.view_payment_modal', function(e) {
      e.preventDefault();
      var container = $('.payment_modal');

      $.ajax({
        url: $(this).attr('href'),
        type: 'GET',
        dataType: 'html',
        success: function(result) {
          $(container).html(result).modal('show');
        },
      });
    });

    function submitSearchForm() {
      var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
      var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
      $('input[name=start]').val(start);
      $('input[name=end]').val(end);
      $('#sale_search_f').submit();
    }
  </script>
@endsection
