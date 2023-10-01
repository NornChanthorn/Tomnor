@extends('layouts/backend')

@section('title', trans('app.payment'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.payment') }}</h3>
      @include('partial/flash-message')
      <div class="d-print-none">
        <div class="card">
          <div class="card-header">
            <form method="get" action="{{ route('repayment.list') }}">
              <div class="row">
                  {{-- Branch --}}
                  @if(empty(auth()->user()->staff))
                  <div class="col-md-3">
                    <label for="">{{ trans('app.branch') }}</label>
                    <select name="branch" id="branch" class="form-control select2">
                      <option value="">{{ trans('app.all_branches') }}</option>
                      @foreach (allBranches() as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>{{ $branch->location }}</option>
                      @endforeach
                    </select>
                  </div>
                  @endif
                  {{-- End date --}}

                  <div class="col-md-3">
                    <label for="start_date">{{ trans('app.date') }}</label>
                    <input type="text" name="date" id="date" class="form-control date-picker" readonly placeholder="{{ trans('app.date_placeholder') }}" value="{{ request('date') }}">
                  </div>
                  <div class="col-md-3">
                    <label for="sort">{{ trans('app.sort') }}</label>
                    <select name="sort" class="form-control" id="">
                      <option value="asc" {{ request('sort')== 'asc' ? 'selected' : '' }}>{{ trans('app.asc') }}</option>
                      <option value="desc" {{ request('sort')== 'desc' ? 'selected' : '' }}>{{ trans('app.desc') }}</option>
                    </select>
                  </div>
                  {{-- Text search --}}
                  <div class="col-md-3">
                    <label for="">{{ trans('app.search_placeholder') }}</label>
                    @include('partial.search-input-group')
                  </div>

              </div>
            </form>
          </div>
        </div>

        <br>
        @include('partial.item-count-label')
        <button onclick="window.print()" class="btn btn-sm btn-success pull-right mb-1">{{ trans('app.print') }}</button>
      </div>

      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>{{ trans('app.no_sign') }}</th>
              <th>@sortablelink('client_code', trans('app.loan_code'))</th>
              <th>{{ trans('app.client') }}</th>
              <th>{{ trans('app.profile_photo') }}</th>
              <th>@sortablelink('client_id', trans('app.client_code'))</th>
              <th>{{ trans('app.phone') }}</th>
              <th>{{ trans('app.branch') }}</th>
              <th>{{ trans('app.next_payment_date') }}</th>
              <th>{{ trans('app.payment_amount') }}</th>
              <th>{{ trans('app.count_late_date') }}</th>
              <th >
                {{ trans('app.product_ime') }}
              </th>
              <th>
                {{ trans('app.icloud') }}
              </th>
              <th style="width: 10%">
                {{ trans('app.note') }}
              </th>


            </tr>
          </thead>
          <tbody>
            @foreach ($loans as $loan)
              @php
                $amountToPay = $loan->total_amount - $loan->total_paid_amount;
                $count_late_date = date_diff(date_create($loan->schedules[0]->payment_date), date_create(now()))->format('%a')
              @endphp
                <tr>
                  <td>
                    {{ no_f($offset++) }}</td>
                  <td>
                    {{ $loan->account_number }}
                  </td>
                  <td>@include('partial.client-detail-link', ['client' => $loan->client])</td>
                  <td>@include('partial.client-profile-photo', ['client' => $loan->client])</td>
                  <td>
                    @include('partial.loan-detail-link')
                  </td>
                  <td>{{ $loan->client->first_phone }} {{ $loan->client->second_phone ? ', '.$loan->client->second_phone : "" }}</td>
                  <td>{{ $loan->branch->location ?? trans('app.n/a') }}</td>
                  <td>{{ displayDate($loan->payment_date) }}</td>
                  <td><b>{{ $amountToPay ? '$ '. decimalNumber($amountToPay, true) : '' }}</b></td>
                  <td>
                    <b>
                      {{ $loan->late_payment }}
                    </b>
                  </td>
                  <td>
                    @foreach ($loan->transaction->sell_lines as $item)
                      @if (@$item->product)
                        @include('partial.product-detail-link', ['product' => @$item->product, 'variantion' => @$item->variantion->name])<br>
                      @endif
                      <b>{{ trans('app.quantity') }}:{{ $item->quantity }}</b>, <b>IME:</b>
                      @if (@$item->transaction->transaction_ime)
                        @foreach ($item->transaction->transaction_ime as $ime)
                          @if (!$loop->first)
                              ,
                          @endif
                          {{ $ime->ime->code }}
                        @endforeach
                      @else
                          {{ trans('app.n/a') }}
                      @endif

                    @endforeach
                  </td>
                  <td>
                    {{ $loan->note ?? trans('app.n/a') }}
                  </td>
                  <td>

                  </td>
                </tr>

            @endforeach
          </tbody>
        </table>


      </div>
    </div>
  </main>
@endsection
@section('css')
@endsection
@section('js')
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>
  <script src="{{ asset('js/agent-retrieval.js') }}"></script>
  <script src="{{ asset('js/date-time-picker.js') }}"></script>
  <script>
      var agentSelectLabel = '<option value="">{{ trans('app.agent') }}';
      var agentRetrievalUrl = '{{ route('staff.get_agents', ':branchId') }}';
    $(document).ready(function() {
      $(".date-picker").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        orientation: 'bottom right'
      });
    });
  </script>
@endsection
