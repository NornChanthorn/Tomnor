@extends('layouts/contract-invoice')

@section('title', trans('app.invoice'))

@section('content')
<div class="row mb-4">
  <div class="col-12 mb-5">
    <h3 class="text-center invoice-title">
      {{ __('app.invoice') }} - {{ trans('app.loan') }}​
    </h3>
  </div>
  <div class="col-6">
      <p>{{ __('app.customer') }} : <span>{{ @$loan->client->name }}</span></p>
      @if (@$loan->client->address)
          {{ @$loan->client->address }}
      @else
        <p>{{ @$loan->client->commune->khmer_name ? __("app.commune").' '.@$loan->client->commune->khmer_name : '' }} {{ @$loan->client->district->khmer_name ? __("app.district").' '.@$loan->client->district->khmer_name : '' }} {{ @$loan->client->province->khmer_name ? __("app.province").' '.@$loan->client->province->khmer_name : '' }}</span></p>
      @endif
      
      <p>
        {{ @$loan->client->first_phone.' '.@$loan->client->second_phone  }}
      </p>
  </div>

  <div class="col-2">

  </div>

  <div class="col-4">
    <p><label>{{ __('app.invoice_number') }}</label> : <span>{{ $sale->invoice_no }}</span></p>
    <p><label>{{ __('app.date') }}</label> : <span>{{ khmerDate($loan->approved_date) }}</span></p>
    <hr>
    <p>
      <b>
        {{ __('ចំនួនសរុបដែលត្រូវបង់') }} : $ {{ decimalNumber($loan->depreciation_amount,2) }}
      </b>
    </p>
  </div>
</div>

    <table class="table-striped mb-4">
      <thead>
        <tr>
          <th class="text-center tbg-header" style="width:5%;">ល.រ<br>No</th>
          <th class="tbg-header" style="width:50%;">ឈ្មោះទំនិញ<br>Name of Goods</th>
          <th class="text-center tbg-header" style="width:10%;">ចំនួន<br>QTY</th>
          <th class="text-center tbg-header" style="width:15%;">តម្លៃរាយ<br>Unit Price</th>
          <th class="text-center tbg-header" style="width:15%;">តម្លែសរុប<br>Amount</th>
        </tr>
      </thead>
      <tbody>
        @php
          $offset = 1;
        @endphp
        @foreach($loan->transaction->sell_lines as $key => $item)
          <tr>
            <td class="text-center">{{ $key+1 }}</td>
            <td style="padding: 10px 10px;">
              {{--  @foreach ($loan->productDetails as $item)  --}}
              {{ $item->product->name.(empty($item->variations->name)||$item->variations->name=='DUMMY' ? '' : '-'.$item->variations->name) }}
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

              {{--  @endforeach  --}}
              {{-- {{ $loan->product->name.(empty($loan->variantion->name)||$loan->variantion->name=='DUMMY' ? '' : '-'.$loan->variantion->name) }}, លេខកូដ {{ $item->product->code }} --}}
            </td>
            <td class="text-center">{{ number_format($item->quantity) }}</td>
            <td class="text-right">{{ decimalNumber($item->unit_price) }}</td>
            <td class="text-right">{{ decimalNumber($item->unit_price*$item->quantity) }}</td>
          </tr>

          @php
            $offset++;
          @endphp
        @endforeach

        @if($loan->branch->others_charges > 0)
          @php $offset = 2; @endphp
          <tr>
            <td class="text-center">{{ 2 }}</td>
            <td style="padding: 10px 10px;">{{ __('app.document') }}</td>
            <td class="text-center">{{ number_format(1) }}</td>
            <td class="text-right">{{ decimalNumber($loan->branch->others_charges) }}</td>
            <td class="text-right">{{ decimalNumber($loan->branch->others_charges) }}</td>
          </tr>
        @endif

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
          <td rowspan="{{ 5 }}" colspan="2" class="border-0" style="border: 0px solid #ddd;">
            <div class="m-4">
              {!! @$sale->warehouse->invoice_footer_text !!}
            </div>
            
          </td>

          <td colspan="2" class="text-right tbg-total">ប្រាក់សរុប / Subtotal</td>
          <td class="text-right tbg-total" >$ {{ decimalNumber($loan->sub_total,2) }}</td>
        </tr>
        <tr>
          <td colspan="2" class="text-right ">ប្រាក់កក់ / Deposit</td>
          <td class="text-right">$ {{ decimalNumber($loan->depreciation_amount,2) }}</td>
        </tr>
        <tr>
          <td colspan="2" class="text-right ">ប្រាក់នៅសល់ / Balance</td>
          <td class="text-right">$ {{ decimalNumber($loan->balance,2) }}</td>
        </tr>
      </tfoot>
    </table>

@endsection
