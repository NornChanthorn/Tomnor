@extends('layouts/contract-invoice')

@section('title', trans('app.invoice'))

@section('content')
  <div class="row">
    <div class="col-md-12">
      <div class="" style="padding-bottom:20px;text-align:center;line-height:1.7;">
        <span style="font-size:16px; font-family: 'Moul', cursive; font-weight:normal;">វិក័យប័ត្រ - ទិញ</span><br/>
        ថ្ងៃទី: {{ khmerDate($purchase->transaction_date) }}<br>
      </div>
      <div class="row">
        <div class="col-sm-6">
          <table>
            <tr>
              <td>អ្នកផ្គត់ផ្គង់</td>
              <td width="10%">:</td>
              <td>{{ $purchase->client->name }}</td>
              <td></td>
            </tr>
            <tr>
              <td>លេខទូរស័ព្ទ</td>
              <td width="10%">:</td>
              <td>{{ $purchase->client->first_phone }}
                @isset ($purchase->client->second_phone)) / {{ $purchase->client->second_phone }} @endisset
              </td>
            </tr>
            <tr>
              <td>អាស័យដ្ឋាន</td>
              <td width="10%">:</td>
              <td>{{ $purchase->client->address }}</td>
            </tr>
          </table>
        </div>
        <div class="col-sm-6">
          <table>
            <tbody>
              <tr>
                <td width="">លេខវិក័យប័ត្រ</td>
                <td width="10%">:</td>
                <td>{{ $purchase->ref_no }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="row render" >
    <div class="col-md-12">
      <table>
        <thead>
          <tr style="background: #EEE;">
            <th style="width:65%;">ពណ៌នាអំពីទំនិញ</th>
            <th class="text-center" style="width:10%;">ចំនួន</th>
            <th class="text-right" style="width:10%;">តម្លៃរាយ</th>
            <th class="text-right" style="width:15%;">តម្លែសរុប (ដុល្លា)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($purchase->purchase_lines as $item)
            <tr>
              <td style="padding: 10px 10px;">
                {{ $item->product->name.($item->variations->name != "DUMMY" ? ('-'.$item->variations->name.', លេខកូដ '.$item->variations->sub_sku) : ', លេខកូដ '.$item->product->code) }}
              </td>
              <td class="text-center">{{ number_format($item->quantity) }}</td>
              <td class="text-right">{{ decimalNumber($item->purchase_price) }}</td>
              <td class="text-right">{{ decimalNumber($item->purchase_price * $item->quantity, true) }}</td>
            </tr>
          @endforeach
        </tbody>
        
        <tr>
          <td rowspan="3" class="border-0">
            <b>ចំណាំ៖</b> - សូមពិនិត្យទំនិញមុនចាកចេញ<br>
            <span style="padding-left:35px"> - មិនទទួលខុសត្រូវចំពោះទូរស័ព្ទដែលដាក់កន្លែងមានសីតុណ្ហភាពខ្ពស់ ផ្ទុះសេ ចូលទឹក </span>
            <br>
            <span style="padding-left:35px">និងធ្លាក់បាក់បែក (ទិញរួចមិនអាចប្តូរយកលុយវិញបានទេ)</span>
            <br>
            <span style="padding-left:35px"> - តម្លៃ​សេវា​បន្ថែម : <b> {{ isset($purchase->shipping_charges) ? '$' . decimalNumber($purchase->shipping_charges) : trans('app.none') }}</b></span>
          </td>
          <td colspan="2" class="text-right">ទឹកប្រាក់សរុប</td>
          <td class="text-right" style="background: #EEE;" >{{ decimalNumber($purchase->final_total ?? 0, true) }}</td>
        </tr>
        <tr>
          <td colspan="2" class="text-right">បានបង់ប្រាក់មុន</td>
          <td class="text-right" style="background: #EEE;">{{ decimalNumber($purchase->depreciation_amount ?? 0, true) }}</td>
        </tr>
        <tr>
          <td colspan="2" class="text-right">នៅសល់</td>
          <td class="text-right" style="background: #EEE;">{{ decimalNumber($purchase->remaining_amount ?? 0, true) }}</td>
        </tr>
      </table>
      <p class="text-right" style="font-size: 12px;">(1 ដុល្លា = 4100 រៀល)</p>
    </div>
    <div class="col-md-12">
      <p class="text-center" style="margin: 20px 0 80px 0">
        <span style="padding:20px; padding-right:300px;"> អ្នកលក់ </span>អ្នកទិញ
      </p>
    </div>
  </div>

  <div class="row hidden-print">
    <div class="col-md-12">
      <a href="{{ route('purchase.index') }}" class="btn btn-block btn-warning" title="{{trans('app.back')}}"><i class="fa fa-return"></i> {{trans('app.back')}}</a>
    </div>
  </div>
@endsection
