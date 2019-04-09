<?php

    $pachno_response->setTitle(__('Configure settings'));
    
?>
<div class="content-with-sidebar">
    <?php include_component('leftmenu', array('selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_SETTINGS)); ?>
    <div class="main_configuration_content">
        <h3>
            <?= __('Configure settings'); ?>
        </h3>
        <div class="content faded_out">
            <p><?= __("These are all the different settings defining most of the behaviour of Pachno. Changing any of these settings will apply globally and immediately, without the need to log out and back in, reboot or anything to that effect."); ?></p>
        </div>
        <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
            <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_settings'); ?>" method="post" onsubmit="Pachno.Main.Helpers.formSubmit('<?= make_url('configure_settings'); ?>', 'config_settings'); return false;" id="config_settings">
        <?php endif; ?>
        <div class="fancytabs" id="settings_menu">
            <a class="tab selected" id="tab_general_settings" onclick="Pachno.Main.Helpers.tabSwitcher('tab_general_settings', 'settings_menu');" href="javascript:void(0);">
                <?= fa_image_tag('cog', ['class' => 'icon']); ?>
                <span class="name"><?= __('General', [], true); ?></span>
            </a>
            <a class="tab" id="tab_reglang_settings" onclick="Pachno.Main.Helpers.tabSwitcher('tab_reglang_settings', 'settings_menu');" href="javascript:void(0);">
                <?= fa_image_tag('globe', ['class' => 'icon']); ?>
                <span class="name"><?= __('Regional & language'); ?></span>
            </a>
            <a class="tab" id="tab_user_settings" onclick="Pachno.Main.Helpers.tabSwitcher('tab_user_settings', 'settings_menu');" href="javascript:void(0);">
                <?= fa_image_tag('unlock-alt', ['class' => 'icon']); ?>
                <span class="name"><?= __('Users & security'); ?></span>
            </a>
            <a class="tab" id="tab_offline_settings" onclick="Pachno.Main.Helpers.tabSwitcher('tab_offline_settings', 'settings_menu');" href="javascript:void(0);">
                <?= fa_image_tag('coffee', ['class' => 'icon']); ?>
                <span class="name"><?= __('Maintenance mode', [], true); ?></span>
            </a>
        </div>
        <div id="settings_menu_panes">
            <div id="tab_general_settings_pane"><?php include_component('general', array('access_level' => $access_level)); ?></div>
            <div id="tab_reglang_settings_pane" style="display: none;"><?php include_component('reglang', array('access_level' => $access_level)); ?></div>
            <div id="tab_user_settings_pane" style="display: none;"><?php include_component('user', array('access_level' => $access_level)); ?></div>
            <div id="tab_offline_settings_pane" style="display: none;"><?php include_component('offline', array('access_level' => $access_level)); ?></div>
        </div>
        <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
            <div class="save-button-container">
                <div class="message"><?= __('Click "%save" to save your changes in all categories', array('%save' => __('Save'))); ?></div>
                <span id="config_settings_indicator" style="display: none;"><?= image_tag('spinning_20.gif'); ?></span>
                <input type="submit" id="config_settings_button" value="<?= __('Save'); ?>">
            </div>
        <?php endif; ?>
        </form>
    </div>
</div>
