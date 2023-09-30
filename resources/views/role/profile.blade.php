@extends('layouts/backend')
@section('title', trans('app.profile'))
@section('content')
    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.profile') }}</h3>
            @include('partial/flash-message')
            <form id="form-profile" method="post" action="{{ route('user.save_profile', $user) }}">
                @csrf
                <div class="row">
                    <div class="col-md-10 col-lg-8">
                        {{-- Username --}}
                        <div class="form-group">
                            <label for="username" class="control-label">
                                {{ trans('app.username') }} <span class="required">*</span>
                            </label>
                            <input type="text" name="username" id="username" class="form-control"
                                   value="{{ old('username') ?? $user->username }}" required>
                        </div>

                        {{-- Current password --}}
                        <div class="form-group">
                            <label for="current_password" class="control-label">
                                {{ trans('app.current_password') }}
                            </label>
                            <input type="password" name="current_password" id="current_password"
                                   class="form-control" value="{{ old('current_password') }}">
                        </div>

                        {{-- New password --}}
                        <div class="form-group">
                            <label for="new_password" class="control-label">
                                {{ trans('app.new_password') }}
                            </label>
                            <input type="password" name="new_password" id="new_password"
                                   class="form-control" value="{{ old('new_password') }}">
                        </div>

                        {{-- Confirmed password --}}
                        <div class="form-group">
                            <label for="confirmed_password" class="control-label">
                                {{ trans('app.confirm_password') }}
                            </label>
                            <input type="password" name="confirmed_password" id="confirmed_password"
                                   class="form-control" value="{{ old('confirmed_password') }}">
                        </div>

                        @include('partial/button-save', [
                            'class' => 'pull-right'
                        ])
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection
@section('js')
    <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
    <script src="{{ asset('js/mask.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script>
        $(function () {
            $('#form-profile').validate();
        });
    </script>
@endsection
