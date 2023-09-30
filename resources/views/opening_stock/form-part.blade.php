@foreach($locations as $key => $value)
<div class="tile box box-solid">
  <div class="box-header">
    <h5 class="box-title">{{__('app.location')}}: {{ $value }}</h5>
  </div>
  <div class="box-body">
    <div class="row">
      <div class="col-sm-12">
        <table class="table table-bordered add_opening_stock_table">
          <thead>
            <tr class="table-success">
              <th>@lang( 'app.product_name' )</th>
              <th>@lang( 'app.in-stock_quantity' )</th>
              <th>@lang( 'app.cost' )</th>
              @if($enable_expiry == 1 && $product->enable_stock == 1)
                <th>Exp. Date</th>
              @endif
              @if($enable_lot == 1)
                <th>@lang( 'app.lot_number' )</th>
              @endif
              <th class="text-right">{{__('app.sub_total')}}</th>
              <th class="text-center">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            @php
              $subtotal = 0;
            @endphp

            @foreach($product->variations as $variation)
              @if(empty($purchases[$key][$variation->id]))
                @php
                  $purchases[$key][$variation->id][] = [
                    'quantity'          => 0, 
                    'purchase_price'    => $variation->default_purchase_price,
                    'purchase_line_id'  => null,
                    'lot_number'        => null
                  ]
                @endphp
              @endif

              @foreach($purchases[$key][$variation->id] as $sub_key => $var)
                @php
                  $purchase_line_id = $var['purchase_line_id'];
                  $qty = $var['quantity'];
                  $purchase_price = $var['purchase_price'];
                  $row_total = $qty * $purchase_price;
                  $subtotal += $row_total;
                  $lot_number = $var['lot_number'];
                @endphp

                <tr>
                  <td>
                    {{ $product->name }}
                    <br> 
                    <span class="text-muted">{{ $variation->name!="DUMMY" ? $variation->name : '' }}</span>

                    @if(!empty($purchase_line_id))
                      {!! Form::hidden('stocks[' . $key . '][' . $variation->id . '][' . $sub_key . '][purchase_line_id]', $purchase_line_id); !!}
                    @endif
                  </td>

                  <td>
                    <div class="input-group">
                      {!! Form::text('stocks[' . $key . '][' . $variation->id . '][' . $sub_key . '][quantity]', ($qty) , ['class' => 'form-control form-control-sm integer-input purchase_quantity input_quantity', 'required', $qty > 0 ? 'readonly' : '']); !!}
                      <div class="input-group-append">
                        <span class="input-group-text">{{ $product->unit }}</span>
                      </div>
                    </div>
                  </td>

                  <td>
                    <input type="text" name="{{ 'stocks['.$key.']['.$variation->id.']['.$sub_key.'][purchase_price]' }}" value="{{ $purchase_price }}" class="form-control form-control-sm decimal-input unit_price" id="" required>
                  </td>

                  @if($enable_expiry == 1 && $product->enable_stock == 1)
                    <td>
                      {!! Form::text('stocks[' . $key . '][' . $variation->id . '][' . $sub_key . '][exp_date]', !empty($var['exp_date']) ? ($var['exp_date']) : null , ['class' => 'form-control form-control-sm os_exp_date', 'readonly']); !!}
                    </td>
                  @endif

                  @if($enable_lot == 1)
                    <td>
                      {!! Form::text('stocks[' . $key . '][' . $variation->id . '][' . $sub_key . '][lot_number]', $lot_number , ['class' => 'form-control form-control-sm']); !!}
                    </td>
                  @endif

                  <td class="text-right">
                    <span class="row_subtotal_before_tax decimal-display">{{ ($row_total) }}</span>
                  </td>

                  <td class="text-center">
                    @if($loop->index == 0)
                      <button type="button" class="btn btn-primary btn-sm add_stock_row" data-sub-key="{{ count($purchases[$key][$variation->id])}}" data-row-html='<tr>
                        <td>
                          {{ $product->name }}
                          <br>
                          <span class="text-muted">{{ $variation->name!="DUMMY" ? $variation->name : '' }}</span>
                        </td>

                        <td>
                          <input class="form-control form-control-sm input_number purchase_quantity" required="" name="stocks[{{$key}}][{{$variation->id}}][__subkey__][quantity]" type="text" value="0">
                        </td>
                        <td>
                          <input class="form-control form-control-sm input_number unit_price" required="" name="stocks[{{$key}}][{{$variation->id}}][__subkey__][purchase_price]" type="text" value="{{($purchase_price)}}">
                        </td>

                        @if($enable_expiry == 1 && $product->enable_stock == 1)
                          <td>
                            <input class="form-control input-sm os_exp_date" required="" name="stocks[{{$key}}][{{$variation->id}}][__subkey__][exp_date]" type="text" readonly>
                          </td>
                        @endif

                        @if($enable_lot == 1)
                          <td>
                            <input class="form-control input-sm" name="stocks[{{$key}}][{{$variation->id}}][__subkey__][lot_number]" type="text">
                          </td>
                        @endif

                        <td>
                          <span class="row_subtotal_before_tax">0.00</span>
                        </td>
                        <td>&nbsp;</td>
                      </tr>'><i class="fa fa-plus"></i></button>
                    @else
                      &nbsp;
                    @endif
                  </td>
                </tr>
              @endforeach
            @endforeach
          </tbody>

          <tfoot>
            <tr>
              <td class="text-right" colspan="@if($enable_expiry == 1 && $product->enable_stock == 1 && $enable_lot == 1) 5 @elseif(($enable_expiry == 1 && $product->enable_stock == 1) || $enable_lot == 1) @else 4 @endif">
                <strong>@lang( 'app.total_amount' ): </strong> <span id="total_subtotal">{{($subtotal)}}</span>
                <input type="hidden" id="total_subtotal_hidden" value=0>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div> <!--box end-->
@endforeach