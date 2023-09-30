<?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p><strong class="text-danger"><?php echo $error; ?></strong></p>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>

<?php if(session()->has(Message::SUCCESS_KEY)): ?>
    <div class="alert alert-success">
        <p><?php echo session(Message::SUCCESS_KEY); ?></p>
    </div>
<?php endif; ?>

<?php if(session()->has(Message::ERROR_KEY)): ?>
    <div class="alert alert-danger">
        <p><strong class="text-danger"><?php echo session(Message::ERROR_KEY); ?></strong></p>
    </div>
<?php endif; ?>

