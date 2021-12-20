<?php

    use pachno\core\framework\Settings;
    use pachno\core\framework\Context;

    /**
     * @var \pachno\core\framework\Response $pachno_response
     * @var int $access_level
     */

    $pachno_response->setTitle(__('Configure settings'));
    
?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => Settings::CONFIGURATION_SECTION_SETTINGS]); ?>
    <div class="configuration-container">
        <div class="configuration-content centered">
            <h1><?= __('Configure settings'); ?></h1>
            <div class="form-container">
                <div class="helper-text">
                    <div class="image-container"><?= image_tag('/unthemed/onboarding_configuration_settings_icon.png', [], true); ?></div>
                    <span class="description"><?= __("These settings configure various features in Pachno. Keep in mind that changing any of these settings will apply globally and immediately. There is no need to log out and back in."); ?></span>
                </div>
                <?php if ($access_level == Settings::ACCESS_FULL): ?>
                    <form accept-charset="<?= Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_settings'); ?>" method="post" data-simple-submit id="config_settings">
                <?php endif; ?>
                <div class="fancy-tabs tab-switcher" id="settings_menu">
                    <a class="tab tab-switcher-trigger selected" data-tab-target="general" href="javascript:void(0);">
                        <?= fa_image_tag('cog', ['class' => 'icon']); ?>
                        <span class="name"><?= __('General', [], true); ?></span>
                    </a>
                    <a class="tab tab-switcher-trigger" data-tab-target="reglang" href="javascript:void(0);">
                        <?= fa_image_tag('globe', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Regional & language'); ?></span>
                    </a>
                    <a class="tab tab-switcher-trigger" data-tab-target="user" href="javascript:void(0);">
                        <?= fa_image_tag('unlock-alt', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Users & security'); ?></span>
                    </a>
                    <a class="tab tab-switcher-trigger" data-tab-target="offline" href="javascript:void(0);">
                        <?= fa_image_tag('coffee', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Maintenance mode', [], true); ?></span>
                    </a>
                </div>
                <div id="settings_menu_panes">
                    <div data-tab-id="general"><?php include_component('configuration/generalsettings', array('access_level' => $access_level)); ?></div>
                    <div data-tab-id="reglang" style="display: none;"><?php include_component('configuration/languagesettings', array('access_level' => $access_level)); ?></div>
                    <div data-tab-id="user" style="display: none;"><?php include_component('configuration/usersettings', array('access_level' => $access_level)); ?></div>
                    <div data-tab-id="offline" style="display: none;"><?php include_component('configuration/offline', array('access_level' => $access_level)); ?></div>
                </div>
                <?php if ($access_level == Settings::ACCESS_FULL): ?>
                    <div class="form-row submit-container">
                        <button type="submit" id="config_settings_button" class="button primary">
                            <span><?= __('Save'); ?></span>
                            <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                        </button>
                    </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
