@extends('layouts/backend')

@section('title', trans('app.purchase_report'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.report').trans('app.purchase_report') }}</h3>
      <form method="get" action="{{ route('report.purchase') }}" class="mb-4" id="sale_search_f">
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
              <div class="col-sm-4 col-lg-2 form-group">
                <label for="status" class="control-label">{{ trans('app.purchase_status') }}</label>
                <select name="group" id="group" class="form-control select2 select2-no-search">
                  <option value="">{{ trans('app.select_option') }}</option>
                  @foreach ($groups as $item)
                    <option value="{{ $item->id }}" {{ selectedOption($item->name, request('group')) }}>
                      {{ $item->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-sm-4 col-lg-2 form-group">
                <label for="status" class="control-label">{{ trans('app.purchase_status') }}</label>
                <select name="status" id="status" class="form-control select2 select2-no-search">
                  <option value="">{{ trans('app.select_option') }}</option>
                  @foreach (purchaseStatuses() as $statusKey => $statusTitle)
                    <option value="{{ $statusKey }}" {{ selectedOption($statusKey, request('status')) }}>
                      {{ $statusTitle }}
                    </option>
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
            </div>
            <div class="text-right">
              @include('partial.button-search', ['class' => 'mt-4'])
            </div>
          </div>
          {{-- Summary info --}}
          <div class="card-body">
            <h5>{!! trans('app.purchase') . trans('app.between') . ' ' . displayDate($startDate) . ' ' . trans('app.to') . ' ' . displayDate($endDate)
              . ' (' . $selectedBranch . ')' !!}</h5>
            <div class="row">
              <div class="col-md-6">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <th>{{ trans('app.total_purchase_product') }}</th>
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
              <th class="text-left">{{ trans('app.invoice_number') }}</th>
              <th>@sortablelink('branch_id', trans('app.branch'))</th>
              <th class="text-left">{{ trans('app.supplier') }}</th>
              <th class="text-center">{{ trans('app.quantity') }}</th>
              <th class="text-right">{{ trans('app.payment_amount') }}</th>
              <th class="text-right">{{ trans('app.paid_amount') }}</th>
              <th class="text-right">{{ trans('app.due_amount') }}</th>
              <th class="text-center">{{ trans('app.purchase_status') }}</th>
              <th class="text-center">{{ trans('app.payment_status') }}</th>
              <th class="text-center">{{ trans('app.note') }}</th>
            </tr>
          </thead>
          <tbody>
            @php
              $due_amount = $total_due = $total_amount = $total_paid = $total_items = 0;
            @endphp
            @foreach($loans as $loan)
              @php
                $items = $loan->purchase_lines->sum('quantity') ?? 0;
                $paid_amount = $loan->invoices->sum('payment_amount') ?? 0;
                $due_amount = $loan->final_total - $paid_amount;
              @endphp
              <tr>
                <td class="text-center">{{ $offset++ }}</td>
                <td class="text-left">{{ displayDate($loan->transaction_date) }}</td>
                <td class="text-left">
                  <a href="{{ route('purchase.show', $loan->id) }}" target="_blank">{{ $loan->ref_no }}</a>
                  @if (@$loan->return_parent->id)
                    <span class="text-danger">
                      <i class="fa fa-undo" aria-hidden="true"></i>
                    </span>
                    
                  @endif
                </td>
                <td class="text-left">
                  @if($loan->warehouse)
                    @include('partial.branch-detail-link', ['branch' => $loan->warehouse])
                  @else
                    {{ trans('app.none') }}
                  @endif
                </td>
                <td class="text-left">{{ $loan->client->supplier_business_name ?? $loan->client->name }}</td>
                <td align="center">{{ $items }}</td>
                <td class="text-right">$ {{ decimalNumber($loan->final_total, true) }}</td>
                <td class="text-right">$ {{ decimalNumber($paid_amount, true) }}</td>
                <td class="text-right">$ {{ decimalNumber($due_amount, true) }}</td>
                <td class="text-center">{{ purchaseStatuses($loan->status) }}</td>
                <td class="text-center">{{ paymentStatus($loan->payment_status) }}</td>
                <td class="text-center">{{ $loan->additional_note }}</td>
              </tr>
            @endforeach
          </tbody>
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
