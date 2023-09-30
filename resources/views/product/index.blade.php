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
      <div class="col-lg-6">
        @include('partial/anchor-create', ['href' => route('product.create')])
      </div>
      <div class="col-lg-6 text-right">
        @include('partial.item-count-label')
      </div>
    </div>

    <div class="table-responsive resize-w">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>{{ trans('app.no_sign') }}</th>
            <th>{{ trans('app.photo') }}</th>
            <td> @sortablelink('code', trans('app.product_code'))</td>
            <td style="width: 23%"> @sortablelink('name', trans('app.name'))</td>
              <td>{{trans('app.location')}}</td>
            {{-- <td>@sortablelink('sku', trans('app.sku'))</td> --}}
            <td>{{ trans('app.type') }}</td>
            <th>{{ trans('app.product_category') }}</th>
            <th>{{ trans('app.brand') }}</th>
            <td class="text-center">{{ trans('app.quantity') }}</td>
            <th class="text-right">@sortablelink('price', trans('app.price'))</th>
            <th class="text-right">{{ trans('app.action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($products as $product)
          <tr>
            <td>{{ $offset++ }}</td>
            <td>@include('partial.product-photo')</td>
            <td>{{ wordwrap($product->code, 4, ' ', true) }}</td>
            <td>{{ $product->name }}</td>
              <td>
                  <button type="button" class="btn btn-link" data-toggle="tooltip" data-placement="bottom" title="
                    <?php
                        $str = "";
                        $exist = ' ';
                      foreach ($product->variations as $v){
                          foreach($v->variation_location_details as $vlc){
                              if(strpos($exist, strval($vlc->location_id))){
                                  continue;
                              }
                              $str = $str.' '.$vlc->location->location;
                              $exist = $exist.$vlc->location_id.' ';
                          }
                      }
                        echo $str;
                    ?>
                    ">
                      <?php
                      $str = 0;
                      $exist = ' ';
                      foreach ($product->variations as $v){
                          foreach($v->variation_location_details as $vlc){
                              if(strpos($exist, strval($vlc->location_id))){
                                  continue;
                              }
                              $str += 1;
                              $exist = $exist.$vlc->location_id.' ';
                          }
                      }
                      echo $str;
                      ?>
                  </button>
              </td>
            {{-- <td>{{ $product->sku }}</td> --}}
            <th>{{ $product->type }}</th>
            <td>{{ $product->category->value ?? trans('app.n/a') }}</td>
            <td>{{ brands($product->brand) }}</td>
            <td class="text-center">{{ $product->getQty($product->id, request('location')) }}</td>
            <td class="text-right">$ {{ $product->prefix_price }}</td>
            <td class="text-right">
              <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                <button class="btn btn-sm btn-primary" type="button"><i class="fa fa-tasks"></i></button>
                <div class="btn-group" role="group">
                  <button class="btn btn-sm btn-primary dropdown-toggle" id="btnGroupDrop1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                  <div class="dropdown-menu dropdown-menu-right">
                    {{-- <a href="{{ route('product.list_warehouse', $product->id) }}" class="btn btn-info btn-sm mb-1">
                      {{ trans('app.stock') }}
                    </a> --}}
                    <a href="{{ route('product.show', $product->id) }}" title="{{ __('app.view_detail') }}" class="dropdown-item"><i class="fa fa-eye"></i> {{ __('app.view_detail') }}</a>

                    @if(Auth()->user()->can('product.edit'))
                      <a href="{{ route('product.edit', $product->id) }}" title="{{ __('app.edit') }}" class="dropdown-item"><i class="fa fa-edit"></i> {{ __('app.edit') }}</a>
                    @endif

                    @if(Auth()->user()->can('product.delete'))
                      <a href="javascript:void(0);" title="{{ __('app.delete') }}" data-url="{{ route('product.destroy', $product->id) }}" class="dropdown-item btn-delete"><i class="fa fa-trash-o"></i> {{ __('app.delete') }}</a>
                    @endif

                    @if($product->variations->count() > 0)
                      <div class="dropdown-divider"></div>
                      <a href="{{ route('opening-stock.add', $product->id) }}" title="{{ __('app.add_edit_opening_stock') }}" class="dropdown-item"><i class="fa fa-database"></i> {{ __('app.add_edit_opening_stock') }}</a>
                    @endif
                  </div>
                </div>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      {!! $products->appends(Request::except('page'))->render() !!}
    </div>
  </div>
</main>
@endsection

@section('js')
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>
  <script type="text/javascript">
    $(function () {
      $('#location, #prod_type, #brand').change(function () {
        $(this).parents('form').submit();
      });
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });
    });
  </script>
@endsection
