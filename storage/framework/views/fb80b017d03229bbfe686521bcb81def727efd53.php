<?php $statusLabel = loanStatuses($loan->status) ?>
<?php switch($loan->status):
    case (LoanStatus::PENDING): ?>
        <label class="badge badge-warning"><?php echo e($statusLabel); ?></label>
        <?php break; ?>
    <?php case (LoanStatus::ACTIVE): ?>
        <label class="badge badge-info"><?php echo e($statusLabel); ?></label>
        <?php break; ?>
    <?php case (LoanStatus::PAID): ?>
        <label class="badge badge-success"><?php echo e($statusLabel); ?></label>
        <?php break; ?>
    <?php case (LoanStatus::REJECTED): ?>
        <label class="badge badge-danger"><?php echo e($statusLabel); ?></label>
        <?php break; ?>
<?php endswitch; ?>
