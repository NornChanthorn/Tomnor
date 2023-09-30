<?php $__env->startSection('title', trans('app.staff')); ?>

<?php $__env->startSection('css'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap4-datetimepicker.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-fileinput.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php
      $isFormShowType = ($formType == FormType::SHOW_TYPE);
      $disabledFormType = ($isFormShowType ? 'disabled' : '');
    ?>

    <main class="app-content">
        <div class="tile">
            <h3 class="page-heading"><?php echo e(trans('app.staff') . ' - ' . $title); ?></h3>
            <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>

            <form id="form-staff" method="post" action="<?php echo e(route('staff.save', $staff)); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="form_type" value="<?php echo e($formType); ?>">

                
                <div class="row">
                    <fieldset class="col-lg-12">
                        
                        <div class="row">
                            <div class="col-6">
                                <h5><?php echo e(trans('app.personal_information')); ?></h5>
                            </div>

                            
                            <div class="col-6 text-right">
                                <?php if($isFormShowType): ?>
                                    <?php echo $__env->make('partial/anchor-edit', ['href' => route('staff.edit', $staff->id)], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                <?php else: ?>
                                    <?php echo $__env->make('partial/button-save', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            
                            <div class="col-lg-6 form-group">
                                <label for="name" class="control-label">
                                    <?php echo e(trans('app.name')); ?> <span class="required">*</span>
                                </label>
                                <input type="text" name="name" id="name" class="form-control" required value="<?php echo e($staff->name ?? old('name')); ?>" <?php echo e($disabledFormType); ?>>
                            </div>

                            
                            <div class="col-lg-6 form-group">
                                <label for="gender" class="control-label">
                                    <?php echo e(trans('app.gender')); ?> <span class="required">*</span>
                                </label>
                                <select name="gender" id="gender" class="form-control select2 select2-no-search" required <?php echo e($disabledFormType); ?>>
                                    <option value=""><?php echo e(trans('app.select_option')); ?></option>
                                    <?php $__currentLoopData = genders(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genderKey => $genderValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($genderKey); ?>" <?php echo e(selectedOption($genderKey, old('gender'), $staff->gender)); ?>>
                                            <?php echo e($genderValue); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-lg-6 form-group">
                                <label for="date_of_birth" class="control-label"><?php echo e(trans('app.date_of_birth')); ?></label>
                                <input type="text" name="date_of_birth" id="date_of_birth" class="form-control date-picker"
                                       value="<?php echo e(displayDate($staff->date_of_birth) ?? old('date_of_birth')); ?>"
                                       placeholder="<?php echo e(trans('app.date_placeholder')); ?>" <?php echo e($disabledFormType); ?>>
                            </div>

                            
                            <div class="col-lg-6 form-group">
                                <label for="id_card_number" class="control-label"><?php echo e(trans('app.id_card_number')); ?></label>
                                <input type="text" name="id_card_number" id="id_card_number" class="form-control id-card"
                                       value="<?php echo e($staff->id_card_number ?? old('id_card_number')); ?>" <?php echo e($disabledFormType); ?>>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-lg-6 form-group">
                                <label for="first_phone" class="control-label">
                                    <?php echo e(trans('app.first_phone')); ?> <span class="required">*</span>
                                </label>
                                <input type="text" name="first_phone" id="first_phone" class="form-control phone"
                                       value="<?php echo e($staff->first_phone ?? old('first_phone')); ?>" required <?php echo e($disabledFormType); ?>>
                            </div>

                            
                            <div class="col-lg-6 form-group">
                                <label for="second_phone" class="control-label"><?php echo e(trans('app.second_phone')); ?></label>
                                <input type="text" name="second_phone" id="second_phone" class="form-control phone"
                                       value="<?php echo e($staff->second_phone ?? old('second_phone')); ?>" <?php echo e($disabledFormType); ?>>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-lg-6 form-group">
                                <label for="branch" class="control-label">
                                    <?php echo e(trans('app.branch')); ?> <span class="required">*</span>
                                </label>
                                <?php if($isFormShowType): ?>
                                    <input type="text" class="form-control" value="<?php echo e($staff->branch->location ?? ''); ?>" disabled>
                                <?php else: ?>
                                    <select name="branch" id="branch" class="form-control select2" required <?php echo e($disabledFormType); ?>>
                                        <option value=""><?php echo e(trans('app.select_option')); ?></option>
                                        <?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($branch->id); ?>" <?php echo e(selectedOption($branch->id, old('branch'), $staff->branch_id)); ?>>
                                                <?php echo e($branch->location); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                <?php endif; ?>
                            </div>

                            
                            <div class="col-lg-6 form-group">
                                <label for="position" class="control-label">
                                    <?php echo e(trans('app.position')); ?> <span class="required">*</span>
                                </label>
                                <?php if($isFormShowType): ?>
                                    <input type="text" class="form-control" value="<?php echo e(positions($staff->position ?? '')); ?>" disabled>
                                <?php else: ?>
                                    <select name="position" id="position" class="form-control select2" required <?php echo e($disabledFormType); ?>>
                                        <option value=""><?php echo e(trans('app.select_option')); ?></option>
                                        <?php $__currentLoopData = positions(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($position->id); ?>" <?php echo e(selectedOption($position->id, old('position'), $staff->position)); ?>>
                                                <?php echo e($position->value); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-lg-6 form-group">
                                <label for="address" class="control-label">
                                    <?php echo e(trans('app.address')); ?>

                                </label>
                                <textarea name="address" id="address" class="form-control" <?php echo e($disabledFormType); ?>><?php echo e($staff->address ?? old('address')); ?></textarea>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-lg-6 form-group">
                                <label for="profile_photo" class="control-label"><?php echo e(trans('app.profile_photo')); ?></label>
                                <?php if($isFormShowType): ?>
                                    <div class="text-left">
                                        <?php if(isset($staff->profile_photo)): ?>
                                            <img src="<?php echo e(asset($staff->profile_photo)); ?>" alt="" width="100%" class="img-responsive">
                                        <?php else: ?>
                                            <?php echo e(trans('app.no_picture')); ?>

                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <input type="file" name="profile_photo" id="profile_photo" class="form-control" accept=".jpg, .jpeg, .png">
                                <?php endif; ?>
                            </div>

                            
                            <div class="col-lg-6 form-group">
                                <label for="id_card_photo" class="control-label"><?php echo e(trans('app.id_card_photo')); ?></label>
                                <?php if($isFormShowType): ?>
                                    <div class="text-left">
                                        <?php if(isset($staff->id_card_photo)): ?>
                                            <img src="<?php echo e(asset($staff->id_card_photo)); ?>" width="100%" class="img-responsive">
                                        <?php else: ?>
                                            <?php echo e(trans('app.no_picture')); ?>

                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <input type="file" name="id_card_photo" id="id_card_photo" class="form-control" accept=".jpg, .jpeg, .png">
                                <?php endif; ?>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <hr>

                

                <?php if($formType != FormType::CREATE_TYPE): ?>
                    <div class="row">
                        <fieldset class="col-lg-12" id="login_setting_9999999" data-id="<?php echo e($staff->id); ?>" data-status=<?php echo e($staff->user_id); ?>>
                            <legend>
                                <div class="row form-group">                                
                                    <?php echo Form::label(trans('app.login_info'), trans('app.login_info'), ['class'=>'col-md-3']); ?>

                                    <?php echo Form::select('can_login_system', ['yes'=>'Yes','no'=>'No'], 'yes', ['class'=>'form-control col-md-2','id'=>'can_login_system']); ?>                                   
                                   
                                </div>
                            </legend>
                            <div class="row" id="can_login_system_888888">

                                
                                <div class="col-lg-4 form-group">
                                    <label for="username" class="control-label">
                                        <?php echo e(trans('app.username')); ?> <span class="required">*</span>
                                    </label>
                                    <input type="text" name="username" id="username" class="form-control"
                                           value="<?php echo e($staff->user->username ?? old('username')); ?>" required
                                           placeholder="<?php echo e(trans('app.at_least_6_char')); ?>" <?php echo e($disabledFormType); ?>>
                                </div>

                                
                                <div class="col-lg-4 form-group">
                                    <label for="password" class="control-label">
                                        <?php echo e(trans('app.password')); ?>

                                        <?php if($formType == FormType::CREATE_TYPE): ?> <span class="required">*</span> <?php endif; ?>
                                    </label>
                                    <input type="password" name="password" id="password" class="form-control"
                                           placeholder="<?php echo e(trans('app.at_least_6_char')); ?>" <?php echo e($disabledFormType); ?>

                                           <?php if($formType == FormType::CREATE_TYPE): ?> value="amazon" required <?php endif; ?>>
                                </div>

                                
                                <div class="col-lg-4 form-group">
                                    <label for="role" class="control-label">
                                        <?php echo e(trans('app.role')); ?> <span class="required">*</span>
                                    </label>
                                    <?php if($isFormShowType): ?>
                                        <input type="text" class="form-control" value="<?php echo e(count($staff->user->roles)? $staff->user->roles[0]->display_name:''); ?>" disabled>
                                    <?php else: ?>
                                        <select name="role" id="role" class="form-control select2 select2-no-search" required <?php echo e($disabledFormType); ?>>
                                            <option value=""><?php echo e(trans('app.select_option')); ?></option>
                                            <?php echo e($roles); ?>

                                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option 
                                                    value="<?php echo e($role->id); ?>"                                                     
                                                    <?php if(null != $staff->user_id and count($staff->user->roles)): ?>
                                                        <?php echo e(selectedOption($role->id, old('role'), 
                                                        $staff->user->roles[0]->id)); ?>

                                                    <?php else: ?>
                                                        <?php echo e(selectedOption($role->id, old('role'),NULL)); ?>

                                                    <?php endif; ?>
                                                    >
                                                    <?php echo e($role->display_name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                <?php endif; ?>  

                <?php if($formType== FormType::CREATE_TYPE): ?>
                    <div class="row">
                        <fieldset class="col-lg-12" id="login_setting_9999999" data-id="<?php echo e($staff->id); ?>">
                            <legend>
                                <div class="row form-group">                                
                                    <?php echo Form::label(trans('app.login_info'), trans('app.login_info'), ['class'=>'col-md-3']); ?>

                                    <?php echo Form::select('can_login_system', ['yes'=>'Yes','no'=>'No'], 'no', ['class'=>'form-control col-md-2','id'=>'can_login_system']); ?>                                   
                                   
                                </div>
                            </legend>
                            <div class="row" id="can_login_system_888888">

                                
                                <div class="col-lg-4 form-group">
                                    <label for="username" class="control-label">
                                        <?php echo e(trans('app.username')); ?> <span class="required">*</span>
                                    </label>
                                    <input type="text" name="username" id="username" class="form-control"
                                           value="<?php echo e($staff->user->username ?? old('username')); ?>" required
                                           placeholder="<?php echo e(trans('app.at_least_6_char')); ?>" <?php echo e($disabledFormType); ?>>
                                </div>

                                
                                <div class="col-lg-4 form-group">
                                    <label for="password" class="control-label">
                                        <?php echo e(trans('app.password')); ?>

                                        <?php if($formType == FormType::CREATE_TYPE): ?> <span class="required">*</span> <?php endif; ?>
                                    </label>
                                    <input type="password" name="password" id="password" class="form-control"
                                           placeholder="<?php echo e(trans('app.at_least_6_char')); ?>" <?php echo e($disabledFormType); ?>

                                           <?php if($formType == FormType::CREATE_TYPE): ?> value="amazon" required <?php endif; ?>>
                                </div>

                                
                                <div class="col-lg-4 form-group">
                                    <label for="role" class="control-label">
                                        <?php echo e(trans('app.role')); ?> <span class="required">*</span>
                                    </label>
                                    <?php if($isFormShowType): ?>
                                        <input type="text" class="form-control" value="<?php echo e(count($staff->user->roles)? $staff->user->roles[0]->display_name:''); ?>" disabled>
                                    <?php else: ?>
                                        <select name="role" id="role" class="form-control select2 select2-no-search" required <?php echo e($disabledFormType); ?>>
                                            <option value=""><?php echo e(trans('app.select_option')); ?></option>
                                            <?php echo e($roles); ?>

                                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option 
                                                    value="<?php echo e($role->id); ?>" 
                                                    <?php echo e(selectedOption($role->id, old('role'), 
                                                    $staff->exists ? $staff->user->roles[0]->id : NULL)); ?>>
                                                    <?php echo e($role->display_name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                <?php endif; ?>

                
                <div class="row">
                    <div class="col-lg-12 text-right">
                        <?php if($isFormShowType): ?>
                            <?php echo $__env->make('partial/anchor-edit', [
                                'href' => route('staff.edit', $staff->id)
                            ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php else: ?>
                            <?php echo $__env->make('partial/button-save', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('js/bootstrap-fileinput.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-fileinput-fa-theme.js')); ?>"></script>
    <script src="<?php echo e(asset('js/init-file-input.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap4-datetimepicker.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/date-time-picker.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/mask.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery.validate.min.js')); ?>"></script>
    <script>
        $(function() {
            callFileInput('#profile_photo, #id_card_photo', 1, 5120, ['jpg', 'jpeg', 'png']);
            $('#form-staff').validate();
        });
        $(document).ready(function(){
            if($('#login_setting_9999999').data('id') != '' && $('#login_setting_9999999').data('status') != ''){
                $('#can_login_system' ).val('yes');
                $('#can_login_system').attr('disabled',true);
                $('#can_login_system_888888').show();
            }else{
                $('#can_login_system_888888').show();
            }            
            $('#can_login_system').change(function(){
                if($(this).val()=='yes'){
                    $('#can_login_system_888888').show();
                }else{
                    $('#can_login_system_888888').hide();
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>