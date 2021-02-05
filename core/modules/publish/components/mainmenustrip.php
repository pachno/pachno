<?php

use pachno\core\framework;

/**
 * @var \pachno\core\entities\User $pachno_user
 */
?>
<div class="dropper-container">
    <button class="secondary dropper">
        <?php if (framework\Context::isProjectContext()): ?>
            <span class="icon"><?php echo image_tag(framework\Context::getCurrentProject()->getIconName(), ['alt' => "[img]"], true); ?></span>
            <span class="name"><?= framework\Context::getCurrentProject()->getName(); ?></span>
        <?php else: ?>
            <?= fa_image_tag('atlas', ['class' => 'icon']); ?>
            <span class="name"><?= __('Site documentation'); ?></span>
        <?php endif; ?>
        <?= fa_image_tag('chevron-down', ['class' => 'icon toggler']); ?>
    </button>
    <div class="dropdown-container from-left">
        <div class="list-mode"></div>
    </div>
</div>
<div class="spacer"></div>
<?php if ($pachno_user->isAuthenticated()): ?>
    <?php include_component('publish/headeractions'); ?>
<?php endif; ?>
