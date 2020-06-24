<div class="backdrop_box large edit_agileboard">
    <div class="backdrop_detail_header">
        <span><?php echo ($role->getId()) ? __('Edit role') : __('Create new role'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form action="<?php echo make_url('configure_role', array('role_id' => $role->getID(), 'mode' => 'edit')); ?>" id="role_<?php echo $role->getID(); ?>_form" method="post" onsubmit="Pachno.Config.Roles.update('<?php echo make_url('configure_role', array('role_id' => $role->getID(), 'mode' => 'edit')); ?>', <?php echo $role->getID(); ?>);return false;">
                <div class="form-row">
                    <input type="text" id="role_<?php echo $role->getID(); ?>_name" name="name" value="<?php echo htmlentities($role->getName(), ENT_COMPAT, \pachno\core\framework\Context::getI18n()->getCharset()); ?>" class="name-input-enhance">
                    <label style for="role_<?php echo $role->getID(); ?>_name"><?php echo __('Role name'); ?></label>
                    <div class="helper-text"><?php echo __('Enter the name of the role, and select permissions inherited by users or teams assigned with this role from the list below'); ?></div>
                </div>
                <h3><?php echo __('Role permissions'); ?></h3>
                <div class="form-row">
                    <div class="list-mode">
                        <div class="list-item filter-container">
                            <input type="search" placeholder="<?= __('Filter available permissions'); ?>">
                        </div>
                        <div class="interactive_menu_values filter_existing_values">
                            <?php include_component('configuration/rolepermissionseditlist', array('role' => $role, 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('project'), 'module' => 'core', 'target_id' => null)); ?>
                            <?php //include_component('configuration/rolepermissionseditlist', array('role' => $role, 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('project_pages'), 'module' => 'core', 'target_id' => null)); ?>
                            <?php include_component('configuration/rolepermissionseditlist', array('role' => $role, 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('issues'), 'module' => 'core', 'target_id' => null)); ?>
                            <?php \pachno\core\framework\Event::createNew('core', 'rolepermissionsedit', $role)->trigger(); ?>
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
