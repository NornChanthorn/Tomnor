<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    <form method="post" id="transaction_payment_add_form" class="no-auto-submit" action="{{ route('payments.save', $transaction->id) }}">
      @csrf
      <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">

      <div class="modal-header">
        <h4 class="modal-title">{{ trans('app.add_payment') }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>

      <div class="modal-body">
        @if(in_array($transaction->type, ['purchase', 'purchase_return']))
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
              @lang('app.supplier'):
              <address>
                <strong>{{ $transaction->client->supplier_business_name }}</strong>
                {{ $transaction->client->name }}
                @if(!empty($transaction->client->landmark))
                  <br>{{ $transaction->client->landmark }}
                @endif

                @if(!empty($transaction->client->mobile))
                  <br>{{ $transaction->client->mobile }}
                @endif
              </address>
            </div>
            <div class="col-md-4 invoice-col">
              {{ $transaction->warehouse->name }}
              <address>
                <strong>{{ $transaction->warehouse->location }}</strong>
                @if(!empty($transaction->warehouse->address))
                  <br>{{ $transaction->warehouse->address }}
                @endif

                @if(!empty($transaction->warehouse->phone_1))
                  <br>@lang('app.phone'): {{ $transaction->warehouse->phone_1 }}
                @endif
              </address>
            </div>

            <div class="col-sm-4 invoice-col">
              <span>@lang('app.reference_number'):</span> #{{ $transaction->ref_no }}<br/>
              <span>@lang('app.date'):</span> {{ $transaction->transaction_date }}<br/>
              <span>@lang('app.total_amount'):</span> ${{ $transaction->final_total }}
            </div>
          </div>
        @else
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
              @lang('app.customer'):
              <address>
                <strong>{{ $transaction->client->name }}</strong>
                @if(!empty($transaction->client->landmark))
                  <br>{{ $transaction->client->landmark }}
                @endif

                @if(!empty($transaction->client->mobile))
                  <br>{{ $transaction->client->mobile }}
                @endif
              </address>
            </div>
            <div class="col-md-4 invoice-col">
              {{ $transaction->warehouse->name }}
              <address>
                <strong>{{ $transaction->warehouse->location }}</strong>
                @if(!empty($transaction->warehouse->address))
                  <br>{{ $transaction->warehouse->address }}
                @endif

                @if(!empty($transaction->warehouse->phone_1))
                  <br>@lang('app.phone'): {{ $transaction->warehouse->phone_1 }}
                @endif
              </address>
            </div>

            <div class="col-sm-4 invoice-col">
              <span>@lang('app.reference_number'):</span> #{{ in_array($transaction->type, ['sell', 'sell_return']) ? $transaction->invoice_no : $transaction->ref_no }}<br/>
              <span>@lang('app.date'):</span> {{ $transaction->transaction_date }}<br/>
              <span>@lang('app.total_amount'):</span> ${{ $transaction->final_total }}<br>
            </div>
          </div>
        @endif

        <hr>
        <div class="row">
          <div class="col-md-4 form-group">
            <label for="amount">{{ trans('app.payment_amount') }} ({{ $payment->amount }}) <span class="text-danger">*</span></label>
            <input type="text" name="payment_amount" id="amount" class="form-control decimal-display" required value="{{ $payment->amount }}" data-rule-max="{{ $payment->amount }}" onclick="$(this).select();">
          </div>
          <div class="col-md-4 form-group">
            <label for="payment_date">{{ trans('app.payment_date') }} <span class="text-danger">*</span></label>
            <input type="text" name="paid_on" id="payment_date" class="form-control" readonly required value="{{ $payment->paid_on }}">
          </div>
          <div class="col-md-4 form-group">
            <label for="payment_method">{{ trans('app.payment_method') }} <span class="text-danger">*</span></label>
            <select name="payment_method" id="payment_method" class="form-control select2 select2-no-search" required>
              @foreach (paymentMethods() as $methodKey => $methodValue)
                <option value="{{ $methodKey }}">
                  {{ $methodValue }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-12 form-group">
            <label for="note">{{ trans('app.payment_note') }}</label>
            <textarea name="payment_note" id="payment_note" class="form-control" rows="3" style="resize:none;"></textarea>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> @lang('app.repay')</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('app.close') }}</button>
      </div>
    </form>
  </div>
</div>
