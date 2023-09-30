<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    @php
      $isFormShowType = ($formType == FormType::SHOW_TYPE);
      $disabledFormType = ($isFormShowType ? 'disabled' : '');

      $form_id = isset($quick_add) ? 'quick_add_contact' : 'form-contact';
    @endphp

    <form id="{{ $form_id }}" method="post" action="{{ route('expense.save',$expense) }}" enctype="multipart/form-data">
      <div class="modal-header">
        <h4 class="modal-title">{{ $title }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>

      <div class="modal-body">
        @include('partial/flash-message')
        @csrf
        {{-- Personal info --}}
        <fieldset class="">
          <div class="row">
            {{-- Type --}}
            <div class="col-md-3 form-group">
              <label for="name" class="control-label">
                {{ trans('app.category') }} <span class="required">*</span>
              </label>
              <select name="category_id" id="category_id" class="form-control select2" required {{ $disabledFormType }}>
                @foreach ($categories as $item)
                    <option value="{{ $item->id }}" {{ $item->id == $expense->category_id ? "selected" : "" }} {{ $disabledFormType }}>
                        {{ $item->value }}
                    </option>
                @endforeach
              </select>
            </div>

            {{-- Name --}}
            <div class="col-md-5 form-group">
                <label for="refno" class="control-label">
                  {{ trans('app.reference_number') }}
                </label>
                <input type="text" name="refno" id="refno" class="form-control" value="{{ $expense->refno ?? old('refno') }}" {{ $disabledFormType }}>
            </div>

            {{-- Name --}}
            <div class="col-md-4 form-group">
                <label for="amount" class="control-label">
                {{ trans('app.amount') }} <span class="required">*</span>
                </label>
                <input type="number" name="amount" id="amount" class="form-control" value="{{ $expense->amount ?? old('amount') }}" required {{ $disabledFormType }}>
            </div>
            <div class="form-group col-lg-6">
                <label for="expense_date" class="control-label">{{ trans('app.date') }}</label>
                <input type="text" name="expense_date" id="expense_date" class="form-control date-picker" placeholder="{{ trans('app.date_placeholder') }}" value="{{ displayDate($expense->expense_date ?? date('Y-m-d')) }}" {{ $disabledFormType }}>
            </div>

             {{-- Ref Doc --}}
             <div class="col-lg-6 form-group">
                <label for="ref_doc" class="control-label">{{ trans('app.document') }}</label>
                @if ($isFormShowType)
                    <div class="text-left">
                        @if (isset($expense->ref_doc))
                            <img src="{{ asset($expense->ref_doc) }}" width="100%" class="img-responsive">
                        @else
                            {{ trans('app.no_picture') }}
                        @endif
                    </div>
                @else
                    <input type="file" name="ref_doc" id="ref_doc" class="form-control" accept=".jpg, .jpeg, .png">
                @endif
            </div>
            <div class="col-lg-12 form-group">
                <label for="note" class="control-label">
                    {{ trans('app.note') }}
                </label>
                <textarea name="note" id="note" class="form-control" {{ $disabledFormType }}>{{ $expense->note ?? old('note') }}</textarea>
            </div>
        </fieldset>
      </div>

      <div class="modal-footer">
        @if ($disabledFormType == !FormType::SHOW_TYPE)
        @include('partial/button-save')
        @endif

        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('app.close') }}</button>
      </div>
    </form>
  </div>
</div>
