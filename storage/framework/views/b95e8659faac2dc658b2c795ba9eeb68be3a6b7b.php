
<?php if($loan->type=='cash'): ?>
    <a href="<?php echo e(route('loan-cash.show', $loan)); ?>">
        <?php echo e($loan->account_number); ?> / <?php echo e(str_pad($loan->client_id, 6, '0',STR_PAD_LEFT)); ?>


    </a>
<?php else: ?>
    <a href="<?php echo e(route('loan.show', $loan)); ?>">
        <?php echo e($loan->account_number); ?> / <?php echo e(str_pad($loan->client_id, 6, '0',STR_PAD_LEFT)); ?>


    </a> 
<?php endif; ?>
