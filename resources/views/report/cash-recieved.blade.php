@extends('layouts/backend')

@section('title', trans('app.cash_recieved_report'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.cash_recieved_report') }}</h3>

      <form method="get" action="">
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
              @endif
              <div class="col-sm-3 col-lg-4 form-group">
                <label for="type">{{ trans('app.type') }}</label>
                <select name="type" class="form-control" id="">
                  <option value="">{{ trans('app.select_option') }} {{ trans('app.type') }}</option>
                  @foreach (sellTypes() as $sk => $sv)
                    <option {{ request('type') == $sk ? 'selected' : '' }} value="{{ $sk }}">{{ $sv }}</option>
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
              <div class="col-lg-12 text-right">
                @include('partial.button-search', ['class' => 'btn-lg'])
              </div>
            </div>
          </div>
        </div>
      </form>
      <br>

      <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-6">
          <table class="table table-bordered">
            @php
                 $total_payment = 0;
            @endphp
            @foreach (paymentMethods() as $pk => $pv)
              @if($summeries->$pk>0)
                <tr>
                  <th>
                    {{ $pv }}
                  </th>
                  <th>
                    {{ num_f($summeries->$pk) }}
                  </th>
                </tr>
              @endif
              @php
                  $total_payment +=$summeries->$pk;
              @endphp
            @endforeach
            <tr class="bg-success text-white">
              <th>
                {{ trans('app.total_amount') }}
              </th>
              <th>
                {{ num_f($total_payment) }}
              </th>
            </tr>
          
          </table>
        </div>
       
      </div>
      <hr>

      @include('partial.flash-message')
      @include('partial.item-count-label',['itemCount' =>$itemCount])
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>
                {{ trans('app.no_sign') }}
              </th>
              <th>
                {{ trans('app.invoice_number') }}
              </th>
              
              <th>
                {{ trans('app.reference_number') }}
              </th>
              <th>
                {{ trans('app.payment_date') }}
              </th>
              <th>
                {{ trans('app.payment_method') }}
              </th>
              <th>
                {{ trans('app.type') }}
              </th>
              <th>
                {{ trans('app.amount') }}
              </th>
            </tr>
          </thead>
          <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                  <td>
                    {{ no_f($offset++) }}
                  </td>
                  <td>
                    @if (@$invoice->loan)
                      <a href="{{ route('loan.invoice',@$invoice->loan) }}">
                        {{ @$invoice->loan->client_code }}
                      </a>
                  
                    @else
                      @if (@$invoice->transaction)
                      <a href="{{ route('sale.invoice',@$invoice->transaction) }}">
                        {{ @$invoice->transaction->invoice_no }}
                      </a>
                      @endif
                     
                    @endif
                   
                  </td>
                  <td>
                    {{ $invoice->reference_number ?? $invoice->invoice_number }}
                  </td>
                  <td>
                    {{ displayDate($invoice->payment_date) }}
                  </td>
                  <td>
                    {{ paymentMethods($invoice->payment_method) }}
                  </td>
                  <td>
                    {{ sellTypes($invoice->type) }}
                  </td>
                  <th>
                    {{ num_f($invoice->total) }}
                  </th>
                </tr>
            @endforeach

          </tbody>
          <tfoot>
            <tr>
              <td colspan="6">
                
              </td>
              <th>
                {{ num_f($invoices->sum('total')) }}
              </th>
            </tr>
          </tfoot>
        </table>
        {!! $invoices->appends(Request::except('page'))->render() !!}
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
