@extends('layouts/contract-invoice')

@section('title', trans('app.invoice'))

@section('content')
<div class="row mb-4">
    <div class="col-12 mb-5">
      <h3 class="text-center invoice-title">
        {{ __('app.invoice') }} - ទិញចូល​
      </h3>
    </div>
    <div class="col-6">
      <p><label>{{ __('app.supplier') }} : <span>{{ $purchase->client->name }}</span> </label></p>
      <p><label>{{ __('app.phone_number') }} : <span>{{ $purchase->client->mobile ?? trans('app.n/a') }}</span> </label></p>
      <p><label>{{ __('app.address') }} : <span>{{ $purchase->client->address ?? trans('app.n/a') }}</span> </label></p>
    </div>
    <div class="col-2"></div>
    <div class="col-4">
      <p><label>{{ __('app.invoice_id') }} : <span>{{ $purchase->ref_no }}</span> </label></p>
      <p><label>{{ __('app.date') }} : <span>{{ khmerDate($purchase->transaction_date) }}</span> </label></p>
      <hr>
      <p><label>{{ __('ចំនួនសរុបដែលត្រូវបង់') }} : <span> $ {{ decimalNumber($purchase->depreciation_amount ?? 0, true) }}</span> </label></p>
    </div>
  </div>

  <div class="row" >
    <div class="col-md-12 mb-4">
      <table class="table-striped">
        <thead>
          <tr style="background: #EEE;" class="tbg-header">
            <th>{{ trans('app.no_sign') }}</th>
            <th style="width:50%;" class="tbg-header">ពណ៌នាអំពីទំនិញ</th>
            <th class="text-center tbg-header" style="width:10%;">ចំនួន</th>
            <th class="text-right tbg-header" style="width:20%;">តម្លៃរាយ</th>
            <th class="text-right tbg-header" style="width:15%;">តម្លែសរុប</th>
          </tr>
        </thead>
        <tbody>
          @php
            $offset = 1;
          @endphp
          @foreach($purchase->purchase_lines as $item)
            <tr>
              <td class="text-center">{{ $offset++ }}</td>
              <td style="padding: 10px 10px;">
                {{ $item->product->name.($item->variations->name != "DUMMY" ? ('-'.$item->variations->name.', លេខកូដ '.$item->variations->sub_sku) : ', លេខកូដ '.$item->product->code) }}
              </td>
              <td class="text-center">{{ number_format($item->quantity) }}</td>
              <td class="text-right">{{ decimalNumber($item->purchase_price) }}</td>
              <td class="text-right">{{ decimalNumber($item->purchase_price * $item->quantity, true) }}</td>
            </tr>
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
            <td rowspan="3" colspan="2" class="border-right p-3">
              {!! $invoice_head->invoice_footer_text !!}
            </td>
            <td colspan="2" class="text-right">ទឹកប្រាក់សរុប</td>
            <td class="text-right" style="background: #EEE;" >{{ decimalNumber($purchase->final_total ?? 0, true) }}</td>
          </tr>
          <tr>
            <td colspan="2" class="text-right">បានបង់ប្រាក់មុន</td>
            <td class="text-right" style="background: #EEE;">$ {{ decimalNumber($purchase->depreciation_amount ?? 0, true) }}</td>
          </tr>
          <tr>
            <td colspan="2" class="text-right">នៅសល់</td>
            <td class="text-right" style="background: #EEE;">{{ decimalNumber($purchase->remaining_amount ?? 0, true) }}</td>
          </tr>
        </tfoot>
        
      </table>
    </div>

  </div>
@endsection
