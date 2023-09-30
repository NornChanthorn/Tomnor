<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          {{ trans('app.view_payments') }}
          ( @lang('app.reference_number'):
            {{ $single_payment_line->invoice_number }}
          )
        </h4>
        <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
  
      <div class="modal-body">
        <div class="row">
          <div class="col-6">
            <p>
              <strong>
                {{ trans('app.client_name') }} : {{ $transaction->client->name }}
              </strong>
            </p>
            <p>
              <strong>
                {{ trans('app.phone_number') }} : {{ $transaction->client->mobile }}
              </strong>
            </p>
            <p>
              <strong>
                {{ trans('app.address') }} : {{ $transaction->client->landline ?? trans('app.n/a') }}
              </strong>
            </p>
          </div>
          <div class="col-6">
            <p>
              <strong>
                {{ trans('app.location') }} : {{ $transaction->warehouse->location.' '.$transaction->warehouse->location_en }}
              </strong>
            </p>
            <p>
              <strong>
                {{ trans('app.phone_number') }} : {{ $transaction->warehouse->phone_1 }}{{ $transaction->warehouse->phone_2 ? ', '.$transaction->warehouse->phone_2:'' }}{{ $transaction->warehouse->phone_3 ? ', '.$transaction->warehouse->phone_3:'' }}{{ $transaction->warehouse->phone_4 ? ', '.$transaction->warehouse->phone_4:'' }}
              </strong>
            </p>
            <p>
              <strong>
                {{ trans('app.address') }} : {{ $transaction->warehouse->address }}
              </strong>
            </p>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-6">
            <p>
              <strong>
                {{ trans('app.amount') }} : {{ num_f($single_payment_line->total) }}
              </strong>
            </p>
            <p>
              <strong>
                {{ trans('app.payment_method') }} : {{ paymentMethods($single_payment_line->payment_method) }}
              </strong>
            </p>
            <p>
              <strong>
                {{ trans('app.payment_note') }} : {{ $single_payment_line->note ?? trans('app.n/a') }}
              </strong>
            </p>
          </div>
          <div class="col-6">
            <p>
              <strong>
                {{ trans('app.reference_number') }} : {{ $single_payment_line->invoice_number }}
              </strong>
            </p>
            <p>
              <strong>
                {{ trans('app.payment_date') }} : {{ displayDate($single_payment_line->payment_date) }}
              </strong>
            </p>
            
          </div>
        </div>
      </div>
  
      <div class="modal-footer no-print">
        <button type="button" class="btn btn-primary" aria-label="Print" onclick="$(this).closest('div.modal').print();">
          <i class="fa fa-print"></i> @lang( 'app.print' )
        </button>
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('app.close') }}</button>
      </div>
    </div>
  </div>
  