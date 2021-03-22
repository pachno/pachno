<?php

    use pachno\core\framework;

    /**
     * @var \pachno\core\entities\User $pachno_user
     * @var framework\Routing $pachno_routing
     */

    $current_route = $pachno_routing->getCurrentRoute()->getName();

?>
<div class="name-container shaded">
    <span class="header">
        <span class="name"><?= __('Configure Pachno'); ?></span>
    </span>
</div>
<div class="spacer"></div>
<?php if ($current_route == 'configure_roles'): ?>
    <div class="action-container">
        <a class="button secondary" href="<?= make_url('configure_groups'); ?>">
            <?= fa_image_tag('users', ['class' => 'icon']); ?>
            <span><?= __('Manage groups'); ?></span>
        </a>
    </div>
    <div class="action-container">
        <button type="button" class="button trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'edit_role']); ?>">
            <?= fa_image_tag('plus', ['class' => 'icon']); ?>
            <span><?= __('Create role'); ?></span>
        </button>
    </div>
<?php elseif ($current_route == 'configure_users'): ?>
    <div class="action-container">
        <a class="button secondary" href="<?= make_url('configure_groups'); ?>">
            <?= fa_image_tag('users', ['class' => 'icon']); ?>
            <span><?= __('Manage groups'); ?></span>
        </a>
    </div>
    <div class="action-container">
        <button type="button" class="button" <?php if (!\pachno\core\framework\Context::getScope()->hasUsersAvailable()): ?>disabled<?php endif; ?> onclick="$('#adduser_div').toggle();">
            <?= fa_image_tag('plus', ['class' => 'icon']); ?>
            <span><?= __('Create user'); ?></span>
        </button>
    </div>
<?php elseif ($current_route == 'configure_groups'): ?>
    <div class="action-container">
        <a class="button secondary" href="<?= make_url('configure_users'); ?>">
            <?= fa_image_tag('users', ['class' => 'icon']); ?>
            <span><?= __('Manage users'); ?></span>
        </a>
        <a class="button secondary" href="<?= make_url('configure_roles'); ?>">
            <?= fa_image_tag('user-tie', ['class' => 'icon']); ?>
            <span><?= __('Manage roles'); ?></span>
        </a>
    </div>
    <div class="action-container">
        <button type="button" class="button trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'edit_group']); ?>">
            <?= fa_image_tag('plus', ['class' => 'icon']); ?>
            <span><?= __('Create group'); ?></span>
        </button>
    </div>
<?php else: ?>
    <span class="version-container">v<?= \pachno\core\framework\Settings::getVersion(); ?></span>
    <div class="action-container">
        <a class="button secondary highlight" id="update_button" href="javascript:void(0);" onclick="Pachno.Config.updateCheck('<?php echo make_url('configure_update_check'); ?>');"><?php echo __('Check for updates'); ?></a>
    </div>
<?php endif; ?>
