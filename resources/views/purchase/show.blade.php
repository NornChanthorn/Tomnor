@extends('layouts.backend')

@section('title', trans('app.purchase'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.purchase') . ' - ' . trans('app.detail') }}</h3>
    @include('partial.flash-message')

    <div class="row">
      <div class="col-lg-5">
        <h5>{{ trans('app.purchase_info') }}</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <tbody>
              <tr>
                <td width="30%">{{ trans('app.purchase_date') }}</td>
                <td>{{ displayDate($purchase->transaction_date) }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.purchase_status') }}</td>
                <td>{{ purchaseStatuses($purchase->status) }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.location') }}</td>
                <td>@include('partial.branch-detail-link', ['branch' => $purchase->warehouse])</td>
              </tr>
              <tr>
                <td>{{ trans('app.invoice_id') }}</td>
                <td>{{ $purchase->ref_no }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.supplier') }}</td>
                <td>{{ $purchase->client->contact_id.' - '.$purchase->client->name }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.supplier_phone') }}</td>
                <td>{{ $purchase->client->mobile }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.total_cost') }}</td>
                <td>$ {{ decimalNumber($purchase->final_total, true) }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.discount') }}</td>
                <td>$ {{ decimalNumber($purchase->discount_amount, true) }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.shipping_cost') }}</td>
                <td>$ {{ decimalNumber($purchase->shipping_charges, true) }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.document') }}</td>
                <td>@include('partial.purchase-doc-view')</td>
              </tr>
              <tr>
                <td>{{ trans('app.note') }}</td>
                <td>{!! $purchase->additional_notes !!}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="row">
          <div class="col-sm-6">
            <h5>{{ trans('app.product_table') }}</h5>
          </div>
          <div class="col-sm-6 text-right">
            @include('partial.item-count-label', ['itemCount' => number_format($purchase->purchase_lines->sum('quantity'))])
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center">{{ trans('app.no_sign') }}</th>
                <th>{{ trans('app.product') }}</th>
                <th class="text-center">{{ trans('app.quantity') }}</th>
                <th class="text-right">{{ trans('app.cost') }}</th>
                <th class="text-right">{{ trans('app.sub_total') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($purchase->purchase_lines as $purchaseDetail)
              <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>
                  @if(!empty($purchaseDetail->product))
                    @include('partial.product-detail-link', ['product' => $purchaseDetail->product])
                    {{ $purchaseDetail->variations->name!='DUMMY' ? ' - '.$purchaseDetail->variations->name : '' }}
                    <a class="btn btn-sm btn-success" href="{{ route('product.ime-create',[
                      'transaction_id'=>$purchase->id,
                      'location_id'=>$purchase->location_id,
                      'product_id'=>$purchaseDetail->product->id,
                      'variantion_id'=>$purchaseDetail->variations->id,
                      'qty'=> $purchaseDetail->quantity,
                      'purchase_sell_id'=>$purchaseDetail->id,
                      'type'=>'purchase'
                      ]) }}">{{ trans('app.product_ime') }}</a>
                  @endif
                </td>
                <td class="text-center">{{ $purchaseDetail->quantity }}</td>
                <td class="text-right">$ {{ $purchaseDetail->purchase_price }}</td>
                <td class="text-right">$ {{ decimalNumber($purchaseDetail->purchase_price * $purchaseDetail->quantity, true) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      
      
    </div>
    <div class="row">
      <div class="col">
        <a class="btn btn-sm btn-info float-right mb-2 mr-2" href="{{ route('purchase.index') }}">
          <i class="fa fa-arrow-circle-left" aria-hidden="true"></i>
          {{ trans('app.back') }}
        </a>
      </div>
    </div>
  </div>
  
</main>
@endsection
