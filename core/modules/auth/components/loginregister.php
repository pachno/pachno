<?php

    use pachno\core\framework\Settings;

?>
<div class="backdrop_box avatar-header hidden" id="create-account-popup">
    <div class="backdrop_detail_header">
        <div class="avatar-container">
            <?php echo image_tag(Settings::getHeaderIconUrl(), [], true); ?>
        </div>
        <span><?php echo __('Create an account'); ?></span>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div id="register" class="form-container">
            <?php if (\pachno\core\framework\Settings::isUsingExternalAuthenticationBackend()): ?>
                <?php echo \pachno\core\helpers\TextParser::parseText(\pachno\core\framework\Settings::get('register_message'), false, null, array('embedded' => true)); ?>
            <?php else: ?>
                <div id="register_container" class="form-container">
                    <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('register'); ?>" method="post" id="register_form" data-simple-submit>
                        <div class="form-row" id="row-register-username" data-field="username">
                            <label for="fieldusername">*&nbsp;<?php echo __('Username'); ?><?= fa_image_tag('indicator'); ?></label>
                            <input type="text" class="required" id="fieldusername" data-verify-on-blur name="username" data-url="<?php echo make_url('register_check_username'); ?>">
                            <div class="error"><?php echo __('This username is invalid or in use'); ?></div>
                        </div>
                        <div class="form-row" data-field="email_address">
                            <label for="email_address">*&nbsp;<?php echo __('E-mail address'); ?></label>
                            <input type="email" class="required" id="email_address" name="email_address">
                            <div class="error"></div>
                        </div>
                        <div class="form-row" data-field="email_confirm">
                            <label for="email_confirm">*&nbsp;<?php echo __('Confirm e-mail'); ?></label>
                            <input type="email" class="required" id="email_confirm" name="email_confirm">
                            <div class="error"></div>
                        </div>
                        <?php include_component('auth/captcha', compact('captcha')); ?>
                        <div class="form-row submit-container">
                            <a class="button secondary trigger-show-login" href="javascript:void(0);">
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
                <div id="register_confirmation" class="form-container hidden">
                    <div class="form-row header">
                        <h3><?php echo __('Thank you for registering!'); ?></h3>
                    </div>
                    <div class="form-row">
                        <h3 id="register_message"></h3>
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
    </div>
</div>
<script type="text/javascript">
    Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
        const json = data.json;
        const $form = $(`#${data.form}`);
        if (data.form == 'register_form' && json.registered) {
            if (json.activated) {
                $('#register_username_hidden').val($('#fieldusername').val());
                $('#register_password_hidden').val(json.one_time_password);
                $('#register_auto_form').show();
            } else {
                $('#register_confirm_back').show();
            }
            $('#register_message').html(json.loginmessage);
            $('#register_container').addClass('hidden');
            $('#register_confirmation').removeClass('hidden');
        }
    });

</script>
