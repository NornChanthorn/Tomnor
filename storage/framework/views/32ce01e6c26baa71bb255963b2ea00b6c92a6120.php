<?php $__env->startSection('title', trans('app.payment')); ?>

<?php $__env->startSection('content'); ?>
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading"><?php echo e(trans('app.payment')); ?></h3>
      <?php echo $__env->make('partial/flash-message', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
      <div class="d-print-none">
        <div class="card">
          <div class="card-header">
            <form method="get" action="<?php echo e(route('repayment.list')); ?>">
              <div class="row">
                  
                  <?php if(empty(auth()->user()->staff)): ?>
                  <div class="col-md-3">
                    <label for=""><?php echo e(trans('app.branch')); ?></label>
                    <select name="branch" id="branch" class="form-control select2">
                      <option value=""><?php echo e(trans('app.all_branches')); ?></option>
                      <?php $__currentLoopData = allBranches(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($branch->id); ?>" <?php echo e(request('branch') == $branch->id ? 'selected' : ''); ?>><?php echo e($branch->location); ?></option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                  </div>
                  <?php endif; ?>
                  
                  
                  <div class="col-md-3">
                    <label for="start_date"><?php echo e(trans('app.date')); ?></label>
                    <input type="text" name="date" id="date" class="form-control date-picker" readonly placeholder="<?php echo e(trans('app.date_placeholder')); ?>" value="<?php echo e(request('date')); ?>">
                  </div>
                  <div class="col-md-3">
                    <label for="sort"><?php echo e(trans('app.sort')); ?></label>
                    <select name="sort" class="form-control" id="">
                      <option value="asc" <?php echo e(request('sort')== 'asc' ? 'selected' : ''); ?>><?php echo e(trans('app.asc')); ?></option>
                      <option value="desc" <?php echo e(request('sort')== 'desc' ? 'selected' : ''); ?>><?php echo e(trans('app.desc')); ?></option>
                    </select>
                  </div>
                  
                  <div class="col-md-3">
                    <label for=""><?php echo e(trans('app.search_placeholder')); ?></label>
                    <?php echo $__env->make('partial.search-input-group', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  </div>

              </div>
            </form>
          </div>
        </div>
  
        <br>
        <?php echo $__env->make('partial.item-count-label', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
        <button onclick="window.print()" class="btn btn-sm btn-success pull-right mb-1"><?php echo e(trans('app.print')); ?></button>
      </div>

      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th><?php echo e(trans('app.no_sign')); ?></th>
              <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('client_code', trans('app.loan_code')));?></th>
              <th><?php echo e(trans('app.client')); ?></th>
              <th><?php echo e(trans('app.profile_photo')); ?></th>
              <th><?php echo \Kyslik\ColumnSortable\SortableLink::render(array ('client_id', trans('app.client_code')));?></th>
              <th><?php echo e(trans('app.phone')); ?></th>
              <th><?php echo e(trans('app.branch')); ?></th>
              <th><?php echo e(trans('app.next_payment_date')); ?></th>
              <th><?php echo e(trans('app.payment_amount')); ?></th>
              <th><?php echo e(trans('app.count_late_date')); ?></th>
              <th >
                <?php echo e(trans('app.product_ime')); ?>

              </th>
              <th>
                <?php echo e(trans('app.icloud')); ?>

              </th>
              <th style="width: 10%">
                <?php echo e(trans('app.note')); ?>

              </th>
    

            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php 
                $amountToPay = $loan->total_amount - $loan->total_paid_amount; 
                $count_late_date = date_diff(date_create($loan->schedules[0]->payment_date), date_create(now()))->format('%a')
              ?>
                <tr>
                  <td>
                    <?php echo e(no_f($offset++)); ?></td>
                  <td>
                    <?php echo e($loan->account_number); ?>

                  </td>
                  <td><?php echo $__env->make('partial.client-detail-link', ['client' => $loan->client], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
                  <td><?php echo $__env->make('partial.client-profile-photo', ['client' => $loan->client], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?></td>
                  <td>
                    <?php echo $__env->make('partial.loan-detail-link', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>
                  </td>
                  <td><?php echo e($loan->client->first_phone); ?> <?php echo e($loan->client->second_phone ? ', '.$loan->client->second_phone : ""); ?></td>
                  <td><?php echo e($loan->branch->location ?? trans('app.n/a')); ?></td>
                  <td><?php echo e(displayDate($loan->payment_date)); ?></td>
                  <td><b><?php echo e($amountToPay ? '$ '. decimalNumber($amountToPay, true) : ''); ?></b></td>
                  <td>
                    <b>
                      <?php echo e($loan->late_payment); ?>

                    </b>
                  </td>
                  <td>
                    <?php $__currentLoopData = $loan->transaction->sell_lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php if(@$item->product): ?>
                        <?php echo $__env->make('partial.product-detail-link', ['product' => @$item->product, 'variantion' => @$item->variantion->name], \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?><br>
                      <?php endif; ?>
                      <b><?php echo e(trans('app.quantity')); ?>:<?php echo e($item->quantity); ?></b>, <b>IME:</b>
                      <?php if(@$item->transaction->transaction_ime): ?>
                        <?php $__currentLoopData = $item->transaction->transaction_ime; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php if(!$loop->first): ?>
                              ,
                          <?php endif; ?>
                          <?php echo e($ime->ime->code); ?>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php else: ?>
                          <?php echo e(trans('app.n/a')); ?>

                      <?php endif; ?>
                        
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </td>
                  <td>
                    <?php echo e($loan->note ?? trans('app.n/a')); ?>

                  </td>
                  <td>
                    
                  </td>
                </tr>
            
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>

        
      </div>
    </div>
  </main>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
  <script src="<?php echo e(asset('js/select2.min.js')); ?>"></script>
  <script src="<?php echo e(asset('js/select-box.js')); ?>"></script>
  <script src="<?php echo e(asset('js/agent-retrieval.js')); ?>"></script>
  <script src="<?php echo e(asset('js/date-time-picker.js')); ?>"></script>
  <script>
      var agentSelectLabel = '<option value=""><?php echo e(trans('app.agent')); ?>';
      var agentRetrievalUrl = '<?php echo e(route('staff.get_agents', ':branchId')); ?>';
    $(document).ready(function() {
      $(".date-picker").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        orientation: 'bottom right'
      });
    });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/backend', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>