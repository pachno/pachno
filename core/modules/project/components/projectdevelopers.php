<?php

/**
 * @var \pachno\core\entities\Project $project
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
    <div class="form-row">
        <h3>
            <span><?= __('Access from '); ?></span>
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