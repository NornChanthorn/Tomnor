@if (isset($transfer->document))
    <a href="{{ asset($transfer->document) }}" class="btn btn-info btn-sm" target="_blank">
        {{ trans('app.view') }}
    </a>
@else
    {{ trans('app.none') }}
@endif
