<?php $__env->startSection('title', trans('app.invoice')); ?>

<?php $__env->startSection('content'); ?>
<div class="row mb-4">
  <div class="col-12 mb-5">
    <h3 class="text-center invoice-title">
      <?php echo e(__('app.invoice')); ?> - <?php echo e(trans('app.loan')); ?>​
    </h3>
  </div>
  <div class="col-6">
      <p><?php echo e(__('app.customer')); ?> : <span><?php echo e(@$loan->client->name); ?></span></p>
      <?php if(@$loan->client->address): ?>
          <?php echo e(@$loan->client->address); ?>

      <?php else: ?>
        <p><?php echo e(@$loan->client->commune->khmer_name ? __("app.commune").' '.@$loan->client->commune->khmer_name : ''); ?> <?php echo e(@$loan->client->district->khmer_name ? __("app.district").' '.@$loan->client->district->khmer_name : ''); ?> <?php echo e(@$loan->client->province->khmer_name ? __("app.province").' '.@$loan->client->province->khmer_name : ''); ?></span></p>
      <?php endif; ?>
      
      <p>
        <?php echo e(@$loan->client->first_phone.' '.@$loan->client->second_phone); ?>

      </p>
  </div>

  <div class="col-2">

  </div>

  <div class="col-4">
    <p><label><?php echo e(__('app.invoice_number')); ?></label> : <span><?php echo e($sale->invoice_no); ?></span></p>
    <p><label><?php echo e(__('app.date')); ?></label> : <span><?php echo e(khmerDate($loan->approved_date)); ?></span></p>
    <hr>
    <p>
      <b>
        <?php echo e(__('ចំនួនសរុបដែលត្រូវបង់')); ?> : $ <?php echo e(decimalNumber($loan->depreciation_amount,2)); ?>

      </b>
    </p>
  </div>
</div>

    <table class="table-striped mb-4">
      <thead>
        <tr>
          <th class="text-center tbg-header" style="width:5%;">ល.រ<br>No</th>
          <th class="tbg-header" style="width:50%;">ឈ្មោះទំនិញ<br>Name of Goods</th>
          <th class="text-center tbg-header" style="width:10%;">ចំនួន<br>QTY</th>
          <th class="text-center tbg-header" style="width:15%;">តម្លៃរាយ<br>Unit Price</th>
          <th class="text-center tbg-header" style="width:15%;">តម្លែសរុប<br>Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $offset = 1;
        ?>
        <?php $__currentLoopData = $loan->transaction->sell_lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <tr>
            <td class="text-center"><?php echo e($key+1); ?></td>
            <td style="padding: 10px 10px;">
              
              <?php echo e($item->product->name.(empty($item->variations->name)||$item->variations->name=='DUMMY' ? '' : '-'.$item->variations->name)); ?>

                  <br>
                  <?php echo e($item->product->enable_sr_no ? 'IMEI: ' : ""); ?>

                  <?php if($item->product->enable_sr_no==1): ?>
                    <?php if(count($item->transaction_ime)>0): ?>
                        <?php $__currentLoopData = $item->transaction_ime; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!$loop->first): ?>
                                ,
                            <?php endif; ?>
                            <?php echo e($ime->ime->code); ?>

                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>

                <?php endif; ?>

              
              
            </td>
            <td class="text-center"><?php echo e(number_format($item->quantity)); ?></td>
            <td class="text-right"><?php echo e(decimalNumber($item->unit_price)); ?></td>
            <td class="text-right"><?php echo e(decimalNumber($item->unit_price*$item->quantity)); ?></td>
          </tr>

          <?php
            $offset++;
          ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if($loan->branch->others_charges > 0): ?>
          <?php $offset = 2; ?>
          <tr>
            <td class="text-center"><?php echo e(2); ?></td>
            <td style="padding: 10px 10px;"><?php echo e(__('app.document')); ?></td>
            <td class="text-center"><?php echo e(number_format(1)); ?></td>
            <td class="text-right"><?php echo e(decimalNumber($loan->branch->others_charges)); ?></td>
            <td class="text-right"><?php echo e(decimalNumber($loan->branch->others_charges)); ?></td>
          </tr>
        <?php endif; ?>

        <?php for($j=$offset; $j<6; $j++): ?>
          <tr>
            <td class="text-center"><?php echo e($j); ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        <?php endfor; ?>
      </tbody>
      <tfoot>
        <tr>
          <td rowspan="<?php echo e(5); ?>" colspan="2" class="border-0" style="border: 0px solid #ddd;">
            <div class="m-4">
              <?php echo @$sale->warehouse->invoice_footer_text; ?>

            </div>
            
          </td>

          <td colspan="2" class="text-right tbg-total">ប្រាក់សរុប / Subtotal</td>
          <td class="text-right tbg-total" >$ <?php echo e(decimalNumber($loan->sub_total,2)); ?></td>
        </tr>
        <tr>
          <td colspan="2" class="text-right ">ប្រាក់កក់ / Deposit</td>
          <td class="text-right">$ <?php echo e(decimalNumber($loan->depreciation_amount,2)); ?></td>
        </tr>
        <tr>
          <td colspan="2" class="text-right ">ប្រាក់នៅសល់ / Balance</td>
          <td class="text-right">$ <?php echo e(decimalNumber($loan->balance,2)); ?></td>
        </tr>
      </tfoot>
    </table>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/contract-invoice', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>