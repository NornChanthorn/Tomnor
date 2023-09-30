@extends('layouts/backend')

@section('title', trans('app.product_sell'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.product_purchase') }}</h3>
      <form method="get" action="{{ route('report.product-purchase') }}">
        <div class="card">
          <div class="card-header">
            <div class="row">
              {{-- <div class="col-sm-6 col-md-3">
                <label for="type" class="control-label">{{ trans('app.type') }}</label>
                <select name="type" id="type" class="form-control select2">
                  @foreach ($types as $type)
                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                      {{ trans('app.'.$type) }}
                    </option>
                  @endforeach
                </select>
              </div> --}}
              <div class="col-sm-6 col-md-3">
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
              <div class="col-sm-6 col-md-3">
                <label for="start_date">{{ trans('app.start_date') }}</label>
                <input type="text" name="start_date" id="start_date" class="form-control date-picker" value="{{ request('start_date') }}" placeholder="{{ trans('app.date_placeholder') }}" readonly>
              </div>
              <div class="col-sm-6 col-md-3">
                <label for="end_date">{{ trans('app.end_date') }}</label>
                <input type="text" name="end_date" id="end_date" class="form-control date-picker" value="{{ request('end_date') }}" placeholder="{{ trans('app.date_placeholder') }}" readonly>
              </div>
            </div>
            <div class="text-right">
              @include('partial.button-search', ['class' => 'mt-4'])
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-sm-6">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr>
                      <th>{{ trans('app.total_product') }}</th>
                      <th>{{ number_format($totalProduct) }}</th>
                    </tr>
                    <tr>
                      <th>{{ trans('app.total_product_price') }}</th>
                      <th>$ {{ decimalNumber($totalProductPrice, true) }}</th>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </form>
      <br>
      @include('partial.item-count-label')
      <div class="table-responsive resize-w">
        <table class="table table-hover table-striped table-bordered">
          <thead>
            <tr>
              <th class="text-center">{{ trans('app.no_sign') }}</th>
              <th>@sortablelink('branch_id', trans('app.branch'))</th>
              <th>@sortablelink('product_id', trans('app.product'))</th>
              <th class="text-left">{{ trans('app.purchase_date') }}</th>
              <th class="text-right">{{ trans('app.product_price') }}</th>
              <th class="text-center">{{ trans('app.quantity') }}</th>
              <th class="text-right">{{ trans('app.total') }}</th>
              <th>{{ trans('app.note') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach($loans as $loan)
              <tr>
                <td class="text-center">{{ $offset++ }}</td>
                <td>{{ $loan->transaction->warehouse->location }}</td>
                <td>{{ $loan->product->name.($loan->variantion&&$loan->variantion->name!='DUMMY' ? ' - '.$loan->variantion->name : '') }}</td>
                <td class="text-left">{{ displayDate($loan->created_at) }}</td>
                <td class="text-right"><b>$ {{ decimalNumber($loan->purchase_price, true) }}</b></td>
                <td class="text-center">{{ $loan->quantity }}</td>
                <td class="text-right"><b>$ {{ decimalNumber(($loan->purchase_price*$loan->quantity), true) }}</b></td>
                <td>{{ $loan->transaction->additional_note }}</td>
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
  </script>
@endsection
