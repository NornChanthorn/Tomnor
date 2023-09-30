<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        @php
            $isFormShowType = ($formType == FormType::SHOW_TYPE);
            $disabledFormType = ($isFormShowType ? 'disabled' : '');
    
            $form_id = isset($quick_add) ? 'quick_add_contact' : 'form-contact';
        @endphp
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">×</span></button>
            <h3 class="page-heading">{{ trans('app.payment_method') . ' - '.$title }}</h3>

            <form  id="{{ $form_id }}" method="post" action="{{ route('method_payment.save',$methodPayment) }}" enctype="multipart/form-data">
                @csrf
                @if ($formType!=FormType::EDIT_TYPE)
                    <div class="form-group">
                        <label for="">{{ trans('app.code') }} (អក្សរកាត់)</label>
                        <input type="text" class="form-control" name="value" value="{{ $methodPayment->property_name }}" required {{ $disabledFormType }}>
                    </div>
                @endif
              
                <div class="form-group">
                    <label for="">{{ trans('app.name') }}</label>
                    <input type="text" class="form-control" name="name" value="{{ $methodPayment->value }}" required {{ $disabledFormType }}>
                </div>
                <button class="btn btn-success" type="submit"  {{ $disabledFormType }}>{{ trans('app.save') }}</button>
            </form>
        </div>

    </div>
</div>
