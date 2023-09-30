@if (isset($product->photo))
    <img src="{{ asset($product->photo) }}" alt="{{ trans('app.missing_image') }}"
         class="img-thumbnail" width="50">
@else
    {{ trans('app.none') }}
@endif
