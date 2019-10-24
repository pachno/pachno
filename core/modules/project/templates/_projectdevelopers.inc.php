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
                <button class="button secondary" onclick="Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', ['key' => 'project_add_people', 'project_id' => $project->getID()]); ?>');">
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
        <div class="column name-container">
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
                <?php include_component('main/identifiableselector', array(    'html_id'        => 'owned_by_change',
                    'header'             => __('Change / set owner'),
                    'clear_link_text'    => __('Set owned by noone'),
                    'style'                => array('position' => 'absolute'),
                    'callback'             => "Pachno.Project.setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'owned_by', 'identifiable_type' => 'user', 'value' => '%identifiable_value')) . "', 'owned_by');",
                    'team_callback'         => "Pachno.Project.setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'owned_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value')) . "', 'owned_by');",
                    'base_id'            => 'owned_by',
                    'absolute'            => true,
                    'hidden'            => false,
                    'classes'            => 'leftie',
                    'include_teams'        => true)); ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="column name-container">
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
                <?php include_component('main/identifiableselector', array(    'html_id'        => 'lead_by_change',
                    'header'             => __('Change / set leader'),
                    'clear_link_text'    => __('Set lead by noone'),
                    'style'                => array('position' => 'absolute'),
                    'callback'             => "Pachno.Project.setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'lead_by', 'identifiable_type' => 'user', 'value' => '%identifiable_value')) . "', 'lead_by');",
                    'team_callback'         => "Pachno.Project.setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'lead_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value')) . "', 'lead_by');",
                    'base_id'            => 'lead_by',
                    'absolute'            => true,
                    'hidden'            => false,
                    'classes'            => 'leftie',
                    'include_teams'        => true)); ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="column name-container">
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
                <?php include_component('main/identifiableselector', array(    'html_id'        => 'qa_by_change',
                    'header'             => __('Change / set QA responsible'),
                    'clear_link_text'    => __('Set QA responsible to noone'),
                    'style'                => array('position' => 'absolute'),
                    'callback'             => "Pachno.Project.setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'qa_by', 'identifiable_type' => 'user', 'value' => '%identifiable_value')) . "', 'qa_by');",
                    'team_callback'         => "Pachno.Project.setUser('" . make_url('configure_project_set_leadby', array('project_id' => $project->getID(), 'field' => 'qa_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value')) . "', 'qa_by');",
                    'base_id'            => 'qa_by',
                    'absolute'            => true,
                    'hidden'            => false,
                    'classes'            => 'leftie',
                    'include_teams'        => true)); ?>
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
