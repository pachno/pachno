<?php

    use pachno\core\framework\Context;
    use pachno\core\framework\Response;
    use pachno\core\framework\Settings;
    use pachno\core\entities\Module;

    /**
     * @var Response $pachno_response
     * @var Module[][] $modules
     * @var Module[] $uninstalled_modules
     * @var bool $is_default_scope
     * @var bool $writable
     * @var bool $can_install_modules
     */

    $pachno_response->setTitle(__('Configure modules'));

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => Settings::CONFIGURATION_SECTION_MODULES]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
            <h1><?= __('Configure modules'); ?></h1>
            <div class="helper-text">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_configuration_modules_icon.png', [], true); ?></div>
                <?php if (\pachno\core\framework\Context::getScope()->isDefault()): ?>
                    <span class="description"><?= __('Manage existing modules or download and install new modules for Pachno here.'); ?></span>
                <?php else: ?>
                    <span class="description"><?= __('Enable or disable modules for Pachno here.'); ?></span>
                <?php endif; ?>
            </div>
            <?php if ($module_error !== null): ?>
                <div class="message-box type-error" id="module_error">
                    <span class="message"><?= fa_image_tag('times') . $module_error; ?></span>
                </div>
            <?php endif; ?>
            <?php if ($module_message !== null): ?>
                <div class="message-box type-info" id="module_message">
                    <?= fa_image_tag('exclamation-circle'); ?>
                    <span class="message">
                        <span><?= $module_message; ?></span>
                    </span>
                </div>
            <?php endif; ?>
            <?php if ($is_default_scope): ?>
                <?php if (!$can_install_modules): ?>
                    <div class="message-box type-warning" id="module_installation_not_possible">
                        <?= fa_image_tag('exclamation-circle'); ?>
                        <span class="message"><?= __('Automatic installation of modules is not possible. Please make sure that both the modules/ path and composer.json is writable.'); ?></span>
                    </div>
                <?php endif; ?>
                <?php if (count($outdated_modules) > 0): ?>
                    <div class="message-box type-warning" id="outdated_module_message">
                        <span class="message">
                            <?= fa_image_tag('exclamation-circle'); ?>
                            <?php if ($is_default_scope): ?>
                                <?= __('You have %count outdated modules. They have been disabled until you upgrade them, you can upgrade them on this page.', array('%count' => count($outdated_modules))); ?>
                            <?php else: ?>
                                <?= __('You have %count outdated modules. They have been disabled until they are updated by an administrator.', array('%count' => count($outdated_modules))); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                <?php endif; ?>
                <h3>
                    <span><?= __('Featured modules'); ?></span>
                    <span class="button-group">
                        <a class="button secondary" href="https://pach.no/modules" target="_blank">
                            <?= fa_image_tag('globe', ['class' => 'icon']); ?>
                            <span><?= __('Find modules online'); ?></span>
                        </a>
                    </span>
                </h3>
                <div id="available_modules_container" class="available_plugins_container plugins-list"><div class="indicator"><?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?></div></div>
            <?php endif; ?>
            <h3><?= __('Manage existing modules'); ?></h3>
            <div class="flexible-table" id="modules-list">
                <div class="row header">
                    <div class="column header info-icons">&nbsp;</div>
                    <div class="column header name-container"><?= __('Module'); ?></div>
                    <div class="column header"><?= __('Status'); ?></div>
                    <div class="column header actions"></div>
                </div>
                <?php foreach ($modules[Context::EXTERNAL_MODULES] as $module_key => $module): ?>
                    <?php include_component('modulebox', array('module' => $module, 'is_default_scope' => $is_default_scope)); ?>
                <?php endforeach; ?>
                <?php if (!count($modules[Context::EXTERNAL_MODULES])): ?>
                    <?php if (\pachno\core\framework\Context::getScope()->isDefault()): ?>
                        <?php if (!count($uninstalled_modules)): ?>
                            <div class="onboarding large">
                                <div class="image-container"><?= image_tag('/unthemed/onboarding_configure_modules_none.png', [], true); ?></div>
                                <div class="helper-text">
                                    <span><?= __('Modules enhance and extend the functionality in Pachno.'); ?></span>
                                    <span><a class="button primary" href="https://pach.no/modules" target="_blank"><?= __('Find modules online'); ?></a></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="onboarding large">
                            <div class="image-container"><?= image_tag('/unthemed/onboarding_configure_modules_none.png', [], true); ?></div>
                            <div class="helper-text">
                                <span><?= __('Available modules will be listed here.'); ?></span>
                                <span><a class="button primary" href="https://pach.no/modules" target="_blank"><?= __('Find modules online'); ?></a></span>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (\pachno\core\framework\Context::getScope()->isDefault()): ?>
                    <?php foreach ($uninstalled_modules as $module_key => $module): ?>
                        <?php include_component('modulebox', array('module' => $module, 'is_default_scope' => $is_default_scope)); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
