<?php $pachno_response->setTitle(__('Configure roles')); ?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_ROLES]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
            <h1><span><?php echo __('Configure roles'); ?></span></h1>
            <div class="helper-text">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_configure_roles_icon.png', [], true); ?></div>
                <span class="description">
                    <?php echo __("Roles are great for defining permission groups that can be granted to specific projects, or give users access to update and edit information around Pachno. Read about roles and permissions in the %online_documentation to learn more about how to create, apply and manage roles.", array('%online_documentation' => link_tag('https://projects.pach.no/pachno/docs/RolesAndPermissions', '<b>'.__('online documentation').'</b>'))); ?>
                </span>
            </div>
            <h3><span><?php echo __('Globally available roles'); ?></span></h3>
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
                <div class="faded_out no_roles" id="global_roles_no_roles"<?php if (count($roles)): ?> style="display: none;"<?php endif; ?>><?php echo __('There are no globally available roles'); ?></div>
            </div>
        </div>
    </div>
</div>
