<?php

    \pachno\core\framework\Context::loadLibrary('ui');

?>
<div id="login_backdrop">
    <div class="backdrop_box login_page login_popup" id="login_popup">
        <div id="backdrop_detail_content" class="backdrop_detail_content rounded_top login_content">
            <?php include_component('main/login', compact('section')); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    require(['domReady', 'pachno/index', 'jquery'], function (domReady, Pachno, jquery) {
        domReady(function () {
        <?php if (\pachno\core\framework\Context::hasMessage('login_message')): ?>
            Pachno.Main.Helpers.Message.success('<?php echo \pachno\core\framework\Context::getMessageAndClear('login_message'); ?>');
        <?php elseif (\pachno\core\framework\Context::hasMessage('login_message_err')): ?>
            Pachno.Main.Helpers.Message.error('<?php echo \pachno\core\framework\Context::getMessageAndClear('login_message_err'); ?>');
        <?php endif; ?>
            jquery('#pachno_username').focus();
        });
    });
</script>
