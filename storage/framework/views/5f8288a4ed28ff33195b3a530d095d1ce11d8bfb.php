<?php $__env->startSection('title', trans('app.customer')); ?>

<?php $__env->startSection('content'); ?>
<main class="app-content">
  <div class="tile">
    <h3 class="page-heading"><?php echo e(trans('app.customer')); ?></h3>
    <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-md-6">
            <a href="javascript::void(0);" class="btn btn-success mb-1 btn-modal" title="<?php echo e(trans('app.create')); ?>" data-href="<?php echo e(route('contact.create', ['type'=>$type])); ?>" data-container=".contact-modal">
              <i class="fa fa-plus-circle pr-1"></i> <?php echo e(trans('app.create')); ?>

            </a>
          </div>
          <div class="col-md-6 text-right">
            <form method="get" action="<?php echo e(route('contact.index', ['type'=>$type])); ?>">
              <?php echo $__env->make('partial.search-input-group', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
            </form>
          </div>
        </div>
      </div>
    </div>
    <br>
    <?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead>
          <tr>
            <th class="text-center"><?php echo e(trans('app.no_sign')); ?></th>
            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('contact_id', trans('app.contact-id')));?></th>
            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('supplier_business_name', trans('app.company')));?></th>
            <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('name', trans('app.name')));?></th>
            <th><?php echo e(trans('app.first_phone')); ?></th>
            <th><?php echo e(trans('app.province')); ?></th>
            <th class="text-right"><?php echo e(trans('app.action')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td class="text-center"><?php echo e($offset++); ?></td>
            <td><?php echo e($customer->contact_id); ?></td>
            <td><?php echo e($customer->supplier_business_name); ?></td>
            <td><?php echo e($customer->name); ?></td>
            <td><?php echo e($customer->mobile); ?></td>
            <td><?php echo e(@$customer->province->khmer_name ?? ''); ?></td>
            <td class="text-right">
              <?php echo $__env->make('partial/anchor-show', ['href' => route('contact.show', $customer->id)], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
              <?php if($customer->is_default == 0): ?>
                <?php if(isAdmin() || Auth::user()->can('supplier.edit')): ?>
                <a href="javascript::void(0);" class="btn btn-sm btn-primary mb-1 btn-modal" title="<?php echo e(trans('app.edit')); ?>" data-href="<?php echo e(route('contact.edit', ['id'=>$customer->id, 'type'=>$customer->type])); ?>" data-container=".contact-modal">
                  <i class="fa fa-edit"></i>
                </a>
                <?php endif; ?>
                <?php if(isAdmin() || Auth::user()->can('supplier.delete')): ?>
                  <?php echo $__env->make('partial/button-delete', ['url' => route('contact.destroy', $customer->id)], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                <?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
      <?php echo $contacts->appends(Request::except('page'))->render(); ?>

    </div>
  </div>
</main>

<div class="modal fade contact-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="gridSystemModalLabel"></div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
  <script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/mask.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>

  <script type="text/javascript">
    var contactExist = "<?php echo e(trans('message.customer_already_exists')); ?>";

    $(document).ready( function() {
      $(".btn-delete").on('click', function() {
        confirmPopup($(this).data('url'), 'error', 'DELETE');
      });

      //On display of add contact modal
      $('.contact-modal').on('shown.bs.modal', function(e) {
        let type = $("select#type").val();
        if(type=="<?php echo e(\App\Constants\ContactType::SUPPLIER); ?>" || type=="<?php echo e(\App\Constants\ContactType::BOTH); ?>") {
          $(".hidden-block").addClass('d-block').removeClass('d-none');
          $("#company").attr('required', true);
        } 
        else {
          $(".hidden-block").addClass('d-none').removeClass('d-block');
          $("#company").attr('required', false);
        }

        $("#type").on('change', function(e) {
          let type = $(this).val();
          if(type=="<?php echo e(\App\Constants\ContactType::SUPPLIER); ?>" || type=="<?php echo e(\App\Constants\ContactType::BOTH); ?>") {
            $(".hidden-block").addClass('d-block').removeClass('d-none');
            $("#company").attr('required', true);
          } 
          else {
            $(".hidden-block").addClass('d-none').removeClass('d-block');
            $("#company").attr('required', false);
          }
        });

        $('form#form-contact').submit(function(e) {
          e.preventDefault();
        }).validate({
          rules: {
            contact_id: {
              remote: {
                url: "<?php echo e(route('contact.check-contact')); ?>",
                type: 'POST',
                data: {
                  type: function() {
                    return $("#type").val();
                  },
                  contact_id: function() {
                    return $('#contact_id').val();
                  },
                  hidden_id: function() {
                    if($('#hidden_id').val()) {
                      return $('#hidden_id').val();
                    } 
                    else {
                      return '';
                    }
                  },
                },
              },
            },
          },
          messages: {
            contact_id: {
              remote: contactExist,
            },
          },
          submitHandler: function(form) {
            e.preventDefault();
            var data = $(form).serialize();
            $(form).find('button[type="submit"]').attr('disabled', true);
            $.ajax({
              method: 'POST',
              url: $(form).attr('action'),
              dataType: 'json',
              data: data,
              success: function(result) {
                if (result.success == true) {
                  window.location.reload();
                } 
                else {
                  // toastr.error(result.msg);
                }
              },
            });
          },
        });
      });
    });
  </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>