<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title">
        {{ trans('app.view_payments') }}
        (@lang('app.reference_number'):
          @if(in_array($transaction->type, ['purchase', 'expense', 'purchase_return', 'payroll']))    
            {{ $transaction->ref_no }} 
          @elseif(in_array($transaction->type, ['sell', 'sell_return']))
            {{ $transaction->invoice_no }}
          @endif
        )
      </h4>
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
            <b>@lang('app.reference_number'):</b> #{{ $transaction->ref_no }}<br/>
            <b>@lang('app.date'):</b> {{ $transaction->transaction_date }}<br/>
            <b>@lang('app.purchase_status'):</b> {{ purchaseStatuses($transaction->status) }}<br>
            <b>@lang('app.payment_status'):</b> {{ paymentStatus( $transaction->payment_status ) }}<br>
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
            <span>@lang('app.reference_number'):</span> #{{ $transaction->invoice_no }}<br/>
            <span>@lang('app.date'):</span> {{ $transaction->transaction_date }}<br/>
            <span>@lang('app.payment_status'):</span> {{ paymentStatus( $transaction->payment_status ) }}<br>
          </div>
        </div>
      @endif

      <div class="row">
        <div class="col-md-12">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>@lang('app.date')</th>
                <th>@lang('app.reference_number')</th>
                <th class="text-right">@lang('app.amount')</th>
                <th class="text-center">@lang('app.payment_method')</th>
                <th>@lang('app.payment_note')</th>
                <th class="no-print">@lang('app.action')</th>
              </tr>
            </thead>
            <tbody>
              @forelse($payments as $payment)
                <tr>
                  <td>{{ $payment->payment_date }}</td>
                  <td>{{ $payment->invoice_number }}</td>
                  <td class="text-right">$ {{ decimalNumber($payment->payment_amount,2) }}</td>
                  <td class="text-center">{{ paymentMethods($payment->payment_method) }}</td>
                  <td>{{ $payment->note }}</td>
                  <td>{{ $payment->id }}</td>
                  {{-- <td class="no-print" style="display: flex;">
                    @if(!empty($payment->document_path))
                      &nbsp;
                      <a href="{{$payment->document_path}}" class="btn btn-success btn-xs" download="{{$payment->document_name}}"><i class="fa fa-download" data-toggle="tooltip" title="{{__('purchase.download_document')}}"></i></a>
                    @endif
                  </td> --}}
                </tr>
              @empty
                <tr class="text-center">
                  <td colspan="6">@lang('message.no_records_found')</td>
                </tr>
              @endforelse
            </tbody>
          </table>
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
