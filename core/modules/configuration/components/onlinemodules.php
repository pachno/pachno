<?php if (!count($modules)): ?>
    <div class="form-container">
        <div class="form-row">
            <div class="helper-text">
                <div class="image-container"><?= image_tag('/unthemed/configuration_modules.png', [], true); ?></div>
                <span class="description">
                    <?= __('There was an error retrieving featured modules from pachno.com. Local modules can be managed from the list below.'); ?>
                </span>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="modules-list plugins-list">
        <?php foreach ($modules as $onlinemodule): ?>
            <?php include_component('configuration/onlinemodule', compact('onlinemodule')); ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
