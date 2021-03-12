<?php if (!count($modules)): ?>
    <div class="faded_out"><?php echo __('No modules found'); ?></div>
<?php else: ?>
    <div class="modules-list plugins-list">
        <?php foreach ($modules as $onlinemodule): ?>
            <?php include_component('configuration/onlinemodule', compact('onlinemodule')); ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
