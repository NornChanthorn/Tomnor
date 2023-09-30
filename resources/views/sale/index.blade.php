@extends('layouts/backend')

@section('title', trans('app.sale'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ @$group_id ? groupContacts($group_id): trans('app.sale').trans('app.all')}}</h3>
      @include('partial.flash-message')

      <div class="card mb-2">
        <div class="card-header">
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
                <label for="sale_date" class="control-label">
                  {{ trans('app.sale_status') }}
                </label>
                <select name="status" id="status" class="form-control">
                  <option value="">{{ trans('app.all') }}</option>
                  @foreach (saleStatuses() as $k => $_sta)
                  <option value="{{ $k }}" {{ selectedOption($k, request('status')) }}>
                    {{ $_sta }}
                  </option>
                  @endforeach
                </select>
              </div>
              <div class="form-group col-sm-3 col-lg-2">
                <label for="start_date" class="control-label">{{ trans('app.start_date') }}</label>
                <input type="text" name="start_date" id="start_date" class="form-control date-picker" readonly placeholder="{{ trans('app.date_placeholder') }}" value="{{ displayDate(request('start_date')) }}">
              </div>
              <div class="form-group col-sm-3 col-lg-2">
                <label for="end_date" class="control-label">{{ trans('app.end_date') }}</label>
                <input type="text" name="end_date" id="end_date" class="form-control date-picker" readonly placeholder="{{ trans('app.date_placeholder') }}" value="{{ displayDate(request('end_date')) }}">
              </div>
              <div class="form-group col-sm-4 col-lg-4">
                    <label for="">{{ trans('app.search') }} {{ trans('app.invoice') }}</label>
                    <input type="text" name="sale_code" value="{{ request('sale_code') }}" class="form-control" id="sale_code" placeholder="{{ trans('app.search') }} ...">
                </div>
                <div class="col-md-4">
                    <label for="sale_date" class="control-label">
                        {{ trans('app.payment_status') }}
                    </label>
                    <select name="payment_status" id="payment_status" class="form-control">
                        <option value="">{{ trans('app.all') }}</option>
                        @foreach (paymentStatus() as $k => $_sta)
                        <option value="{{ $k }}" {{ selectedOption($k, request('status')) }}>
                        {{ $_sta }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                  <label for="client" class="control-label">
                    {{ trans('app.client') }}
                  </label>
                  <select name="client" id="client" class="form-control select2">
                    <option value="">{{ trans('app.select_option') }}</option>
                  </select>
                </div>
            </div>
            <div class="text-right">
              @include('partial.button-search', ['class' => 'mt-4'])
            </div>
          </form>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-6">
          @include('partial.anchor-create', ['href' => route('sale.create')])
        </div>
        <div class="col-lg-6 text-right">@include('partial.item-count-label')</div>
      </div>

      <div class="table-responsive" style="min-height: 400px">
        <table class="table table-bordered table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center">{{ trans('app.no_sign') }}</th>
              <td>@sortablelink('sale_date', trans('app.sale_date'))</td>
              <th>{{ trans('app.location') }}</th>
              <td>@sortablelink('sale_code', trans('app.sale_code'))</td>
              <th>{{ trans('app.client') }}</th>
              @if(isAdmin() || empty(auth()->user()->staff))
                <th>{{ trans('app.agent') }}</th>
              @endif
              <th class="text-right">{{ trans('app.total_amount') }}</th>
              <th class="text-right">{{ trans('app.paid_amount') }}</th>
              <th class="text-right">{{ trans('app.due_amount') }}</th>
              <th class="text-center">{{ trans('app.payment_status') }}</th>
              <th class="text-center">{{ trans('app.sale_status') }}</th>
              <th class="text-center">{{ trans('app.total_product') }}</th>
              <th class="text-right">{{ trans('app.action') }}</th>
            </tr>
          </thead>
          <tbody>
            @php
              $due_amount = $total_due = $total_amount = $total_paid = $total_items = 0;
            @endphp
            @foreach ($sales as $sale)
              @php
                $items = $sale->sell_lines->count() ?? 0;
                $paid_amount = $sale->invoices->sum('payment_amount');
                $due_amount = $sale->final_total - $paid_amount;
                $total_due += $due_amount;
                $total_amount += $sale->final_total;
                $total_paid += $paid_amount;
                $total_items += $items;
              @endphp
            <tr>
              <td align="center">{{ $offset++ }}</td>
              <td>{{ displayDate($sale->transaction_date) }}</td>
              <td>
                @if($sale->warehouse)
                  @include('partial.branch-detail-link', ['branch' => $sale->warehouse])
                @else
                  {{ trans('app.none') }}
                @endif
                
              </td>
              <td>
                {{ $sale->invoice_no }}
                @if (@$sale->return_parent->id)
                  <span class="text-danger">
                    <i class="fa fa-undo" aria-hidden="true"></i>
                  </span>
                  
                @endif
                
              
              </td>
              <td>
                {{ $sale->client->name }}
              </td>
              @if(isAdmin() || empty(auth()->user()->staff))
                <td>
                  @if($sale->staff)
                    @include('partial.staff-detail-link', ['staff' => $sale->staff])
                  @else
                    {{ trans('app.none') }}
                  @endif
                </td>
              @endif
              <td align="right">$ {{ decimalNumber($sale->final_total, true) }}</td>
              <td align="right">$ {{ decimalNumber($paid_amount, true) }}</td>
              <td align="right">$ {{ decimalNumber(($due_amount), true) }}</td>
              <td align="center">{{ paymentStatus($sale->payment_status) }}</td>
              <td align="center">{{ saleStatuses($sale->status) }}</td>
              <td align="center">{{ $items }}</td>
              <td class="text-center">
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                  <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-tasks"></i></button>
                  <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-primary dropdown-toggle" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu dropdown-menu-right">
                      @if (Auth::user()->can('sale.browse'))
                        <a href="{{ route('sale.invoice', $sale->id) }}" title="{{ trans('app.invoice') }}" class="dropdown-item" target="_blank"><i class="fa fa-print"></i> {{ trans('app.invoice') }}</a>
                      @endif

                      <div class="dropdown-divider"></div>
                      @if(Auth::user()->can('sale.browse'))
                        <a href="{{ route('sale.show', $sale->id) }}" class="dropdown-item" title="{{ __('app.view_detail') }}"><i class="fa fa-eye"></i> {{ __('app.view_detail') }}</a>
                      @endif

                      @if(Auth::user()->can('sale.edit'))
                        <a href="{{ route('sale.edit', $sale->id) }}" class="dropdown-item" title="{{ __('app.edit') }}"><i class="fa fa-pencil-square-o"></i> {{ __('app.edit') }}</a>
                      @endif
                      @if(Auth::user()->can('sell-return.add') && $sale->payment_status = 'paid')
                        <a href="{{ route('sell-return.add', $sale->id) }}" class="dropdown-item" title="{{ __('app.sell-return') }}"><i class="fa fa-reply"></i> {{ __('app.sell-return') }}</a>
                      @endif
                      @if(Auth::user()->can('sale.delete'))
                        <a href="javascript:void(0);" title="{{ __('app.delete') }}" data-url="{{ route('sale.destroy', $sale->id) }}" class="dropdown-item btn-delete"><i class="fa fa-trash-o"></i> {{ __('app.delete') }}</a>
                      @endif

                      <div class="dropdown-divider"></div>
                      @if($sale->payment_status ='paid')
                        <a href="{{ route('payments.create', $sale->id) }}" class="dropdown-item add_payment_modal"><i class="fa fa-money"></i> {{ trans('app.add_payment') }}</a>
                      @endif

                      <a href="{{ route('payments.show', $sale->id) }}" class="dropdown-item view_payment_modal"><i class="fa fa-money"></i> {{ trans('app.view_payments') }}</a>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <td colspan="{{ empty(auth()->user()->staff) ? 6 : 5 }}" align="right"><b>{{ trans('app.total') }}</b></td>
            <td align="right"><b>$ {{ decimalNumber($total_amount, true) }}</b></td>
            <td align="right"><b>$ {{ decimalNumber($total_paid, true) }}</b></td>
            <td align="right"><b>$ {{ decimalNumber($total_due, true) }}</b></td>
            <td colspan="2"></td>
            <td align="center">{{ $total_items }}</td>
            <td></td>
          </tfoot>
        </table>
        {!! $sales->appends(Request::except('page'))->render() !!}
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
  <script type="text/javascript">
    $(document).ready( function() {
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });

      $(".date-picker").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        orientation: 'bottom right'
      });

      $("#client").select2({
        ajax: {
          url: "{{ route('contact.client-list') }}",
          dataType: 'json',
          data: function(params) {
            return {
              search: params.term,
              type: 'public',
            }
          },
          processResults: function(data) {
            return {
              results: data
            }
          }
        }
      });

      $('#client').change(function () {
        $('#client').select2('close');
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
      // var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
      // var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
      // $('input[name=start]').val(start);
      // $('input[name=end]').val(end);
      $('#sale_search_f').submit();
    }
  </script>
@endsection
