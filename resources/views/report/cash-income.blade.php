@extends('layouts/backend')

@section('title', trans('app.cash_income_report'))

@section('content')
<main class="app-content">
  <div class="tile">
    <div class="row">
      <div class="col-sm-12">
        <h3 class="page-heading">{{ trans('app.cash_income_report') }}</h3>
        @include('partial/flash-message')
        <form action="" method="get" class="mb-4">
            <div class="card">
                <div class="card-header">
                  <div class="row">
                    @if(empty(auth()->user()->staff))
                      <div class="col-sm-6 col-md-4 form-group">
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
                      <div class="col-sm-3 col-lg-4 form-group">
                        <label for="start_date">{{ trans('app.start_date') }}</label>
                        <input type="text" name="start_date" id="start_date" class="form-control date-picker" value="{{ request('start_date') ?? displayDate($startDate) }}" placeholder="{{ trans('app.date_placeholder') }}">
                      </div>
                      <div class="col-sm-3 col-lg-4 form-group">
                        <label for="end_date">{{ trans('app.end_date') }}</label>
                        <input type="text" name="end_date" id="end_date" class="form-control date-picker" value="{{ request('end_date') ?? displayDate($startDate) }}" placeholder="{{ trans('app.date_placeholder') }}">
                      </div>
                    @endif
                    <div class="col-md-12 text-right">
                      @include('partial.button-search', ['class' => 'btn-lg'])
                    </div>
                  </div>
                </div>
            </div>
        </form>
      </div>
     
    </div>
    <div class="row justify-content-center">
      <div class="col-md-10">
        <h5 class="pull-left">{{ trans('app.cash_income') }} ({{ displayDate($startDate) }} {{ trans('app.to') }} {{ displayDate($endDate) }})</h5>
        <table class="table table-hover table-bordered">
            <tbody>
              <tr>
                  <th style="width: 40%">{{ trans('app.total').trans('app.depreciation_amount') }}</th>
                  <th>$ {{ decimalNumber($total->downPayment, true) }}</th>
                  <th><a href="{{ url('/report/client-payment?type=leasing-dp') }}">បង់ប្រាក់ដើម</a></th>
              </tr>
              <tr>
                  <th>{{ trans('app.total_repayment_loan_amount') }}</th>
                  <th>$ {{ decimalNumber($total->loanRepayment, true) }}</th>
                  <th>
                    <a href="{{ url('/report/client-payment?type=leasing') }}">​បង់ប្រាក់ប្រចាំខែ</a>
                  </th>
              </tr>
              <tr>
                  <th>{{ trans('app.total_sale_amount') }}</th>
                  <th>$ {{ decimalNumber(($total->saleAmount - 0), true) }}</th>
                  <th><a href="{{ url('/report/sell') }}">{{ trans('app.sell_report') }}</a></th>
              </tr>
              <tr class="bg-success text-white">
                @php
                  $totalAmount =$total->saleAmount + $total->loanRepayment + $total->downPayment;
                @endphp
                  <th>{{ trans('app.total_amount') }}</th>
                  <th>$ {{ decimalNumber($totalAmount, true) }}</th>
                  <th>
                  
                  </th>
              </tr>
              {{-- @php
                  $total_group_amount=0;
              @endphp
                @foreach ($groups as $item)
                    <tr>
                      <th>{{ $item->name }}</th>
                      <th>$ {{ decimalNumber($item->amount,true) }}</th>
                      <th>
                        @php
                            $total_group_amount+=$item->amount;
                        @endphp
                      </th>
                    </tr>

                @endforeach
                <tr>
                  <th>
                    សរុបទឹកប្រាក់ចំណាយលើការទិញសរុប
                  </th>
                  <th>
                    $ {{ decimalNumber($total_group_amount,true) }}
                  </th>
                  <th></th>
                </tr> --}}
                <tr>
                  <th>ទឹកប្រាក់ចំណាយទិញពីអតិថិជន</th>
                  <th>$ {{ decimalNumber($purchaseCustomer,true) }}</th>
                  <th>
                    <a href="   {{ url('/report/purchase') }}">{{ trans('app.purchase_report') }}</a>
                 
                  </th>
                </tr>
                <tr class="bg-success text-white">
                  <th>
                    សរុបទឹកប្រាក់នៅសល់
                  </th>
                  <th>
                    $ {{ decimalNumber(($totalAmount-$purchaseCustomer),true) }}
                  </th>
                  <th></th>
                </tr>
            </tbody>
        </table>
      </div>
    </div>
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