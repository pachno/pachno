<?php

    use pachno\core\framework\Settings;

?>
<div class="backdrop_box avatar-header" id="login-popup">
    <div class="backdrop_detail_header">
        <div class="avatar-container">
            <?php echo image_tag(Settings::getHeaderIconUrl(), [], true); ?>
        </div>
        <span><?php echo __('Login'); ?></span>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container" id="regular_login_container">
            <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('auth_login'); ?>" method="post" id="login-form" data-simple-submit>
                <?php if (!\pachno\core\framework\Context::hasMessage('login_force_redirect') || \pachno\core\framework\Context::getMessage('login_force_redirect') !== true): ?>
                    <input type="hidden" id="pachno_referer" name="referer" value="<?= $referer; ?>" />
                <?php else: ?>
                    <input type="hidden" id="return_to" name="return_to" value="<?= $referer; ?>" />
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="form-row">
                        <div class="message-box type-error">
                            <?= fa_image_tag('exclamation-triangle'); ?>
                            <span class="message"><?php echo $error; ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-row">
                    <label for="pachno_username"><?= __('Username'); ?></label>
                    <input type="text" id="pachno_username" name="username">
                </div>
                <div class="form-row">
                    <label for="pachno_password"><?= __('Password'); ?></label>
                    <input type="password" id="pachno_password" name="password"><br>
                </div>
                <div class="form-row">
                    <input type="checkbox" class="fancy-checkbox" name="rememberme" value="1" id="pachno_rememberme"><label class="login_fieldlabel" for="pachno_rememberme"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?><span><?= __('Keep me logged in'); ?></span></label>
                </div>
                <div class="form-row" id="login-error-container">
                    <div class="error" id="login-error-message"></div>
                </div>
                <div class="form-row submit-container" id="login-submit-container">
                    <?php \pachno\core\framework\Event::createNew('core', 'login_button_container')->trigger(); ?>
                    <button type="submit" id="login_button" class="button primary">
                        <span><?= __('Log in'); ?></span>
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                    </button>
                </div>
                <div id="registration-button-container" class="form-container">
                    <div class="form">
                        <fieldset>
                            <legend><span><?= __('%login or %signup', array('%login' => '', '%signup' => '')); ?></span></legend>
                        </fieldset>
                        <div class="row centered">
                            <a href="javascript:void(0);" class="button secondary highlight trigger-show-register" id="create-account-button"><?= __('Create an account'); ?></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php \pachno\core\framework\Event::createNew('core', 'login_form_pane')->trigger(array_merge(array('selected_tab' => $selected_tab), $options)); ?>
<?php if (\pachno\core\framework\Settings::isRegistrationAllowed()): ?>
    <?php include_component('auth/loginregister', compact('captcha')); ?>
<?php endif; ?>
<script>
    Pachno.on(Pachno.EVENTS.ready, () => {
        public const $body = $('body');

        $body.on('click', '.trigger-show-register', () => {
            $('#login-popup').addClass('hidden');
            $('#create-account-popup').removeClass('hidden');
        });

        $body.on('click', '.trigger-show-login', () => {
            $('#create-account-popup').addClass('hidden');
            $('#login-popup').removeClass('hidden');
        });
    })
</script>
