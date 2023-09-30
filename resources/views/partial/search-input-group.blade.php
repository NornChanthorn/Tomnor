<div class="input-group">
    <input type="text" name="search" class="form-control" value="{{ request('search') ?? '' }}"
           placeholder="{{ $placeholder ?? trans('app.search_placeholder') }}">
    <button type="submit" class="btn btn-success" title="{{ trans('app.search') }}">
        <i class="fa fa-search"></i>
    </button>
</div>
