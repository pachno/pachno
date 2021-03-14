<?php

    use pachno\core\framework\Context;

?>
<div id="login_backdrop" class="fullpage_backdrop">
    <div class="fullpage_backdrop_content">
        <?php include_component('auth/login', compact('section', 'captcha')); ?>
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
