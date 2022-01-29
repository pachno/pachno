<?php
    
    use pachno\core\framework\Context;
    use pachno\core\framework\Settings;
    use pachno\core\helpers\TextParser;

?>
<div class="backdrop_box avatar-header hidden" id="forgot_password_container">
    <div class="backdrop_detail_header">
        <div class="avatar-container">
            <?php echo image_tag(Settings::getHeaderIconUrl(), [], true); ?>
        </div>
        <span><?php echo __('Login'); ?></span>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <?php if (Settings::isUsingExternalAuthenticationBackend()): ?>
                <?php echo TextParser::parseText(Settings::get('forgot_message'), false, null, array('embedded' => true)); ?>
            <?php else: ?>
                <form accept-charset="<?php echo Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('mailing_forgot'); ?>" method="post" id="forgot_password_form" data-simple-submit>
                    <div class="form-row">
                        <div class="helper-text">
                            <div class="image-container"><?= image_tag('/unthemed/onboarding_forgot_password.png', [], true); ?></div>
                            <div class="description"><?= __("If you've forgot your password, enter your username or email address here. An email will be sent with instructions on how to reset your password"); ?></div>
                        </div>
                    </div>
                    <div class="form-row" id="row-forgot-username" data-field="username">
                        <label for="forgot_password_username">*&nbsp;<?php echo __('Username or email address'); ?></label>
                        <input type="text" name="forgot_password_username" class="required" id="forgot_password_username">
                    </div>
                    <div class="form-row submit-container">
                        <a class="button secondary trigger-show-login" href="javascript:void(0);">
                            <?= fa_image_tag('angle-left', ['class' => 'icon']); ?>
                            <span><?php echo __('Back'); ?></span>
                        </a>
                        <button type="submit" class="button primary" id="forgot_password_submit_button">
                            <?= fa_image_tag('check', ['class' => 'icon']); ?>
                            <span><?php echo __('Send email'); ?></span>
                            <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
        const json = data.json;
        switch (data.form) {
            case 'forgot_password_form':
                if (json.message) {
                    $('#row-forgot-username').addClass('hidden');
                    $('#forgot_password_submit_button').remove();
                }
                break;
        }
    });
</script>