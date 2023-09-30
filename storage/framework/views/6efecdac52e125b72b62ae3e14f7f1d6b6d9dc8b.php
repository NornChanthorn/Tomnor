<div class="input-group">
    <input type="text" name="search" class="form-control" value="<?php echo e(request('search') ?? ''); ?>"
           placeholder="<?php echo e($placeholder ?? trans('app.search_placeholder')); ?>">
    <button type="submit" class="btn btn-success" title="<?php echo e(trans('app.search')); ?>">
        <i class="fa fa-search"></i>
    </button>
</div>
