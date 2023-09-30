<div class="card mb-2">
  <div class="card-header">
    <form method="get" action="">
      <div class="row">
        <div class="col-lg-12 pl-1 pr-0">
          <div class="row">
            @if(Route::current()->uri() == 'product/stock')
              <div class="col-md-3">
                <label for="location">{{ trans('app.warehouse') }}</label>
                <select name="location" id="location" class="form-control select2">
                  <option value="">{{ trans('app.all') }}</option>
                  @foreach($locations as $location)
                    <option value="{{ $location->id }}" {{ request('location') == $location->id ? 'selected' : '' }}>
                      {{ $location->location }}
                    </option>
                  @endforeach
                </select>
              </div>
            @endif

            <div class="col-md-3">
              <label for="">{{ trans('app.product_type') }}</label>
              <select name="type" class="form-control">
                <option value="">{{ trans('app.all') }}</option>
                @foreach(['single', 'variant'] as $productType)
                  <option value="{{$productType}}" {{ $productType==request('type') ? 'selected' : '' }}>{{ ucfirst($productType) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label for="prod_type">{{ trans('app.product_category') }}</label>
              <select name="prod_type" id="prod_type" class="form-control select2">
                <option value="">{{ trans('app.all') }}</option>
                @foreach ($productCategories as $t)
                  <option value="{{ $t->id }}" {{ request('prod_type') == $t->id ? 'selected' : '' }}>
                    {{ $t->value }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label for="brand">{{ trans('app.brand') }}</label>
              <select name="brand" id="brand" class="form-control select2">
                <option value="">{{ trans('app.all') }}</option>
                @foreach ($brands as $brand)
                  <option value="{{ $brand->id }}" {{ request('brand') == $brand->id ? 'selected' : '' }}>
                    {{ $brand->value }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label for="brand">{{ trans('app.name') }}/{{ trans('app.product_code/sku') }}/{{ trans('app.selling_price') }}</label>
              @include('partial.search-input-group')
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>