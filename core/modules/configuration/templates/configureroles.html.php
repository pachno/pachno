<?php $pachno_response->setTitle(__('Configure roles')); ?>
<div class="content-with-sidebar">
    <?php include_component('leftmenu', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_ROLES]); ?>
    <div class="configuration-container">
        <h1>
            <span><?php echo __('Configure roles'); ?></span>
            <div class="dropper-container">
                <a class="dropper button secondary icon"><?php echo fa_image_tag('ellipsis-v'); ?></a>
                <span class="dropdown-container">
                    <span class="list-mode">
                        <a href="<?= make_url('configure_permissions'); ?>" class="list-item">
                            <?= fa_image_tag('users', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Show advanced permissions'); ?></span>
                        </a>
                    </span>
                </span>
            </div>
        </h1>
        <div class="helper-text">
            <?php echo __("Roles are applied when assigning users or teams to a project, granting them access to specific parts of the project or giving users access to update and edit information. Updating permissions in this list will add or remove permissions for all users and / or team members with that role, on all assigned projects. Removing a role removes all permissions granted by that role for all users and teams. Read more about roles and permissions in the %online_documentation", array('%online_documentation' => link_tag('https://projects.pachno.com/pachno/docs/RolesAndPermissions', '<b>'.__('online documentation').'</b>'))); ?>
        </div>
        <div class="lightyellowbox" id="new_role" style="margin-top: 15px;">
            <form id="new_role_form" method="post" action="<?php echo make_url('configure_roles', array('mode' => 'new')); ?>" onsubmit="Pachno.Config.Roles.add('<?php echo make_url('configure_roles', array('mode' => 'new')); ?>'); return false;" accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>">
                <label for="new_project_role_name"><?php echo __('Add new role'); ?></label>
                <input type="text" style="width: 300px;" name="role_name" id="add_new_role_input">
                <div style="text-align: right; float: right;">
                    <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; margin: 3px 5px -4px;', 'id' => 'new_role_form_indicator')); ?>
                    <input type="submit" value="<?php echo __('Create role'); ?>" class="button">
                </div>
            </form>
        </div>
        <h5 style="margin-top: 10px;">
            <?php echo __('Globally available roles'); ?>
        </h5>
        <ul id="global_roles_list" class="simple-list">
            <?php foreach ($roles as $role): ?>
                <?php include_component('configuration/role', array('role' => $role)); ?>
            <?php endforeach; ?>
            <li class="faded_out no_roles" id="global_roles_no_roles"<?php if (count($roles)): ?> style="display: none;"<?php endif; ?>><?php echo __('There are no globally available roles'); ?></li>
        </ul>
    </div>
</div>
