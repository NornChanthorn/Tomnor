<ul class="nav nav-tabs mb-4">
  <li class="nav-item">
    <a class="nav-link {{ Route::current()->uri()=='product' ? 'active' : '' }}" href="{{ route('product.index') }}" title="" style="font-size:16px;"><i class="fa fa-cubes"></i> {{ __('app.all_products') }}</a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ Route::current()->uri()=='product/stock' ? 'active' : '' }}" href="{{ route('product.product_stock') }}" title="" style="font-size:16px;"><i class="fa fa-hourglass-half"></i> {{ __('app.stock_products') }}</a>
  </li>
</ul>