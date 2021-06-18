<?php

    use pachno\core\framework\Context;
    use pachno\core\framework\Event;
    use pachno\core\entities\Role;

    /**
     * @var Role $role
     * @var string $form_url
     */

?>
<div class="backdrop_box large edit_agileboard">
    <div class="backdrop_detail_header">
        <span><?php echo ($role->getId()) ? __('Edit role') : __('Create new role'); ?></span>
        <a href="javascript:void(0);" class="closer"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form action="<?php echo $form_url; ?>" id="edit_role_form" method="post" data-simple-submit data-auto-close <?php if (!$role->getID()): ?> data-update-container="#<?= ($role->getProjectId()) ? 'project_roles_list' : 'global_roles_list'; ?>"<?php else: ?> data-update-container="#role_<?= $role->getID(); ?>_container" data-update-replace<?php endif; ?>>
                <div class="form-row">
                    <input type="text" id="role_<?php echo $role->getID(); ?>_name" name="name" value="<?php echo __e($role->getName()); ?>" class="name-input-enhance">
                    <label style for="role_<?php echo $role->getID(); ?>_name"><?php echo __('Role name'); ?></label>
                    <div class="helper-text"><?php echo __('Enter the name of the role, and select permissions inherited by users or teams assigned with this role from the list below'); ?></div>
                </div>
                <input type="hidden" name="project_id" value="<?= $role->getProjectId(); ?>">
                <h3><?php echo __('Role permissions'); ?></h3>
                <div class="form-row">
                    <div class="list-mode">
                        <div class="interactive_menu_values filter_existing_values">
                            <?php include_component('configuration/rolepermissionseditlist', array('role' => $role, 'permissions_list' => Context::getAvailablePermissions('project'), 'module' => 'core', 'target_id' => null)); ?>
                            <?php include_component('configuration/rolepermissionseditlist', array('role' => $role, 'permissions_list' => Context::getAvailablePermissions('issues'), 'module' => 'core', 'target_id' => null)); ?>
                            <?php Event::createNew('core', 'rolepermissionsedit', $role)->trigger(); ?>
                        </div>
                    </div>
                </div>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary">
                        <?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin icon']); ?>
                        <span><?php echo __('Save role'); ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
