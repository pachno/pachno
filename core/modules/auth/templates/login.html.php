<?php

    use pachno\core\framework\Context;
    use pachno\core\framework\Settings;

?>
<div id="login_backdrop" class="fullpage_backdrop">
    <div class="fullpage_backdrop_content">
        <div class="backdrop_box avatar-header login_page login_popup" id="login_popup">
            <div class="backdrop_detail_header">
                <div class="avatar-container">
                    <?php echo image_tag(Settings::getHeaderIconUrl(), [], true); ?>
                </div>
                <span><?php echo __('Login'); ?></span>
                <?php /* <div class="status-badge">
                <span class="icon"><?php echo pachno_get_userstate_image($user); ?></span>
                <span class="name"><?= __($user->getState()->getName()); ?></span>
            </div> */ ?>
            </div>
            <div id="backdrop_detail_content" class="backdrop_detail_content rounded_top login_content">
                <?php include_component('auth/login', compact('section')); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    Pachno.on(Pachno.EVENTS.ready, () => {
        <?php if (Context::hasMessage('login_message')): ?>
            Pachno.UI.Message.success('<?php echo Context::getMessageAndClear('login_message'); ?>');
        <?php elseif (Context::hasMessage('login_message_err')): ?>
            Pachno.UI.Message.error('<?php echo Context::getMessageAndClear('login_message_err'); ?>');
        <?php endif; ?>
        $('#pachno_username').focus();
    });
</script>
