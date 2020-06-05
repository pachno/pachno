<div class="logindiv form-container active" id="regular_login_container">
    <?php if ($loginintro instanceof \pachno\core\entities\Article): ?>
        <?php include_component('publish/articledisplay', array('article' => $loginintro, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
    <?php endif; ?>
    <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('login'); ?>" method="post" id="login_form" onsubmit="Pachno.Main.Login.login(); return false;">
        <?php if (!\pachno\core\framework\Context::hasMessage('login_force_redirect') || \pachno\core\framework\Context::getMessage('login_force_redirect') !== true): ?>
            <input type="hidden" id="pachno_referer" name="referer" value="<?= $referer; ?>" />
        <?php else: ?>
            <input type="hidden" id="return_to" name="return_to" value="<?= $referer; ?>" />
        <?php endif; ?>
        <div class="form-row">
            <input type="text" id="pachno_username" name="username">
            <label for="pachno_username"><?= __('Username'); ?></label>
        </div>
        <div class="form-row">
            <input type="password" id="pachno_password" name="password"><br>
            <label for="pachno_password"><?= __('Password'); ?></label>
        </div>
        <div class="form-row">
            <input type="checkbox" class="fancy-checkbox" name="rememberme" value="1" id="pachno_rememberme"><label class="login_fieldlabel" for="pachno_rememberme"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Keep me logged in'); ?></label>
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
    </form>
</div>
<?php \pachno\core\framework\Event::createNew('core', 'login_form_pane')->trigger(array_merge(array('selected_tab' => $selected_tab), $options)); ?>
<?php if (\pachno\core\framework\Settings::isRegistrationAllowed()): ?>
    <div style="text-align: center;" id="registration-button-container" class="logindiv form-container login_button_container registration_button_container active">
        <fieldset style="border: 0; border-top: 1px dotted rgba(0, 0, 0, 0.3); padding: 5px 100px; width: 100px; margin: 5px auto 0 auto;">
            <legend style="text-align: center; width: 100%; background-color: transparent;"><?= __('%login or %signup', array('%login' => '', '%signup' => '')); ?></legend>
        </fieldset>
        <a href="javascript:void(0);" class="button secondary highlight" id="create-account-button" onclick="$('#register').addClass('active');$('#registration-button-container').removeClass('active');$('#regular_login_container').removeClass('active');"><?= __('Create an account'); ?></a>
    </div>
    <?php include_component('main/loginregister', compact('registrationintro')); ?>
<?php endif; ?>
<?php if (isset($error)): ?>
    <script type="text/javascript">
        require(['domReady', 'pachno/index'], function (domReady, Pachno) {
            domReady(function () {
                Pachno.UI.Message.error('<?= $error; ?>');
            });
        });
    </script>
<?php endif; ?>
