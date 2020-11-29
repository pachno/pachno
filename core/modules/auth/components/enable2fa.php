<?php

/**
 * @var \pachno\core\entities\User $pachno_user
 */

?>
<div class="backdrop_box large" id="project_config_popup_main_container">
    <div class="backdrop_detail_header">
        <span><?= __('Enable two-factor authentication'); ?></span>
        <a class="closer" href="javascript:void(0);" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('auth_user_verify_2fa'); ?>" method="post" id="enable_2fa_form" data-simple-submit>
                <div class="form-row centered">
                    <img src="<?= $qr_code_inline; ?>">
                </div>
                <div class="helper-text">
                    <?= __('Scan the code above with your 2FA application (such as Google authenticator, Bitwarden, Authy or similar. Alternatively, use the code below as the 2FA key.'); ?>
                </div>
                <div class="form-row">
                    <div class="application_password_preview password-preview">
                        <?php for ($cc = 0; $cc < 4; $cc++): ?>
                            <span><?= substr($pachno_user->get2faToken(), $cc * 4, 4); ?></span>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="form-row centered">
                    <input type="hidden" id="pachno_username" name="username" value="<?= $pachno_user->getUsername(); ?>">
                    <input type="hidden" name="session_token" value="<?= $session_token; ?>">
                    <input type="text" id="pachno_2fa_code" name="2fa_code" class="code-input-6 name-input-enhance" pattern="\d{6}" autocomplete="0">
                    <label for="pachno_2fa_code"><?= __('Generated code'); ?></label>
                </div>
                <div class="form-row error-container" id="tfa-error-container">
                    <div class="error" id="tfa-error-message"></div>
                </div>
                <div class="form-row submit-container">
                    <a href="javascript:void(0);" onclick="Pachno.UI.Backdrop.reset();" class="button secondary"><?= __('Never mind'); ?></a>
                    <button type="submit" class="button primary"><span><?= __('Verify code'); ?></span><?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="application/javascript">
    Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
        const json = data.json;
        switch (data.form) {
            case 'enable_2fa_form':
                if (json.result === 'verified') {
                    $('#account_2fa_enabled').show();
                    $('#account_2fa_disabled').hide();
                }
                Pachno.UI.Backdrop.reset();
                break;
        }
    });

</script>