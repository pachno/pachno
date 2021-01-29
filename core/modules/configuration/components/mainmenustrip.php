<?php

use pachno\core\framework;

/**
 * @var \pachno\core\entities\User $pachno_user
 */
?>
<div class="name-container shaded">
    <span class="header">
        <span class="name"><?= __('Configure Pachno'); ?></span>
    </span>
</div>
<div class="spacer"></div>
<span class="version-container">v<?= \pachno\core\framework\Settings::getVersion(); ?></span>
<div class="action-container">
    <a class="button secondary highlight" id="update_button" href="javascript:void(0);" onclick="Pachno.Config.updateCheck('<?php echo make_url('configure_update_check'); ?>');"><?php echo __('Check for updates'); ?></a>
</div>
