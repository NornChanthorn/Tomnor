<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    @php
      $isFormShowType = ($formType == FormType::SHOW_TYPE);
      $disabledFormType = ($isFormShowType ? 'disabled' : '');

      const supplier_FIELD_TYPE = 'c';
      const SPONSOR_FIELD_TYPE = 's';

      $form_id = isset($quick_add) ? 'quick_add_contact' : 'form-contact';
    @endphp

    <form id="{{ $form_id }}" method="post" action="{{ route('contact.save', $contact) }}" enctype="multipart/form-data">
      <div class="modal-header">
        <h4 class="modal-title">{{ trans('app.'.$contact->type) . ' - ' . $title }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>

      <div class="modal-body">
        @include('partial/flash-message')
        @csrf
        <input type="hidden" name="type"  id="type" value="{{ $contact->type }}">
        <input type="hidden" id="form-type" name="form-type" value="{{ $formType }}">
        {{-- Personal info --}}
        <fieldset class="">
          <div class="row">

            {{-- Type --}}
            @if ($contact->type=='supplier')
              @if(count($groups)>0)
                <div class="col-md-4 form-group">
                    <label for="name" class="control-label">
                      {{ trans('app.contact_group') }} <span class="required">*</span>
                    </label>
                    <select name="contact_group_id" id="contact_group_id" class="form-control select2" required>
                      @foreach ($groups as $group)
                        <option value="{{ $group->id }}" {{ selectedOption($group->id, old('contact_group_id', $contact->contact_group_id)) }}>
                          {{ $group->name }}
                        </option>
                      @endforeach
                    </select>
                </div>
              @endif
            @endif
              
            @if (isAdmin())
              <div class="col-md-4 form-group">
                <label for="name" class="control-label">
                  {{ trans('app.creator') }} <span class="required">*</span>
                </label>
                <select name="created_by" id="created_by" class="form-control select2" required>
                  @foreach (staffs() as $staff)
                    <option value="{{ $staff->user_id }}" {{ selectedOption($staff->user_id, old('created_by', $contact->created_by)) }}>
                      {{ $staff->name }}
                    </option>
                  @endforeach
                </select>
              </div>
             
            @endif
            {{-- Branch --}} 
            <div class="col-md-4 form-group">
              <label for="name" class="control-label">
                {{ trans('app.contact_group') }} <span class="required">*</span>
              </label>
              <select name="contact_group_id" id="contact_group_id" class="form-control select2" required>
                @foreach ($groups as $group)
                  <option value="{{ $group->id }}" {{ selectedOption($group->id, old('contact_group_id', $contact->contact_group_id)) }}>
                    {{ $group->name }}
                  </option>
                @endforeach
              </select>
            </div>
            {{-- Name --}}
            <div class="col-lg-4 form-group">
              <label for="name" class="control-label">
                {{ trans('app.name') }} <span class="required">*</span>
              </label>
              <input type="text" name="name" id="name" class="form-control" value="{{ $contact->name ?? old('name') }}" required {{ $disabledFormType }}>
            </div>

            {{-- Contact Id --}}
            <div class="col-lg-4 form-group">
              <label for="contact_id" class="control-label">{{ trans('app.contact-id') }}</label>
              <input type="hidden" id="hidden_id" value="{{$contact->id}}">
              <input type="text" name="contact_id" id="contact_id" class="form-control" value="{{ $contact->contact_id ?? old('contact_id') }}" {{ $disabledFormType }}>
            </div>

            {{-- Company --}}
            <div class="col-lg-4 form-group hidden-block {{ $contact->type==\App\Constants\ContactType::CUSTOMER ? 'd-none' : '' }}">
              <label for="company" class="control-label">
                {{ trans('app.company') }}
              </label>
              <input type="text" name="company" id="company" class="form-control" value="{{ $contact->supplier_business_name ?? old('company') }}" {{ $disabledFormType }}>
            </div>

            {{-- Ref Code --}}
            <div class="col-lg-4 form-group">
              <label for="ref_code" class="control-label">{{ trans('app.reference_number') }}</label>
              <input type="text" name="ref_code" id="ref_code" class="form-control" value="{{ $contact->custom_field1 ?? old('ref_code') }}" {{ $disabledFormType }}>
            </div>

            {{-- Opening Balance --}}
            <div class="col-lg-4 form-group">
              <label for="opening_balance" class="control-label">{{ trans('app.open_balance') }}</label>
              <input type="text" name="opening_balance" id="opening_balance" class="form-control" value="{{ $opening_balance ?? old('opening_balance') }}" {{ $disabledFormType }}>
            </div>
          </div>

          <hr>

          <div class="row">
            {{-- ID card number --}}
            <div class="col-lg-4 form-group">
              <label for="id_card_number" class="control-label">
                {{ trans('app.id_card_number') }}
              </label>
              <input type="text" name="id_card_number" id="id_card_number" class="form-control id-card" value="{{ $contact->custom_field2 ?? old('id_card_number') }}" {{ $disabledFormType }}>
            </div>

            {{-- First phone --}}
            <div class="col-lg-4 form-group">
              <label for="phone" class="control-label">
                {{ trans('app.first_phone') }} <span class="required">*</span>
              </label>
              <input type="text" name="phone" id="phone" class="form-control phone" value="{{ $contact->mobile ?? old('phone') }}" required {{ $disabledFormType }}>
            </div>

            {{-- Second phone --}}
            <div class="col-lg-4 form-group">
              <label for="second_phone" class="control-label">{{ trans('app.second_phone') }}</label>
              <input type="text" name="second_phone" id="second_phone" class="form-control phone" value="{{ $contact->alternate_number ?? old('second_phone') }}" {{ $disabledFormType }}>
            </div>
          </div>

          {{-- Address --}}
          <div class="row">
            {{-- Province --}}
            <div class="col-lg-4 form-group">
              <label for="province" class="control-label">
                {{ trans('app.province') }}
              </label>
              @if ($isFormShowType)
                <input type="text" class="form-control" value="{{ $contact->province->name ?? '' }}" disabled>
              @else
                <select name="province" id="province" class="form-control select2" {{ $disabledFormType }} data-address-type="{{ AddressType::PROVINCE }}" data-field-type="{{ supplier_FIELD_TYPE }}">
                  <option value="">{{ trans('app.select_option') }}</option>
                  @foreach ($provinces as $province)
                    <option value="{{ $province->id }}" {{ $province->id == $contact->city ? 'selected' : '' }}>
                      {{ $province->khmer_name }}
                    </option>
                  @endforeach
                </select>
              @endif
            </div>

            {{-- Address --}}
            <div class="col-lg-8 form-group">
              <label for="addresses" class="control-label">
                {{ trans('app.address') }}
              </label>
              <input type="text" name="address" id="address" class="form-control" value="{{ $contact->address ?? old('address') }}" {{ $disabledFormType }}>
            </div>
          </div>
        </fieldset>
      </div>

      <div class="modal-footer">
        @if ($isFormShowType)
          @if (isAdmin() && $contact->is_default == 0)
            @include('partial/anchor-edit', ['href' => route('contact.edit', $contact->id)])
          @endif
        @else
          @include('partial/button-save')
        @endif
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('app.close') }}</button>
      </div>
    </form>
  </div>
</div>
