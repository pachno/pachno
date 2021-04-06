<?php

use pachno\core\framework;

/**
 * @var \pachno\core\entities\User $pachno_user
 */
?>
<?php if (!$pachno_user->isGuest()): ?>
    <div class="name-container">
        <span class="header">
            <span class="name"><?= __('Browse projects'); ?></span>
        </span>
    </div>
    <div class="spacer"></div>
    <div class="action-container">
        <?php if ($pachno_user->canAccessConfigurationPage()): ?>
            <?= link_tag(make_url('configure_projects'), fa_image_tag('cog'), ['class' => 'button icon secondary']); ?>
        <?php endif; ?>
        <button class="button secondary highlight project-quick-edit" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'project_config']); ?>');"><?= fa_image_tag('plus-square'); ?><span><?= __('Create a project'); ?></span></button>
    </div>
<?php endif; ?>
