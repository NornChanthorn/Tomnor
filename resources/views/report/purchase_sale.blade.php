@extends('layouts/backend')

@section('title', trans('app.purchase_sale'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.report').trans('app.purchase_sale') }}</h3>
      <form method="get" action="{{ route('report.purchase-sale') }}" class="mb-4" id="sale_search_f">
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
            <h5>{!! trans('app.purchase_sale') .' '. trans('app.between') . ' ' . displayDate($startDate) . ' ' . trans('app.to') . ' ' . displayDate($endDate)
              . ' (' . $selectedBranch . ')' !!}</h5>
            <div class="row">
              <div class="col-md-6">
                <table class="table table-bordered table-hover">
                  <tbody>
                    <tr class="bg-success text-white">
                      <td colspan="2">{{ trans('app.purchase') }}</td>
                    </tr>
                    <tr>
                      <th>{{ trans('app.total_purchase_amount') }}</th>
                      <td>$ <span class="total_items">{{ decimalNumber($report->total_purchase, true) }}</span></td>
                    </tr>
                    <tr>
                      <th>{{ trans('app.total_due_purchase_amount') }}</th>
                      <td>$ <span class="total_amount">{{ decimalNumber($report->total_due_purchase, true) }}</span></td>
                    </tr>
                    <tr class="bg-success text-white">
                      <td colspan="2">{{ trans('app.sale') }}</td>
                    </tr>
                    <tr>
                      <th>{{ trans('app.total_sale_amount') }}</th>
                      <td>$ <span class="total_paid">{{ decimalNumber($report->total_sale, true) }}</span></td>
                    </tr>
                    <tr>
                      <th>{{ trans('app.total_due_sale_amount') }}</th>
                      <td>$ <span class="total_due">{{ decimalNumber($report->total_due_sale, true) }}</span></td>
                    </tr>
                    <tr class="bg-success text-white">
                      <td colspan="2">ប្រាក់ចំនេញ</td>
                    </tr>
                    <tr>
                      <th>{{ trans('app.total_purchase_amount') }} - {{ trans('app.total_sale_amount') }}</th>
                      <td>$ <span class="total_summary">{{ decimalNumber($report->summary, true) }}</span></td>
                    </tr>
                  </tbody>
                </table>
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
