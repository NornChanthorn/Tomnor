@extends('layouts/backend')

@section('title', $title)

@section('css')
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css"> --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">

@endsection
@section('content')
<main class="app-content">
  <div class="tile">

    @include('partial/flash-message')
    <div class="card mb-4">
      <div class="card-header">
        <h3>{{ $title }}</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4" style="font-weight:bold">
            <div class="row">
              <div class="col-5">
                <p>
                  {{ trans('app.customer') }}{{ trans('app.name') }}
                </p>
              </div>
              <div class="col-6">
                <p>
                  :&nbsp; &nbsp; &nbsp; {{ $contact->name }}
                </p>
              </div>
            </div>
            @if ($contact->type=='supplier')
              <div class="row">
                <div class="col-5">
                  <p>
                    {{ trans('app.company_name') }}
                  </p>
                </div>
                <div class="col-6">
                  <p>
                    :&nbsp; &nbsp; &nbsp; {{ $contact->supplier_business_name }}
                  </p>
                </div>
              </div>
            @endif
            <div class="row">
              <div class="col-5">
                <p>
                  {{ trans('app.type') }}
                </p>
              </div>
              <div class="col-6">
                <p>
                  :&nbsp; &nbsp; &nbsp; {{ contacttypes($contact->type) }}
                </p>
              </div>
            </div>
            <div class="row">
              <div class="col-5">
                <p>
                  {{ trans('app.phone_number') }}
                </p>
              </div>
              <div class="col-6">
                <p>
                 : &nbsp; &nbsp; &nbsp; {{ $contact->mobile }}
                </p>
              </div>
            </div>
            <div class="row">
              <div class="col-5">
                <p>
                  {{ trans('app.created_date') }}
                </p>
              </div>
              <div class="col-6">
                <p>
                  : &nbsp; &nbsp; &nbsp;{{ displayDate($contact->created_at) }}
                </p>
              </div>
            </div>
            <div class="row">
              <div class="col-5">
                <p>
                  {{ trans('app.group') }}
                </p>
              </div>
              <div class="col-6">
                <p>
                 : &nbsp; &nbsp; &nbsp;{{ @$contact->contact_group->name }}
                </p>
              </div>
            </div>
            <div class="row">
              <div class="col-5">
                <p>
                  {{ trans('app.limit_credit_balance') }}
                </p>
              </div>
              <div class="col-6">
                <p>
                 : &nbsp; &nbsp; &nbsp;{{ num_f($contact->credit_limit) }}
                </p>
              </div>
            </div>
            <a href="{{ route('payments.getPayContactDue', [$contact,'type'=> $contact->type == 'sell' ? 'sell' : 'purchase']) }}" class="btn btn-success add_payment_modal" ><i class="fa fa-money"></i> {{ trans('app.add_payment') }}</a>
          </div>
          <div class="col-md-4">
            <p>{{ trans('app.account_summary') }} {{ displayDate(@$transactions->first()->transaction_date) }} {{ trans('app.to') }} {{ displayDate(@$payments->first()->payment_date) }}</p>
            <p></p>
          </div>
          <div class="col-md-4">
            <div class="row">
              <div class="col-6">
                <p class="text-right">
                  <strong>
                    {{ trans('app.open_balance') }}
                  </strong>
             
                </p>
              </div>
              <div class="col-6">
                <p>
                  <strong>
                    :&nbsp; &nbsp; &nbsp; {{ num_f($opening_balance) }}
                  </strong>
            
                </p>
              </div>
              
            </div>
            <div class="row">
              <div class="col-6">
                <p class="text-right">
                  <strong>
                    {{ trans('app.total_invoice') }}
                  </strong>
      
                </p>
              </div>
              <div class="col-6">
                <p>
                  <strong>
                    :&nbsp; &nbsp; &nbsp; {{ num_f(@$transactions->whereIn('type',['sell','purchase','sell_return','purchase_return'])->sum('final_total')) }}
                  </strong>
          
                </p>
              </div>
            </div>
            <div class="row">
              <div class="col-6">
                <p class="text-right">
                  <strong>
                    {{ trans('app.total_paid_amount') }}
                  </strong>
          
                </p>
              </div>
              <div class="col-6">
                @php
                    $total = 0;
                @endphp
                <p>
                  <strong>
                    :&nbsp; &nbsp; &nbsp; {{ num_f(@$payments->whereIn('invoice_type',['sell','purchase','sell_return','purchase_return'])->sum('total')) }}
                  </strong>
                 
                </p>
              </div>
            </div>
            <div class="row">
              <div class="col-6">
                <p class="text-right">
                  <strong>
                    {{ trans('app.balance_due') }}
                  </strong>
         
                </p>
              </div>
              <div class="col-6">
                <p>
                  <strong>
                    :&nbsp; &nbsp; &nbsp; {{ num_f(@$transactions->whereIn('type',['sell','purchase','sell_return','purchase_return'])->sum('final_total') - @$payments->whereIn('invoice_type',['sell','purchase','sell_return','purchase_return'])->sum('total')) }}
                  </strong>
                </p>
              </div>
            </div>
            <div class="row">
              <div class="col-6">
                <p class="text-right">
                  <strong>
                    {{ trans('app.opening_balance_due') }}
                  </strong>
             
                </p>
              </div>
              <div class="col-6">
                <p>
                  <strong>
                    :&nbsp; &nbsp; &nbsp; {{ num_f($opening_balance - $opening_balance_due) }}
                  </strong>
            
                </p>
              </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card mb-4">
      <div class="card-header">
        <h3>{{ trans('app.transactions') }}</h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th style="width: 5%">{{ trans('app.no_sign') }}</th>
              <th>{{ trans('app.type') }}</th>
              <th>{{ trans('app.date') }}</th>
              <th>{{ trans('app.invoice_number') }}</th>
              <th>{{ trans('app.location') }}</th>
              <th>{{ trans('app.payment_status') }}</th>
              <th>{{ trans('app.total_amount') }}</th>
              <th>{{ trans('app.paid_amount') }}</th>
              <th>{{ trans('app.due_amount') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($transactions as $key => $transaction)
              <tr>
                  <td>
                    {{ no_f($key+1) }}
                  </td>
                  <td>
                    {{ sellTypes($transaction->type) }}
                  </td>
                  <td>
                    {{ displayDate($transaction->transaction_date) }}
                  </td>
                  <td>
                    @if ($transaction->type=='sell')
                      <a href="{{ route('sale.invoice', $transaction) }}"  title="{{ __('app.invoice') }}">{{ $transaction->invoice_no ?? $transaction->ref_no }}</i></a>
                    @endif
                    @if ($transaction->type=='purchase')
                      <a href="{{ route('purchase.invoice', $transaction) }}"  title="{{ __('app.invoice') }}">{{ $transaction->invoice_no ?? $transaction->ref_no }}</i></a>
                    @endif
             
                  </td>
              
                  <td>
                      @if($transaction->warehouse)
                        @include('partial.branch-detail-link', ['branch' => $transaction->warehouse])
                      @else
                        {{ trans('app.none') }}
                      @endif
                  </td>
                  <td>
                    {{ paymentStatus($transaction->payment_status) }}
                  </td>
                  <td>
                    {{ num_f($transaction->final_total) }}
                  </td>
                  <td>
                    {{ num_f(@$transaction->invoices->sum('total')) }}
                  </td>
                  <td>
                    {{ num_f($transaction->final_total - @$transaction->invoices->sum('total')) }}
                  </td>
                
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
     
    </div>
    <div class="card mb-4">
      <div class="card-header">
        <h3>{{ trans('app.payment') }}</h3>
      </div>
      <div class="card-body table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>{{ trans('app.action') }}</th>
              <th>{{ trans('app.date') }}</th>
              <th>{{ trans('app.reference_number') }}</th>
              <th>{{ trans('app.amount') }}</th>
              <th>{{ trans('app.payment_method') }}</th>
              <th>{{ trans('app.invoice_number') }}</th>
            </tr>
          </thead>
          <tbody>
            
            @foreach ($payments as $payment)
                <tr>
                  <td>
                    @include('partial/button-delete', ['url' => route('payments.destroy', $payment)])
                    <a href="" class="btn btn-sm btn-info mb-1 btn-modal" data-href="{{ route('payments.viewPayment',$payment) }}" data-container=".payment_modal">
                      <i class="fa fa-eye"></i>
                    </a>
                  </td>
                  <td>
                    {{ displayDate($payment->payment_date) }}
                  </td>
                  <td>
                      {{ $payment->invoice_number ?? $payment->reference_number }}
                  </td>
                  <td>
                    {{ num_f($payment->total) }}
                  </td>
                  <td>
                    {{ paymentMethods($payment->payment_method) }}
                  </td>
                  <td>

                      @if ($payment->transaction_type=='sell')
                        <a href="{{ route('sale.invoice', $payment->transaction_id) }}"  title="{{ __('app.invoice') }}">
                          {{ @$payment->transaction->invoice_no }}
                        
                        </a> 
                        <br>
                        ({{ sellTypes($payment->transaction_type) }})
                      @endif
                      @if ($payment->transaction_type=='purchase')
                        <a href="{{ route('purchase.invoice', $payment->transaction_id) }}"  title="{{ __('app.invoice') }}">
                          {{ @$payment->transaction->ref_no ?? @$payment->transaction->invoice_no  }}
                        
                        </a> ({{ sellTypes($payment->transaction_type) }})
                      @endif
                      
                  </td>
                </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        <a href="" class="btn btn-success">
          {{ trans('app.back') }}
        </a>
      </div>
    </div>
    
  </div>
  <div class="modal fade payment_modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>
</main>
@endsection
@section('js')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('js/jquery-mask.min.js') }}"></script>
<script src="{{ asset('js/date-time-picker.js') }}"></script>
<script src="{{ asset('js/jquery-number.min.js') }}"></script>
<script src="{{ asset('js/number.js') }}"></script>


<script>

  window.$('.table').DataTable();
  $(document).on('click', '.add_payment_modal', function(e) {
    e.preventDefault();
    var container = $('.payment_modal');

    $.ajax({
      url: $(this).attr('href'),
      type: "GET",
      dataType: 'json',
      success: function(result) {
        if (result.status == 'due') {
          container.html(result.view).modal('show');
          $('.datepicker').datepicker({
            format: 'dd-mm-yyyy'
          });
          formatNumericFields();
        }else{
            toastr.error(result.message);
        }
      },
    });
  });
</script>
@endsection