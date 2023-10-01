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
              @include('partial/anchor-create', ['href' => route('loan.create')])
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
            <th>@sortablelink('client_code', trans('app.loan_code'))</th>
            <th>{{ trans('app.client') }}</th>
            <th>{{ trans('app.profile_photo') }}</th>
            <th>@sortablelink('client_id', trans('app.client_code'))</th>
            <th>{{ trans('app.branch') }}</th>

            @if (isAdmin())
            <th>{{ trans('app.agent') }}</th>
            @endif

            <th>{{ trans('app.product') }}</th>
            <th>{{ trans('app.next_payment_date') }}</th>
            <th>{{ trans('app.payment_amount') }}</th>
            <th>@sortablelink('status', trans('app.status'))</th>
            <th>{{ trans('app.action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($loans as $loan)
          @php $dueSchedule = $loan->schedules()->first(); @endphp
          <tr>
            <td>{{ $offset++ }}</td>
            <td>
              {{ $loan->client_code }}
            </td>
            <td>@include('partial.client-detail-link', ['client' => $loan->client])</td>
            <td>@include('partial.client-profile-photo', ['client' => $loan->client])</td>
            <td>
              @include('partial.loan-detail-link')
            </td>
            <td>{{ @$loan->branch->location ?? trans('app.n/a') }}</td>

            @if (isAdmin())
              <td>@include('partial.staff-detail-link', ['staff' => $loan->staff])</td>
            @endif

            {{-- <td>@include('partial.product-detail-link', ['product' => $loan->product, 'variantion' => $loan->variantion->name])</td> --}}
            <td>
                @foreach ($loan->productDetails as $item)
                  @if (@$item->product)
                    @include('partial.product-detail-link', ['product' => @$item->product, 'variantion' => @$item->variantion->name])<br>
                  @endif

                @endforeach
            </td>
            <td>{{ displayDate($loan->schedules[0]->payment_date ?? null) }}</td>
            <td><b>$ {{ decimalNumber($dueSchedule['total']) }}</b></td>
            <td class="text-center">@include('partial.loan-status-label')</td>
            <td class="text-center">
              <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-tasks"></i></button>
                <div class="btn-group" role="group">
                  <button class="btn btn-sm btn-primary dropdown-toggle" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ route('loan.show', $loan->id) }}" class="dropdown-item" title="{{ __('app.view_detail') }}"><i class="fa fa-eye"></i> {{ __('app.detail') }}</a>

                    @if (Auth::user()->can('loan.print') && in_array($loan->status, [LoanStatus::ACTIVE, LoanStatus::PAID]))
                      <a href="{{ route('loan.print_contract', $loan) }}" title="{{ trans('app.print_contract') }}" class="dropdown-item" target="_blank"><i class="fa fa-print"></i> {{ trans('app.print') }}</a>
                    @endif

                    @if (Auth::user()->can('loan.print') && $loan->disbursed_date != NULL)
                      <a href="{{ route('loan.invoice', $loan->id) }}" title="{{ trans('app.invoice') }}" class="dropdown-item" target="_blank"><i class="fa fa-print"></i> {{ trans('app.invoice') }}</a>
                    @endif

                    <div class="dropdown-divider"></div>
                    @if( Auth::user()->can('loan.edit') && !isPaidLoan($loan->id))
                      <a href="{{ route('loan.edit', $loan->id) }}" class="dropdown-item" title="{{ __('app.edit') }}"><i class="fa fa-edit"></i> {{ __('app.edit') }}</a>
                    @endif

                    @if(isAdmin() || Auth::user()->can('loan.delete') && !isPaidLoan($loan->id))
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
