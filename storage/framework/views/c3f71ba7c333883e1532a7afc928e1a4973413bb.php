<div class="tab-pane active table-responsive" role="tabpanel">
    <table class="table table-hover table-bordered">
        <tbody>
            <tr>
                <td>
                    <?php echo e(__('app.loan_transaction_processing_strategy')); ?>

                </td>
                <td>
                    <?php echo e(__('app.principal')); ?>, <?php echo e(__('app.interest')); ?>, <?php echo e(__('app.penalty')); ?>, <?php echo e(__('app.fees_order')); ?>

                </td>
            </tr>
            <tr>
                <td>
                    <?php echo e(__('app.loan_amount')); ?>

                </td>
                <td>
                    <?php echo e(num_f($loan->loan_amount)); ?>

                </td>
            </tr>
            <tr>
                <td>
                    <?php echo e(__('app.installment')); ?>

                </td>
                <td>
                    <?php echo e($loan->installment); ?> <?php echo e(__('app.times')); ?>

                </td>
            </tr>
            <tr>
                <td>
                    <?php echo e(__('app.repayment')); ?>

                </td>
                <td>
                    <?php echo e($loan->payment_per_month); ?> <?php echo e(__('app.month')); ?>

                </td>
            </tr>
            <tr>
                <td>
                    <?php echo e(__('app.interest_methodology')); ?>

                </td>
                <td>
                    <?php echo e(__('app.flat_interest')); ?>

                </td>
            </tr>
            <tr>
                <td>
                    <?php echo e(__('app.interest_rate')); ?>

                </td>
                <td>
                    <?php echo e($loan->interest_rate); ?>% / <?php echo e(__('app.month')); ?>

                </td>
            </tr>
            <tr>
                <td>
                    <?php echo e(__('app.request_loan')); ?>

                </td>
                <td>
                    <?php echo e(@$loan->staff->name); ?>

                </td>
            </tr>
            <tr>
                <td>
                    <?php echo e(__('app.approved_by')); ?>

                </td>
                <td>
                    <?php echo e(@$loan->changedBy->staff->name ?? @$loan->changedBy->name); ?>

                </td>
            </tr>
        </tbody>
    </table>
</div>