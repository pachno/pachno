<?php

    use pachno\core\framework\Context;

?>
<div id="login_backdrop">
    <div class="backdrop_box login_page login_popup" id="login_popup">
        <div id="backdrop_detail_content" class="backdrop_detail_content rounded_top login_content">
            <?php include_component('auth/login', compact('section')); ?>
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
