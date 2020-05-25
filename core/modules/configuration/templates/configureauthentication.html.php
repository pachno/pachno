<?php

    /**
     * @var \pachno\core\framework\Response $pachno_response
     * @var \pachno\core\entities\Module[] $modules
     */

    $pachno_response->setTitle(__('Configure authentication'));

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_AUTHENTICATION]); ?>
    <div class="configuration-container">
        <div class="configuration-content centered">
            <h1><?php echo __('Configure authentication'); ?></h1>
            <div class="form-container">
                <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                    <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_authentication_pt2'); ?>" method="post" id="config_auth">
                <?php endif; ?>
                <?php if (count($modules)): ?>
                    <div class="form-row">
                        <div class="helper-text">
                            <div class="message-box type-warning">
                                <?= fa_image_tag('exclamation-circle') . '<span>'.__('Please remember to install and configure your chosen authentication backend before setting it here. Changing settings on this page will result in you being logged out. If you find yourself unable to log in, use the %bin/pachno command line client to revert these settings.', ['%bin/pachno' => '<span class="command_box">bin/pachno</span>']).'</span>'; ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="fancy-dropdown-container">
                            <div class="fancy-dropdown">
                                <label><?= __('Authentication backend'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <div class="column">
                                        <input type="radio" name="auth_backend" id="auth-backend-default" value="default" class="fancy-checkbox" <?php if (\pachno\core\framework\Settings::getAuthenticationBackendIdentifier() == 'default' || \pachno\core\framework\Settings::getAuthenticationBackendIdentifier() == null) echo ' checked'; ?>>
                                        <label for="auth-backend-default" class="list-item">
                                            <span class="icon"><?= fa_image_tag('lock'); ?></span>
                                            <span class="name value"><?php echo __('Pachno authentication (use internal user mechanisms)'); ?></span>
                                        </label>
                                        <?php foreach ($modules as $module): ?>
                                            <input type="radio" name="auth_backend" id="auth-backend-<?= $module->getTabKey(); ?>" value="<?= $module->getTabKey(); ?>" class="fancy-checkbox" <?php if (\pachno\core\framework\Settings::getAuthenticationBackendIdentifier() == $module->getTabKey()) echo ' checked'; ?>>
                                            <label for="auth-backend-<?= $module->getTabKey(); ?>" class="list-item">
                                                <span class="icon"><?= fa_image_tag('lock'); ?></span>
                                                <span class="name value"><?= $module->getLongName(); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="helper-text">
                            <?php echo __('All modules which provide authentication are shown here. Please ensure your chosen backend is configured first, and please read the warnings included with your chosen backend to ensure that you do not lose administrator access.'); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="message-box type-info">
                        <?= fa_image_tag('info-circle') . '<span>'.__('If you install additional authentication modules such as LDAP authentication, you can configure them from this page').'</span>'; ?>
                    </div>
                <?php endif; ?>
                <div class="form-row">
                    <?php include_component('main/textarea', array('area_name' => 'register_message', 'area_id' => 'register_message', 'height' => '75px', 'width' => '100%', 'value' => \pachno\core\framework\Settings::get('register_message'), 'hide_hint' => true)); ?>
                    <label for="register_message"><?php echo __('Registration message'); ?></label>
                </div>
                <div class="form-row">
                    <div class="helper-text">
                        <?php echo __("Pachno's registration page is unavailable when using a different backend. Write a message here to be shown to users instead. WikiFormatting can be used in this box and similar ones on this page."); ?>
                    </div>
                </div>
                <div class="form-row">
                    <?php include_component('main/textarea', array('area_name' => 'forgot_message', 'area_id' => 'forgot_message', 'height' => '75px', 'width' => '100%', 'value' => \pachno\core\framework\Settings::get('forgot_message'), 'hide_hint' => true)); ?>
                    <label for="forgot_message"><?php echo __('Forgot password message'); ?></label>
                </div>
                <div class="form-row">
                    <?php include_component('main/textarea', array('area_name' => 'changepw_message', 'area_id' => 'changepw_message', 'height' => '75px', 'width' => '100%', 'value' => \pachno\core\framework\Settings::get('changepw_message'), 'hide_hint' => true)); ?>
                    <label for="changepw_message"><?php echo __('Change password message'); ?></label>
                </div>
                <div class="form-row">
                    <?php include_component('main/textarea', array('area_name' => 'changedetails_message', 'area_id' => 'changedetails_message', 'height' => '75px', 'width' => '100%', 'value' => \pachno\core\framework\Settings::get('changedetails_message'), 'hide_hint' => true)); ?>
                    <label for="changedetails_message"><?php echo __('Change account details message'); ?></label>
                </div>
                <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                        <div class="form-row submit-container">
                            <input type="submit" value="<?php echo __('Save'); ?>" class="button primary">
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
