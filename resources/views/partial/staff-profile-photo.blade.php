@if (isset($staff->profile_photo))
    <img src="{{ asset($staff->profile_photo) }}" alt="{{ trans('app.missing_image') }}"
         class="img-thumbnail" width="50">
@else
    {{ trans('app.none') }}
@endif
