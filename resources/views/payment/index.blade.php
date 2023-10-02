@extends('layouts/backend')

@section('title', trans('app.payment'))

@section('content')
  <main class="app-content">
    <div class="tile">
       <h1>Here is payment index</h1>
      <h3 class="page-heading">{{ trans('app.payment') }}</h3>
      @include('partial/flash-message')
      <div class="card">
        <div class="card-header">
          <form method="get" action="{{ route('repayment.index') }}">
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
      <a href="{{ url('repayment/list') }}" class="btn btn-sm btn-success pull-right mb-1">{{ trans('app.print') }}</a>
      <div class="table-responsive resize-w">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              @if (isAdmin() || Auth::user()->can('loan.pay'))
                <th>{{ trans('app.action') }}</th>
              @endif
              <th>@sortablelink('client_code', trans('app.loan_code'))</th>
              <th style="width: 10%">{{ trans('app.client') }}</th>
              <th style="width: 10%">{{ trans('app.profile_photo') }}</th>
              <th style="width: 10%">@sortablelink('client_id', trans('app.client_code'))</th>
              <th style="width: 10%">{{ trans('app.phone_number') }}</th>
              <th style="width: 10%">{{ trans('app.branch') }}</th>
              @if (isAdmin())
                <th>{{ trans('app.agent') }}</th>
              @endif
              <th style="width: 10%">{{ trans('app.next_payment_date') }}</th>
              <th>{{ trans('app.payment_amount') }}</th>
              <th>{{  trans('app.count_late_date') }}</th>
              <th style="width: 10%">
                {{ trans('app.product_ime') }}
              </th>
              <th>
                {{ trans('app.icloud') }}
              </th>


            </tr>
          </thead>
          <tbody>
            @foreach ($loans as $loan)
              <tr>
                <td class="text-center">
                  @if (isAdmin() || Auth::user()->can('loan.pay'))
                  <div class="btn-group mr-2" role="group" aria-label="Second group">
                      {{-- Simple repayment --}}
                      <a href="{{ route('repayment.show', [$loan->id, RepayType::REPAY]) }}" class="btn btn-success btn-sm  mr-1 mb-1">
                        {{ trans('app.repay') }}
                      </a>
                      {{-- Payoff --}}
                      <a href="{{ route('repayment.show', [$loan->id, RepayType::PAYOFF]) }}" class="btn btn-success btn-sm mb-1">
                        {{ trans('app.pay_off') }}
                      </a>
                  </div>


                  @endif
                </td>
                <td>
                  {{ $loan->client_code }}
                </td>
                <td>@include('partial.client-detail-link', ['client' => $loan->client])</td>
                <td>@include('partial.client-profile-photo', ['client' => $loan->client])</td>
                <td>
                  @include('partial.loan-detail-link')
                </td>
                <td>{{ $loan->client->first_phone }} {{ $loan->client->second_phone ? ', '.$loan->client->second_phone : "" }}</td>
                <td>{{ $loan->branch->location ?? trans('app.n/a') }}</td>
                @if (isAdmin())
                  <td>@include('partial.staff-detail-link', ['staff' => $loan->staff])</td>
                @endif

                <td>{{ displayDate(@$loan->payment_date) }}</td>
                <td><b>$ {{ decimalNumber($loan->total_amount - $loan->total_paid_amount, true) }}</b></td>
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
                  {!! $loan->note ?? trans('app.n/a') !!}
                </td>

              </tr>
            @endforeach
          </tbody>
        </table>

        @if (count($loans) > 0)
          {!! $loans->appends(Request::except('page'))->render() !!}
        @endif
      </div>
    </div>
  </main>
@endsection

@section('js')
  <script>
    var agentSelectLabel = '<option value="">{{ trans('app.agent') }}';
    var agentRetrievalUrl = '{{ route('staff.get_agents', ':branchId') }}';
  </script>
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>
  <script src="{{ asset('js/agent-retrieval.js') }}"></script>
  <script src="{{ asset('js/date-time-picker.js') }}"></script>
@endsection
