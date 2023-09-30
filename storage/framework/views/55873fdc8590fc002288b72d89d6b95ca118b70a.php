<?php $__env->startSection('title', trans('app.client')); ?>

<?php $__env->startSection('css'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap4-datetimepicker.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-fileinput.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <?php
    $isFormShowType = ($formType == FormType::SHOW_TYPE);
    $disabledFormType = ($isFormShowType ? 'disabled' : '');

    const CLIENT_FIELD_TYPE = 'c';
    const SPONSOR_FIELD_TYPE = 's';
  ?>

  <main class="app-content">
    <div class="tile">
      <p>It from cliend.form</p>

      <h3 class="page-heading"><?php echo e(trans('app.client') . ' - ' . $title); ?></h3>
      <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      <form id="form-client" method="post" action="<?php echo e(route('client.save', $client)); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        
        <div class="row">
          <fieldset class="col-lg-12">
            
            <div class="row">
              <div class="col-6">
                <h5><?php echo e(trans('app.personal_information')); ?></h5>
              </div>
              <div class="col-6 text-right">
                <?php if($isFormShowType): ?>
                  <?php if(isAdmin() && $client->is_default == 0): ?>
                      <?php echo $__env->make('partial/anchor-edit', [
                        'href' => route('client.edit', $client->id)
                      ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  <?php endif; ?>
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
                <input type="text" name="name" id="name" class="form-control" value="<?php echo e($client->name ?? old('name')); ?>" required <?php echo e($disabledFormType); ?>>
              </div>

              
              <div class="col-lg-6 form-group">
                <label for="gender" class="control-label"><?php echo e(trans('app.gender')); ?></label>
                <select name="gender" id="gender" class="form-control select2 select2-no-search" <?php echo e($disabledFormType); ?>>
                  <option value=""><?php echo e(trans('app.select_option')); ?></option>
                  <?php $__currentLoopData = genders(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genderKey => $genderValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($genderKey); ?>" <?php echo e($client->gender == $genderKey || old('gender') == $genderKey ? 'selected' : ''); ?>>
                      <?php echo e($genderValue); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
            </div>

            <div class="row">
              
              <div class="col-lg-6 form-group">
                <label for="date_of_birth" class="control-label"><?php echo e(trans('app.date_of_birth')); ?></label>
                <input type="text" name="date_of_birth" id="date_of_birth" class="form-control date-picker" value="<?php echo e(displayDate($client->date_of_birth) ?? old('date_of_birth')); ?>" placeholder="<?php echo e(trans('app.date_placeholder')); ?>" <?php echo e($disabledFormType); ?>>
              </div>

              
              <div class="col-lg-6 form-group">
                <label for="id_card_number" class="control-label"><?php echo e(trans('app.id_card_number')); ?> <span class="required">*</span></label>
                <input type="text" name="id_card_number" id="id_card_number" class="form-control id-card" required value="<?php echo e($client->id_card_number ?? old('id_card_number')); ?>" <?php echo e($disabledFormType); ?>>
              </div>
            </div>

            <div class="row">
              
              <div class="col-lg-6 form-group">
                <label for="first_phone" class="control-label"><?php echo e(trans('app.first_phone')); ?> <span class="required">*</span></label>
                <input type="text" name="first_phone" id="first_phone" class="form-control phone" value="<?php echo e($client->first_phone ?? old('first_phone')); ?>" required <?php echo e($disabledFormType); ?>>
              </div>

              
              <div class="col-lg-6 form-group">
                <label for="second_phone" class="control-label"><?php echo e(trans('app.second_phone')); ?></label>
                <input type="text" name="second_phone" id="second_phone" class="form-control phone" value="<?php echo e($client->second_phone ?? old('second_phone')); ?>" <?php echo e($disabledFormType); ?>>
              </div>
            </div>
            
            <div class="row">
              
              <div class="col-lg-6 form-group">
                <label for="facebook_name" class="control-label">Facebook Name</label>
                <input type="text" name="facebook_name" id="facebook_name" class="form-control" value="<?php echo e($client->facebook_name ?? old('facebook_name')); ?>" <?php echo e($disabledFormType); ?>>
              </div>

              
              <div class="col-lg-6 form-group">
                <label for="facebook_url" class="control-label">Facebook Url</label>
                <input type="text" name="facebook_url" id="facebook_url" class="form-control" value="<?php echo e($client->facebook_url ?? old('facebook_url')); ?>" <?php echo e($disabledFormType); ?>>
              </div>
              <div class="col-lg-6 form-group">
                <label for="telegram" class="control-label">Telegram</label>
                <input type="text" name="telegram" id="telegram" class="form-control" value="<?php echo e($client->telegram ?? old('telegram')); ?>" <?php echo e($disabledFormType); ?>>
              </div>
            </div>

            
            <div class="row">
              
              <div class="col-lg-6 form-group">
                <label for="province" class="control-label"><?php echo e(trans('app.province')); ?></label>
                <?php if($isFormShowType): ?>
                  <input type="text" class="form-control" value="<?php echo e($client->province->name ?? ''); ?>" disabled>
                <?php else: ?>
                  <select name="province" id="province" class="form-control select2" <?php echo e($disabledFormType); ?> data-address-type="<?php echo e(AddressType::PROVINCE); ?>" data-field-type="<?php echo e(CLIENT_FIELD_TYPE); ?>">
                    <option value=""><?php echo e(trans('app.select_option')); ?></option>
                    <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($province->id); ?>" <?php echo e($province->id == $client->province_id ? 'selected' : ''); ?>>
                        <?php echo e($province->name); ?>

                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                <?php endif; ?>
              </div>

              
              <div class="col-lg-6 form-group">
                <label for="district" class="control-label"><?php echo e(trans('app.district')); ?></label>
                <?php if($isFormShowType): ?>
                  <input type="text" class="form-control" value="<?php echo e($client->district->name ?? ''); ?>" disabled>
                <?php else: ?>
                  <select name="district" id="district" class="form-control select2" <?php echo e($disabledFormType); ?> data-address-type="<?php echo e(AddressType::DISTRICT); ?>" data-field-type="<?php echo e(CLIENT_FIELD_TYPE); ?>">
                    <option value=""><?php echo e(trans('app.select_option')); ?></option>
                    <?php $__currentLoopData = $districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($district->id); ?>" <?php echo e($district->id == $client->district_id ? 'selected' : ''); ?>>
                        <?php echo e($district->name); ?>

                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                <?php endif; ?>
              </div>

              
              <div class="col-lg-6 form-group">
                <label for="commune" class="control-label"><?php echo e(trans('app.commune')); ?></label>
                <?php if($isFormShowType): ?>
                  <input type="text" class="form-control" value="<?php echo e($client->commune->name ?? ''); ?>" disabled>
                <?php else: ?>
                  <select name="commune" id="commune" class="form-control select2" <?php echo e($disabledFormType); ?> data-address-type="<?php echo e(AddressType::COMMUNE); ?>" data-field-type="<?php echo e(CLIENT_FIELD_TYPE); ?>">
                    <option value=""><?php echo e(trans('app.select_option')); ?></option>
                    <?php $__currentLoopData = $communes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commune): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($commune->id); ?>" <?php echo e($commune->id == $client->commune_id ? 'selected' : ''); ?>>
                        <?php echo e($commune->name); ?>

                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                <?php endif; ?>
              </div>

              
              <div class="col-lg-6 form-group">
                <label for="village" class="control-label"><?php echo e(trans('app.village')); ?></label>
                <?php if($isFormShowType): ?>
                  <input type="text" class="form-control" value="<?php echo e($client->village->name ?? ''); ?>" disabled>
                <?php else: ?>
                  <select name="village" id="village" class="form-control select2" <?php echo e($disabledFormType); ?> data-address-type="<?php echo e(AddressType::VILLAGE); ?>" data-field-type="<?php echo e(CLIENT_FIELD_TYPE); ?>">
                    <option value=""><?php echo e(trans('app.select_option')); ?></option>
                    <?php $__currentLoopData = $villages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $village): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($village->id); ?>" <?php echo e($village->id == $client->village_id ? 'selected' : ''); ?>>
                        <?php echo e($village->name); ?>

                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </select>
                <?php endif; ?>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-6 form-group">
                <label for="address" class="control-label"><?php echo e(trans('app.address')); ?></label>
                <textarea name="address" id="address" class="form-control" <?php echo e($disabledFormType); ?>><?php echo e($client->address ?? old('address')); ?></textarea>
              </div>
            </div>

            <div class="row">
              
              <div class="col-lg-6 form-group">
                <label for="profile_photo" class="control-label"><?php echo e(trans('app.profile_photo')); ?></label>
                <?php if($isFormShowType): ?>
                  <div class="text-left">
                    <?php if(isset($client->profile_photo)): ?>
                      <img src="<?php echo e(asset($client->profile_photo)); ?>" width="100%" class="img-responsive">
                    <?php else: ?>
                      <?php echo e(trans('app.no_picture')); ?>

                    <?php endif; ?>
                  </div>
                <?php else: ?>
                  <input type="file" name="profile_photo" id="profile_photo" class="form-control" accept=".jpg, .jpeg, .png" value="<?php echo e($client->profile_photo ?? old('profile_photo')); ?>">
                <?php endif; ?>
              </div>

              
              <div class="col-lg-6 form-group">
                  <label for="id_card_photo" class="control-label"><?php echo e(trans('app.id_card_photo')); ?></label>
                  <?php if($isFormShowType): ?>
                    <div class="text-left">
                      <?php if(isset($client->id_card_photo)): ?>
                        <img src="<?php echo e(asset($client->id_card_photo)); ?>" width="100%" class="img-responsive">
                      <?php else: ?>
                        <?php echo e(trans('app.no_picture')); ?>

                      <?php endif; ?>
                    </div>
                  <?php else: ?>
                    <input type="file" name="id_card_photo" id="id_card_photo" class="form-control" accept=".jpg, .jpeg, .png" value="<?php echo e($client->id_card_photo ?? old('id_card_photo')); ?>">
                  <?php endif; ?>
              </div>
            </div>

            <div class="row">
              
              <div class="col-lg-6 form-group">
                <label for="occupation_1" class="control-label"><?php echo e(trans('app.occupation_1')); ?> <span class="required">*</span></label>
                <input type="text" name="occupation_1" id="occupation_1" class="form-control" value="<?php echo e($client->occupation_1 ?? old('occupation_1')); ?>" required <?php echo e($disabledFormType); ?>>
              </div>

              
              <div class="col-lg-6 form-group">
                <label for="occupation_2" class="control-label"><?php echo e(trans('app.occupation_2')); ?></label>
                <input type="text" name="occupation_2" id="occupation_2" class="form-control" value="<?php echo e($client->occupation_2 ?? old('occupation_2')); ?>" <?php echo e($disabledFormType); ?>>
              </div>
            </div>
            <div class="row">
              
              <div class="col-lg-6 form-group">
                <label for="related_document_1" class="control-label"><?php echo e(trans('app.related_document_1')); ?></label>
                <?php if($isFormShowType): ?>
                  <div class="text-left">
                    <?php if(isset($client->related_document_1)): ?>
                      <img src="<?php echo e(asset($client->related_document_1)); ?>" width="100%" class="img-responsive">
                    <?php else: ?>
                      <?php echo e(trans('app.no_picture')); ?>

                    <?php endif; ?>
                  </div>
                <?php else: ?>
                  <input type="file" name="related_document_1" id="related_document_1" class="form-control" accept=".jpg, .jpeg, .png" value="<?php echo e($client->related_document_1 ?? old('related_document_1')); ?>">
                <?php endif; ?>
              </div>

              
              <div class="col-lg-6 form-group">
                  <label for="related_document_2" class="control-label"><?php echo e(trans('app.related_document_2')); ?></label>
                  <?php if($isFormShowType): ?>
                    <div class="text-left">
                      <?php if(isset($client->related_document_2)): ?>
                        <img src="<?php echo e(asset($client->related_document_2)); ?>" width="100%" class="img-responsive">
                      <?php else: ?>
                        <?php echo e(trans('app.no_picture')); ?>

                      <?php endif; ?>
                    </div>
                  <?php else: ?>
                    <input type="file" name="related_document_2" id="related_document_2" class="form-control" accept=".jpg, .jpeg, .png" value="<?php echo e($client->related_document_2 ?? old('related_document_2')); ?>">
                  <?php endif; ?>
              </div>
            </div>
          </fieldset>
        </div>
        <hr>

        
        <fieldset class="">
          <legend><h5><?php echo e(trans('app.sponsor_information')); ?></h5></legend>
          <div class="row">
            
            <div class="col-lg-6 form-group">
              <label for="sponsor_name" class="control-label"><?php echo e(trans('app.name')); ?></label>
              <input type="text" name="sponsor_name" id="sponsor_name" class="form-control" value="<?php echo e($client->sponsor_name ?? old('sponsor_name')); ?>" <?php echo e($disabledFormType); ?>>
            </div>

            
            <div class="col-lg-6 form-group">
              <label for="sponsor_gender" class="control-label"><?php echo e(trans('app.gender')); ?></label>
              <select name="sponsor_gender" id="sponsor_gender" class="form-control select2 select2-no-search" <?php echo e($disabledFormType); ?>>
                <option value=""><?php echo e(trans('app.select_option')); ?></option>
                <?php $__currentLoopData = genders(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $genderKey => $genderValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($genderKey); ?>" <?php echo e($client->sponsor_gender == $genderKey || old('sponsor_gender') == $genderKey ? 'selected' : ''); ?>>
                    <?php echo e($genderValue); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
          </div>

          <div class="row">
            
            <div class="col-lg-6 form-group">
              <label for="sponsor_date_of_birth" class="control-label"><?php echo e(trans('app.date_of_birth')); ?></label>
              <input type="text" name="sponsor_date_of_birth" id="sponsor_date_of_birth" class="form-control date-picker" value="<?php echo e(displayDate($client->sponsor_dob) ?? old('sponsor_date_of_birth')); ?>" placeholder="<?php echo e(trans('app.date_placeholder')); ?>" <?php echo e($disabledFormType); ?>>
            </div>

            
            <div class="col-lg-6 form-group">
              <label for="sponsor_id_card_number" class="control-label"><?php echo e(trans('app.id_card_number')); ?></label>
              <input type="text" name="sponsor_id_card_number" id="sponsor_id_card_number" class="form-control id-card" value="<?php echo e($client->sponsor_id_card ?? old('sponsor_id_card_number')); ?>" <?php echo e($disabledFormType); ?>>
            </div>
          </div>

          <div class="row">
            
            <div class="col-lg-6 form-group">
              <label for="sponsor_first_phone" class="control-label"><?php echo e(trans('app.first_phone')); ?></label>
              <input type="text" name="sponsor_first_phone" id="sponsor_first_phone" class="form-control phone" value="<?php echo e($client->sponsor_phone ?? old('sponsor_first_phone')); ?>" <?php echo e($disabledFormType); ?>>
            </div>

            
            <div class="col-lg-6 form-group">
              <label for="sponsor_second_phone" class="control-label"><?php echo e(trans('app.second_phone')); ?></label>
              <input type="text" name="sponsor_second_phone" id="sponsor_second_phone" class="form-control phone" value="<?php echo e($client->sponsor_phone_2 ?? old('sponsor_second_phone')); ?>" <?php echo e($disabledFormType); ?>>
            </div>
          </div>

          
          <div class="row">
            
            <div class="col-lg-3 form-group">
              <label for="sponsor_province" class="control-label"><?php echo e(trans('app.province')); ?></label>
              <?php if($isFormShowType): ?>
                <input type="text" class="form-control" value="<?php echo e($client->sponsorProvince->name ?? ''); ?>" disabled>
              <?php else: ?>
                <select name="sponsor_province" id="sponsor_province" class="form-control select2" <?php echo e($disabledFormType); ?> data-address-type="<?php echo e(AddressType::PROVINCE); ?>" data-field-type="<?php echo e(SPONSOR_FIELD_TYPE); ?>">
                  <option value=""><?php echo e(trans('app.select_option')); ?></option>
                  <?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($province->id); ?>" <?php echo e($province->id == $client->sponsor_province_id ? 'selected' : ''); ?>>
                      <?php echo e($province->name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              <?php endif; ?>
            </div>

            
            <div class="col-lg-3 form-group">
              <label for="sponsor_district" class="control-label"><?php echo e(trans('app.district')); ?></label>
              <?php if($isFormShowType): ?>
                <input type="text" class="form-control" value="<?php echo e($client->sponsorDistrict->name ?? ''); ?>" disabled>
              <?php else: ?>
                <select name="sponsor_district" id="sponsor_district" class="form-control select2" <?php echo e($disabledFormType); ?> data-address-type="<?php echo e(AddressType::DISTRICT); ?>" data-field-type="<?php echo e(SPONSOR_FIELD_TYPE); ?>">
                  <option value=""><?php echo e(trans('app.select_option')); ?></option>
                  <?php $__currentLoopData = $sponsorDistricts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($district->id); ?>" <?php echo e($district->id == $client->sponsor_district_id ? 'selected' : ''); ?>>
                      <?php echo e($district->name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              <?php endif; ?>
            </div>

            
            <div class="col-lg-3 form-group">
              <label for="sponsor_commune" class="control-label"><?php echo e(trans('app.commune')); ?></label>
              <?php if($isFormShowType): ?>
                <input type="text" class="form-control" value="<?php echo e($client->sponsorCommune->name ?? ''); ?>" disabled>
              <?php else: ?>
                <select name="sponsor_commune" id="sponsor_commune" class="form-control select2" <?php echo e($disabledFormType); ?> data-address-type="<?php echo e(AddressType::COMMUNE); ?>" data-field-type="<?php echo e(SPONSOR_FIELD_TYPE); ?>">
                  <option value=""><?php echo e(trans('app.select_option')); ?></option>
                  <?php $__currentLoopData = $sponsorCommunes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $commune): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($commune->id); ?>" <?php echo e($commune->id == $client->sponsor_commune_id ? 'selected' : ''); ?>>
                      <?php echo e($commune->name); ?>

                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              <?php endif; ?>
            </div>

            
            
          </div>

          <div class="row">
            
            <div class="col-lg-6 form-group">
              <label for="sponsor_profile_photo" class="control-label"><?php echo e(trans('app.profile_photo')); ?></label>
              <?php if($isFormShowType): ?>
                <div class="text-left">
                  <?php if(isset($client->sponsor_profile_photo)): ?>
                    <img src="<?php echo e(asset($client->sponsor_profile_photo)); ?>" width="100%" class="img-responsive">
                  <?php else: ?>
                    <?php echo e(trans('app.no_picture')); ?>

                  <?php endif; ?>
                </div>
              <?php else: ?>
                <input type="file" name="sponsor_profile_photo" id="sponsor_profile_photo" class="form-control" value="<?php echo e($client->sponsor_profile_photo ?? old('sponsor_profile_photo')); ?>" accept=".jpg, .jpeg, .png">
              <?php endif; ?>
            </div>

            
            <div class="col-lg-6 form-group">
              <label for="sponsor_id_card_photo" class="control-label"><?php echo e(trans('app.id_card_photo')); ?></label>
              <?php if($isFormShowType): ?>
                <div class="text-left">
                  <?php if(isset($client->sponsor_id_card_photo)): ?>
                    <img src="<?php echo e(asset($client->sponsor_id_card_photo)); ?>" width="100%" class="img-responsive">
                  <?php else: ?>
                    <?php echo e(trans('app.no_picture')); ?>

                  <?php endif; ?>
                </div>
              <?php else: ?>
                <input type="file" name="sponsor_id_card_photo" id="sponsor_id_card_photo" class="form-control" value="<?php echo e($client->sponsor_id_card_photo ?? old('sponsor_id_card_photo')); ?>" accept=".jpg, .jpeg, .png">
              <?php endif; ?>
            </div>
          </div>
        </fieldset>

        
        <div class="row">
          <div class="col-lg-12 text-right">
            <?php if($isFormShowType): ?>
              <?php if(isAdmin() && $client->is_default == 0): ?>
                <?php echo $__env->make('partial/anchor-edit', [
                  'href' => route('client.edit', $client->id)
                ], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
              <?php endif; ?>
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
  <script src="<?php echo e(asset('js/jquery-mask.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/date-time-picker.js')); ?>"></script>
  <script src="<?php echo e(asset('js/mask.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
  <script>
    $(function() {
      callFileInput('#profile_photo, #id_card_photo, #related_document_1, #related_document_2, #sponsor_profile_photo, #sponsor_id_card_photo', 1, 5120, ['jpg', 'jpeg', 'png']);

      $('#form-client').validate();

      var emptyOptionElm = '<option value=""><?php echo e(trans('app.select_option')); ?></option>';
      var provinceAddressType = '<?php echo e(AddressType::PROVINCE); ?>';
      var districtAddressType = '<?php echo e(AddressType::DISTRICT); ?>';
      var addressClientType = '<?php echo e(CLIENT_FIELD_TYPE); ?>';
      var addressSponsorType = '<?php echo e(SPONSOR_FIELD_TYPE); ?>';

      // When change province, district, or commune of client or sponsor
      $('#province, #district, #commune, #sponsor_province, #sponsor_district, #sponsor_commune').change(function () {
        var addressFieldType = $(this).data('field-type');
        if (![addressClientType, addressSponsorType].includes(addressFieldType)) {
          $(this).focus();
          return false;
        }

        var subAddressElm;
        var addressType = $(this).data('address-type');
        if (addressFieldType == addressClientType) {
          subAddressElm = (addressType == provinceAddressType ? $('#district') : (addressType == districtAddressType ? $('#commune') : $('#village')));
        }
        else {
          subAddressElm = (addressType == provinceAddressType ? $('#sponsor_district') : (addressType == districtAddressType ? $('#sponsor_commune') : $('#sponsor_village')));
        }

        if ($(this).val() != '') {
          var getSubAddressesUrl = ('<?php echo e(route('address.get_sub_addresses', ':id')); ?>').replace(':id', $(this).val());
          $.ajax({
            url: getSubAddressesUrl,
            success: function (result) {
              var subAddressData = emptyOptionElm;

              $.each(result.addresses, function (key, value) {
                subAddressData += '<option value="' + value.id + '">' + value.name + '</option>';
              });
              subAddressElm.html(subAddressData).trigger('change');
            }
          });
        }
        else {
          subAddressElm.html(emptyOptionElm).trigger('change');
        }
      });
    });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>