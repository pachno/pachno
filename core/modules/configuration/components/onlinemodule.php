<div class="configurable-component module <?php if (\pachno\core\framework\Context::isModuleLoaded($onlinemodule->key)) echo ' installed'; ?>" id="online-module-<?php echo $onlinemodule->key; ?>">
    <div class="row">
        <div class="name">
            <div class="title"><?= $onlinemodule->name; ?></div>
            <div class="count-badge"><?= __('v%version_number', ['%version_number' => $onlinemodule->version]); ?></div>
        </div>
    </div>
    <div class="row">
        <div class="rating">
            <div class="score"><?php for ($cc = 1; $cc <= floor($onlinemodule->rating); $cc++) echo fa_image_tag('star'); ?></div>
            <?php for ($cc = floor($onlinemodule->rating) + 1; $cc < 6; $cc++) echo fa_image_tag('star'); ?>
        </div>
    </div>
    <div class="row">
        <div class="description">
            <span><?php echo $onlinemodule->introduction; ?></span>
        </div>
    </div>
    <div class="row actions actions-container">
        <a href="<?php echo $onlinemodule->url; ?>" class="button secondary"><?php echo fa_image_tag('link') . '<span>'.__('Open website').'</span>'; ?></a>
        <button class="install-button button primary" data-key="<?= $onlinemodule->key; ?>"><?= fa_image_tag('download', ['class' => 'icon']); ?><span><?php echo __('Install'); ?></span><?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?></button>
    </div>
</div>
