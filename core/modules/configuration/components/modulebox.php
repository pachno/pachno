<?php

    use pachno\core\entities\Module;
    use pachno\core\framework\interfaces\ModuleInterface;
    use pachno\core\framework\CoreModule;

    /**
     * @var Module $module
     */

?>
<div class="row module <?php if (!$module instanceof CoreModule && !$module->isEnabled()) echo ' disabled'; ?> <?php if (!$module instanceof CoreModule && $module->isOutdated()) echo ' can-update out-of-date'; ?>" id="module_<?php echo $module->getName(); ?>" data-module-key="<?php echo $module->getName(); ?>" <?php if (!$module instanceof CoreModule): ?>data-version="<?php echo $module->getVersion(); ?>"<?php endif; ?>>
    <div class="column info-icons centered">
        <?php if ($module->getID()): ?>
            <input type="checkbox" class="fancy-checkbox" name="enabled" data-interactive-toggle value="1" id="toggle_enable_module_<?= $module->getId(); ?>_input" data-url="<?= make_url('configure_toggle_disable_module', array('module_key' => $module->getName())); ?>" <?php if ($module->isEnabled()) echo ' checked'; ?>><label class="button secondary icon" for="toggle_enable_module_<?= $module->getId(); ?>_input"><?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?></label>
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
            <div class="status-badge module_status plugin_status outdated">
                <?php echo __('Needs update'); ?>
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
        <?php if ($is_default_scope): ?>
            <?php if (!$module instanceof CoreModule && $module->getID()): ?>
                <div class="dropper-container">
                    <button class="dropper button secondary">
                        <span><?= __('Actions'); ?></span>
                        <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
                    </button>
                    <div class="dropdown-container">
                        <div class="list-mode">
                            <?php if ($module->isOutdated()): ?>
                                <a href="<?php echo make_url('configure_module_update', array('module_key' => $module->getName())); ?>" class="list-item"><span class="name"><?= __('Update to latest version'); ?></span></a>
                            <?php endif; ?>
                            <a href="<?php echo make_url('configure_download_module_update', array('module_key' => $module->getName())); ?>" class="list-item"><span class="name"><?= __('Install latest version'); ?></span></a>
                            <div class="list-item separator"></div>
                            <a href="javascript:void(0);" class="list-item update-module-menu-item"><span class="name"><?php echo __('Manual update'); ?></span></a>
                            <?php if ($module->hasConfigSettings()): ?>
                                <a href="<?php echo make_url('configure_module', array('config_module' => $module->getName())); ?>" class="list-item"><span class="name"><?= __('Configure module'); ?></span></a>
                            <?php endif; ?>
                            <div class="list-item separator"></div>
                            <?php if (!$module instanceof CoreModule): ?>
                                <a href="javascript:void(0);" class="list-item danger" onclick="$('#uninstall_module_<?php echo $module->getID(); ?>').toggle();"><span class="name"><?php echo __('Uninstall module'); ?></span></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php echo link_tag(make_url('configure_install_module', ['module_key' => $module->getName()]), __('Install'), array('class' => 'button primary')); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php if (!$module instanceof CoreModule && $module->getID() && $is_default_scope): ?>
    <div id="update_module_help_<?php echo $module->getID(); ?>" class="fullpage_backdrop" style="display: none;">
        <div class="backdrop_box medium">
            <div class="backdrop_detail_header">
                <span><?php echo __('Install downloaded module update file'); ?></span>
                <a href="javascript:void(0);" class="closer" onclick="Pachno.Core.cancelManualUpdatePoller();$('#update_module_help_<?php echo $module->getID(); ?>').hide();"><?php echo image_tag('times'); ?></a>
            </div>
            <div class="backdrop_detail_content">
                <?php echo __('Please click the download link below and download the update file. Place the downloaded file in the cache folder (%cache_folder) on this server. As soon as the file has been verified, the %update button below will be enabled, and you can press the button to update the module.',
                    array('%cache_folder' => '<span class="command_box">'.PACHNO_CACHE_PATH.$module->getName().'.zip</span>', '%update' => '"'.__('Update').'"'));
                ?>
            </div>
            <form id="module_<?php echo $module->getName(); ?>_perform_update" style="display: inline-block; float: right; padding: 10px;" action="<?php echo make_url('configure_module_update', array('module_key' => $module->getName())); ?>">
                <input type="submit" disabled value="<?php echo __('Update module'); ?>" class="button button-lightblue">
            </form>
            <div style="display: inline-block; float: none; padding: 10px;">
                <a id="module_<?php echo $module->getName(); ?>_download_location" class="button" href="#" target="_blank"><?php echo __('Download update file'); ?></a>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if (!$module instanceof CoreModule && $module->getID()): ?>
<div id="uninstall_module_<?php echo($module->getID()); ?>" class="fullpage_backdrop" style="display: none;">
    <div class="fullpage_backdrop_content">
        <div class="backdrop_box medium">
            <div class="backdrop_detail_header">
                <span><?php echo __('Really uninstall "%module_name"?', array('%module_name' => $module->getLongname())); ?></span>
                <a href="javascript:void(0);" class="closer" onclick="$('#uninstall_module_<?php echo $module->getID(); ?>').hide();"><?php echo image_tag('times'); ?></a>
            </div>
            <div class="backdrop_detail_content">
                <span class="question_header"><?php echo __('Uninstalling this module will permanently prevent users from accessing it or any associated data. If you just want to prevent access to the module temporarily, disable the module instead.'); ?></span><br>
            </div>
            <div class="backdrop_details_submit" id="uninstall_module_controls_<?php echo $module->getID(); ?>">
                <?php echo link_tag(make_url('configure_uninstall_module', array('module_key' => $module->getName())), __('Yes'), array('class' => 'button primary')); ?>
                <a href="javascript:void(0)" class="button secondary" onclick="$('#uninstall_module_<?php echo $module->getID(); ?>').hide();"><?php echo __('No'); ?></a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
