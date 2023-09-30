<div class="modal-dialog modal-sm modal-dialog-center" role="document">
    <div class="modal-content">

        <form action="{{ route('payments.savePaymentDate',$payment) }}" method="post">
            {{ csrf_field() }}
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="">{{ trans('app.payment_date') }}</label>
                        <input type="text" class="form-control date-picker" name="payment_date" value="{{ displayDate($payment->payment_date) }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('app.close') }}</button>
                @include('partial.button-save')
            </div>
            
        </form>
    </div>
</div>