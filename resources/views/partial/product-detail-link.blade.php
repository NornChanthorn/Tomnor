<a href="{{ route('product.show',$product) }}">
    {{ (@$product->name).(empty($variantion)||$variantion=='DUMMY' ? '' : '-'.@$variantion) }}
</a>