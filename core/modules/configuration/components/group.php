<?php

    use pachno\core\entities\Group;
    use pachno\core\framework\Settings;

    /**
     * @var Group $group
     */

?>
<div class="row" id="configure-group-<?php echo $group->getID(); ?>" data-group data-group-id="<?= $group->getID(); ?>">
    <div class="column info-icons"><?= fa_image_tag('users'); ?></div>
    <div class="column name-container">
        <span><?php echo $group->getName(); ?></span>
        <?php if ($group->isDefaultUserGroup()): ?>
            <span class="count-badge"><?= __('Default user group'); ?></span>
        <?php endif; ?>
    </div>
    <div class="column numeric"><span class="count-badge"><?= $group->getNumberOfMembers(); ?></span></div>
    <div class="column actions">
        <div class="dropper-container">
            <button class="dropper button secondary">
                <span><?= __('Actions'); ?></span>
                <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
            </button>
            <div class="dropdown-container">
                <div class="list-mode">
                    <a href="javascript:void(0);" class="list-item trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'edit_group', 'group_id' => $group->getId()]); ?>">
                        <?= fa_image_tag('edit', ['class' => 'icon']); ?>
                        <span class="name"><?php echo __('Edit'); ?></span>
                    </a>
                    <div class="list-item disabled" data-url="<?= make_url('configure_users_clone_group', ['group_id' => $group->getID()]); ?>">
                        <?= fa_image_tag('copy', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Copy this group'); ?></span>
                    </div>
                    <div class="list-item separator"></div>
                    <?php if (!$group->isDefaultUserGroup() && !in_array($group->getID(), Settings::getDefaultGroupIDs())): ?>
                        <a href="javascript:void(0);" onclick="Pachno.UI.Dialog.show('<?= __('Do you really want to delete this group?'); ?>', '<?= __('If you delete this group, then all users in this group will be disabled until moved to a different group'); ?>', {yes: {click: function() { Pachno.trigger(Pachno.EVENTS.group.delete, { url: '<?= make_url('configure_users_delete_group', ['group_id' => $group->getID()]); ?>', group_id: <?= $group->getID(); ?> }); }}, no: { click: Pachno.UI.Dialog.dismiss }});" class="list-item danger">
                            <?= fa_image_tag('times', ['class' => 'icon']); ?>
                            <span><?= __('Delete'); ?></span>
                        </a>
                    <?php else: ?>
                        <div class="list-item disabled" title="<?php echo __('The default group cannot be deleted'); ?>">
                            <span class="name"><?php echo __('The default group cannot be deleted'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
