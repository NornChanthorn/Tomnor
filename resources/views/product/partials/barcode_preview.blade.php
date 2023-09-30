<div class="barcode">
  @foreach($products as $product)
    @for($i=1; $i<=$product->quantity; $i++)
      @if(!$request['displayVariantion'])
        @php 
          $productSku = $request['displayVariantion'] ? ($variantion->sub_sku) : ($product->sku ?? $product->code);
        @endphp
        <div class="item">
          <p class="" style="margin-bottom:0.05in;">
            <b class="barcode-name">{{ $request['displayName'] ? $product->name : '' }} {{ $request['displayVariantion'] ? ' - '.$variantion->name : '' }}</b>

            @if($request['displayPrice'])
              <br>
              <span class="barcode-price"><b>{{ __('app.price') }}</b> $ {{ number_format(($request['displayVariantion'] ? $variantion->default_sell_price : $product->price ), 2) }} {{ $request['displayUnit'] ? $product->unit : '' }}</span>
            @endif

            @if($request['displayCategory'])
              <br>
              <span class="barcode-category"><b>{{__('app.category')}}</b> {{ $product->category->name }}</span>
            @endif
          </p>
          <img src="data:image/png;base64,{!! DNS1D::getBarcodePNG($productSku, $product->barcode_type, 1, 46, [1,1,1], true) !!}" alt="">
        </div>
      @else
        @foreach($product->variations as $variantion)
          @php 
            $productSku = $request['displayVariantion'] ? ($variantion->sub_sku) : ($product->sku ?? $product->code);
          @endphp
          <div class="item">
            <p class="" style="margin-bottom:0.05in;">
              <b class="barcode-name">{{ $request['displayName'] ? $product->name : '' }} {{ $request['displayVariantion'] ? ' - '.$variantion->name : '' }}</b>

              @if($request['displayPrice'])
                <br>
                <span class="barcode-price"><b>{{ __('app.price') }}</b> $ {{ number_format(($request['displayVariantion'] ? $variantion->default_sell_price : $product->price ), 2) }} {{ $request['displayUnit'] ? $product->unit : '' }}</span>
              @endif

              @if($request['displayCategory'])
                <br>
                <span class="barcode-category"><b>{{__('app.category')}}</b> {{ $product->category->name }}</span>
              @endif
            </p>
            <img src="data:image/png;base64,{!! DNS1D::getBarcodePNG($productSku, $product->barcode_type, 1, 46, [1,1,1], true) !!}" alt="">
          </div>
        @endforeach
      @endif
    @endfor
  @endforeach
</div>
 
<style>
  .barcode .item img{
    width: 100%;

  }
  .barcode .item {
    width: {{ $barcode->width}}in;
    height: {{ $barcode->height}}in;
    margin-left:{{ $barcode->left_margin}}in;
    margin-top:{{ $barcode->top_margin}}in;
  }
  @page {
    size: {{$barcode->paper_width}}in @if(!$barcode->is_continuous && $barcode->paper_height != 0){{$barcode->paper_height}}in @endif;
    margin-top: 0in;
    margin-bottom: 0in;
    margin-left: 0in;
    margin-right: 0in;
    
    @if($barcode->is_continuous)
      /*page-break-inside : avoid !important;*/
    @endif
  }
</style>
