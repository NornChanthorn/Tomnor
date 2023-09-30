@extends('layouts/backend')

@section('title', trans('app.sell_report'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.report').trans('app.sell_report') }}</h3>
      <form method="get" action="{{ route('report.sell') }}" class="mb-4" id="sale_search_f">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-sm-6 col-lg-3 form-group">
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
              <div class="col-sm-3 col-lg-2 form-group">
                <label for="status" class="control-label">{{ trans('app.sale_status') }}</label>
                <select name="status" id="status" class="form-control">
                  <option value="">{{ trans('app.select_option') }}</option>
                  @foreach (saleStatuses() as $k => $_sta)
                    <option value="{{ $k }}" {{ $k==request('status') ? 'selected' : '' }}>
                      {{ $_sta }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-sm-3 col-lg-2 form-group">
                <label for="payment_status" class="control-label">{{ trans('app.payment_status') }}</label>
                <select name="payment_status" id="payment_status" class="form-control">
                  <option value="">{{ trans('app.select_option') }}</option>
                  @foreach (paymentStatus() as $k => $_sta)
                    <option value="{{ $k }}" {{ $k==request('payment_status') ? 'selected' : '' }}>{{ $_sta }}</option>
                  @endforeach
                </select>
              </div>
              {{-- Start date --}}
              <div class="form-group col-sm-3 col-lg-2 pr-0">
                <label for="start_date" class="control-label">{{ trans('app.start_date') }}</label>
                <input type="text" name="start_date" id="start_date" class="form-control date-picker" placeholder="{{ trans('app.date_placeholder') }}" value="{{ displayDate($startDate) }}">
              </div>

              {{-- End date --}}
              <div class="form-group col-sm-3 col-lg-2 pr-0">
                <label for="end_date" class="control-label">{{ trans('app.end_date') }}</label>
                <input type="text" name="end_date" id="end_date" class="form-control date-picker" placeholder="{{ trans('app.date_placeholder') }}" value="{{ displayDate($endDate) }}">
              </div>
              <div class="col-sm-6 col-lg-3">
                <label for="">{{ trans('app.product_name') }}/{{ trans('app.product_code/sku') }}</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" id="q" placeholder="{{ trans('app.search') }} ...">
              </div>
            </div>
            <div class="text-right">
              @include('partial.button-search', ['class' => 'mt-4'])
            </div>
          </div>
          {{-- Summary info --}}
          <div class="card-body">
            <h5>{!! trans('app.sale') . trans('app.between') . ' ' . displayDate($startDate) . ' ' . trans('app.to') . ' ' . displayDate($endDate)
              . ' (' . $selectedBranch . ')' !!}</h5>
            <div class="row">
              <div class="col-md-6">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <th>{{ trans('app.total_sale_product') }}</th>
                      <td><span class="total_items">{{ $summeries->items }}</span></td>
                    </tr>
                    <tr>
                      <th>{{ trans('app.total_sale_amount') }}</th>
                      <td>$ <span class="total_amount">{{ decimalNumber($summeries->total_amount, true) }}</span></td>
                    </tr>
                    <tr>
                      <th>{{ trans('app.total_sale_paid_amount') }}</th>
                      <td>$ <span class="total_paid">{{ decimalNumber($summeries->paid_amount, true) }}</span></td>
                    </tr>
                    <tr>
                      <th>{{ trans('app.total_sale_due_amount') }}</th>
                      <td>$ <span class="total_due">{{ decimalNumber($summeries->due_amount, true) }}</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </form>

      <div class="table-responsive resize-w">
        <table class="table table-hover table-striped table-bordered">
          <thead>
            <tr>
              <th class="text-center">{{ trans('app.no_sign') }}</th>
              <th class="text-left">{{ trans('app.purchase_date') }}</th>
              <th>@sortablelink('branch_id', trans('app.branch'))</th>
              <th class="text-left">{{ trans('app.invoice_number') }}</th>
              <th class="text-left">{{ trans('app.client') }}</th>
              <th class="text-left">{{ trans('app.agent') }}</th>
              <th class="text-center">{{ trans('app.product') }}</th>
              <th class="text-right">{{ trans('app.total_amount') }}</th>
              <th class="text-right">{{ trans('app.paid_amount') }}</th>
              <th class="text-right">{{ trans('app.due_amount') }}</th>
              
              <th class="text-center">{{ trans('app.payment_status') }}</th>
              <th class="text-center">{{ trans('app.sale_status') }}</th>
              <th class="text-center">{{ trans('app.note') }}</th>
            </tr>
          </thead>
          <tbody>
            @php
              $due_amount = $total_due = $total_amount = $total_paid = $total_items = 0;
            @endphp
            @foreach($loans as $loan)
              @php
                $items = $loan->sell_lines->sum('quantity') ?? 0;
                $paid_amount = $loan->invoices->sum('payment_amount');
                $due_amount = $loan->final_total - $paid_amount;
              @endphp
              <tr>
                <td class="text-center">{{ $offset++ }}</td>
                <td class="text-left">{{ displayDate($loan->transaction_date) }}</td>
                <td class="text-left">
                  @if($loan->warehouse)
                    @include('partial.branch-detail-link', ['branch' => $loan->warehouse])
                  @else
                    {{ trans('app.none') }}
                  @endif
                </td>
                <td class="text-left">
                  <a href="{{ route('sale.show', $loan->id) }}" target="_blank">{{ $loan->invoice_no }}</a>
                  @if (@$loan->return_parent->id)
                    <span class="text-danger">
                      <i class="fa fa-undo" aria-hidden="true"></i>
                    </span>
                    
                  @endif
                </td>
                <td class="text-left">
                  @if($loan->client)
                    @include('partial.client-detail-link', ['client' => $loan->client])
                  @else
                    {{ trans('app.none') }}
                  @endif
                </td>
                <td>@include('partial.staff-detail-link', ['staff' => $loan->staff])</td>
                <td align="left">
                  @if($loan->sell_lines)
                    @foreach($loan->sell_lines as $sell_line)
                      @include('partial.product-detail-link', ['product' => $sell_line->product])
                    @endforeach
                  @else
                    {{ trans('app.none') }}
                  @endif
                </td>
                <td align="right">$ {{ decimalNumber($loan->final_total, true) }}</td>
                <td align="right">$ {{ decimalNumber($paid_amount, true) }}</td>
                <td align="right">$ {{ decimalNumber(($due_amount), true) }}</td>
                <td align="center">{{ paymentStatus($loan->payment_status) }}</td>
                <td align="center">{{ saleStatuses($loan->status) }}</td>
                <td class="text-center">{{ $loan->additional_note }}</td>
              </tr>
            @endforeach
          </tbody>
          {{-- <tfoot>
            <td colspan="6" align="right"><b>{{ trans('app.total') }}</b></td>
            <td align="center">{{ $total_items }}</td>
            <td align="right"><b>$ {{ decimalNumber($total_amount, true) }}</b></td>
            <td align="right"><b>$ {{ decimalNumber($total_paid, true) }}</b></td>
            <td align="right"><b>$ {{ decimalNumber($total_due, true) }}</b></td>
            <td colspan="3"></td>
          </tfoot> --}}
        </table>
      </div>
      {!! $loans->appends(Request::except('page'))->render() !!}
    </div>
  </main>
@endsection

@section('js')
  <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
  <script src="{{ asset('js/date-time-picker.js') }}"></script>
  <script>
    $(document).ready(function() {
      $(".date-picker").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        orientation: 'bottom right'
      });
    });

    function submitSearchForm() {
      $('#sale_search_f').submit();
    }
  </script>
@endsection
