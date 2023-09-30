@extends('layouts/backend')

@section('title', trans('app.loan'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.loan') }}</h3>
    @include('partial/flash-message')
    <div class="card">
      <div class="card-header">
        <form method="get" action="">
          <div class="row">
            <div class="col-lg-2 col-md-4">
              @if(Auth::user()->can('loan-cash.add'))
                @include('partial/anchor-create', ['href' => route('loan-cash.create')])
              @endif
            </div>
            @include('partial.loan-search-fields')
          </div>
        </form>
      </div>
    </div>
    <br>

    @include('partial.item-count-label')
    <div class="table-responsive resize-w" style="min-height: 500px">
      <table class="table table-hover table-bordered" >
        <thead>
          <tr>
            <th>{{ trans('app.no_sign') }}</th>
            <th>@sortablelink('account_number', trans('app.loan_code'))</th>
            <th>{{ trans('app.client') }}</th>
            <th>{{ trans('app.gender') }}</th>
            <th>{{ trans('app.phone_number') }}</th>
            <th>{{ trans('app.request') }}{{ trans('app.amount') }} </th>
            {{-- <th>{{ trans('app.approved') }}{{ trans('app.amount') }} </th> --}}
            <th>{{ trans('app.installment') }} </th>
            <th>{{ trans('app.frequency') }} </th>
            <th> @sortablelink('created_at', trans('app.request_date')) </th>

            <th>{{ trans('app.loan_disbursement') }} </th>
            <th>{{ trans('app.next_payment_date') }} </th>
            <th>{{ trans('app.loan_status') }} </th>
            <th>{{ trans('app.action') }} </th>
          </tr>
        </thead>
        <tbody>
          @foreach ($loans as $loan)
            <tr>
              <td>{{ $offset++ }}</td>
              <td>@include('partial.loan-detail-link')</td>
              <td>@include('partial.client-detail-link', ['client' => $loan->client])</td>
              <td>{{ @$loan->client->gender? genders(@$loan->client->gender) : __('app.n/a') }}</td>
              <td>{{ @$loan->client->first_phone }} {{ @$loan->client->second_phone ? ', '.@$loan->client->second_phone : '' }}</td>
              <td>{{ num_f(@$loan->loan_amount) }}</td>
              {{-- <td>{{ num_f(@$loan->down_payment_amount) }}</td> --}}
              <td>{{ $loan->installment ?? __('app.n/a') }}</td>
              <td>{{ @$loan->frequency ? frequencies(@$loan->frequency,false) : trans('app.n/a') }}</td>
              <td>{{ displayDate($loan->loan_start_date ?? $loan->created_at) }}</td>
              <td>{{ displayDate($loan->disbursed_date) }}</td>
              <td>{{ displayDate($loan->payment_date) }}</td>
              <td>{{ loanStatuses($loan->status) }}</td>
              <td>
                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                  <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-tasks"></i></button>
                  <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-primary dropdown-toggle" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                    <div class="dropdown-menu dropdown-menu-right">

                      @if( Auth::user()->can('loan-cash.edit') && $loan->status!='ac')
                        <a href="{{ route('loan-cash.edit', $loan->id) }}" class="dropdown-item" title="{{ __('app.edit') }}"><i class="fa fa-edit"></i> {{ __('app.edit') }}</a>
                      @endif
                      <a href="{{ route('loan-cash.show', $loan->id) }}" class="dropdown-item" title="{{ __('app.view_detail') }}"><i class="fa fa-eye"></i> {{ __('app.detail') }}</a>
                      @if(isAdmin() || Auth::user()->can('loan-cash.delete') && !isPaidLoan($loan->id))
                        <a href="javascript:void(0);" title="{{ __('app.delete') }}" data-url="{{ route('loan.destroy', $loan->id) }}" class="dropdown-item btn-delete"><i class="fa fa-trash-o"></i> {{ __('app.delete') }}</a>
                      @endif
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      {!! $loans->appends(Request::except('page'))->render() !!}
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
  <script>
    $(document).ready(function() {
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });
    });
  </script>
    {{--  <script src="{{ asset('js/date-time-picker.js') }}"></script>  --}}
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
