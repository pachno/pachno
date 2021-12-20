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
        <?php if ($pachno_user->canCreateProjects() || $pachno_user->canSaveConfiguration()): ?>
            <button class="button secondary highlight" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'project_config']); ?>');"><?= fa_image_tag('plus-square'); ?><span><?= __('Create a project'); ?></span></button>
        <?php endif; ?>
    </div>
<?php endif; ?>
