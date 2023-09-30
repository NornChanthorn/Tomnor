@extends('layouts/backend')

@section('title', trans('app.stock_adjustment'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.stock_adjustment') }}</h3>
    @include('partial/flash-message')

    <div class="card">
      <div class="card-header">
        <form method="get" action="">
          <div class="row">
            <div class="col-lg-6">
              @include('partial.anchor-create', ['href' => route('adjustment.create')])
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
            <td>@sortablelink('adjustment_date', trans('app.adjustment_date'))</td>
            <th>{{ trans('app.location') }}</th>
            <th>{{ trans('app.product') }}</th>
            <td>@sortablelink('reason', trans('app.reason'))</td>
            <th>{{ trans('app.creator') }}</th>
            <th class="text-right">{{ trans('app.action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($adjustments as $adjustment)
          <tr>
            <td>{{ $offset++ }}</td>
            <td>{{ displayDate($adjustment->transaction_date) }}</td>
            <td>@include('partial.branch-detail-link', ['branch' => $adjustment->warehouse])</td>
            <td>
              @if(!empty($adjustment->stock_adjustment_lines))
                @foreach($adjustment->stock_adjustment_lines as $stock_adjustment)
                  @if($stock_adjustment->product)
                  <li>{{ $stock_adjustment->product->name }}{{ $stock_adjustment->variantion->name!='DUMMY' ? ' - '.$stock_adjustment->variantion->name : '' }} ({{ ($stock_adjustment->type=='stock_out' ? '-' : '').(int)($stock_adjustment->quantity) }})</li>
                  @endif
                @endforeach
              @endif
            </td>
            <td>{{ $adjustment->additional_notes }}</td>
            <td>{{ $adjustment->creator->name ?? trans('app.n/a') }}</td>
            <td class="text-center">
              <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-tasks"></i></button>
                <div class="btn-group" role="group">
                  <button class="btn btn-sm btn-primary dropdown-toggle" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a href="javascript:void(0);" title="{{ __('app.delete') }}" data-url="{{ route('adjustment.destroy', $adjustment->id) }}" class="dropdown-item btn-delete"><i class="fa fa-trash-o"></i> {{ __('app.delete') }}</a>
                  </div>
                </div>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      {!! $adjustments->appends(Request::except('page'))->render() !!}
    </div>
  </div>
</main>
@endsection

@section('js')
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>
  <script>
    $(document).ready(function() {
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });
    });
  </script>
@endsection
