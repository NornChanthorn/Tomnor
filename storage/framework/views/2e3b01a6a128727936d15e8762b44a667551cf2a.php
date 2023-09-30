

<?php if(empty(auth()->user()->staff)): ?>
  <div class="col-sm-6 col-lg-3 pl-1 pr-0">
    <select name="branch" id="branch" class="form-control select2">
      <option value=""><?php echo e(trans('app.branch')); ?></option>
      <?php $__currentLoopData = allBranches(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch') == $branch->id ? 'selected' : ''); ?>><?php echo e($branch->location); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>

  
    
    <div class="col-sm-6 col-lg-2 pl-1 pr-0">
      <select name="agent" id="agent" class="form-control select2">
        <option value=""><?php echo e(trans('app.agent')); ?></option>
        <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($agent->id); ?>" <?php echo e(request('agent') == $agent->id ? 'selected' : ''); ?>>
            <?php echo e($agent->name); ?>

          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>

  
<?php endif; ?>
 
 
<div class="form-group col-sm-3 col-lg-2">
  
  <input type="text" name="date" id="date" class="form-control date-picker" readonly placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(request('date') ? displayDate(request('date')) : ''); ?>">
</div>


<div class="col-lg-3 pl-1">
  <?php echo $__env->make('partial.search-input-group', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
