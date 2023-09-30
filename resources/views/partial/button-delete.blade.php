<button class="btn btn-danger btn-sm mb-1 btn-delete {{ $class ?? '' }}" {{ $disabled ?? '' }}
        title="{{ trans('app.delete') }}" data-url="{{ $url ?? '' }}">
    <i class="fa fa-trash-o"></i>
</button>
