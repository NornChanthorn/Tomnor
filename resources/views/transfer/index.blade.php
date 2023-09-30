@extends('layouts/backend')

@section('title', trans('app.stock_transfer'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.stock_transfer') }}</h3>
    @include('partial/flash-message')

    <div class="card">
      <div class="card-header">
        <form method="get" action="">
          <div class="row">
            <div class="col-lg-6">
              @include('partial.anchor-create', ['href' => route('transfer.create')])
            </div>
            <div class="col-lg-6">
              @include('partial.search-input-group')
            </div>
          </div>
        </form>
      </div>
    </div>
    <br>

    @include('partial.item-count-label')
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr>
            <th>{{ trans('app.no_sign') }}</th>
            <td>@sortablelink('transfer_date', trans('app.transfer_date'))</td>
            <td>@sortablelink('reference_no', trans('app.invoice_id'))</td>
            <th>{{ trans('app.original_location') }}</th>
            <th>{{ trans('app.target_location') }}</th>
            <th>{{ trans('app.quantity') }}</th>
            <td>@sortablelink('note', trans('app.note'))</td>
            <th>{{ trans('app.creator') }}</th>
            <th>{{ trans('app.action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($transfers as $transfer)
          <tr>
            <td>{{ $offset++ }}</td>
            <td>{{ displayDate($transfer->transaction_date) }}</td>
            <td>{{ $transfer->ref_no }}</td>
            <td>{{ $transfer->location_from }}</td>
            <td>{{ $transfer->location_to }}</td>
            <td>{{ @$transfer->sell_lines->sum('quantity') ?? 0 }}</td>
            {{-- <td>@include('partial.branch-detail-link', ['branch' => $transfer->fromWarehouse])</td> --}}
            {{-- <td>@include('partial.branch-detail-link', ['branch' => $transfer->toWarehouse])</td> --}}
            <td>{{ $transfer->note }}</td>
            <td>{{ $transfer->creator->name ?? trans('app.n/a') }}</td>
            <td class="text-center">
              <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-tasks"></i></button>
                <div class="btn-group" role="group">
                  <button class="btn btn-sm btn-primary dropdown-toggle" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a href="{{ route('transfer.show', $transfer->id) }}" class="dropdown-item" title="{{ __('app.view_detail') }}"><i class="fa fa-eye"></i> {{ __('app.view_detail') }}</a>
                    {{--  <a href="javascript:void(0);" title="{{ __('app.delete') }}" data-url="{{ route('transfer.destroy', $transfer->id) }}" class="dropdown-item btn-delete"><i class="fa fa-trash-o"></i> {{ __('app.delete') }}</a>  --}}
                  </div>
                </div>
              </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        {!! $transfers->appends(Request::except('page'))->render() !!}
      </div>
    </div>
  </main>
  @endsection

  @section('js')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/select-box.js') }}"></script>
    {{--  <script>
      $(document).ready(function() {
        $(".btn-delete").on('click', function() {
          confirmPopup($(this).data('url'), 'error', 'DELETE');
        });
      });
    </script>  --}}
  @endsection
