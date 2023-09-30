<button type="submit" class="btn btn-success {{ $class ?? '' }}"
    @isset($onClick) onclick="{{ $onClick }}" @endisset>
    <i class="fa fa-save pr-1"></i> {{ trans('app.save') }}
</button>
