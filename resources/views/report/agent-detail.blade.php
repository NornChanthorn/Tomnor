@extends('layouts/backend')

@section('title', trans('app.agent_detail'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <div class="row">
        <div class="col-sm-12">
          <h3 class="page-heading">{{ trans('app.agent') . ' - ' . $agent->name }}</h3>
          @include('partial.item-count-label')
          <div class="table-responsive">
            <table class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th>{{ trans('app.no_sign') }}</th>
                  <th>{{ trans('app.name') }}</th>
                  <th>{{ trans('app.profile_photo') }}</th>
                  <th>{{ trans('app.gender') }}</th>
                  <th>{{ trans('app.first_phone') }}</th>
                  {{-- <th>{{ trans('app.action') }}</th> --}}
                </tr>
              </thead>
              <tbody>
                @foreach ($loans as $loan)
                <tr>
                  <td>{{ $offset++ }}</td>
                  <td>{{ $loan->client->name }}</td>
                  <td>@include('partial.client-profile-photo', ['client' => $loan->client])</td>
                  <td>{{ $loan->client->gender!='' ? genders($loan->client->gender) : '' }}</td>
                  <td>{{ $loan->client->first_phone }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          {!! $loans->render() !!}
        </div>
      </div>
    </div>
  </main>
@endsection