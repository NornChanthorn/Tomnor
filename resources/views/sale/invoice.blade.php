@extends('layouts/contract-invoice')

@section('title', trans('app.invoice'))

@section('content')
<div class="row mb-4">
  <div class="col-12">
    <h3 class="text-center invoice-title">
      {{ __('app.invoice') }}​
    </h3>
  </div>
  <div class="col-6">
  
    <p>{{ __('app.client') }} : <b> {{ $sale->client->name }}</b></p>
    <p>{{ $sale->client->landmark ?? "N/A" }}</p>
    <p>{{ $sale->client->mobile ?? "N/A"}}</p>

  </div>
  <div class="col-2"></div>
  <div class="col-4">
    <p><label>{{ __('app.invoice_number') }}</label> : <span>{{ $sale->invoice_no }}</span></p>
    <p><label>{{ __('app.issue_date') }}</label> : <span>{{ khmerDate($sale->transaction_date) }}</span></p>
    <hr>
    <p>
        {{ __('ចំនួនសរុបដែលត្រូវបង់') }} : $ {{ decimalNumber($sale->depreciation_amount) }}
    </p>

  </div>
</div>

<table class="table-striped">
  <thead>
    <tr>
      <th class="text-center tbg-header" style="width:5%;">ល.រ<br>No</th>
      <th class="tbg-header" style="width:50%;">ឈ្មោះទំនិញ<br>Name of Goods</th>
      <th class="text-center tbg-header" style="width:10%;">ចំនួន<br>QTY</th>
      <th class="text-right tbg-header" style="width:15%;">តម្លៃរាយ<br>Unit Price</th>
      <th class="text-right tbg-header" style="width:15%;">តម្លែសរុប<br>Amount</th>
    </tr>
  </thead>
  <tbody>
    @php
      $offset = 1;
    @endphp
    @foreach($sale->sell_lines as $key => $item)
      <tr>
        <td class="text-center">{{ $key+1 }}</td>
        <td style="padding: 10px 10px;">
          {{ $item->product->name.(empty($item->variations->name)||$item->variations->name=='DUMMY' ? '' : '-'.$item->variations->name.'-'.$item->variations->sub_sku) }}, លេខកូដ {{ $item->product->code }}
          <br>
          {{ $item->product->enable_sr_no ? 'IMEI: ' : "" }}
            @if ($item->product->enable_sr_no==1)
                @if (count($item->transaction_ime)>0)
                    @foreach ($item->transaction_ime as $ime)
                        @if (!$loop->first)
                            ,
                        @endif
                        {{ $ime->ime->code }}
                    @endforeach
                @else
                    N/A
                @endif

            @endif



        </td>
        <td class="text-center">{{ number_format($item->quantity) }}</td>
        <td class="text-right">{{ decimalNumber($item->unit_price,2) }}</td>
        <td class="text-right">{{ decimalNumber($item->unit_price*$item->quantity ,true) }}</td>
      </tr>

      @php
        $offset++;
      @endphp
    @endforeach
    @for($j=$offset; $j<6; $j++)
      <tr>
        <td class="text-center">{{ $j }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    @endfor
  </tbody>
  <tfoot>
    <tr>
      <td rowspan="5" colspan="2" class="border-0">
        {!! $invoice_head->invoice_footer_text !!}
      </td>

      <td colspan="2" class="text-right tbg-total border-0">ប្រាក់សរុប / Total</td>
      <td class="text-right tbg-total border-0">$ {{ decimalNumber($sale->final_total) }}</td>
    </tr>
    <tr>
      <td colspan="2" class="text-right border-0">បញ្ចុះ​តម្លៃ / Discount</td>
      <td class="text-right border-0" >$ {{ decimalNumber($sale->discount_amount) }}</td>
    </tr>
    <tr>
      <td colspan="2" class="text-right border-0">តម្លែសេវាផ្សេងៗ / Others</td>
      <td class="text-right border-0" >$ {{ decimalNumber($sale->others_charges) }}</td>
    </tr>
    <tr>
      <td colspan="2" class="text-right  border-0">ប្រាក់កក់ / Deposit</td>
      <td class="text-right border-0" >$ {{ decimalNumber($sale->depreciation_amount) }}</td>
    </tr>
    <tr>
      <td colspan="2" class="text-right border-0">ប្រាក់នៅសល់ / Balance</td>
      <td class="text-right border-0" >$ {{ decimalNumber($sale->remaining_amount) }}</td>
    </tr>
  </tfoot>
</table>
@endsection
