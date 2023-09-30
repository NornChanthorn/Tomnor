@extends('layouts.backend')

@section('title', trans('app.stock_transfer'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.stock_transfer') . ' - ' . trans('app.detail') }}</h3>
    @include('partial.flash-message')

    <div class="row">
      <div class="col-lg-6">
        <h5>{{ trans('app.transfer_info') }}</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <tbody>
              <tr>
                <td width="30%">{{ trans('app.transfer_date') }}</td>
                <td>{{ displayDate($transfer->transaction_date) }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.transfer_status') }}</td>
                <td>@include('partial.transfer-status-label')</td>
              </tr>
              <tr>
                <td>{{ trans('app.original_location') }}</td>
                <td>{{ $transfer->location_from }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.target_location') }}</td>
                <td>{{ $transfer->location_to }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.invoice_id') }}</td>
                <td>{{ $transfer->ref_no }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.shipping_cost') }}</td>
                <td>$ {{ decimalNumber($transfer->shipping_charges, true) }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.document') }}</td>
                <td>@include('partial.transfer-doc-view')</td>
              </tr>
              <tr>
                <td>{{ trans('app.note') }}</td>
                <td>{!! $transfer->additional_notes !!}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-lg-6">
        <h5>{{ trans('app.product_table') }}</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center">{{ trans('app.no_sign') }}</th>
                <th class="text-left">{{ trans('app.product') }}</th>
                <th class="text-left">{{ trans('app.cost') }}</th>   
                <th class="text-center">{{ trans('app.quantity') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($transfer->sell_lines as $transferDetail)
              <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-left">
                  @include('partial.product-detail-link', ['product' => $transferDetail->product])
                  {{ $transferDetail->variations->name!='DUMMY' ? ' - '.$transferDetail->variations->name : '' }} {{ $transferDetail->variations->sub_sku ? trans('app.code').': '.$transferDetail->variations->sub_sku : '' }}
                  <a class="btn btn-sm btn-success" href="{{ route('product.ime-create',[
                    'transaction_id'=>$transfer->id,
                    'location_id'=>$transfer->location_id,
                    'product_id'=>$transferDetail->product->id,
                    'variantion_id'=>$transferDetail->variations->id,
                    'qty'=> $transferDetail->quantity,
                    'purchase_sell_id'=>$transferDetail->id,
                    'type'=>'transfer'
                    ]) }}">{{ trans('app.product_ime') }}</a>
                </td>
                <td>$ {{ decimalNUmber($transferDetail->variations->default_purchase_price,2) }}</td>
                <td class="text-center">{{ $transferDetail->quantity }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection
