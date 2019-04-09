<?php

    \pachno\core\framework\Context::loadLibrary('ui');

?>
<div id="elevated_login_container">
    <div class="backdrop_box login_page login_popup" id="login_popup">
        <div id="backdrop_detail_content" class="backdrop_detail_content rounded_top login_content">
            <div class="logindiv regular active" id="regular_login_container">
                <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('elevated_login'); ?>" method="post" id="login_form" onsubmit="Pachno.Main.Login.elevatedLogin('<?php echo make_url('elevated_login'); ?>'); return false;">
                    <h2 class="login_header"><?php echo __('Authentication required'); ?></h2>
                    <div class="article">
                        <?php echo __('This page requires an extra authentication step. Please re-enter your password to continue'); ?>
                    </div>
                    <ul class="login_formlist">
                        <li>
                            <label for="pachno_username"><?php echo __('Username'); ?></label>
                            <input type="text" id="pachno_username" name="dummy_username" disabled value="<?php echo $pachno_user->getUsername(); ?>">
                        </li>
                        <li>
                            <label for="pachno_password"><?php echo __('Password'); ?></label>
                            <input type="password" id="pachno_password" name="elevated_password"><br>
                        </li>
                        <li>
                            <label for="pachno_elevation_duration"><?php echo __('Re-authentication duration'); ?></label>
                            <select name="elevation_duration" id="pachno_elevation_duration">
                                <?php foreach (array(5, 10, 15, 30, 60) as $minute): ?>
                                    <option value="<?php echo $minute; ?>" <?php if ($minute == 30) echo 'selected'; ?>><?php echo __('Remember for %minutes minutes', array('%minutes' => $minute)); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </li>
                    </ul>
                    <div class="login_button_container">
                        <?php echo image_tag('spinning_20.gif', array('id' => 'elevated_login_indicator', 'style' => 'display: none;')); ?>
                        <input type="submit" id="login_button" class="button" value="<?php echo __('Authenticate'); ?>">
                    </div>
                </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    require(['domReady', 'pachno/index', 'prototype'], function (domReady, Pachno, prototype) {
        domReady(function () {
        <?php if (\pachno\core\framework\Context::hasMessage('elevated_login_message')): ?>
            Pachno.Main.Helpers.Message.success('<?php echo \pachno\core\framework\Context::getMessageAndClear('elevated_login_message'); ?>');
        <?php elseif (\pachno\core\framework\Context::hasMessage('elevated_login_message_err')): ?>
            Pachno.Main.Helpers.Message.error('<?php echo \pachno\core\framework\Context::getMessageAndClear('elevated_login_message_err'); ?>');
        <?php endif; ?>
            $('pachno_password').focus();
        });
    });
</script>