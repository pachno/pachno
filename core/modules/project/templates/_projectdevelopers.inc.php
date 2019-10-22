<?php

/**
 * @var \pachno\core\entities\Project $project
 */

?>
<?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
    <div class="project_save_container">
        <div class="button" onclick="$('add_people_to_project_container').toggle();"><?= __('Add people'); ?></div>
        <button class="button dropper"><?= __('More actions'); ?></button>
        <ul class="simple-list rounded_box white shadowed rightie popup_box more_actions_dropdown">
            <li><a href="javascript:void(0);" onclick="$('owned_by_change').up('td').down('label').toggleClassName('button-pressed');$('owned_by_change').toggle();"><?= __('Change / set project owner'); ?></a></li>
            <li><a href="javascript:void(0);" onclick="$('lead_by_change').up('td').down('label').toggleClassName('button-pressed');$('lead_by_change').toggle();"><?= __('Change / set project leader'); ?></a></li>
            <li><a href="javascript:void(0);" onclick="$('qa_by_change').up('td').down('label').toggleClassName('button-pressed');$('qa_by_change').toggle();"><?= __('Change / set project qa responsible'); ?></a></li>
        </ul>
    </div>
    <div class="rounded_box lightgrey" style="margin: 0 0 10px 0; width: 765px; padding: 5px 10px 5px 10px; display: none;" id="add_people_to_project_container">
        <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_project_find_assignee', array('project_id' => $project->getID())); ?>" method="post" onsubmit="Pachno.Project.findDevelopers('<?= make_url('configure_project_find_assignee', array('project_id' => $project->getID())); ?>');return false;" id="find_dev_form">
            <table style="width: 100%; margin-top: 3px;" cellpadding=0 cellspacing=0 id="find_user">
                <tr>
                    <td style="width: 200px; padding: 2px; text-align: left;"><label for="find_by"><?= __('Find team or user'); ?></label></td>
                    <td style="width: auto; padding: 2px;"><input type="text" name="find_by" id="find_by" value="" style="width: 100%;"></td>
                    <td style="width: 50px; padding: 2px; text-align: right;"><input type="submit" value="<?= __('Find'); ?>" style="width: 45px;"></td>
                </tr>
            </table>
        </form>
        <div style="padding: 10px 0 10px 0; display: none;" id="find_dev_indicator"><span style="float: left;"><?= image_tag('spinning_16.gif'); ?></span>&nbsp;<?= __('Please wait'); ?></div>
        <div id="find_dev_results">
            <div class="faded_out" style="padding: 4px;"><?= __('To add people to this project, enter the name of a user or team to search for it'); ?></div>
        </div>
    </div>
<?php endif; ?>
<h4><?= __('Project administration'); ?></h4>
<p class="faded_out" style="margin-bottom: 10px;">
    <?= __('These are the people in charge of different areas of the project. The project owner has total control over this project and can edit information, settings, and anything about it. The project leader does not have this power, but will be notified of anything happening in the project. The QA responsible role does not grant any special privileges, it is purely an informational setting.'); ?>
</p>
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
<div class="form-row">
    <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
        <div class="fancy-dropdown-container">
            <div class="fancy-dropdown">
                <label><?= __('Client'); ?></label>
                <span class="value"><?= __('No client assigned'); ?></span>
                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                <div class="dropdown-container list-mode">
                    <?php if (count(\pachno\core\entities\Client::getAll())): ?>
                        <input type="radio" class="fancy-checkbox" id="client_id_checkbox_0" name="client_id" value="0" <?php if (!$project->hasClient()) echo 'checked'; ?>>
                        <label for="client_id_checkbox_0" class="list-item">
                            <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                            <span class="name value"><?= __('No client assigned'); ?></span>
                        </label>
                        <?php foreach (\pachno\core\entities\Client::getAll() as $client): ?>
                            <input type="radio" class="fancy-checkbox" id="client_id_checkbox_<?= $client->getID(); ?>" name="client_id" value="<?= $client->getID(); ?>" <?php if ($project->hasClient() && $project->getClient()->getID() == $client->getID()) echo 'checked'; ?>>
                            <label for="client_id_checkbox_<?= $client->getID(); ?>" class="list-item">
                                <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                <span class="name value"><?= $client->getName(); ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <input type="radio" class="fancy-checkbox" id="client_id_checkbox_0" name="client_id" value="0">
                        <label for="client_id_checkbox_0" class="list-item disabled">
                            <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                            <span class="name value"><?= __('No clients exist'); ?></span>
                        </label>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php if ($project->getClient() == null): echo __('No client'); else: echo $project->getClient()->getName(); endif; ?>
        <label for="client"><?= __('Client'); ?></label>
    <?php endif; ?>
</div>
