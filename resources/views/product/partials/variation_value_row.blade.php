@php
  $variation_name = !empty($variation_name) ? $variation_name : null;
  $variation_value_id = !empty($variation_value_id) ? $variation_value_id : null;

  $name = (empty($row_type) || $row_type == 'add') ? 'product_variation' : 'product_variation_edit';

  $readonly = !empty($variation_value_id) ? 'readonly' : '';
@endphp

@if(!session('business.enable_price_tax')) 
  @php
    $default = 0;
    $class = 'hide';
  @endphp
@else
  @php
    $default = null;
    $class = '';
  @endphp
@endif

<tr class="row_index_{{$value_index}}" data-index="{{$value_index}}">
  <td>
    <input type="text" name="variant[{{$value_index}}][sku]" value="{{ $productCode.'-'.($value_index+1) }}" placeholder="" class="form-control form-control-sm variant-sku" id="variant-sku">
  </td>
  <td>
    <input type="text" name="variant[{{$value_index}}][value]" value="" placeholder="" class="form-control form-control-sm" id="variant-value" required>
  </td>
  <td>
    <input type="text" name="variant[{{$value_index}}][purchase_price]" value="" placeholder="" class="form-control form-control-sm decimal-input variant-purchase-price" id="variant-purchase-price">
  </td>
  <td>
    <input type="text" name="variant[{{$value_index}}][selling_price]" value="" placeholder="" class="form-control form-control-sm decimal-input variant-selling-price" id="variant-selling-price">
  </td>
  <td class="text-center">
    <button type="button" class="btn btn-sm btn-danger remove-variant-row" id="remove-variant-row"><i class="fa fa-minus"></i></button>
    <input type="hidden" class="variant-row-index" value="{{$value_index}}">
  </td>
</tr>
<script>formatNumericFields();</script>