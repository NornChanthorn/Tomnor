@extends('layouts/backend')

@section('title', trans('app.product_stock'))

@section('css')
  <style>
    .line-2 { line-height: 2.2; }
  </style>
@endsection

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.product_stock_report') }}</h3>
      <form method="get" action="{{ route('report.stock') }}" id="sale_search_f">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-md-6 col-lg-3 form-group">
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
              <div class="form-group col-md-6 col-lg-2">
                <label for="start_date" class="control-label">{{ trans('app.start_date') }}</label>
                <input type="text" name="start_date" id="start_date" class="form-control date-picker" placeholder="{{ trans('app.date_placeholder') }}" value="{{ displayDate($startDate) }}" readonly>
              </div>
              <div class="form-group col-md-6 col-lg-2">
                <label for="end_date" class="control-label">{{ trans('app.end_date') }}</label>
                <input type="text" name="end_date" id="end_date" class="form-control date-picker" placeholder="{{ trans('app.date_placeholder') }}" value="{{ displayDate($endDate) }}" readonly>
              </div>
              <div class="form-group col-md-6 col-lg-3">
                <label for="">{{ trans('app.product_name') }}/{{ trans('app.product_code/sku') }}</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" id="q" placeholder="{{ trans('app.search') }} ...">
              </div>
              <div class="col-sm-12 text-right">
                @include('partial.button-search', ['class' => 'mt-4 line-2'])
              </div>
            </div>
          </div>

          <div class="card-body">
            <h5>{!! trans('app.product_stock_report') .' '. trans('app.between') . ' ' . ($startDate ? displayDate($startDate) : '___') . ' ' . trans('app.to') . ' ' . ($endDate ? displayDate($endDate) : '___') . ' (' . $selectedBranch . ')' !!}</h5>
            <div class="row">
              <div class="col-md-6">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <th>{{ trans('app.total_products_stock') }}</th>
                      <td>{{ $report->total_stock }}</td>
                    </tr>
                    <tr>
                      <th>{{ trans('app.total_amount') }}</th>
                      <td>$ {{ decimalNumber($report->total_stock_amount, true) }}</td>
                    </tr>

                    <tr>
                      <th>{{ trans('app.total_products_stock_oversale') }}</th>
                      <td>{{ $report->total_stock_oversale }}</td>
                    </tr>
                    <tr>
                      <th>{{ trans('app.total_amount_oversale') }}</th>
                      <td>$ {{ decimalNumber($report->total_stock_amount_oversale, true) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-md-6">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <th>{{ trans('app.purchased_products') }}</th>
                      <td>{{ $report->total_purchase }}</td>
                    </tr>
                    <tr>
                      <th>{{ trans('app.sold_products') }}</th>
                      <td>{{ $report->total_sale }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </form>
      <br>

      <div class="table-responsive resize-w">
        <table class="table table-hover table-striped table-bordered">
          <thead>
            <tr>
              <th class="text-center">{{ trans('app.no_sign') }}</th>
              <th>{{ trans('app.product_code') }}</th>
              <th>{{ trans('app.product') }}</th>
              <th class="text-right">{{ trans('app.product_price') }}</th>
              <th class="text-center">{{ trans('app.current_stock') }}</th>
              <th class="text-center">{{ trans('app.purchased_unit') }}</th>
              <th class="text-center">{{ trans('app.sold_unit') }}</th>
              <th class="text-center">{{ trans('app.transfered_unit_in') }}</th>
              <th class="text-center">{{ trans('app.transfered_unit_out') }}</th>
              <th class="text-center">{{ trans('app.adjusted_unit') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($loans as $loan)
              @php
                $unit = $loan->unit;
                $stock = $loan->stock ?? 0;
              @endphp
              <tr>
                <td class="text-center">{{ $offset++ }}</td>
                <td>
                  <a href="{{ route('product.show', $loan->product_id) }}" target="_blank">{{ wordwrap(strlen($loan->variantion_sku)==0 ? $loan->sku : $loan->variantion_sku, 4, ' ', true) }}</a>
                </td>
                <td>{{ $loan->product.($loan->variantion_name!='DUMMY' ? ' - '.$loan->variantion_name : '') }}</td>
                <td class="text-right"><b>$ {{ decimalNumber($loan->unit_price, true) }}</b></td>
                <td class="text-center">{{ number_format($loan->stock, 0) .' '. $unit }}</td>
                <td class="text-center">{{ number_format($loan->total_purchased, 0) .' '. $unit }}</td>
                <td class="text-center">{{ number_format($loan->total_sold, 0) .' '. $unit }}</td>
                <td class="text-center">{{ number_format($loan->total_transfered_in, 0) .' '. $unit }}</td>
                <td class="text-center">{{ number_format($loan->total_transfered_out, 0) .' '. $unit }}</td>
                <td class="text-center">{{ number_format($loan->total_adjusted, 0) .' '. $unit }}</td>
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
