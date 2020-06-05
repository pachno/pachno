<?php

    \pachno\core\framework\Context::loadLibrary('ui');

?>
<div id="elevated_login_container">
    <div class="backdrop_box login_page login_popup" id="login_popup">
        <div id="backdrop_detail_content" class="backdrop_detail_content rounded_top login_content">
            <div class="logindiv form-container active" id="regular_login_container">
                <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('elevated_login'); ?>" method="post" id="login_form" onsubmit="Pachno.Main.Login.login();return false;">
                    <div class="form-row">
                        <h3><?php echo __('Authentication required'); ?></h3>
                    </div>
                    <div class="form-row">
                        <div class="helper-text">
                            <?php echo __('This page requires an extra authentication step. Please re-enter your password to continue'); ?>
                        </div>
                    </div>
                    <div class="form-row">
                        <input type="hidden" id="pachno_username" name="dummy_username" disabled value="<?php echo $pachno_user->getUsername(); ?>">
                        <input type="password" id="pachno_password" name="elevated_password">
                        <label for="pachno_password"><?php echo __('Password'); ?></label>
                    </div>
                    <div class="form-row">
                        <div class="fancy-dropdown-container">
                            <div class="fancy-dropdown">
                                <label><?php echo __('Keep elevated privileges'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach ([5, 10, 15, 30, 60] as $minute): ?>
                                        <input class="fancy-checkbox" type="radio" name="elevation_duration" value="<?= $minute; ?>" id="elevation_duration_<?= $minute; ?>" <?php if ($minute == 30) echo 'checked'; ?>>
                                        <label for="elevation_duration_<?= $minute; ?>" class="list-item">
                                            <span class="value name"><?= __('Remember for %minutes minutes', ['%minutes' => $minute]); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row" id="login-error-container">
                        <div class="error" id="login-error-message"></div>
                    </div>
                    <div class="form-row submit-container">
                        <button type="submit" id="login_button" class="button primary"><span><?php echo __('Authenticate'); ?></span><?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?></button>
                    </div>
                </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    require(['domReady', 'pachno/index'], function (domReady, Pachno) {
        domReady(function () {
        <?php if (\pachno\core\framework\Context::hasMessage('elevated_login_message')): ?>
            Pachno.UI.Message.success('<?php echo \pachno\core\framework\Context::getMessageAndClear('elevated_login_message'); ?>');
        <?php elseif (\pachno\core\framework\Context::hasMessage('elevated_login_message_err')): ?>
            Pachno.UI.Message.error('<?php echo \pachno\core\framework\Context::getMessageAndClear('elevated_login_message_err'); ?>');
        <?php endif; ?>
            $('#pachno_password').focus();
        });
    });
</script>
