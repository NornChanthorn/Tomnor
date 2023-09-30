@extends('layouts/backend')

@section('title', trans('app.agent_report'))

@section('content')
<main class="app-content">
  <div class="tile">
    <div class="row">
      <div class="col-sm-12">
        <h3 class="page-heading">{{ trans('app.agent_report') }}</h3>
        @include('partial/flash-message')
        <div class="row">
          <div class="col-sm-6">
            <h5 class="pull-left">{{ trans('app.commission') }}</h5>
            <a href="{{ route('report.agent_commission') }}" class="btn btn-info pull-right">{{ trans('app.detail') }}</a>
            <table class="table table-hover table-bordered">
              <tbody>
                <tr>
                  <th>{{ trans('app.total_commission') }}</th>
                  <th>$ {{ decimalNumber($totalCommission, true) }}</th>
                </tr>
                <tr>
                  <th>{{ trans('app.paid_commission') }}</th>
                  <th>$ {{ decimalNumber($paidCommission, true) }}</th>
                </tr>
                <tr>
                  <th>{{ trans('app.balance') }}</th>
                  <th>$ {{ decimalNumber(($totalCommission - $paidCommission), true) }}</th>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <br>
        @include('partial.item-count-label')
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>{{ trans('app.no_sign') }}</th>
                <th>@sortablelink('name', trans('app.name'))</th>
                <th>{{ trans('app.profile_photo') }}</th>
                <th>@sortablelink('first_phone', trans('app.first_phone'))</th>
                <th>{{ trans('app.number_of_client') }}</th>
                <th width="10%">{{ trans('app.action') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($agents as $agent)
              <tr>
                <td>{{ $offset++ }}</td>
                <td>@include('partial.staff-detail-link', ['staff' => $agent])</td>
                <td>@include('partial.staff-profile-photo', ['staff' => $agent])</td>
                <td>{{ $agent->first_phone }}</td>
                <td>{{ $agent->loans->count() ?? 0 }}</td>
                <td class="text-center">
                  @include('partial/anchor-show', [
                    'href' => route('report.agent_detail', $agent->id),
                  ])
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        {!! $agents->appends(Request::except('page'))->render() !!}
      </div>
    </div>
  </div>
</main>
@endsection