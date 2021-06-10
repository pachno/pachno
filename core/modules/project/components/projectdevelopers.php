<?php

/**
 * @var \pachno\core\entities\Project $project
 * @var \pachno\core\entities\Group $user_group
 */

?>
<div class="form-container">
    <div class="form-row">
        <h3>
            <span><?= __('Project team'); ?></span>
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <button class="button secondary trigger-backdrop" data-url="<?php echo make_url('get_partial_for_backdrop', ['key' => 'project_add_people', 'project_id' => $project->getID()]); ?>">
                    <?= fa_image_tag('user-plus', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Add people'); ?></span>
                </button>
            <?php endif; ?>
        </h3>
        <div class="helper-text">
            <div class="image-container"><?= image_tag('/unthemed/onboarding_project_team_icon.png', [], true); ?></div>
            <span class="description">
                <?= __('Invite team members to collaborate on the project. Assign roles to make sure everyone has access to the project, and to let everyone else know who is involved.'); ?>
            </span>
        </div>
    </div>
</div>
<div id="project_team_list" class="flexible-table">
    <div class="row header">
        <div class="column header name-container"><?= __('Name'); ?></div>
        <div class="column header role"><?= __('Role'); ?></div>
        <div class="column header actions"></div>
    </div>
    <div class="row">
        <div class="column name-container" id="project-owner-container">
            <?php if ($project->getOwner() instanceof \pachno\core\entities\User): ?>
                <?php include_component('main/userdropdown', ['user' => $project->getOwner(), 'size' => 'small']); ?>
            <?php elseif ($project->getOwner() instanceof \pachno\core\entities\Team): ?>
                <?php include_component('main/teamdropdown', ['team' => $project->getOwner(), 'size' => 'small']); ?>
            <?php else: ?>
                <?= __('No project owner assigned'); ?>
            <?php endif; ?>
        </div>
        <div class="column">
            <span class="count-badge"><?= __('Project owner'); ?></span>
        </div>
        <div class="column actions dropper-container">
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <button class="button dropper secondary icon"><?= fa_image_tag('ellipsis-v'); ?></button>
                <?php include_component('main/identifiableselector', [
                    'base_id'         => 'owned_by',
                    'header'          => __('Change / set owner'),
                    'clear_link_text' => __('Set owned by noone'),
                    'trigger_class'   => "trigger-set-project-owner",
                    'allow_clear'     => true,
                    'include_teams'   => true
                ]); ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="column name-container" id="project-lead-container">
            <?php if ($project->getLeader() instanceof \pachno\core\entities\User): ?>
                <?php include_component('main/userdropdown', ['user' => $project->getLeader(), 'size' => 'small']); ?>
            <?php elseif ($project->getLeader() instanceof \pachno\core\entities\Team): ?>
                <?php include_component('main/teamdropdown', ['team' => $project->getLeader(), 'size' => 'small']); ?>
            <?php else: ?>
                <?= __('No project leader assigned'); ?>
            <?php endif; ?>
        </div>
        <div class="column">
            <span class="count-badge"><?= __('Project leader'); ?></span>
        </div>
        <div class="column actions dropper-container">
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <button class="button dropper secondary icon"><?= fa_image_tag('ellipsis-v'); ?></button>
                <?php include_component('main/identifiableselector', [
                    'base_id'         => 'lead_by',
                    'header'          => __('Change / set leader'),
                    'clear_link_text' => __('Set lead by noone'),
                    'trigger_class'   => 'trigger-set-project-lead',
                    'allow_clear'     => true,
                    'include_teams'   => true
                ]); ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="column name-container" id="project-qa-container">
            <?php if ($project->getQaResponsible() instanceof \pachno\core\entities\User): ?>
                <?php include_component('main/userdropdown', ['user' => $project->getQaResponsible(), 'size' => 'small']); ?>
            <?php elseif ($project->getQaResponsible() instanceof \pachno\core\entities\Team): ?>
                <?php include_component('main/teamdropdown', ['team' => $project->getQaResponsible(), 'size' => 'small']); ?>
            <?php else: ?>
                <?= __('No QA responsible assigned'); ?>
            <?php endif; ?>
        </div>
        <div class="column">
            <span class="count-badge"><?= __('Project QA lead'); ?></span>
        </div>
        <div class="column actions dropper-container">
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <button class="button dropper secondary icon"><?= fa_image_tag('ellipsis-v'); ?></button>
                <?php include_component('main/identifiableselector', [
                    'base_id'         => 'qa_by',
                    'header'          => __('Change / set QA responsible'),
                    'clear_link_text' => __('Set QA responsible to noone'),
                    'trigger_class'   => 'trigger-set-project-qa',
                    'allow_clear'     => true,
                    'include_teams'   => true
                ]); ?>
            <?php endif; ?>
        </div>
    </div>
    <?php foreach ($project->getAssignedUsers() as $assignee): ?>
        <?php include_component('project/settings_project_assignee', ['assignee' => $assignee, 'project' => $project]); ?>
    <?php endforeach; ?>
    <?php foreach ($project->getAssignedTeams() as $assignee): ?>
        <?php include_component('project/settings_project_assignee', ['assignee' => $assignee, 'project' => $project]); ?>
    <?php endforeach; ?>
</div>
<div class="form-container">
    <div class="form-row">&nbsp;</div>
    <div class="form-row header">
        <h3><span><?= __('Access for other users'); ?></span></h3>
        <div class="helper-text">
            <div class="image-container"><?= image_tag('/unthemed/onboarding_project_other_users_access.png', [], true); ?></div>
            <span class="description">
                <?= __('Users without specific roles can still be granted access to this project. Use the settings below to tune access for users without a role in this project.'); ?>
            </span>
        </div>
    </div>
    <div class="list-mode">
        <div class="list-item multiline">
            <span class="icon"><?= fa_image_tag('boxes'); ?></span>
            <span class="name">
                <span class="title"><?= __('Limited read access to the project'); ?></span>
                <span class="description"><?= __('If allowed, users can see the project in the main project list, and access it'); ?></span>
            </span>
            <span class="button-group">
                <input type="checkbox" class="fancy-checkbox" data-interactive-toggle value="1" id="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_DASHBOARD; ?>" data-url="<?= make_url('configure_project_add_assignee', ['project_id' => $project->getId()]); ?>?permission=<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_DASHBOARD; ?>" <?php if ($user_group->hasPermission(\pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_DASHBOARD, $project->getID())) echo ' checked'; ?>>
                <label class="button secondary" for="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_DASHBOARD; ?>"><?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?><span class="checked"><?= __('Allowed'); ?></span><span class="unchecked"><?= __('Not allowed'); ?></span></label>
            </span>
        </div>
        <div class="list-item multiline expandable expanded">
            <span class="icon"><?= fa_image_tag('th-list'); ?></span>
            <span class="name">
                <span class="title"><?= __('Access project issues and roadmap'); ?></span>
                <span class="description"><?= __('If allowed, users can see issues reported in this project and access the project roadmap.'); ?></span>
            </span>
            <span class="button-group">
                <input type="checkbox" class="fancy-checkbox" data-interactive-toggle value="1" id="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_ISSUES; ?>" data-url="<?= make_url('configure_project_add_assignee', ['project_id' => $project->getId()]); ?>?permission=<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_ISSUES; ?>" <?php if ($user_group->hasPermission(\pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_ISSUES, $project->getID())) echo ' checked'; ?>>
                <label class="button secondary" for="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_ISSUES; ?>"><?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?><span class="checked"><?= __('Allowed'); ?></span><span class="unchecked"><?= __('Not allowed'); ?></span></label>
            </span>
        </div>
        <div class="submenu">
            <div class="list-item">
                <span class="icon"><?= fa_image_tag('plus-square'); ?></span>
                <span class="name"><?= __('Report new issues'); ?></span>
                <span class="button-group">
                    <input type="checkbox" class="fancy-checkbox" data-interactive-toggle value="1" id="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_CREATE_ISSUES; ?>" data-url="<?= make_url('configure_project_add_assignee', ['project_id' => $project->getId()]); ?>?permission=<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_CREATE_ISSUES; ?>" <?php if ($user_group->hasPermission(\pachno\core\entities\Permission::PERMISSION_PROJECT_CREATE_ISSUES, $project->getID())) echo ' checked'; ?>>
                    <label class="button secondary" for="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_CREATE_ISSUES; ?>"><?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?><span class="checked"><?= __('Allowed'); ?></span><span class="unchecked"><?= __('Not allowed'); ?></span></label>
                </span>
            </div>
            <div class="list-item">
                <span class="icon"><?= fa_image_tag('comment-medical'); ?></span>
                <span class="name"><?= __('Comment on issues'); ?></span>
                <span class="button-group">
                    <input type="checkbox" class="fancy-checkbox" data-interactive-toggle value="1" id="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_EDIT_ISSUES_COMMENTS; ?>" data-url="<?= make_url('configure_project_add_assignee', ['project_id' => $project->getId()]); ?>?permission=<?= \pachno\core\entities\Permission::PERMISSION_EDIT_ISSUES_COMMENTS; ?>" <?php if ($user_group->hasPermission(\pachno\core\entities\Permission::PERMISSION_EDIT_ISSUES_COMMENTS, $project->getID())) echo ' checked'; ?>>
                    <label class="button secondary" for="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_EDIT_ISSUES_COMMENTS; ?>"><?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?><span class="checked"><?= __('Allowed'); ?></span><span class="unchecked"><?= __('Not allowed'); ?></span></label>
                </span>
            </div>
        </div>
        <div class="list-item multiline">
            <span class="icon"><?= fa_image_tag('chalkboard'); ?></span>
            <span class="name">
                <span class="title"><?= __('Access public project boards'); ?></span>
                <span class="description"><?= __('If allowed, users can access public project boards and see issues across columns.'); ?></span>
            </span>
            <span class="button-group">
                <input type="checkbox" class="fancy-checkbox" data-interactive-toggle value="1" id="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_BOARDS; ?>" data-url="<?= make_url('configure_project_add_assignee', ['project_id' => $project->getId()]); ?>?permission=<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_BOARDS; ?>" <?php if ($user_group->hasPermission(\pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_BOARDS, $project->getID())) echo ' checked'; ?>>
                <label class="button secondary" for="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_BOARDS; ?>"><?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?><span class="checked"><?= __('Allowed'); ?></span><span class="unchecked"><?= __('Not allowed'); ?></span></label>
            </span>
        </div>
        <div class="list-item multiline expandable expanded">
            <span class="icon"><?= fa_image_tag('code'); ?></span>
            <span class="name">
                <span class="title"><?= __('Access project code'); ?></span>
                <span class="description"><?= __('If allowed, users can see project commits and discussions.'); ?></span>
            </span>
            <span class="button-group">
                <input type="checkbox" class="fancy-checkbox" data-interactive-toggle value="1" id="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_CODE; ?>" data-url="<?= make_url('configure_project_add_assignee', ['project_id' => $project->getId()]); ?>?permission=<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_CODE; ?>" <?php if ($user_group->hasPermission(\pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_CODE, $project->getID())) echo ' checked'; ?>>
                <label class="button secondary" for="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_CODE; ?>"><?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?><span class="checked"><?= __('Allowed'); ?></span><span class="unchecked"><?= __('Not allowed'); ?></span></label>
            </span>
        </div>
        <div class="submenu">
            <div class="list-item">
                <span class="icon"><?= fa_image_tag('comment-medical'); ?></span>
                <span class="name"><?= __('Participate in code discussions'); ?></span>
                <span class="button-group">
                    <input type="checkbox" class="fancy-checkbox" data-interactive-toggle value="1" id="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_DEVELOPER_DISCUSS_CODE; ?>" data-url="<?= make_url('configure_project_add_assignee', ['project_id' => $project->getId()]); ?>?permission=<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_DEVELOPER_DISCUSS_CODE; ?>" <?php if ($user_group->hasPermission(\pachno\core\entities\Permission::PERMISSION_PROJECT_DEVELOPER_DISCUSS_CODE, $project->getID())) echo ' checked'; ?>>
                    <label class="button secondary" for="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_DEVELOPER_DISCUSS_CODE; ?>"><?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?><span class="checked"><?= __('Allowed'); ?></span><span class="unchecked"><?= __('Not allowed'); ?></span></label>
                </span>
            </div>
        </div>
        <div class="list-item multiline expandable expanded">
            <span class="icon"><?= fa_image_tag('book'); ?></span>
            <span class="name">
                <span class="title"><?= __('Access project documentation'); ?></span>
                <span class="description"><?= __('If allowed, users can access project documentation, their attached resources and comments.'); ?></span>
            </span>
            <span class="button-group">
                <input type="checkbox" class="fancy-checkbox" data-interactive-toggle value="1" id="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_DOCUMENTATION; ?>" data-url="<?= make_url('configure_project_add_assignee', ['project_id' => $project->getId()]); ?>?permission=<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_DOCUMENTATION; ?>" <?php if ($user_group->hasPermission(\pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_DOCUMENTATION, $project->getID())) echo ' checked'; ?>>
                <label class="button secondary" for="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_ACCESS_DOCUMENTATION; ?>"><?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?><span class="checked"><?= __('Allowed'); ?></span><span class="unchecked"><?= __('Not allowed'); ?></span></label>
            </span>
        </div>
        <div class="submenu">
            <div class="list-item">
                <span class="icon"><?= fa_image_tag('comment-medical'); ?></span>
                <span class="name"><?= __('Comment on documentation'); ?></span>
                <span class="button-group">
                    <input type="checkbox" class="fancy-checkbox" data-interactive-toggle value="1" id="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_EDIT_DOCUMENTATION_POST_COMMENTS; ?>" data-url="<?= make_url('configure_project_add_assignee', ['project_id' => $project->getId()]); ?>?permission=<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_EDIT_DOCUMENTATION_POST_COMMENTS; ?>" <?php if ($user_group->hasPermission(\pachno\core\entities\Permission::PERMISSION_PROJECT_EDIT_DOCUMENTATION_POST_COMMENTS, $project->getID())) echo ' checked'; ?>>
                    <label class="button secondary" for="toggle_project_permissions_<?= \pachno\core\entities\Permission::PERMISSION_PROJECT_EDIT_DOCUMENTATION_POST_COMMENTS; ?>"><?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?><span class="checked"><?= __('Allowed'); ?></span><span class="unchecked"><?= __('Not allowed'); ?></span></label>
                </span>
            </div>
        </div>
        <div class="form-row">&nbsp;</div>
    </div>
</div>
<script>
    Pachno.on(Pachno.EVENTS.ready, () => {
        const $body = $('body');
        const setProjectAssignee = function (url, field, $link, $container) {
            const identifiable_type = $link.data('identifiable-type');
            const value = $link.data('identifiable-value');
            $container.html(Pachno.UI.fa_image_tag('spinner', { classes: 'fa-spin' }));

            Pachno.fetch(url, {
                method: 'POST',
                data: {
                    field,
                    identifiable_type,
                    value
                }
            }).then((json) => {
                $container.html(json.field.name);
            });
        }

        $body.on('click', '.trigger-set-project-qa', function (event) {
            event.preventDefault();

            const url = '<?= make_url('configure_project_set_leadby', ['project_id' => $project->getID()]); ?>';
            const $link = $(this);
            const $container = $('#project-qa-container');

            setProjectAssignee(url, 'qa_by', $link, $container);
        });

        $body.on('click', '.trigger-set-project-owner', function (event) {
            event.preventDefault();

            const url = '<?= make_url('configure_project_set_leadby', ['project_id' => $project->getID()]); ?>';
            const $link = $(this);
            const $container = $('#project-owner-container');

            setProjectAssignee(url, 'owned_by', $link, $container);
        });

        $body.on('click', '.trigger-set-project-lead', function (event) {
            event.preventDefault();

            const url = '<?= make_url('configure_project_set_leadby', ['project_id' => $project->getID()]); ?>';
            const $link = $(this);
            const $container = $('#project-lead-container');

            setProjectAssignee(url, 'lead_by', $link, $container);
        });
    });
</script>