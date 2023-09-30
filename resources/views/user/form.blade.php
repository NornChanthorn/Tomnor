@extends('layouts/backend')
@section('title', trans('app.user'))
@section('content')
    @php
        $isFormShowType = ($formType == FormType::SHOW_TYPE);
        $disabledFormType = ($isFormShowType ? 'disabled' : '');
    @endphp
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.user') . ' - ' . $title }}</h3>
            @include('partial/flash-message')
            <form id="form-user" method="post" action="{{ route('user.save', $user->id) }}">
                @csrf
                <input type="hidden" name="form_type" value="{{ $formType }}">
                
                <div class="row">
                    
                    {{-- Username --}}
                    <div class="col-lg-6 form-group">
                        <label for="username" class="control-label">
                            {{ trans('app.username') }} <span class="required">*</span>
                        </label>
                        <input type="text" name="username" id="username" class="form-control"
                               value="{{ old('username') ?? $user->username }}" required
                               placeholder="{{ trans('app.at_least_6_char') }}" {{ $disabledFormType }}>
                    </div>

                    {{-- Password --}}
                    <div class="col-lg-6 form-group">
                        <label for="password" class="control-label">
                            {{ trans('app.password') }}
                            @if ($formType == FormType::CREATE_TYPE) <span class="required">*</span> @endif
                        </label>
                        <input type="password" name="password" id="password" class="form-control"
                               placeholder="{{ trans('app.at_least_6_char') }}" {{ $disabledFormType }}
                               @if ($formType == FormType::CREATE_TYPE) value="amazon" required @endif>
                    </div>
                </div>
                <div class="row">
                    {{-- Role --}}
                    <div class="col-lg-6 form-group">
                        <label for="role" class="control-label">
                            {{ trans('app.role') }} <span class="required">*</span>
                        </label>
                        @if ($isFormShowType)
                            <input type="text" class="form-control" value="{{ count($user->roles)? $user->roles[0]->display_name:'' }}" disabled>
                        @else
                            <select name="role" id="role" class="form-control select2 select2-no-search" required {{ $disabledFormType }}>
                                <option value="">{{trans('app.select_option')}}</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ selectedOption($role->id, old('role'), $user->roles[0]->id ?? null) }}>
                                        {{ $role->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div class="col-lg-6 form-group">
                        <label for="status" class="control-label">
                            {{ trans('app.status') }}
                        </label>
                        <select name="status" id="status" class="form-control select2 select2-no-search" required>
                            <option value="1">{{ trans('app.active') }}</option>
                            <option value="0" {{ old('status') === 0 || $user->active === 0 ? 'selected' : '' }}>
                                {{ trans('app.inactive') }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-right">
                        @include('partial/button-save')
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection

@section('js')
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/select-box.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script>
        $(function () {
            $('#form-user').validate();
        });
    </script>
@endsection
