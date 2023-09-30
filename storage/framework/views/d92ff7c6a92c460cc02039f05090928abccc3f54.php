<?php switch($purchase->status):
  case (PurchaseStatus::RECEIVED): ?>
    <label class="badge badge-success"><?php echo e(purchaseStatuses($purchase->status)); ?></label>
  <?php break; ?>
  <?php case (PurchaseStatus::ORDERED): ?>
    <label class="badge badge-warning"><?php echo e(purchaseStatuses($purchase->status)); ?></label>
  <?php break; ?>
<?php endswitch; ?>
