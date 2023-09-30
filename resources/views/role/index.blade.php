@extends('layouts/backend')
@section('title', trans('app.role'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.role') }}</h3>
            @include('partial/flash-message')
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            @include('partial/anchor-create', [
                                'href' => route('role.create')
                            ])
                        </div>
                        <div class="col-md-6">
                            <form method="get" action="{{ route('role.index') }}">
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
                        <!-- <td>@sortablelink('name', trans('app.name'))</td> -->
                        <td>@sortablelink('display_name', trans('app.name'))</td>
                        <td>@sortablelink('description', trans('app.description'))</td>
                        <th>{{ trans('app.action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $offset++ }}</td>
                                <!-- <td>{{ $role->name }}</td> -->
                                <td>{{ $role->display_name }}</td>
                                <td>{{ $role->description }}</td>
                                <td>
                                    @include('partial.anchor-edit', [
                                        'href' => route('role.edit', $role->id),
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $roles->appends(Request::except('page'))->render() !!}
            </div>
        </div>
    </main>
@endsection
