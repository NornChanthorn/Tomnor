@if (isset($client->profile_photo))
    <img src="{{ asset($client->profile_photo) }}" onerror="this.src='{{ asset('/user.png') }}';this.onerror='';" alt="{{ trans('app.missing_image') }}"
         class="img-thumbnail" width="50">
@else
    {{ trans('app.none') }}
@endif
