<div class="form-container">
    <div class="form-row">
        <h3>
            <span><?php echo __('Project roles'); ?></span>
            <button class="button secondary" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_role']); ?>');">
                <?= fa_image_tag('user-shield', ['class' => 'icon']); ?>
                <span><?= __('Create role'); ?></span>
            </button>
        </h3>
        <div class="helper-text">
            <div class="image-container"><?= image_tag('/unthemed/onboarding_configure_roles_icon.png', [], true); ?></div>
            <span class="description">
                <?php echo __("Roles are great for defining permission groups that can be granted to specific projects, or give users access to update and edit information around Pachno. Read about roles and permissions in the %online_documentation to learn more about how to create, apply and manage roles.", array('%online_documentation' => link_tag('https://projects.pachno.com/pachno/docs/RolesAndPermissions', '<b>'.__('online documentation').'</b>'))); ?>
            </span>
        </div>
    </div>
</div>
<div class="flexible-table">
    <div class="row header">
        <div class="column header name-container"><?= __('Role name'); ?></div>
        <div class="column header numeric"><?= __('User(s)'); ?></div>
        <div class="column header"><?= __('Permission(s)'); ?></div>
        <div class="column header actions"></div>
    </div>
    <?php foreach ($project_roles as $role): ?>
        <?php include_component('configuration/role', ['role' => $role]); ?>
    <?php endforeach; ?>
    <?php if (!count($project_roles)): ?>
        <div class="row disabled">
            <div class="column name-container"><?= __('There are no project-specific roles'); ?></div>
        </div>
    <?php endif; ?>
    <div class="row header">
        <div class="column header name-container"><?= __('Global roles'); ?></div>
    </div>
</div>
<div class="flexible-table" id="global_roles_list">
    <?php foreach ($roles as $role): ?>
        <?php include_component('configuration/role', ['role' => $role, 'global' => true]); ?>
    <?php endforeach; ?>
</div>
