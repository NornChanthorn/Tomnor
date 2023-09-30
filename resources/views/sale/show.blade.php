@extends('layouts.backend')

@section('title', trans('app.sale'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.sale') . ' - ' . trans('app.detail') }}</h3>
    @include('partial.flash-message')

    <div class="row">
      <div class="col-lg-6">
        <h5>{{ trans('app.sale_info') }}</h5>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <tbody>
              <tr>
                <td width="30%">{{ trans('app.sale_date') }}</td>
                <td>{{ displayDate($sale->transaction_date) }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.sale_status') }}</td>
                <td>{{ saleStatuses($sale->status) }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.location') }}</td>
                <td>
                  @if($sale->warehouse)
                    @include('partial.branch-detail-link', ['branch' => $sale->warehouse])
                  @else
                    {{ trans('app.none') }}
                  @endif
                </td>
              </tr>
              <tr>
                <td>{{ trans('app.customer') }}</td>
                <td>
                  @if($sale->client)
                    @include('partial.client-detail-link', ['client' => $sale->client])
                  @else
                    {{ trans('app.none') }}
                  @endif
                </td>
              </tr>
              <tr>
                <td>{{ trans('app.invoice_id') }}</td>
                <td>{{ $sale->invoice_no }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.total_cost') }}</td>
                <td>$ {{ decimalNumber($sale->final_total, true) }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.discount') }}</td>
                <td>$ {{ decimalNumber($sale->discount_amount, true) }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.shipping_cost') }}</td>
                <td>$ {{ decimalNumber($sale->shipping_charges, true) }}</td>
              </tr>
              <tr>
                <td>{{ trans('app.document') }}</td>
                <td>@include('partial.purchase-doc-view', ['transfer' => $sale])</td>
              </tr>
              <tr>
                <td>{{ trans('app.note') }}</td>
                <td>{!! $sale->additional_notes !!}</td>
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
                <th class="text-center">{{ trans('app.quantity') }}</th>
                <th class="text-right">{{ trans('app.cost') }}</th>
                <th class="text-right">{{ trans('app.sub_total') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($sale->sell_lines as $sale_line)
              <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-left">
                  @if(!empty($sale_line->product))
                    @include('partial.product-detail-link', ['product' => $sale_line->product])
                    {{ $sale_line->variations->name!='DUMMY' ? ' - '.$sale_line->variations->name : '' }}
                    <form action="{{ route('product.ime-create') }}" method="get">
                        <input type="hidden" name="transaction_id" value="{{ $sale->id }}">
                        <input type="hidden" name="location_id" value="{{ $sale->location_id }}">
                        <input type="hidden" name="product_id" value="{{ $sale_line->product->id }}">
                        <input type="hidden" name="variantion_id" value="{{ $sale_line->variations->id }}">
                        <input type="hidden" name="qty" value="{{ $sale_line->quantity }}">
                        <input type="hidden" name="purchase_sell_id" value="{{ $sale_line->id }}">
                        <input type="hidden" name="type" value="sale">
                        <button class="btn btn-sm btn-success" type="submit">{{ trans('app.product_ime') }}</button>
                    </form>
                  @endif
                </td>
                <td class="text-center">{{ $sale_line->quantity }}</td>
                <td class="text-right">$ {{ decimalNumber($sale_line->unit_price, true) }}</td>
                <td class="text-right">$ {{ decimalNumber($sale_line->unit_price * $sale_line->quantity, true) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
         
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col">
        @if(count(@$sale->loan)>0)
        <a class="btn btn-sm btn-success float-right mb-2 " href="{{ route('loan.invoice',@$sale->loan->first()->id) }}">
          <i class="fa fa-print"> </i> {{ trans('app.print') }}
        </a>
        <a class="btn btn-sm btn-info float-right mb-2 mr-2" href="{{ route('loan.show', @$sale->loan->first()->id) }}">
          <i class="fa fa-arrow-circle-left" aria-hidden="true"></i>
          {{ trans('app.back') }}
        </a>
      @else
      <a class="btn btn-sm btn-success float-right mb-2" href="{{ route('sale.invoice',$sale->id) }}">
        <i class="fa fa-print"> </i> {{ trans('app.print') }}
      </a>
      <a class="btn btn-sm btn-info float-right mb-2 mr-2" href="{{ route('sale.index') }}">
        <i class="fa fa-arrow-circle-left" aria-hidden="true"></i>
        {{ trans('app.back') }}
      </a>
      @endif
      </div>
    </div>
  </div>
</main>
@endsection
