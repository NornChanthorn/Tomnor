<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    <?php
      $isFormShowType = ($formType == FormType::SHOW_TYPE);
      $disabledFormType = ($isFormShowType ? 'disabled' : '');

      const supplier_FIELD_TYPE = 'c';
      const SPONSOR_FIELD_TYPE = 's';

      $form_id = isset($quick_add) ? 'quick_add_contact' : 'form-contact';
    ?>

    <form id="<?php echo e($form_id); ?>" method="post" action="<?php echo e(route('contact.save', $contact)); ?>" enctype="multipart/form-data">
      <div class="modal-header">
        <h4 class="modal-title"><?php echo e(trans('app.'.$contact->type) . ' - ' . $title); ?></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>

      <div class="modal-body">
        <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php echo csrf_field(); ?>
        <input type="hidden" name="type"  id="type" value="<?php echo e($contact->type); ?>">
        <input type="hidden" id="form-type" name="form-type" value="<?php echo e($formType); ?>">
        
        <fieldset class="">
          <div class="row">

            
            <?php if($contact->type=='supplier'): ?>
              <?php if(count($groups)>0): ?>
                <div class="col-md-4 form-group">
                    <label for="name" class="control-label">
                      <?php echo e(trans('app.contact_group')); ?> <span class="required">*</span>
                    </label>
                    <select name="contact_group_id" id="contact_group_id" class="form-control select2" required>
                      <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($group->id); ?>" <?php echo e(selectedOption($group->id, old('contact_group_id', $contact->contact_group_id))); ?>>
                          <?php echo e($group->name); ?>

                        </option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
              <?php endif; ?>
            <?php endif; ?>
              
            <?php if(isAdmin()): ?>
              <div class="col-md-4 form-group">
                <label for="name" class="control-label">
                  <?php echo e(trans('app.creator')); ?> <span class="required">*</span>
                </label>
                <select name="created_by" id="created_by" class="form-control select2" required>
                  <?php $__currentLoopData = staffs(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($staff->user_id); ?>" <?php echo e(selectedOption($staff->user_id, old('created_by', $contact->created_by))); ?>>
                      <?php echo e($staff->name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
             
            <?php endif; ?>
             
            <div class="col-md-4 form-group">
              <label for="name" class="control-label">
                <?php echo e(trans('app.contact_group')); ?> <span class="required">*</span>
              </label>
              <select name="contact_group_id" id="contact_group_id" class="form-control select2" required>
                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($group->id); ?>" <?php echo e(selectedOption($group->id, old('contact_group_id', $contact->contact_group_id))); ?>>
                    <?php echo e($group->name); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            
            <div class="col-lg-4 form-group">
              <label for="name" class="control-label">
                <?php echo e(trans('app.name')); ?> <span class="required">*</span>
              </label>
              <input type="text" name="name" id="name" class="form-control" value="<?php echo e($contact->name ?? old('name')); ?>" required <?php echo e($disabledFormType); ?>>
            </div>

            
            <div class="col-lg-4 form-group">
              <label for="contact_id" class="control-label"><?php echo e(trans('app.contact-id')); ?></label>
              <input type="hidden" id="hidden_id" value="<?php echo e($contact->id); ?>">
              <input type="text" name="contact_id" id="contact_id" class="form-control" value="<?php echo e($contact->contact_id ?? old('contact_id')); ?>" <?php echo e($disabledFormType); ?>>
            </div>

            
            <div class="col-lg-4 form-group hidden-block <?php echo e($contact->type==\App\Constants\ContactType::CUSTOMER ? 'd-none' : ''); ?>">
              <label for="company" class="control-label">
                <?php echo e(trans('app.company')); ?>

              </label>
              <input type="text" name="company" id="company" class="form-control" value="<?php echo e($contact->supplier_business_name ?? old('company')); ?>" <?php echo e($disabledFormType); ?>>
            </div>

            
            <div class="col-lg-4 form-group">
              <label for="ref_code" class="control-label"><?php echo e(trans('app.reference_number')); ?></label>
              <input type="text" name="ref_code" id="ref_code" class="form-control" value="<?php echo e($contact->custom_field1 ?? old('ref_code')); ?>" <?php echo e($disabledFormType); ?>>
            </div>

            
            <div class="col-lg-4 form-group">
              <label for="opening_balance" class="control-label"><?php echo e(trans('app.open_balance')); ?></label>
              <input type="text" name="opening_balance" id="opening_balance" class="form-control" value="<?php echo e($opening_balance ?? old('opening_balance')); ?>" <?php echo e($disabledFormType); ?>>
            </div>
          </div>

          <hr>

          <div class="row">
            
            <div class="col-lg-4 form-group">
              <label for="id_card_number" class="control-label">
                <?php echo e(trans('app.id_card_number')); ?>

              </label>
              <input type="text" name="id_card_number" id="id_card_number" class="form-control id-card" value="<?php echo e($contact->custom_field2 ?? old('id_card_number')); ?>" <?php echo e($disabledFormType); ?>>
            </div>

            
            <div class="col-lg-4 form-group">
              <label for="phone" class="control-label">
                <?php echo e(trans('app.first_phone')); ?> <span class="required">*</span>
              </label>
              <input type="text" name="phone" id="phone" class="form-control phone" value="<?php echo e($contact->mobile ?? old('phone')); ?>" required <?php echo e($disabledFormType); ?>>
            </div>

            
            <div class="col-lg-4 form-group">
              <label for="second_phone" class="control-label"><?php echo e(trans('app.second_phone')); ?></label>
              <input type="text" name="second_phone" id="second_phone" class="form-control phone" value="<?php echo e($contact->alternate_number ?? old('second_phone')); ?>" <?php echo e($disabledFormType); ?>>
            </div>
          </div>

          
          <div class="row">
            
            <div class="col-lg-4 form-group">
              <label for="province" class="control-label">
                <?php echo e(trans('app.province')); ?>

              </label>
              <?php if($isFormShowType): ?>
                <input type="text" class="form-control" value="<?php echo e($contact->province->name ?? ''); ?>" disabled>
              <?php else: ?>
                <select name="province" id="province" class="form-control select2" <?php echo e($disabledFormType); ?> data-address-type="<?php echo e(AddressType::PROVINCE); ?>" data-field-type="<?php echo e(supplier_FIELD_TYPE); ?>">
                  <option value=""><?php echo e(trans('app.select_option')); ?></option>
                  <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($province->id); ?>" <?php echo e($province->id == $contact->city ? 'selected' : ''); ?>>
                      <?php echo e($province->khmer_name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              <?php endif; ?>
            </div>

            
            <div class="col-lg-8 form-group">
              <label for="addresses" class="control-label">
                <?php echo e(trans('app.address')); ?>

              </label>
              <input type="text" name="address" id="address" class="form-control" value="<?php echo e($contact->address ?? old('address')); ?>" <?php echo e($disabledFormType); ?>>
            </div>
          </div>
        </fieldset>
      </div>

      <div class="modal-footer">
        <?php if($isFormShowType): ?>
          <?php if(isAdmin() && $contact->is_default == 0): ?>
            <?php echo $__env->make('partial/anchor-edit', ['href' => route('contact.edit', $contact->id)], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
          <?php endif; ?>
        <?php else: ?>
          <?php echo $__env->make('partial/button-save', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
        <?php endif; ?>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo e(trans('app.close')); ?></button>
      </div>
    </form>
  </div>
</div>
