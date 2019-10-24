<?php

    $pachno_response->setTitle(__('Configure settings'));
    
?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_SETTINGS]); ?>
    <div class="configuration-container">
        <div class="configuration-content centered">
            <div class="form-container">
                <h1><?= __('Configure settings'); ?></h1>
                <div class="helper-text">
                    <div class="image-container"><?= image_tag('/unthemed/onboarding_configuration_settings_icon.png', [], true); ?></div>
                    <span class="description"><?= __("These settings configure various features in Pachno. Keep in mind that changing any of these settings will apply globally and immediately. There is no need to log out and back in."); ?></span>
                </div>
                <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                    <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_settings'); ?>" method="post" onsubmit="Pachno.Main.Helpers.formSubmit('<?= make_url('configure_settings'); ?>', 'config_settings'); return false;" id="config_settings">
                <?php endif; ?>
                <div class="fancy-tabs" id="settings_menu">
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
                    <div id="tab_general_settings_pane"><?php include_component('generalsettings', array('access_level' => $access_level)); ?></div>
                    <div id="tab_reglang_settings_pane" style="display: none;"><?php include_component('languagesettings', array('access_level' => $access_level)); ?></div>
                    <div id="tab_user_settings_pane" style="display: none;"><?php include_component('usersettings', array('access_level' => $access_level)); ?></div>
                    <div id="tab_offline_settings_pane" style="display: none;"><?php include_component('offline', array('access_level' => $access_level)); ?></div>
                </div>
                <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                    <div class="form-row submit-container">
                        <button type="submit" id="config_settings_button" class="button primary">
                            <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin']);?>
                            <span><?= __('Save settings'); ?></span>
                        </button>
                    </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
