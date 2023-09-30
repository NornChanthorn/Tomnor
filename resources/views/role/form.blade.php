@extends('layouts/backend')
@section('title', trans('app.role'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.role') . ' - ' . $title }}</h3>
            @include('partial/flash-message')
                @if($role->id)
                    <form id="role-frm" method="post" action="{{ route('role.update', $role->id) }}">
                    <input type="hidden" name="id" value="{{ $role->id }}">
                    {{ method_field('PUT') }}
                @else
                    <form id="role-frm" method="post" action="{{ route('role.store') }}">
                @endif
                @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="display_name" class="control-label">{{trans('app.name')}}<span class="red-star">*</span></label>
                                    <input type="text" class="form-control" value="{{old('display_name', $role->display_name)}}" name="display_name" required >
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="description" class="control-label">{{trans('app.description')}}</label>
                                    <textarea class="form-control" id="description" name="description" rows="5">{!! old('description', $role->description) !!}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group row">
                                <label for="product" class="ml-4 form-control-label"><h5>{{trans('app.permission')}}</h5></label>
                                <div class="col-sm-12">
                                    @foreach($permissions as $key => $pms_arr)
                                        <div class="row">
                                            <div class="col-md-3 mb-2"><strong>{{$key}}</strong></div>
                                            <div class="col-md-9">
                                                <ul class="permission list-unstyled">
                                                    @foreach($pms_arr as $pms)
                                                        <li class="mb-2">
                                                            <label>
                                                                <input type="checkbox" name="permissions[]" class="" value="{{$pms->id}}" @if(in_array($pms->id, $rolePermissions)) checked @endif>
                                                                {{$pms->display_name}}
                                                            </label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            @include('partial/button-save')
                            <a href="javascript: window.history.go(-1)" class="btn btn-secondary"> Close</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection

@section('js')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/select-box.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script>
        $(function () {
            $('#role-frm').validate();
        });
    </script>
@endsection
