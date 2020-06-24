<?php if ($role instanceof \pachno\core\entities\Role): ?>
    <div class="row tooltip-container" id="role_<?php echo $role->getID(); ?>_container">
        <div class="column name-container">
            <?php if (isset($global)): ?>
                <div class="tooltip">
                    <?= __('This global role applies to any user with this role on all projects'); ?>
                </div>
                <?= fa_image_tag('globe', ['class' => 'icon']); ?>
            <?php endif; ?>
            <span class="name"><?php echo $role->getName(); ?></span>
        </div>
        <div class="column numeric">
            <span class="count-badge"><?= fa_image_tag('user'); ?><span><?= $role->getNumberOfRoleUsers(); ?></span></span>
        </div>
        <div class="column">
            <span class="count-badge"><?php echo __('%number_of_permissions permission(s)', array('%number_of_permissions' => '<span id="role_'.$role->getID().'_permissions_count">'.count($role->getPermissions()).'</span>')); ?></span>
        </div>
        <div class="column actions">
            <div class="dropper-container">
                <button class="dropper button secondary">
                    <span><?= __('Actions'); ?></span>
                    <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
                </button>
                <div class="dropdown-container">
                    <div class="list-mode">
                        <a href="javascript:void(0);" class="list-item" onclick="$('#role_<?= $role->getID(); ?>_permissions_list').toggle();">
                            <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Details'); ?></span>
                        </a>
                        <?php if (!\pachno\core\framework\Context::isProjectContext() || !$role->isSystemRole()): ?>
                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_role', 'role_id' => $role->getId()]); ?>');">
                                <?= fa_image_tag('edit', ['class' => 'icon']); ?>
                                <span class="name"><?php echo __('Edit'); ?></span>
                            </a>
                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.UI.Dialog.show('<?php echo __('Delete this role?'); ?>', '<?php echo __('Do you really want to delete this role?').'<br>'.__('Users assigned via this role will be unassigned, and depending on other roles their project permissions may be reset.').'<br><b>'.__('This action cannot be reverted').'</b>'; ?>', {yes: {click: function() {Pachno.Config.Roles.remove('<?php echo make_url('configure_role', array('role_id' => $role->getID(), 'mode' => 'delete')); ?>', <?php print $role->getID(); ?>);}}, no: {click: Pachno.UI.Dialog.dismiss}});">
                                <?= fa_image_tag('times', ['class' => 'icon']); ?>
                                <span class="name"><?php echo __('Delete'); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="role_<?php echo $role->getID(); ?>_permissions_list" style="display: none;">
        <?php include_component('configuration/rolepermissionslist', ['role' => $role]); ?>
    </div>
<?php endif; ?>
