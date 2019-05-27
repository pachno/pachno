<?php $pachno_response->setTitle(__('Configure roles')); ?>
<div class="content-with-sidebar">
    <?php include_component('leftmenu', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_ROLES]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
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
            <h3>
                <span><?php echo __('Globally available roles'); ?></span>
                <button class="button" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_role']); ?>');"><?= __('Create role'); ?></button>
            </h3>
            <div id="global_roles_list" class="flexible-table">
                <div class="row header">
                    <div class="column header name-container"><?= __('Role name'); ?></div>
                    <div class="column header numeric"><?= __('User(s)'); ?></div>
                    <div class="column header"><?= __('Permission(s)'); ?></div>
                    <div class="column header actions"></div>
                </div>
                <?php foreach ($roles as $role): ?>
                    <?php include_component('configuration/role', array('role' => $role)); ?>
                <?php endforeach; ?>
                <li class="faded_out no_roles" id="global_roles_no_roles"<?php if (count($roles)): ?> style="display: none;"<?php endif; ?>><?php echo __('There are no globally available roles'); ?></li>
            </div>
        </div>
    </div>
</div>
