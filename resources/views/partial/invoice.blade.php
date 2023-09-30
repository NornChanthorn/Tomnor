@extends('layouts/contract-invoice')

@section('title', trans('app.invoice'))

@section('content')
  <div class="row content">
    <div class="col-xs-4" style="padding-right: 0;">
      <p><span><b>{{ $sale->warehouse->name . ' ' . $sale->warehouse->location }}</b></span></p>
      <p>{{ $sale->warehouse->address }}</p>
      <p><b style="display: inline-block;width: 18px;">{{ __('Tel') }}</b> : <b>
        {{ $sale->warehouse->phone_1 }} {{ $sale->warehouse->phone_2 ? '/ '.$sale->warehouse->phone_2 : '' }}
        @if($sale->warehouse->phone_3 || $sale->warehouse->phone_4)
          <br>
          <b style="display: inline-block;width: 18px;"></b>
          &nbsp;
          {{ $sale->warehouse->phone_3 ? $sale->warehouse->phone_3 : ''}} {{ $sale->warehouse->phone_4 ? '/ '.$sale->warehouse->phone_4 : '' }}
        @endif
      </b></p>
    </div>

    <div class="col-xs-4 content-title" style="padding: 0;">
      <h4 class="content-title-header">វិក័យប័ត្រ - លក់</h4>
      <h4 class="content-title-heading">Sale invoice</h4>
    </div>

    <div class="col-xs-4" style="padding-left: 0;">
      <p><label>{{ __('app.invoice_number') }}</label> : <span>{{ $sale->invoice_no }}</span></p>
      <p><label>{{ __('app.date') }}</label> : <span>{{ khmerDate($sale->transaction_date) }}</span></p>
      <p><label>{{ __('app.sale_agency') }}</label> : <span>
        @if($sale->type == 'leasing')
          {{ $loan->staff->name }}
        @else
          {{ $sale->staff->name }}
        @endif
      </span></p>
      <p><label>{{ __('app.customer') }}</label> : <span>
        @if($sale->type == 'leasing')
          {{ $sale->customer->name }}, {{ trans('app.id_card_number') }} : {{ $sale->customer->id_card_number }}
        @else
          {{ $sale->client->name }}
        @endif
      </span></p>
      <p><label>{{ __('app.contact') }}</label> : <span>
        @if($sale->type == 'leasing')
          {{ $sale->customer->first_phone }}
          {{ isset($sale->customer->second_phone) ? ' / ' . $sale->customer->second_phone : '' }}
        @else
          {{ $sale->client->mobile }}
        @endif
      </span></p>
      <p><label>{{ __('app.address') }}</label> : <span>{{ ($sale->type=='leasing' ? $sale->customer->address : $sale->client->landmark) ?? 'N/A' }}</span></p>
    </div>
  </div>

  <div class="render">
    <table>
      <thead>
        <tr style="background: #EEE;">
          <th class="text-center" style="width:7%;">ល.រ<br>No</th>
          <th style="width:50%;">ឈ្មោះទំនិញ<br>Name of Goods</th>
          <th class="text-center" style="width:10%;">ចំនួន<br>QTY</th>
          <th class="text-right" style="width:10%;">តម្លៃរាយ<br>Unit Price</th>
          <th class="text-right" style="width:15%;">តម្លែសរុប<br>Amount</th>
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
              {{ $item->product->name }}, លេខកូដ {{ $item->product->code }}
            </td>
            <td class="text-center">{{ number_format($item->quantity) }}</td>
            <td class="text-right">{{ decimalNumber($item->unit_price) }}</td>
            <td class="text-right">{{ decimalNumber($item->unit_price) }}</td>
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
          <td rowspan="{{ 3 }}" colspan="2" class="border-0">
            <b>ចំណាំ៖</b> 
            {!! $sale->warehouse->invoice_footer_text !!}
          </td>

          <td colspan="2" class="text-right">ប្រាក់សរុប / Subtotal</td>
          <td class="text-right" style="background: #EEE;" >$ {{ decimalNumber($sale->final_total) }}</td>
        </tr>
        @if($sale->type != 'leasing')
          <tr>
            <td colspan="2" class="text-right">ប្រាក់កក់ / Deposit</td>
            <td class="text-right" style="background: #EEE;">$ {{ decimalNumber($sale->depreciation_amount) }}</td>
          </tr>
          <tr>
            <td colspan="2" class="text-right">ប្រាក់នៅសល់ / Balance</td>
            <td class="text-right" style="background: #EEE;">$ {{ decimalNumber($sale->remaining_amount) }}</td>
          </tr>
        @else
          <tr>
            <td colspan="2" class="text-right">តម្លែសេវាផ្សេងៗ / Others</td>
            <td class="text-right" style="background: #EEE;">$ {{ decimalNumber($sale->warehouse->others_charges) }}</td>
          </tr>
          <tr>
            <td colspan="2" class="text-right">ប្រាក់សរុបទាំងអស់ / Total</td>
            <td class="text-right" style="background: #EEE;">$ {{ decimalNumber($sale->final_total + $sale->warehouse->others_charges) }}</td>
          </tr>
        @endif
      </tfoot>
    </table>
    {{-- <p class="text-right" style="font-size: 12px;">(1 ដុល្លា = 4100 រៀល)</p> --}}
  </div>

  <div class="footer">
    <p class="text-center" style="">
      អ្នកលក់ / <span>buyer</span>
    </p>
    <p class="text-center" style="">
      អ្នកទិញ / <span>seller</span>
    </p>
  </div>

  <div class="row hidden-print">
    <div class="col-md-12" style="margin-bottom:50px;">
      <a href="{{ route('sale.index') }}" class="btn btn-block btn-warning" title="{{trans('app.back')}}"><i class="fa fa-return"></i> {{trans('app.back')}}</a>
    </div>
  </div>
@endsection
