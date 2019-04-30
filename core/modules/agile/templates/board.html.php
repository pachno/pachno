<?php

    use pachno\core\entities\AgileBoard;

    $pachno_response->addBreadcrumb(__('Planning'), make_url('agile_board', array('project_key' => $selected_project->getKey(), 'board_id' => $board->getId())));
    $pachno_response->setTitle(__('"%project_name" project planning', array('%project_name' => $selected_project->getName())));

    switch ($board->getType())
    {
        case AgileBoard::TYPE_GENERIC:
        case AgileBoard::TYPE_KANBAN:
            $newmilestonelabel = __('New milestone');
            $togglemilestoneslabel = __('Toggle hidden milestones');
            $no_milestones_header = __('Organize what needs to be done with milestones');
            $no_milestones_onboarding_text = __('Plan, prioritize and execute with confidence');
            break;
        case AgileBoard::TYPE_SCRUM:
            $newmilestonelabel = __('Add new sprint');
            $togglemilestoneslabel = __('Toggle hidden sprints');
            $no_milestones_header = __('Sprints are the cornerstones of agile deliveries');
            $no_milestones_onboarding_text = __('Plan, prioritize and execute with confidence');
            break;
    }

?>
<div class="content-with-sidebar">
    <?php include_component('project/sidebar', ['dashboard' => __('Releases'), 'collapsed' => true]); ?>
    <div id="project_planning" class="board-backlog-container <?php if ($board->getType() == AgileBoard::TYPE_GENERIC) echo 'type-generic'; if ($board->getType() == AgileBoard::TYPE_SCRUM) echo 'type-scrum'; if ($board->getType() == AgileBoard::TYPE_KANBAN) echo 'type-kanban'; ?>" data-last-refreshed="<?php echo time(); ?>" data-poll-url="<?php echo make_url('agile_poll', array('project_key' => $selected_project->getKey(), 'board_id' => $board->getID(), 'mode' => 'planning')); ?>" data-retrieve-issue-url="<?php echo make_url('agile_retrieveissue', array('project_key' => $selected_project->getKey(), 'board_id' => $board->getID(), 'mode' => 'planning')); ?>" data-board-id="<?php echo $board->getID(); ?>">
        <div class="planning_indicator" id="planning_indicator">
            <?php echo image_tag('spinning_30.gif'); ?>
            <div class="milestone_percentage" id="planning_loading_progress_indicator">
                <div class="filler" id="planning_percentage_filler" style="width: 5%;"></div>
            </div>
        </div>
        <div class="top-search-filters-container" id="project_planning_action_strip">
            <div class="header">
                <div class="fancytabs">
                    <a class="tab selected" href="<?= make_url('agile_board', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>">
                        <span class="icon"><?= fa_image_tag('stream'); ?></span>
                        <span class="name"><?= ($board->getType() == AgileBoard::TYPE_GENERIC) ? __('Planning') : __('Backlog'); ?></span>
                    </a>
                    <a class="tab" href="<?= make_url('agile_whiteboard', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>">
                        <span class="icon"><?= fa_image_tag('columns'); ?></span>
                        <span class="name"><?= __('Whiteboard'); ?></span>
                    </a>
                </div>
            </div>
            <div class="search-and-filters-strip">
                <div class="search-strip" id="project_planning_action_strip">
                    <input type="search" class="planning_filter_title" id="planning_filter_title_input" disabled placeholder="<?php echo __('Filter issues by title'); ?>">
                    <?php if ($board->getProject()->isBuildsEnabled()): ?>
                        <a class="button" id="releases_toggler_button" href="javascript:void(0);" onclick="$(this).toggleClassName('button-pressed');$('builds-list').toggleClassName('expanded');"><?php echo __('Releases'); ?></a>
                    <?php endif; ?>
                    <?php if ($board->getEpicIssuetypeID()): ?>
                        <button class="button" id="epics_toggler_button" onclick="$(this).toggleClassName('button-pressed');$('epics-list').toggleClassName('expanded');" disabled><?php echo __('Epics'); ?></button>
                    <?php endif; ?>
                    <?php echo javascript_link_tag($newmilestonelabel, array('class' => 'button', 'onclick' => "Pachno.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'agilemilestone', 'project_id' => $board->getProject()->getId(), 'board_id' => $board->getID()))."');")); ?>
                    <?php echo image_tag('spinning_16.gif', array('id' => 'retrieve_indicator', 'class' => 'indicator', 'style' => 'display: none;')); ?>
                    <?php if ($pachno_user->canManageProjectReleases($selected_project)): ?>
                        <div class="dropper-container">
                            <?php echo fa_image_tag('cog', array('class' => 'dropper', 'id' => 'planning_board_settings_gear')); ?>
                            <div class="dropdown-container">
                                <div class="list-mode">
                                    <a href="javascript:void(0);" class="list-item" onclick="Pachno.Project.Planning.toggleMilestoneSorting();">
                                        <span class="icon"><?= fa_image_tag('sort'); ?></span>
                                        <span class="name"><?= __('Sort milestones'); ?></span>
                                    </a>
                                    <div class="list-item separator"></div>
                                    <input type="checkbox" class="fancycheckbox" name="show_closed_issues" id="board-show-closed-issues-checkbox" onchange="Pachno.Project.Planning.toggleClosedIssues();">
                                    <label for="board-show-closed-issues-checkbox" class="list-item toggler">
                                        <span class="icon"><?= fa_image_tag('toggle-on', ['class' => 'checked']) . fa_image_tag('toggle-off', ['class' => 'unchecked']); ?></span>
                                        <span class="name"><?= __('Show closed issues'); ?></span>
                                    </label>
                                    <input type="checkbox" class="fancycheckbox" name="show_hidden_milestones" id="board-show-hidden-milestones-checkbox" onchange="$('planning_container').toggleClassName('show-unavailable');Pachno.Main.Profile.clearPopupsAndButtons();">
                                    <label for="board-show-hidden-milestones-checkbox" class="list-item toggler">
                                        <span class="icon"><?= fa_image_tag('toggle-on', ['class' => 'checked']) . fa_image_tag('toggle-off', ['class' => 'unchecked']); ?></span>
                                        <span class="name"><?= $togglemilestoneslabel; ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="project_save_container" id="milestone-sort-actions">
                    <button class="button" id="milestone_sort_toggler_button" onclick="Pachno.Project.Planning.toggleMilestoneSorting();"><?php echo __('Done sorting'); ?></button>
                </div>
            </div>
        </div>
        <div class="planning-container" id="planning_container">
            <?php if ($board->getProject()->isBuildsEnabled()): ?>
                <ul id="builds-list" data-releases-url="<?php echo make_url('agile_getreleases', array('project_key' => $selected_project->getKey(), 'board_id' => $board->getID())); ?>"></ul>
            <?php endif; ?>
            <?php if ($board->getEpicIssuetypeID()): ?>
                <ul id="epics-list" data-epics-url="<?php echo make_url('agile_getepics', array('project_key' => $selected_project->getKey(), 'board_id' => $board->getID())); ?>"></ul>
            <?php endif; ?>
            <?php if ($pachno_user->canManageProjectReleases($selected_project)): ?>
                <table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="sprint_add_indicator">
                    <tr>
                        <td style="width: 20px; padding: 2px;"><?php echo image_tag('spinning_20.gif'); ?></td>
                        <td style="padding: 0px; text-align: left;"><?php echo __('Adding sprint, please wait'); ?>...</td>
                    </tr>
                </table>
            <?php endif; ?>
            <div id="milestones-list" class="milestones-list jsortable" data-sort-url="<?php echo make_url('project_sort_milestones', array('project_key' => $selected_project->getKey())); ?>">
                <?php foreach ($board->getMilestones() as $milestone): ?>
                    <?php include_component('agile/milestonebox', array('milestone' => $milestone, 'board' => $board, 'include_counts' => !$milestone->isVisibleRoadmap())); ?>
                <?php endforeach; ?>
            </div>
            <div id="no_milestones" style="<?php if (isset($milestone)) echo 'display: none;'; ?>">
                <?= image_tag('/unthemed/navigation/turn.png', ['id' => 'indicate-button'], true); ?>
                <div class="onboarding large">
                    <div class="image-container"><?= image_tag('/unthemed/backlog-no-milestones.png', [], true); ?></div>
                    <div class="helper-text">
                        <?= $no_milestones_header; ?><br>
                        <?= $no_milestones_onboarding_text; ?>
                    </div>
                </div>
            </div>
            <div id="board-backlog-container">
                <div id="milestone_0" class="milestone-box backlog open available backlog_milestone" data-milestone-id="0" data-issues-url="<?php echo make_url('agile_milestoneissues', array('project_key' => $board->getProject()->getKey(), 'milestone_id' => 0, 'board_id' => $board->getID())); ?>" data-assign-issue-url="<?php echo make_url('agile_assignmilestone', array('project_key' => $board->getProject()->getKey(), 'milestone_id' => 0)); ?>" data-backlog-search="<?php echo ($board->usesAutogeneratedSearchBacklog()) ? 'predefined_'.$board->getAutogeneratedSearch() : 'saved_'.$board->getBacklogSearchObject()->getID(); ?>">
                    <div class="planning_indicator" id="milestone_0_indicator" style="display: none;"><?php echo image_tag('spinning_30.gif'); ?></div>
                    <div class="header backlog" id="milestone_0_header">
                        <div class="main-details">
                            <div class="name-container">
                                <span class="name"><?php echo __('Backlog'); ?></span>
                            </div>
                        </div>
                        <div class="milestone-counts-container">
                            <div class="count">
                                <span id="milestone_0_issues_count">-</span>
                                <span><?php echo __('Issues'); ?></span>
                            </div>
                            <div class="count">
                                <span id="milestone_0_points_count" class="issue_estimates estimated_points">-</span>
                                <span class="issue_estimates estimated_points"><?php echo __('Points'); ?></span>
                            </div>
                            <div class="count">
                                <span id="milestone_0_hours_count" class="issue_estimates estimated_hours">-</span>
                                <span class="issue_estimates estimated_hours"><?php echo __('Hours'); ?></span>
                            </div>
                        </div>
                        <?php echo image_tag('spinning_20.gif', array('id' => 'milestone_0_issues_indicator', 'class' => 'milestone_issues_indicator', 'style' => 'display: none;')); ?>
                    </div>
                    <div id="milestone_0_issues" class="milestone-issues jsortable intersortable empty collapsed"></div>
                    <div class="milestone_no_issues" style="display: none;" id="milestone_0_unassigned"><?php echo __('There are no issues in the backlog'); ?></div>
                    <div class="milestone_no_issues" style="display: none;" id="milestone_0_unassigned_filtered"><?php echo __('No issues in the backlog matches selected filters'); ?></div>
                    <div class="milestone_error_issues" style="display: none;" id="milestone_0_initialize_error"><?php echo __('The issue list could not be loaded'); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($pachno_user->isPlanningTutorialEnabled()): ?>
    <?php include_component('main/tutorial_planning', compact('board')); ?>
<?php endif; ?>
<script type="text/javascript">
    require(['domReady', 'pachno/index'], function (domReady, Pachno) {
        domReady(function () {
            Pachno.Project.Planning.initialize({dragdrop: <?php echo ($pachno_user->canAssignScrumUserStories($selected_project)) ? 'true' : 'false'; ?>});
        });
    });
</script>
