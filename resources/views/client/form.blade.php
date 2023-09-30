@extends('layouts/backend')

@section('title', trans('app.client'))

@section('css')
  <link rel="stylesheet" href="{{ asset('css/bootstrap4-datetimepicker.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap-fileinput.css') }}">
@endsection

@section('content')
  @php
    $isFormShowType = ($formType == FormType::SHOW_TYPE);
    $disabledFormType = ($isFormShowType ? 'disabled' : '');

    const CLIENT_FIELD_TYPE = 'c';
    const SPONSOR_FIELD_TYPE = 's';
  @endphp

  <main class="app-content">
    <div class="tile">
      <p>It from cliend.form</p>

      <h3 class="page-heading">{{ trans('app.client') . ' - ' . $title }}</h3>
      @include('partial/flash-message')
      <form id="form-client" method="post" action="{{ route('client.save', $client) }}" enctype="multipart/form-data">
        @csrf

        {{-- Personal info --}}
        <div class="row">
          <fieldset class="col-lg-12">
            {{-- Fieldset title and button --}}
            <div class="row">
              <div class="col-6">
                <h5>{{ trans('app.personal_information') }}</h5>
              </div>
              <div class="col-6 text-right">
                @if ($isFormShowType)
                  @if (isAdmin() && $client->is_default == 0)
                      @include('partial/anchor-edit', [
                        'href' => route('client.edit', $client->id)
                      ])
                  @endif
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
                <input type="text" name="name" id="name" class="form-control" value="{{ $client->name ?? old('name') }}" required {{ $disabledFormType }}>
              </div>

              {{-- Gender --}}
              <div class="col-lg-6 form-group">
                <label for="gender" class="control-label">{{ trans('app.gender') }}</label>
                <select name="gender" id="gender" class="form-control select2 select2-no-search" {{ $disabledFormType }}>
                  <option value="">{{ trans('app.select_option') }}</option>
                  @foreach (genders() as $genderKey => $genderValue)
                    <option value="{{ $genderKey }}" {{ $client->gender == $genderKey || old('gender') == $genderKey ? 'selected' : '' }}>
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
                <input type="text" name="date_of_birth" id="date_of_birth" class="form-control date-picker" value="{{ displayDate($client->date_of_birth) ?? old('date_of_birth') }}" placeholder="{{ trans('app.date_placeholder') }}" {{ $disabledFormType }}>
              </div>

              {{-- ID card number --}}
              <div class="col-lg-6 form-group">
                <label for="id_card_number" class="control-label">{{ trans('app.id_card_number') }} <span class="required">*</span></label>
                <input type="text" name="id_card_number" id="id_card_number" class="form-control id-card" required value="{{ $client->id_card_number ?? old('id_card_number') }}" {{ $disabledFormType }}>
              </div>
            </div>

            <div class="row">
              {{-- First phone --}}
              <div class="col-lg-6 form-group">
                <label for="first_phone" class="control-label">{{ trans('app.first_phone') }} <span class="required">*</span></label>
                <input type="text" name="first_phone" id="first_phone" class="form-control phone" value="{{ $client->first_phone ?? old('first_phone') }}" required {{ $disabledFormType }}>
              </div>

              {{-- Second phone --}}
              <div class="col-lg-6 form-group">
                <label for="second_phone" class="control-label">{{ trans('app.second_phone') }}</label>
                <input type="text" name="second_phone" id="second_phone" class="form-control phone" value="{{ $client->second_phone ?? old('second_phone') }}" {{ $disabledFormType }}>
              </div>
            </div>
            {{-- Social Media --}}
            <div class="row">
              {{-- First phone --}}
              <div class="col-lg-6 form-group">
                <label for="facebook_name" class="control-label">Facebook Name</label>
                <input type="text" name="facebook_name" id="facebook_name" class="form-control" value="{{ $client->facebook_name ?? old('facebook_name') }}" {{ $disabledFormType }}>
              </div>

              {{-- Second phone --}}
              <div class="col-lg-6 form-group">
                <label for="facebook_url" class="control-label">Facebook Url</label>
                <input type="text" name="facebook_url" id="facebook_url" class="form-control" value="{{ $client->facebook_url ?? old('facebook_url') }}" {{ $disabledFormType }}>
              </div>
              <div class="col-lg-6 form-group">
                <label for="telegram" class="control-label">Telegram</label>
                <input type="text" name="telegram" id="telegram" class="form-control" value="{{ $client->telegram ?? old('telegram') }}" {{ $disabledFormType }}>
              </div>
            </div>

            {{-- Address --}}
            <div class="row">
              {{-- Province --}}
              <div class="col-lg-6 form-group">
                <label for="province" class="control-label">{{ trans('app.province') }}</label>
                @if ($isFormShowType)
                  <input type="text" class="form-control" value="{{ $client->province->name ?? '' }}" disabled>
                @else
                  <select name="province" id="province" class="form-control select2" {{ $disabledFormType }} data-address-type="{{ AddressType::PROVINCE }}" data-field-type="{{ CLIENT_FIELD_TYPE }}">
                    <option value="">{{ trans('app.select_option') }}</option>
                    @foreach ($provinces as $province)
                      <option value="{{ $province->id }}" {{ $province->id == $client->province_id ? 'selected' : '' }}>
                        {{ $province->name }}
                      </option>
                    @endforeach
                  </select>
                @endif
              </div>

              {{-- District --}}
              <div class="col-lg-6 form-group">
                <label for="district" class="control-label">{{ trans('app.district') }}</label>
                @if ($isFormShowType)
                  <input type="text" class="form-control" value="{{ $client->district->name ?? '' }}" disabled>
                @else
                  <select name="district" id="district" class="form-control select2" {{ $disabledFormType }} data-address-type="{{ AddressType::DISTRICT }}" data-field-type="{{ CLIENT_FIELD_TYPE }}">
                    <option value="">{{ trans('app.select_option') }}</option>
                    @foreach ($districts as $district)
                      <option value="{{ $district->id }}" {{ $district->id == $client->district_id ? 'selected' : '' }}>
                        {{ $district->name }}
                      </option>
                    @endforeach
                  </select>
                @endif
              </div>

              {{-- Commune --}}
              <div class="col-lg-6 form-group">
                <label for="commune" class="control-label">{{ trans('app.commune') }}</label>
                @if ($isFormShowType)
                  <input type="text" class="form-control" value="{{ $client->commune->name ?? '' }}" disabled>
                @else
                  <select name="commune" id="commune" class="form-control select2" {{ $disabledFormType }} data-address-type="{{ AddressType::COMMUNE }}" data-field-type="{{ CLIENT_FIELD_TYPE }}">
                    <option value="">{{ trans('app.select_option') }}</option>
                    @foreach ($communes as $commune)
                      <option value="{{ $commune->id }}" {{ $commune->id == $client->commune_id ? 'selected' : '' }}>
                        {{ $commune->name }}
                      </option>
                    @endforeach
                  </select>
                @endif
              </div>

              {{-- Village --}}
              <div class="col-lg-6 form-group">
                <label for="village" class="control-label">{{ trans('app.village') }}</label>
                @if ($isFormShowType)
                  <input type="text" class="form-control" value="{{ $client->village->name ?? '' }}" disabled>
                @else
                  <select name="village" id="village" class="form-control select2" {{ $disabledFormType }} data-address-type="{{ AddressType::VILLAGE }}" data-field-type="{{ CLIENT_FIELD_TYPE }}">
                    <option value="">{{ trans('app.select_option') }}</option>
                    @foreach ($villages as $village)
                      <option value="{{ $village->id }}" {{ $village->id == $client->village_id ? 'selected' : '' }}>
                        {{ $village->name }}
                      </option>
                    @endforeach
                  </select>
                @endif
              </div>
            </div>

            <div class="row">
              <div class="col-lg-6 form-group">
                <label for="address" class="control-label">{{ trans('app.address') }}</label>
                <textarea name="address" id="address" class="form-control" {{ $disabledFormType }}>{{ $client->address ?? old('address') }}</textarea>
              </div>
            </div>

            <div class="row">
              {{-- Profile photo --}}
              <div class="col-lg-6 form-group">
                <label for="profile_photo" class="control-label">{{ trans('app.profile_photo') }}</label>
                @if ($isFormShowType)
                  <div class="text-left">
                    @if (isset($client->profile_photo))
                      <img src="{{ asset($client->profile_photo) }}" width="100%" class="img-responsive">
                    @else
                      {{ trans('app.no_picture') }}
                    @endif
                  </div>
                @else
                  <input type="file" name="profile_photo" id="profile_photo" class="form-control" accept=".jpg, .jpeg, .png" value="{{ $client->profile_photo ?? old('profile_photo') }}">
                @endif
              </div>

              {{-- ID card photo --}}
              <div class="col-lg-6 form-group">
                  <label for="id_card_photo" class="control-label">{{ trans('app.id_card_photo') }}</label>
                  @if ($isFormShowType)
                    <div class="text-left">
                      @if (isset($client->id_card_photo))
                        <img src="{{ asset($client->id_card_photo) }}" width="100%" class="img-responsive">
                      @else
                        {{ trans('app.no_picture') }}
                      @endif
                    </div>
                  @else
                    <input type="file" name="id_card_photo" id="id_card_photo" class="form-control" accept=".jpg, .jpeg, .png" value="{{ $client->id_card_photo ?? old('id_card_photo') }}">
                  @endif
              </div>
            </div>

            <div class="row">
              {{-- First phone --}}
              <div class="col-lg-6 form-group">
                <label for="occupation_1" class="control-label">{{ trans('app.occupation_1') }} <span class="required">*</span></label>
                <input type="text" name="occupation_1" id="occupation_1" class="form-control" value="{{ $client->occupation_1 ?? old('occupation_1') }}" required {{ $disabledFormType }}>
              </div>

              {{-- Second phone --}}
              <div class="col-lg-6 form-group">
                <label for="occupation_2" class="control-label">{{ trans('app.occupation_2') }}</label>
                <input type="text" name="occupation_2" id="occupation_2" class="form-control" value="{{ $client->occupation_2 ?? old('occupation_2') }}" {{ $disabledFormType }}>
              </div>
            </div>
            <div class="row">
              {{-- Profile photo --}}
              <div class="col-lg-6 form-group">
                <label for="related_document_1" class="control-label">{{ trans('app.related_document_1') }}</label>
                @if ($isFormShowType)
                  <div class="text-left">
                    @if (isset($client->related_document_1))
                      <img src="{{ asset($client->related_document_1) }}" width="100%" class="img-responsive">
                    @else
                      {{ trans('app.no_picture') }}
                    @endif
                  </div>
                @else
                  <input type="file" name="related_document_1" id="related_document_1" class="form-control" accept=".jpg, .jpeg, .png" value="{{ $client->related_document_1 ?? old('related_document_1') }}">
                @endif
              </div>

              {{-- ID card photo --}}
              <div class="col-lg-6 form-group">
                  <label for="related_document_2" class="control-label">{{ trans('app.related_document_2') }}</label>
                  @if ($isFormShowType)
                    <div class="text-left">
                      @if (isset($client->related_document_2))
                        <img src="{{ asset($client->related_document_2) }}" width="100%" class="img-responsive">
                      @else
                        {{ trans('app.no_picture') }}
                      @endif
                    </div>
                  @else
                    <input type="file" name="related_document_2" id="related_document_2" class="form-control" accept=".jpg, .jpeg, .png" value="{{ $client->related_document_2 ?? old('related_document_2') }}">
                  @endif
              </div>
            </div>
          </fieldset>
        </div>
        <hr>

        {{-- Sponsor info --}}
        <fieldset class="">
          <legend><h5>{{ trans('app.sponsor_information') }}</h5></legend>
          <div class="row">
            {{-- Name --}}
            <div class="col-lg-6 form-group">
              <label for="sponsor_name" class="control-label">{{ trans('app.name') }}</label>
              <input type="text" name="sponsor_name" id="sponsor_name" class="form-control" value="{{ $client->sponsor_name ?? old('sponsor_name') }}" {{ $disabledFormType }}>
            </div>

            {{-- Gender --}}
            <div class="col-lg-6 form-group">
              <label for="sponsor_gender" class="control-label">{{ trans('app.gender') }}</label>
              <select name="sponsor_gender" id="sponsor_gender" class="form-control select2 select2-no-search" {{ $disabledFormType }}>
                <option value="">{{ trans('app.select_option') }}</option>
                @foreach (genders() as $genderKey => $genderValue)
                  <option value="{{ $genderKey }}" {{ $client->sponsor_gender == $genderKey || old('sponsor_gender') == $genderKey ? 'selected' : '' }}>
                    {{ $genderValue }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="row">
            {{-- Date of birth --}}
            <div class="col-lg-6 form-group">
              <label for="sponsor_date_of_birth" class="control-label">{{ trans('app.date_of_birth') }}</label>
              <input type="text" name="sponsor_date_of_birth" id="sponsor_date_of_birth" class="form-control date-picker" value="{{ displayDate($client->sponsor_dob) ?? old('sponsor_date_of_birth') }}" placeholder="{{ trans('app.date_placeholder') }}" {{ $disabledFormType }}>
            </div>

            {{-- ID card number --}}
            <div class="col-lg-6 form-group">
              <label for="sponsor_id_card_number" class="control-label">{{ trans('app.id_card_number') }}</label>
              <input type="text" name="sponsor_id_card_number" id="sponsor_id_card_number" class="form-control id-card" value="{{ $client->sponsor_id_card ?? old('sponsor_id_card_number') }}" {{ $disabledFormType }}>
            </div>
          </div>

          <div class="row">
            {{-- First phone --}}
            <div class="col-lg-6 form-group">
              <label for="sponsor_first_phone" class="control-label">{{ trans('app.first_phone') }}</label>
              <input type="text" name="sponsor_first_phone" id="sponsor_first_phone" class="form-control phone" value="{{ $client->sponsor_phone ?? old('sponsor_first_phone') }}" {{ $disabledFormType }}>
            </div>

            {{-- Second phone --}}
            <div class="col-lg-6 form-group">
              <label for="sponsor_second_phone" class="control-label">{{ trans('app.second_phone') }}</label>
              <input type="text" name="sponsor_second_phone" id="sponsor_second_phone" class="form-control phone" value="{{ $client->sponsor_phone_2 ?? old('sponsor_second_phone') }}" {{ $disabledFormType }}>
            </div>
          </div>

          {{-- Address --}}
          <div class="row">
            {{-- Province --}}
            <div class="col-lg-3 form-group">
              <label for="sponsor_province" class="control-label">{{ trans('app.province') }}</label>
              @if ($isFormShowType)
                <input type="text" class="form-control" value="{{ $client->sponsorProvince->name ?? '' }}" disabled>
              @else
                <select name="sponsor_province" id="sponsor_province" class="form-control select2" {{ $disabledFormType }} data-address-type="{{ AddressType::PROVINCE }}" data-field-type="{{ SPONSOR_FIELD_TYPE }}">
                  <option value="">{{ trans('app.select_option') }}</option>
                  @foreach ($provinces as $province)
                    <option value="{{ $province->id }}" {{ $province->id == $client->sponsor_province_id ? 'selected' : '' }}>
                      {{ $province->name }}
                    </option>
                  @endforeach
                </select>
              @endif
            </div>

            {{-- District --}}
            <div class="col-lg-3 form-group">
              <label for="sponsor_district" class="control-label">{{ trans('app.district') }}</label>
              @if ($isFormShowType)
                <input type="text" class="form-control" value="{{ $client->sponsorDistrict->name ?? '' }}" disabled>
              @else
                <select name="sponsor_district" id="sponsor_district" class="form-control select2" {{ $disabledFormType }} data-address-type="{{ AddressType::DISTRICT }}" data-field-type="{{ SPONSOR_FIELD_TYPE }}">
                  <option value="">{{ trans('app.select_option') }}</option>
                  @foreach ($sponsorDistricts as $district)
                    <option value="{{ $district->id }}" {{ $district->id == $client->sponsor_district_id ? 'selected' : '' }}>
                      {{ $district->name }}
                    </option>
                  @endforeach
                </select>
              @endif
            </div>

            {{-- Commune --}}
            <div class="col-lg-3 form-group">
              <label for="sponsor_commune" class="control-label">{{ trans('app.commune') }}</label>
              @if ($isFormShowType)
                <input type="text" class="form-control" value="{{ $client->sponsorCommune->name ?? '' }}" disabled>
              @else
                <select name="sponsor_commune" id="sponsor_commune" class="form-control select2" {{ $disabledFormType }} data-address-type="{{ AddressType::COMMUNE }}" data-field-type="{{ SPONSOR_FIELD_TYPE }}">
                  <option value="">{{ trans('app.select_option') }}</option>
                  @foreach ($sponsorCommunes as $commune)
                    <option value="{{ $commune->id }}" {{ $commune->id == $client->sponsor_commune_id ? 'selected' : '' }}>
                      {{ $commune->name }}
                    </option>
                  @endforeach
                </select>
              @endif
            </div>

            {{-- Village --}}
            {{-- <div class="col-lg-3 form-group">
              <label for="sponsor_village" class="control-label">{{ trans('app.village') }}</label>
              @if ($isFormShowType)
                <input type="text" class="form-control" value="{{ $client->sponsorVillage->name ?? '' }}" disabled>
              @else
                <select name="village" id="sponsor_village" class="form-control select2" {{ $disabledFormType }} data-address-type="{{ AddressType::VILLAGE }}" data-field-type="{{ SPONSOR_FIELD_TYPE }}">
                  <option value="">{{ trans('app.select_option') }}</option>
                  @foreach ($sponsorVillages as $village)
                    <option value="{{ $village->id }}" {{ $village->id == $client->sponsor_village_id ? 'selected' : '' }}>
                      {{ $village->name }}
                    </option>
                  @endforeach
                </select>
              @endif
            </div> --}}
          </div>

          <div class="row">
            {{-- Profile photo --}}
            <div class="col-lg-6 form-group">
              <label for="sponsor_profile_photo" class="control-label">{{ trans('app.profile_photo') }}</label>
              @if ($isFormShowType)
                <div class="text-left">
                  @if (isset($client->sponsor_profile_photo))
                    <img src="{{ asset($client->sponsor_profile_photo) }}" width="100%" class="img-responsive">
                  @else
                    {{ trans('app.no_picture') }}
                  @endif
                </div>
              @else
                <input type="file" name="sponsor_profile_photo" id="sponsor_profile_photo" class="form-control" value="{{ $client->sponsor_profile_photo ?? old('sponsor_profile_photo') }}" accept=".jpg, .jpeg, .png">
              @endif
            </div>

            {{-- ID card photo --}}
            <div class="col-lg-6 form-group">
              <label for="sponsor_id_card_photo" class="control-label">{{ trans('app.id_card_photo') }}</label>
              @if ($isFormShowType)
                <div class="text-left">
                  @if (isset($client->sponsor_id_card_photo))
                    <img src="{{ asset($client->sponsor_id_card_photo) }}" width="100%" class="img-responsive">
                  @else
                    {{ trans('app.no_picture') }}
                  @endif
                </div>
              @else
                <input type="file" name="sponsor_id_card_photo" id="sponsor_id_card_photo" class="form-control" value="{{ $client->sponsor_id_card_photo ?? old('sponsor_id_card_photo') }}" accept=".jpg, .jpeg, .png">
              @endif
            </div>
          </div>
        </fieldset>

        {{-- Button save or edit --}}
        <div class="row">
          <div class="col-lg-12 text-right">
            @if ($isFormShowType)
              @if (isAdmin() && $client->is_default == 0)
                @include('partial/anchor-edit', [
                  'href' => route('client.edit', $client->id)
                ])
              @endif
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
  <script src="{{ asset('js/jquery-mask.min.js') }}"></script>
  <script src="{{ asset('js/date-time-picker.js') }}"></script>
  <script src="{{ asset('js/mask.js') }}"></script>
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>
  <script>
    $(function() {
      callFileInput('#profile_photo, #id_card_photo, #related_document_1, #related_document_2, #sponsor_profile_photo, #sponsor_id_card_photo', 1, 5120, ['jpg', 'jpeg', 'png']);

      $('#form-client').validate();

      var emptyOptionElm = '<option value="">{{ trans('app.select_option') }}</option>';
      var provinceAddressType = '{{ AddressType::PROVINCE }}';
      var districtAddressType = '{{ AddressType::DISTRICT }}';
      var addressClientType = '{{ CLIENT_FIELD_TYPE }}';
      var addressSponsorType = '{{ SPONSOR_FIELD_TYPE }}';

      // When change province, district, or commune of client or sponsor
      $('#province, #district, #commune, #sponsor_province, #sponsor_district, #sponsor_commune').change(function () {
        var addressFieldType = $(this).data('field-type');
        if (![addressClientType, addressSponsorType].includes(addressFieldType)) {
          $(this).focus();
          return false;
        }

        var subAddressElm;
        var addressType = $(this).data('address-type');
        if (addressFieldType == addressClientType) {
          subAddressElm = (addressType == provinceAddressType ? $('#district') : (addressType == districtAddressType ? $('#commune') : $('#village')));
        }
        else {
          subAddressElm = (addressType == provinceAddressType ? $('#sponsor_district') : (addressType == districtAddressType ? $('#sponsor_commune') : $('#sponsor_village')));
        }

        if ($(this).val() != '') {
          var getSubAddressesUrl = ('{{ route('address.get_sub_addresses', ':id') }}').replace(':id', $(this).val());
          $.ajax({
            url: getSubAddressesUrl,
            success: function (result) {
              var subAddressData = emptyOptionElm;

              $.each(result.addresses, function (key, value) {
                subAddressData += '<option value="' + value.id + '">' + value.name + '</option>';
              });
              subAddressElm.html(subAddressData).trigger('change');
            }
          });
        }
        else {
          subAddressElm.html(emptyOptionElm).trigger('change');
        }
      });
    });
  </script>
@endsection
