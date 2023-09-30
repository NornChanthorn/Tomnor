@extends('layouts/backend')

@section('title', trans('app.staff'))

@section('css')
  <link rel="stylesheet" href="{{ asset('css/bootstrap4-datetimepicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap-fileinput.css') }}">
@endsection

@section('content')
    @php
      $isFormShowType = ($formType == FormType::SHOW_TYPE);
      $disabledFormType = ($isFormShowType ? 'disabled' : '');
    @endphp

    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading">{{ trans('app.staff') . ' - ' . $title }}</h3>
            @include('partial/flash-message')

            <form id="form-staff" method="post" action="{{ route('staff.save', $staff) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="form_type" value="{{ $formType }}">

                {{-- Personal info --}}
                <div class="row">
                    <fieldset class="col-lg-12">
                        {{-- Fieldset title and button --}}
                        <div class="row">
                            <div class="col-6">
                                <h5>{{ trans('app.personal_information') }}</h5>
                            </div>

                            {{-- Save or edit button --}}
                            <div class="col-6 text-right">
                                @if ($isFormShowType)
                                    @include('partial/anchor-edit', ['href' => route('staff.edit', $staff->id)])
                                @else
                                    @include('partial/button-save')
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            {{-- Name --}}
                            <div class="col-lg-6 form-group">
                                <label for="name" class="control-label">
                                    {{ trans('app.name') }} <span class="required">*</span>
                                </label>
                                <input type="text" name="name" id="name" class="form-control" required value="{{ $staff->name ?? old('name') }}" {{ $disabledFormType }}>
                            </div>

                            {{-- Gender --}}
                            <div class="col-lg-6 form-group">
                                <label for="gender" class="control-label">
                                    {{ trans('app.gender') }} <span class="required">*</span>
                                </label>
                                <select name="gender" id="gender" class="form-control select2 select2-no-search" required {{ $disabledFormType }}>
                                    <option value="">{{ trans('app.select_option') }}</option>
                                    @foreach (genders() as $genderKey => $genderValue)
                                        <option value="{{ $genderKey }}" {{ selectedOption($genderKey, old('gender'), $staff->gender) }}>
                                            {{ $genderValue }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            {{-- Date of birth --}}
                            <div class="col-lg-6 form-group">
                                <label for="date_of_birth" class="control-label">{{ trans('app.date_of_birth') }}</label>
                                <input type="text" name="date_of_birth" id="date_of_birth" class="form-control date-picker"
                                       value="{{ displayDate($staff->date_of_birth) ?? old('date_of_birth') }}"
                                       placeholder="{{ trans('app.date_placeholder') }}" {{ $disabledFormType }}>
                            </div>

                            {{-- ID card number --}}
                            <div class="col-lg-6 form-group">
                                <label for="id_card_number" class="control-label">{{ trans('app.id_card_number') }}</label>
                                <input type="text" name="id_card_number" id="id_card_number" class="form-control id-card"
                                       value="{{ $staff->id_card_number ?? old('id_card_number') }}" {{ $disabledFormType }}>
                            </div>
                        </div>
                        <div class="row">
                            {{-- First phone --}}
                            <div class="col-lg-6 form-group">
                                <label for="first_phone" class="control-label">
                                    {{ trans('app.first_phone') }} <span class="required">*</span>
                                </label>
                                <input type="text" name="first_phone" id="first_phone" class="form-control phone"
                                       value="{{ $staff->first_phone ?? old('first_phone') }}" required {{ $disabledFormType }}>
                            </div>

                            {{-- Second phone --}}
                            <div class="col-lg-6 form-group">
                                <label for="second_phone" class="control-label">{{ trans('app.second_phone') }}</label>
                                <input type="text" name="second_phone" id="second_phone" class="form-control phone"
                                       value="{{ $staff->second_phone ?? old('second_phone') }}" {{ $disabledFormType }}>
                            </div>
                        </div>
                        <div class="row">
                            {{-- Branch --}}
                            <div class="col-lg-6 form-group">
                                <label for="branch" class="control-label">
                                    {{ trans('app.branch') }} <span class="required">*</span>
                                </label>
                                @if ($isFormShowType)
                                    <input type="text" class="form-control" value="{{ $staff->branch->location ?? '' }}" disabled>
                                @else
                                    <select name="branch" id="branch" class="form-control select2" required {{ $disabledFormType }}>
                                        <option value="">{{ trans('app.select_option') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ selectedOption($branch->id, old('branch'), $staff->branch_id) }}>
                                                {{ $branch->location}}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            {{-- Position --}}
                            <div class="col-lg-6 form-group">
                                <label for="position" class="control-label">
                                    {{ trans('app.position') }} <span class="required">*</span>
                                </label>
                                @if ($isFormShowType)
                                    <input type="text" class="form-control" value="{{ positions($staff->position ?? '') }}" disabled>
                                @else
                                    <select name="position" id="position" class="form-control select2" required {{ $disabledFormType }}>
                                        <option value="">{{ trans('app.select_option') }}</option>
                                        @foreach (positions() as $position)
                                            <option value="{{ $position->id }}" {{ selectedOption($position->id, old('position'), $staff->position) }}>
                                                {{ $position->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            {{-- Address --}}
                            <div class="col-lg-6 form-group">
                                <label for="address" class="control-label">
                                    {{ trans('app.address') }}
                                </label>
                                <textarea name="address" id="address" class="form-control" {{ $disabledFormType }}>{{ $staff->address ?? old('address') }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            {{-- Profile photo --}}
                            <div class="col-lg-6 form-group">
                                <label for="profile_photo" class="control-label">{{ trans('app.profile_photo') }}</label>
                                @if ($isFormShowType)
                                    <div class="text-left">
                                        @if (isset($staff->profile_photo))
                                            <img src="{{ asset($staff->profile_photo) }}" alt="" width="100%" class="img-responsive">
                                        @else
                                            {{ trans('app.no_picture') }}
                                        @endif
                                    </div>
                                @else
                                    <input type="file" name="profile_photo" id="profile_photo" class="form-control" accept=".jpg, .jpeg, .png">
                                @endif
                            </div>

                            {{-- ID card photo --}}
                            <div class="col-lg-6 form-group">
                                <label for="id_card_photo" class="control-label">{{ trans('app.id_card_photo') }}</label>
                                @if ($isFormShowType)
                                    <div class="text-left">
                                        @if (isset($staff->id_card_photo))
                                            <img src="{{ asset($staff->id_card_photo) }}" width="100%" class="img-responsive">
                                        @else
                                            {{ trans('app.no_picture') }}
                                        @endif
                                    </div>
                                @else
                                    <input type="file" name="id_card_photo" id="id_card_photo" class="form-control" accept=".jpg, .jpeg, .png">
                                @endif
                            </div>
                        </div>
                    </fieldset>
                </div>
                <hr>

                {{-- Login info --}}

                @if ($formType != FormType::CREATE_TYPE)
                    <div class="row">
                        <fieldset class="col-lg-12" id="login_setting_9999999" data-id="{{ $staff->id }}" data-status={{ $staff->user_id }}>
                            <legend>
                                <div class="row form-group">                                
                                    {!! Form::label(trans('app.login_info'), trans('app.login_info'), ['class'=>'col-md-3']) !!}
                                    {!! Form::select('can_login_system', ['yes'=>'Yes','no'=>'No'], 'yes', ['class'=>'form-control col-md-2','id'=>'can_login_system']) !!}                                   
                                   
                                </div>
                            </legend>
                            <div class="row" id="can_login_system_888888">

                                {{-- Username --}}
                                <div class="col-lg-4 form-group">
                                    <label for="username" class="control-label">
                                        {{ trans('app.username') }} <span class="required">*</span>
                                    </label>
                                    <input type="text" name="username" id="username" class="form-control"
                                           value="{{ $staff->user->username ?? old('username') }}" required
                                           placeholder="{{ trans('app.at_least_6_char') }}" {{ $disabledFormType }}>
                                </div>

                                {{-- Password --}}
                                <div class="col-lg-4 form-group">
                                    <label for="password" class="control-label">
                                        {{ trans('app.password') }}
                                        @if ($formType == FormType::CREATE_TYPE) <span class="required">*</span> @endif
                                    </label>
                                    <input type="password" name="password" id="password" class="form-control"
                                           placeholder="{{ trans('app.at_least_6_char') }}" {{ $disabledFormType }}
                                           @if ($formType == FormType::CREATE_TYPE) value="amazon" required @endif>
                                </div>

                                {{-- User role --}}
                                <div class="col-lg-4 form-group">
                                    <label for="role" class="control-label">
                                        {{ trans('app.role') }} <span class="required">*</span>
                                    </label>
                                    @if ($isFormShowType)
                                        <input type="text" class="form-control" value="{{ count($staff->user->roles)? $staff->user->roles[0]->display_name:'' }}" disabled>
                                    @else
                                        <select name="role" id="role" class="form-control select2 select2-no-search" required {{ $disabledFormType }}>
                                            <option value="">{{trans('app.select_option')}}</option>
                                            {{ $roles }}
                                            @foreach ($roles as $role)
                                                <option 
                                                    value="{{ $role->id }}"                                                     
                                                    @if(null != $staff->user_id and count($staff->user->roles))
                                                        {{ selectedOption($role->id, old('role'), 
                                                        $staff->user->roles[0]->id) }}
                                                    @else
                                                        {{ selectedOption($role->id, old('role'),NULL) }}
                                                    @endif
                                                    >
                                                    {{ $role->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </fieldset>
                    </div>
                @endif  

                @if($formType== FormType::CREATE_TYPE)
                    <div class="row">
                        <fieldset class="col-lg-12" id="login_setting_9999999" data-id="{{ $staff->id }}">
                            <legend>
                                <div class="row form-group">                                
                                    {!! Form::label(trans('app.login_info'), trans('app.login_info'), ['class'=>'col-md-3']) !!}
                                    {!! Form::select('can_login_system', ['yes'=>'Yes','no'=>'No'], 'no', ['class'=>'form-control col-md-2','id'=>'can_login_system']) !!}                                   
                                   
                                </div>
                            </legend>
                            <div class="row" id="can_login_system_888888">

                                {{-- Username --}}
                                <div class="col-lg-4 form-group">
                                    <label for="username" class="control-label">
                                        {{ trans('app.username') }} <span class="required">*</span>
                                    </label>
                                    <input type="text" name="username" id="username" class="form-control"
                                           value="{{ $staff->user->username ?? old('username') }}" required
                                           placeholder="{{ trans('app.at_least_6_char') }}" {{ $disabledFormType }}>
                                </div>

                                {{-- Password --}}
                                <div class="col-lg-4 form-group">
                                    <label for="password" class="control-label">
                                        {{ trans('app.password') }}
                                        @if ($formType == FormType::CREATE_TYPE) <span class="required">*</span> @endif
                                    </label>
                                    <input type="password" name="password" id="password" class="form-control"
                                           placeholder="{{ trans('app.at_least_6_char') }}" {{ $disabledFormType }}
                                           @if ($formType == FormType::CREATE_TYPE) value="amazon" required @endif>
                                </div>

                                {{-- User role --}}
                                <div class="col-lg-4 form-group">
                                    <label for="role" class="control-label">
                                        {{ trans('app.role') }} <span class="required">*</span>
                                    </label>
                                    @if ($isFormShowType)
                                        <input type="text" class="form-control" value="{{ count($staff->user->roles)? $staff->user->roles[0]->display_name:'' }}" disabled>
                                    @else
                                        <select name="role" id="role" class="form-control select2 select2-no-search" required {{ $disabledFormType }}>
                                            <option value="">{{trans('app.select_option')}}</option>
                                            {{ $roles }}
                                            @foreach ($roles as $role)
                                                <option 
                                                    value="{{ $role->id }}" 
                                                    {{ selectedOption($role->id, old('role'), 
                                                    $staff->exists ? $staff->user->roles[0]->id : NULL) }}>
                                                    {{ $role->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </fieldset>
                    </div>
                @endif

                {{-- Save or edit buttont --}}
                <div class="row">
                    <div class="col-lg-12 text-right">
                        @if ($isFormShowType)
                            @include('partial/anchor-edit', [
                                'href' => route('staff.edit', $staff->id)
                            ])
                        @else
                            @include('partial/button-save')
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection
@section('js')
    <script src="{{ asset('js/bootstrap-fileinput.js') }}"></script>
    <script src="{{ asset('js/bootstrap-fileinput-fa-theme.js') }}"></script>
    <script src="{{ asset('js/init-file-input.js') }}"></script>
    <script src="{{ asset('js/bootstrap4-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('js/date-time-picker.js') }}"></script>
    <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
    <script src="{{ asset('js/mask.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/select-box.js') }}"></script>
    <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
    <script>
        $(function() {
            callFileInput('#profile_photo, #id_card_photo', 1, 5120, ['jpg', 'jpeg', 'png']);
            $('#form-staff').validate();
        });
        $(document).ready(function(){
            if($('#login_setting_9999999').data('id') != '' && $('#login_setting_9999999').data('status') != ''){
                $('#can_login_system' ).val('yes');
                $('#can_login_system').attr('disabled',true);
                $('#can_login_system_888888').show();
            }else{
                $('#can_login_system_888888').show();
            }            
            $('#can_login_system').change(function(){
                if($(this).val()=='yes'){
                    $('#can_login_system_888888').show();
                }else{
                    $('#can_login_system_888888').hide();
                }
            });
        });
    </script>
@endsection
