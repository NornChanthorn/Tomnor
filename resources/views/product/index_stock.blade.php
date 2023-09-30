@extends('layouts/backend')

@section('title', trans('app.product'))

@section('content')
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading">{{ trans('app.product') }}</h3>
    @include('partial/flash-message')

    @include('product.partials.search')

    @include('product.partials.tabable_list')

    <div class="row">
      <div class="col-lg-6"></div>
      <div class="col-lg-6 text-right">
        @include('partial.item-count-label')
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead>
          <tr>
            <td> @sortablelink('code', trans('app.product_code'))</td>
            <td> @sortablelink('name', trans('app.product_name'))</td>
            <th class="text-right"> @sortablelink('price', trans('app.price'))</th>
            <th class="text-center">{{ trans('app.current_stock') }}</th>
            <th class="text-center">{{ trans('app.purchased_unit') }}</th>
            <th class="text-center">{{ trans('app.sold_unit') }}</th>
            <th class="text-center">{{ trans('app.transfered_unit_in') }}</th>
            <th class="text-center">{{ trans('app.transfered_unit_out') }}</th>
            <th class="text-center">{{ trans('app.adjusted_unit') }}</th>
          </tr>
        </thead>
        <tbody>
          @php
            $totalCurrentStock = 0;
            $totalSold = 0;
            $totalPurchased = 0;
            $totalTransfered_in = 0;
            $totalTransfered_out = 0;
            $totalAdjusted = 0;
            $unit = '';
          @endphp
     
          @foreach ($products as $product)
            @php
              $unit = $product->unit;
              $stock = $product->stock;
              $totalCurrentStock += $stock;
              $totalSold += $product->total_sold;
              $totalPurchased += $product->total_purchased;
              $totalTransfered_in += $product->total_transfered_in;
              $totalTransfered_out += $product->total_transfered_out;
              $totalAdjusted += $product->total_adjusted;
            @endphp

          <tr>
            <td>{{ wordwrap(strlen($product->variantion_sku)==0 ? $product->code : $product->variantion_sku, 4, ' ', true) }}</td>
            <td>
              {{ $product->name . ($product->variantion_name!='DUMMY' ? ' - '.$product->variantion_name : '') }}
                <a class="btn btn-sm btn-success btn-modal"  href="#" data-container=".ime-modal"  data-href="{{ route('product.show_ime',['product_id'=>$product->product_id,'variantion_id'=>$product->variantion_id]) }}">
                  {{ trans('app.product_ime') }}
                </a>
  
            </td>
            <td class="text-right">$ {{ $product->unit_price ?? $product->price }}</td>
            <td class="text-center">
              @if (Config::get('app.WRONG_STOCK')==true)
                @if(($product->total_purchased + $product->total_transfered_in) - ($product->total_sold + $product->total_transfered_out + $product->total_adjusted) == $stock)
                  @else

                    @if(!empty(Request::get('location')))
                      <a class="btn btn-sm btn-danger" href="{{ route('updated-qty',[
                        'id'=>$product->product_id,
                        'location_id'=>Request::get('location'),
                        'variantion_id'=>$product->variantion_id,
                        'qty_available'=> ($product->total_purchased + $product->total_transfered_in) - ($product->total_sold + $product->total_transfered_out + $product->total_adjusted) ]) }}">Wrong Stock</a>
                    @endif
                @endif
              @endif
               
            {{ (number_format($stock, 0)).' '.$unit }}</td>
            <td class="text-center">{{ (number_format($product->total_purchased) ?? 0).' '.$unit }}</td>
            <td class="text-center">{{ (number_format($product->total_sold) ?? 0).' '.$unit }}</td>
            <td class="text-center">{{ (number_format($product->total_transfered_in) ?? 0).' '.$unit }}</td>
            <td class="text-center">{{ (number_format($product->total_transfered_out) ?? 0).' '.$unit }}</td>
            <td class="text-center">{{ (number_format($product->total_adjusted) ?? 0).' '.$unit }}</td>
          </tr>
          @endforeach
      
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" class="text-right">{{ __('app.total') }}</td>
            <td class="text-center">{{ $totalCurrentStock.' '.$unit }}</td>
            <td class="text-center">{{ $totalPurchased.' '.$unit }}</td>
            <td class="text-center">{{ $totalSold.' '.$unit }}</td>
            <td class="text-center">{{ $totalTransfered_in.' '.$unit }}</td>
            <td class="text-center">{{ $totalTransfered_out.' '.$unit }}</td>
            <td class="text-center">{{ $totalAdjusted.' '.$unit }}</td>
          </tr>
        </tfoot>
      </table>
      {!! $products->appends(Request::except('page'))->render() !!}
    </div>
  </div>
</main>
<div class="modal fade ime-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>
@endsection

@section('js')
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>
  <script type="text/javascript">
    $(function () {
      $('#location,#prod_variant, #prod_type, #brand').change(function () {
        $(this).parents('form').submit();
      });
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'danger', 'DELETE');
      });
    });
  </script>
@endsection
