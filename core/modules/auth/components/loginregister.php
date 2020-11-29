<div id="register" class="logindiv regular">
    <?php if (\pachno\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
        <?php echo \pachno\core\helpers\TextParser::parseText(\pachno\core\framework\Settings::get('register_message'), false, null, array('embedded' => true)); ?>
    <?php else: ?>
        <div id="register_container" class="form-container">
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('register'); ?>" method="post" id="register_form" onsubmit="Pachno.Main.Login.register('<?php echo make_url('register'); ?>'); return false;">
                <div class="form-row header">
                    <h3><?php echo __('Create an account'); ?></h3>
                </div>
                <?php if ($registrationintro instanceof \pachno\core\entities\Article): ?>
                    <?php include_component('publish/articledisplay', array('article' => $registrationintro, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
                <?php endif; ?>
                <div class="form-row" id="row-register-username">
                    <input type="text" class="required" id="fieldusername" name="username" onblur="Pachno.Main.Login.checkUsernameAvailability('<?php echo make_url('register_check_username'); ?>');">
                    <label for="fieldusername">*&nbsp;<?php echo __('Username'); ?><?= fa_image_tag('indicator'); ?></label>
                    <div class="error"><?php echo __('This username is invalid or in use'); ?></div>
                </div>
                <div class="form-row">
                    <input type="email" class="required" id="email_address" name="email_address">
                    <label for="email_address">*&nbsp;<?php echo __('E-mail address'); ?></label>
                </div>
                <div class="form-row">
                    <input type="email" class="required" id="email_confirm" name="email_confirm">
                    <label for="email_confirm">*&nbsp;<?php echo __('Confirm e-mail'); ?></label>
                </div>
                <?php include_component('auth/captcha'); ?>
                <div class="form-row submit-container">
                    <a class="button secondary" href="javascript:void(0);" onclick="Pachno.Main.Login.showLogin('#regular_login_container');">
                        <?= fa_image_tag('angle-left', ['class' => 'icon']); ?>
                        <span><?php echo __('Back'); ?></span>
                    </a>
                    <button type="submit" class="button primary" id="register_button">
                        <?= fa_image_tag('check', ['class' => 'icon']); ?>
                        <span><?php echo __('Register'); ?></span>
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                    </button>
                </div>
            </form>
        </div>
        <div style="display: none;" id="register_confirmation" class="form-container">
            <div class="form-row header">
                <h3><?php echo __('Thank you for registering!'); ?></h3>
            </div>
            <div class="form-row">
                <span class="helper-text" id="register_message"></span>
            </div>
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('auth_login'); ?>" method="post" id="register_auto_form" onsubmit="Pachno.Main.Login.registerAutologin('<?php echo make_url('auth_login'); ?>'); return false;">
                <input id="register_username_hidden" name="username" type="hidden" value="">
                <input id="register_password_hidden" name="password" type="hidden" value="">
                <input type="hidden" name="return_to" value="<?php echo make_url('profile_account'); ?>">
                <div class="form-row submit-container">
                    <button type="submit" class="button" id="register_autologin_button">
                        <?= fa_image_tag('angle-right', ['class' => 'icon indicator']); ?>
                        <span><?php echo __('Continue'); ?></span>
                        <?= fa_image_tag('angle-right', ['class' => 'icon']); ?>
                    </button>
                </div>
                <div class="login_button_container" id="register_confirm_back" style="display: none;">
                    <a style="float: left;" href="javascript:void(0);" onclick="Pachno.Main.Login.showLogin('#regular_login_container');">&laquo;&nbsp;<?php echo __('Back'); ?></a>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>
