<?php

    /**
     * @var \pachno\core\entities\Group $group
     */

?>
<div class="row" id="configure-group-<?php echo $group->getID(); ?>">
    <div class="column info-icons"><?= fa_image_tag('users'); ?></div>
    <div class="column name-container"><?php echo $group->getName(); ?></div>
    <div class="column numeric"><span class="count-badge"><?= $group->getNumberOfMembers(); ?></span></div>
    <div class="column actions">
        <div class="dropper-container">
            <button class="dropper button secondary">
                <span><?= __('Actions'); ?></span>
                <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
            </button>
            <div class="dropdown-container">
                <div class="list-mode">
                    <a href="javascript:void(0);" class="list-item" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_group', 'group_id' => $group->getId()]); ?>');">
                        <?= fa_image_tag('edit', ['class' => 'icon']); ?>
                        <span class="name"><?php echo __('Edit'); ?></span>
                    </a>
                    <div class="list-item disabled" data-url="<?= make_url('configure_users_clone_group', ['group_id' => $group->getID()]); ?>">
                        <?= fa_image_tag('copy'); ?>
                        <span class="name"><?= __('Copy this group'); ?></span>
                    </div>
                    <div class="list-item separator"></div>
                    <?php if (!$group->isDefaultUserGroup() && !in_array($group->getID(), \pachno\core\framework\Settings::getDefaultGroupIDs())): ?>
                        <?php echo javascript_link_tag(__('Delete this user group'), array('onclick' => "Pachno.UI.Dialog.show('".__('Do you really want to delete this group?')."', '".__('If you delete this group, then all users in this group will be disabled until moved to a different group')."', {yes: {click: function() {Pachno.Config.Group.remove('".make_url('configure_users_delete_group', array('group_id' => $group->getID()))."', {$group->getID()}); }}, no: { click: Pachno.UI.Dialog.dismiss }});", 'class' => 'list-item')); ?>
                    <?php else: ?>
                        <div class="list-item disabled" title="<?php echo __('The default group cannot be deleted'); ?>"><span class="name"><?php echo __('Delete this user group'); ?></span></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
