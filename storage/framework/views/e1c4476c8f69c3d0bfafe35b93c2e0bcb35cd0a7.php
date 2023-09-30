<?php $__env->startSection('title', trans('app.user')); ?>

<?php $__env->startSection('content'); ?> 
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(trans('app.user')); ?></h3>
      <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      <div class="card">
        <div class="card-header">
          <form method="get" action="<?php echo e(route('user.index')); ?>">
            <div class="row">
              <div class="col-lg-4">
                <?php echo $__env->make('partial/anchor-create', [
                  'href' => route('user.create')
                ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
              </div>
              <div class="col-lg-8">
                <div class="row">
                  <?php echo $__env->make('partial.search-input-group', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
      <br>

      <?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th><?php echo e(trans('app.no_sign')); ?></th>
              <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('name', trans('app.staff')));?></td>
              <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('username', trans('app.username')));?></td>
              <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('role', trans('app.role')));?></td>
              <td><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('active', trans('app.status')));?></td>
              <th><?php echo e(trans('app.action')); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e($offset++); ?></td>
                <td>
                  <?php if(!empty($user->staff)): ?>
                    <a href="<?php echo e(route('staff.show', $user->staff->id)); ?>"><?php echo e($user->staff->name); ?></a>
                  <?php else: ?>
                    <?php if($user->id === 2): ?>
                      System Administrator
                    <?php else: ?>
                      System user
                    <?php endif; ?>
                    
                  <?php endif; ?>
                </td>
                <td><?php echo e($user->username); ?></td>
                <td><?php echo e($user->roles[0]->display_name); ?></td>
                <td class="text-center">
                  <?php if($user->active): ?>
                    <label class="badge badge-success"><?php echo e(trans('app.active')); ?></label>
                  <?php else: ?>
                    <label class="badge badge-danger"><?php echo e(trans('app.inactive')); ?></label>
                  <?php endif; ?>
                </td>
                <td>
                  <?php echo $__env->make('partial.anchor-edit', [
                    'href' => route('user.edit', $user->id),
                  ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  <?php if(2 != $user->id): ?>
                    <?php if(!empty($user->staff)): ?>
                      <?php echo $__env->make('partial.button-jv-delete', [
                        'url' => route('staff.destroy', $user->staff->id)
                      ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <?php else: ?>
                      <?php echo $__env->make('partial.button-jv-delete', [
                        'url' => route('user.destroy', $user->id)
                      ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
        <?php echo $users->appends(Request::except('page'))->render(); ?>

      </div>
    </div>
  </main>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  
  <script type="text/javascript">
    function deleteAction(argument) {
      console.log($(this).attr("data-url"));
    }

    $(document).ready(function() {
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });

      // let root_url= window.location.href;
      // $(".btn-delete").click(function(evt) {
      //   evt.preventDefault();
      //   let url = $(this).data('url');
      //   Swal.fire({
      //     title: 'Are you sure?',
      //     text: 'You will not be able to recover this record!',
      //     type: 'warning',
      //     showCancelButton: true,
      //     confirmButtonText: 'Yes, delete it!',
      //     cancelButtonText: 'No, keep it'
      //   }).then((result) => {
      //     if (result.value) {
      //       axios.delete(url).then(res => {
      //         if(200 ===res.status){
      //           window.location.href=root_url;
      //         }
      //       }).catch(err => {
      //         console.log(err);
      //       });
      //     }
      //   });
      // });
    });
  </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>