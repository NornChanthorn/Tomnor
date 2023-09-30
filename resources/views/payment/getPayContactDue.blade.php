<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{ trans('app.add_payment') }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form action="{{ route('payments.savePayContactDue',$contact_details->contact_id) }}" id="transaction_payment_add_form" class="no-auto-submit" method="post" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="contact_id" value="{{ $contact_details->contact_id }}">
        <input type="hidden" name="due_payment_type" value="{{ $due_payment_type }}">
        <div class="modal-body">
          <div class="card mb-4">
            <div class="card-body">
              <div class="row">

                @if($due_payment_type == 'purchase')
                  <div class="col-md-6">
                    <p>{{ trans('app.client_name') }} : {{ $contact_details->name }}</p>
                    <p>{{ trans('app.company_name') }} : {{ $contact_details->supplier_business_name }}</p>
                    <p>{{ trans('app.phone_number') }} : {{ $contact_details->mobile }}</p>
                  </div>
                  <div class="col-md-6">
                    <p>
                      <b>
                        {{ trans('app.total_purchase_amount') }} : {{ num_f($contact_details->total_purchase) }}
                      </b>
                    </p>
                    <p>
                      <b>
                        {{ trans('app.total_purchase_paid_amount') }} : {{ num_f($contact_details->total_paid) }}
                      </b>
                    </p>
                    <p>
                      <b>
                        {{ trans('app.total_due_purchase_amount') }} : {{ num_f($contact_details->total_purchase - $contact_details->total_paid) }}
                      </b>
                    </p>
                    @if(!empty($contact_details->opening_balance) || $contact_details->opening_balance != '0.00')
                      <p>
                        <b>
                          {{ trans('app.open_balance') }} : {{ num_f($contact_details->opening_balance) }}
                        </b>
                      </p>
                      <p>
                        <b>
                          {{ trans('app.opening_balance_due') }} : {{ num_f($ob_due) }}
                        </b>
                      </p>
                    @endif
                  </div>
                @else
                  <div class="col-md-6">
                    <p>{{ trans('app.client_name') }} : {{ $contact_details->name }}</p>
                    <p>{{ trans('app.phone_number') }} : {{ $contact_details->mobile }}</p>
                  </div>
                  <div class="col-md-6">
                    <p>
                      <b>
                        {{ trans('app.total_sale_amount') }} : {{ num_f($contact_details->total_invoice) }}
                      </b>
                    </p>
                    <p>
                      <b>
                        {{ trans('app.total_sale_paid_amount') }} : {{ num_f($contact_details->total_paid) }}
                      </b>
                    </p>
                    <p>
                      <b>
                        {{ trans('app.total_sale_due_amount') }} : {{ num_f($contact_details->total_invoice - $contact_details->total_paid) }}
                      </b>
                    </p>
                    @if(!empty($contact_details->opening_balance) || $contact_details->opening_balance != '0.00')
                      <p>
                        <b>
                          {{ trans('app.open_balance') }} : {{ num_f($contact_details->opening_balance) }}
                        </b>
                      </p>
                      <p>
                        <b>
                          {{ trans('app.opening_balance_due') }} : {{ num_f($ob_due) }}
                        </b>
                      </p>
                    @endif
                  </div>
                  @endif
                
              </div>
            </div>       
          </div>
          <div class="row">
            <div class="col-md-6 form-group">
              <label for="amount">
                {{ trans('app.payment_amount') }}
                <span class="text-danger">*</span>
              </label>
              <input type="text" class="form-control decimal-input" name="payment_amount" value="{{  $payment_line->amount }}">
            </div>
            <div class="col-md-6 form-group">
              <label for="amount">
                {{ trans('app.paid_date') }}
                <span class="text-danger">*</span>
              </label>
              <input type="text" class="form-control datepicker" name="payment_date" value="{{ displayDate($payment_line->paid_on) }}">
            </div>
            <div class="col-md-6 form-group">
              <label for="amount">
                {{ trans('app.payment_method') }}
                <span class="text-danger">*</span>
              </label>
              <select name="payment_method" id="" class="form-control">
                @foreach (paymentMethods() as $pk =>$pv)
                    <option value="{{ $pk }}" @if ($pk=='dp') selected @endif> {{ $pv }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 form-group">
              <label for="amount">
                {{ trans('app.payment_note') }}
              </label>
              <textarea class="form-control" name="payment_note" id="" cols="10" rows="1"></textarea>
          </div>
            
           
            
            
           
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> {{ trans('app.add_payment') }}</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('app.close') }}</button>
        </div>
      </form>
    </div>
  </div>