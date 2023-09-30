@extends('layouts/backend')

@section('title', trans('app.branch'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.branch') }}</h3>
      @include('partial/flash-message')
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-6">
              @include('partial/anchor-create', [
                'href' => route('branch.create')
              ])
            </div>
            <div class="col-lg-6 text-right">
              <form method="get" action="{{ route('branch.index') }}">
                @include('partial.search-input-group')
              </form>
            </div>
          </div>
        </div>
      </div>
      <br>
      @include('partial.item-count-label')
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>{{ trans('app.no_sign') }}</th>
              <td>@sortablelink('name', trans('app.name'))</td>
              <td>@sortablelink('location', trans('app.location'))</td>
              <td>{{ trans('app.branch_type') }}</td>
              <td>@sortablelink('phone_1', trans('app.first_phone'))</td>
              <td>@sortablelink('phone_2', trans('app.second_phone'))</td>
              <td>@sortablelink('address', trans('app.address'))</td>
              <th>{{ trans('app.action') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($branches as $branch)
              <tr>
                <td>{{ $offset++ }}</td>
                <td>{{ $branch->name }}</td>
                <td>{{ $branch->location }}</td>
                <td>{{ branchTypes($branch->type ?? '') }}</td>
                <td>{{ $branch->phone_1 }}</td>
                <td>{{ $branch->phone_2 }}</td>
                <td>{{ $branch->address }}</td>
                <td>
                  {{-- <a href="{{ route('branch.list_product', $branch->id) }}" class="btn btn-info btn-sm mb-1">{{ trans('app.product') }}</a> --}}
                  @include('partial.anchor-show', [
                    'href' => route('branch.show', $branch->id)
                  ])
                  @include('partial.anchor-edit', [
                    'href' => route('branch.edit', $branch->id),
                  ])
                  @include('partial.button-delete', [
                    'url' => route('branch.destroy', $branch->id),
                    'disabled' => 'disabled',
                  ])
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        {!! $branches->appends(Request::except('page'))->render() !!}
      </div>
    </div>
  </main>
@endsection
