<?php

    use pachno\core\entities\Module;
    use pachno\core\framework\interfaces\ModuleInterface;
    use pachno\core\framework\Context;

    /**
     * @var Module $module
     */

?>
<div class="row module <?php if (!$module->isEnabled()) echo ' disabled'; ?>" id="module_<?php echo $module->getName(); ?>" data-module-key="<?php echo $module->getName(); ?>" data-version="<?php echo $module->getVersion(); ?>">
    <div class="column info-icons centered">
        <?php if (Context::isModuleLoaded($module->getName()) && $module->getID()): ?>
            <?php if (Context::getScope()->isDefault()): ?>
                <input type="checkbox" class="fancy-checkbox" name="enabled" data-interactive-toggle value="1" id="toggle_enable_module_<?= $module->getId(); ?>_input" data-url="<?= make_url('userdata'); ?>?say=toggle-module&module_key=<?= $module->getName(); ?>" <?php if (Context::getModule($module->getName())->isEnabled()) echo ' checked'; ?>><label class="button secondary icon" for="toggle_enable_module_<?= $module->getId(); ?>_input"><?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?></label>
            <?php else: ?>
                <?= fa_image_tag('puzzle-piece', ['class' => 'icon']); ?>
            <?php endif; ?>
        <?php else: ?>
            <?= fa_image_tag('certificate', ['class' => 'icon']); ?>
        <?php endif; ?>
    </div>
    <div class="column name-container multiline">
        <div class="title"><?= $module->getLongName(); ?></div>
        <div class="description"><?php echo __($module->getDescription()); ?></div>
    </div>
    <div class="column">
        <?php if ($module->getID()): ?>
            <div class="status-badge module_status can-update outdated hidden">
                <?php echo __('Update available'); ?>
            </div>
        <?php else: ?>
            <div class="status-badge module_status plugin_status outdated">
                <?php echo __('Not installed'); ?>
            </div>
        <?php endif; ?>
        <?php if ($module->getType() == ModuleInterface::MODULE_AUTH): ?>
            <div class="status-badge authentication-module">
                <?php echo image_tag('cfg_icon_authentication.png') . __('Authentication module'); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="column actions">
        <?php if (Context::getScope()->isDefault()): ?>
            <?php if ($module->getID()): ?>
                <?php if ($module->isOutdated()): ?>
                    <button class="list-item trigger-install-module" data-key="<?= $module->getName(); ?>" data-install-update="1">
                        <?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin icon']); ?>
                        <span class="name"><?= __('Install update'); ?></span>
                    </button>
                <?php else: ?>
                    <div class="dropper-container">
                        <button class="dropper button secondary">
                            <span><?= __('Actions'); ?></span>
                            <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
                        </button>
                        <div class="dropdown-container">
                            <div class="list-mode">
                                <a href="javascript:void(0);" class="list-item trigger-install-module can-update hidden" data-key="<?= $module->getName(); ?>" data-update="1">
                                    <span class="name"><?= __('Update to latest version'); ?></span>
                                </a>
                                <a href="javascript:void(0);" class="list-item can-update hidden" onclick="$('#update_module_help_<?php echo $module->getID(); ?>').toggleClass('hidden');"><span class="name"><?php echo __('Manual update'); ?></span></a>
                                <div class="list-item separator can-update hidden"></div>
                                <?php if ($module->hasConfigSettings()): ?>
                                    <a href="<?php echo make_url('configure_module', ['config_module' => $module->getName()]); ?>" class="list-item"><span class="name"><?= __('Configure module'); ?></span></a>
                                    <div class="list-item separator can-update hidden"></div>
                                <?php endif; ?>
                                <a href="javascript:void(0);" class="list-item danger" onclick="$('#uninstall_module_<?php echo $module->getID(); ?>').toggleClass('hidden');"><span class="name"><?php echo __('Uninstall module'); ?></span></a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <button type="button" class="button secondary highlight trigger-install-module" data-key="<?= $module->getName(); ?>">
                    <?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?>
                    <span><?= __('Install module'); ?></span>
                </button>
            <?php endif; ?>
        <?php else: ?>
            <?php if (!Context::isModuleLoaded($module->getName())): ?>
                <button type="button" class="button primary trigger-install-module" data-key="<?= $module->getName(); ?>">
                    <span class="indicator"><?php echo fa_image_tag('spinner', ['class' => 'fa-spin']); ?></span>
                    <span><?= __('Enable module'); ?></span>
                </button>
            <?php else: ?>
                <a href="javascript:void(0);" class="button danger" onclick="$('#uninstall_module_<?php echo $module->getID(); ?>').toggleClass('hidden');"><span class="name"><?php echo __('Disable module'); ?></span></a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php if ($module->getID()): ?>
        <?php if (Context::getScope()->isDefault()): ?>
            <div id="update_module_help_<?php echo $module->getID(); ?>" class="fullpage_backdrop hidden">
                <div class="fullpage_backdrop_content">
                    <div class="backdrop_box medium">
                        <div class="backdrop_detail_header">
                            <span><?php echo __('Manually update module'); ?></span>
                            <a href="javascript:void(0);" class="closer" onclick="$('#update_module_help_<?php echo $module->getID(); ?>').toggleClass('hidden');"><?php echo fa_image_tag('times'); ?></a>
                        </div>
                        <div class="backdrop_detail_content">
                            <div class="form-container">
                                <div class="form">
                                    <div class="form-row">
                                        <div class="helper-text">
                                            <?php echo __('If a module update is available and the automatic update fails, click the download link below and download the update file. Place the file in the location described below then press the "%update_module" button to update the module.', ['%update_module' => __('Update module')]); ?>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <label><?= __('Update file target location'); ?></label>
                                        <div class="list-mode">
                                            <div class="list-item expandable expanded">
                                                <?= fa_image_tag('folder', ['class' => 'icon']); ?>
                                                <span class="name"><?= PACHNO_CACHE_PATH; ?></span>
                                            </div>
                                            <div class="submenu">
                                                <div class="list-item">
                                                    <?= fa_image_tag('file-archive', ['class' => 'icon']); ?>
                                                    <span class="name"><?= $module->getName(); ?>.zip</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <a class="button secondary highlight" id="module_<?php echo $module->getName(); ?>_download_location" href="#" target="_blank">
                                            <?= fa_image_tag('external-link-alt', ['class' => 'icon']); ?>
                                            <span><?php echo __('Find module update file'); ?></span>
                                        </a>
                                    </div>
                                    <div class="form-row submit-container">
                                        <button type="button" class="button primary trigger-install-module" data-install-update="1">
                                            <?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin icon']); ?>
                                            <span><?php echo __('Update module'); ?></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div id="uninstall_module_<?php echo($module->getID()); ?>" class="fullpage_backdrop hidden">
            <div class="fullpage_backdrop_content">
                <div class="backdrop_box medium">
                    <div class="backdrop_detail_header">
                        <span><?php echo (Context::getScope()->isDefault()) ? __('Really uninstall "%module_name"?', ['%module_name' => $module->getLongname()]) : __('Really disable "%module_name"?', ['%module_name' => $module->getLongname()]); ?></span>
                        <a href="javascript:void(0);" class="closer" onclick="$('#uninstall_module_<?php echo $module->getID(); ?>').toggleClass('hidden');"><?php echo fa_image_tag('times'); ?></a>
                    </div>
                    <div class="backdrop_detail_content">
                        <span class="content">
                            <?php if (Context::getScope()->isDefault()): ?>
                                <?php echo __('Uninstalling this module will permanently prevent users from accessing it or any associated data. If you just want to prevent access to the module temporarily, disable the module instead.'); ?>
                            <?php else: ?>
                                <?php echo __('Disabling this module will permanently prevent users from accessing it or any associated data.'); ?>
                            <?php endif; ?>
                        </span>
                        <div class="backdrop_buttons">
                            <button class="button secondary" onclick="$('#uninstall_module_<?php echo $module->getID(); ?>').toggleClass('hidden');"><?php echo __('No'); ?></button>
                            <button class="button primary trigger-uninstall-module danger" data-key="<?= $module->getName(); ?>">
                                <?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?>
                                <span><?= (Context::getScope()->isDefault()) ? __('Uninstall module') : __('Disable module'); ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
