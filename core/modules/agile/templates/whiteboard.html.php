<?php

    use pachno\core\entities\AgileBoard;
    $pachno_response->addBreadcrumb(__('Planning'), make_url('agile_whiteboard', array('project_key' => $selected_project->getKey(), 'board_id' => $board->getId())));
    $pachno_response->setTitle(__('"%project_name" agile whiteboard', array('%project_name' => $selected_project->getName())));

?>
<div class="content-with-sidebar">
    <?php include_component('project/sidebar', ['dashboard' => __('Releases')]); ?>
    <div id="project_planning" class="project_info_container boards-container whiteboard <?php if ($board->getType() == AgileBoard::TYPE_GENERIC) echo 'type-generic'; if ($board->getType() == AgileBoard::TYPE_SCRUM) echo 'type-scrum'; if ($board->getType() == AgileBoard::TYPE_KANBAN) echo 'type-kanban'; ?>" data-last-refreshed="<?php echo time(); ?>" data-poll-url="<?php echo make_url('agile_poll', array('project_key' => $selected_project->getKey(), 'board_id' => $board->getID(), 'mode' => 'whiteboard')); ?>" data-retrieve-issue-url="<?php echo make_url('agile_retrieveissue', array('project_key' => $selected_project->getKey(), 'board_id' => $board->getID(), 'mode' => 'whiteboard')); ?>" data-board-id="<?php echo $board->getID(); ?>">
        <div class="planning_indicator" id="planning_indicator"><?php echo image_tag('spinning_30.gif'); ?></div>
        <h3>
            <span class="name"><?= $board->getName(); ?></span>
        </h3>
        <div class="top-search-filters-container" id="project_planning_action_strip">
            <div class="search-and-filters-strip">
                <div class="search-strip">
                    <div class="fancydropdown-container filter">
                        <div class="fancydropdown">
                            <label><?= __('Milestone'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode from-left" id="selected_milestone_input" data-status-url="<?php echo make_url('agile_whiteboardmilestonestatus', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID(), 'mode' => 'getmilestonestatus')); ?>">
                                <?php foreach ($board->getMilestones() as $milestone): ?>
                                    <input type="radio" name="selected_milestone" id="selected_milestone_<?= $milestone->getId(); ?>" class="fancycheckbox" <?php if ($selected_milestone instanceof \pachno\core\entities\Milestone && $selected_milestone->getID() == $milestone->getID()) echo 'checked'; ?>>
                                    <label class="list-item multiline" for="selected_milestone_<?= $milestone->getId(); ?>" data-board-value="<?php echo $board->getID(); ?>" onclick="window.location='#<?php echo $milestone->getID(); ?>';">
                                        <span class="icon"><?php echo image_tag('icon_milestone_issues.png'); ?></span>
                                        <span class="name">
                                            <span class="title value"><?php echo $milestone->getName(); ?></span>
                                            <span class="description">
                                                <span><?php echo __('Start date'); ?></span>
                                                <span><?php echo ($milestone->getStartingDate()) ? \pachno\core\framework\Context::getI18n()->formatTime($milestone->getStartingDate(), 22, true, true) : '-'; ?></span>
                                                <span><?php echo __('End date'); ?></span>
                                                <span><?php echo ($milestone->getScheduledDate()) ? \pachno\core\framework\Context::getI18n()->formatTime($milestone->getScheduledDate(), 22, true, true) : '-'; ?></span>
                                            </span>
                                        </dl>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <input type="search" class="planning_filter_title" id="planning_filter_title_input" disabled placeholder="<?php echo __('Filter issues by title'); ?>">
                    <?php if ($pachno_user->canManageProject($selected_project)): ?>
                        <div class="edit-mode-buttons">
                            <?php if (count($board->getColumns())): ?>
                                <a class="button" href="javascript:void(0);" onclick="Pachno.Project.Planning.Whiteboard.toggleEditMode();"><?php echo __('Cancel'); ?></a>
                            <?php endif; ?>
                            <a class="button" href="javascript:void(0);" onclick="Pachno.Project.Planning.Whiteboard.addColumn(this);" data-url="<?php echo make_url('agile_whiteboardcolumn', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>"><?php echo __('Add column'); ?></a>
                            <a class="button" href="javascript:void(0);" onclick="Pachno.Project.Planning.Whiteboard.saveColumns($('planning_whiteboard_columns_form'));"><?php echo __('Save columns'); ?></a>
                        </div>
                    <?php endif; ?>
                    <a class="button secondary icon" href="javascript:void(0);" onclick="$('main_container').toggleClassName('fullscreen');"><?php echo fa_image_tag('arrows-alt'); ?></a>
                    <div class="dropper-container">
                        <button class="button secondary icon dropper"><?= fa_image_tag('stream'); ?></button>
                        <div class="dropdown-container">
                            <div class="list-mode">
                                <input type="radio" class="fancycheckbox" name="card_mode" value="simple" checked id="card_mode_simple">
                                <label for="card_mode_simple" class="list-item" onclick="Pachno.Project.Planning.Whiteboard.setViewMode(this, 'simple');">
                                    <span class="icon"><?php echo fa_image_tag('list'); ?></span>
                                    <span class="name"><?= __('Summary card view'); ?></span>
                                </label>
                                <input type="radio" class="fancycheckbox" name="card_mode" value="detailed" checked id="card_mode_detailed">
                                <label for="card_mode_detailed" class="list-item" onclick="Pachno.Project.Planning.Whiteboard.setViewMode(this, 'detailed');">
                                    <span class="icon"><?php echo fa_image_tag('th-list'); ?></span>
                                    <span class="name"><?= __('Detailed card view'); ?></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="planning_whiteboard">
            <div class="planning_indicator" id="whiteboard_indicator"><?php echo image_tag('spinning_30.gif'); ?></div>
            <?php if ($pachno_user->canManageProject($selected_project)): ?>
                <form id="planning_whiteboard_columns_form" onsubmit="Pachno.Project.Planning.Whiteboard.saveColumns(this);return false;" action="<?php echo make_url('agile_whiteboard', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>">
                    <table class="whiteboard-columns">
                        <tr id="planning_whiteboard_columns_form_row">
                            <?php foreach ($board->getColumns() as $column): ?>
                                <?php include_component('agile/editboardcolumn', compact('column')); ?>
                            <?php endforeach; ?>
                        </tr>
                    </table>
                </form>
            <?php endif; ?>
            <div class="table whiteboard-columns <?php echo ($board->usesSwimlanes()) ? ' swimlanes' : ' no-swimlanes'; ?>" id="whiteboard" data-whiteboard-url="<?php echo make_url('agile_whiteboardissues', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>" data-swimlane-type="<?php echo $board->getSwimlaneType(); ?>">
                <div class="thead" id="whiteboard-headers-placeholder">
                    <div class="tr">
                        <?php foreach ($board->getColumns() as $column): ?>
                            <div class="td">&nbsp;</div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php if (count($board->getColumns())): ?>
                    <div class="thead" id="whiteboard-headers">
                        <div class="tr">
                            <?php foreach ($board->getColumns() as $column): ?>
                                <?php include_component('agile/boardcolumnheader', compact('column')); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php /* <div class="project_left_container" id="project_backlog_sidebar">
            <div class="project_left">
                <div id="milestone_0" class="milestone_box" data-milestone-id="0" data-issues-url="<?php echo make_url('agile_milestoneissues', array('project_key' => $board->getProject()->getKey(), 'milestone_id' => 0, 'board_id' => $board->getID())); ?>" data-assign-issue-url="<?php echo make_url('agile_assignmilestone', array('project_key' => $board->getProject()->getKey(), 'milestone_id' => 0)); ?>" data-backlog-search="<?php echo ($board->usesAutogeneratedSearchBacklog()) ? 'predefined_'.$board->getAutogeneratedSearch() : 'saved_'.$board->getBacklogSearchObject()->getID(); ?>">
                    <div class="planning_indicator" id="milestone_0_indicator" style="display: none;"><?php echo image_tag('spinning_30.gif'); ?></div>
                    <div class="header backlog" id="milestone_0_header">
                        <div class="milestone_basic_container">
                            <span class="milestone_name"><?php echo __('Backlog'); ?></span>
                            <div class="backlog_toggler dynamic_menu_link" onclick="$('project_planning').toggleClassName('left_toggled');" title="<?php echo __('Click to toggle the show / hide the backlog'); ?>"><?php echo image_tag('icon_sidebar_collapse.png'); ?></div>
                        </div>
                        <div class="milestone_counts_container">
                            <table>
                                <tr>
                                    <td id="milestone_0_issues_count">-</td>
                                    <td id="milestone_0_points_count" class="issue_estimates">-</td>
                                    <td id="milestone_0_hours_count" class="issue_estimates">-</td>
                                </tr>
                                <tr>
                                    <td><?php echo __('Issues'); ?></td>
                                    <td class="issue_estimates"><?php echo __('Points'); ?></td>
                                    <td class="issue_estimates"><?php echo __('Hours'); ?></td>
                                </tr>
                            </table>
                        </div>
                        <?php echo image_tag('spinning_20.gif', array('id' => 'milestone_0_issues_indicator', 'class' => 'milestone_issues_indicator', 'style' => 'display: none;')); ?>
                    </div>
                    <ul id="milestone_0_issues" class="milestone_issues jsortable intersortable <?php //if ($board->getBacklogSearchObject()->getTotalNumberOfIssues() == 0) echo 'empty'; ?>"></ul>
                    <div class="milestone_no_issues" style="display: none;" id="milestone_0_unassigned"><?php echo __('No issues are assigned to this milestone'); ?></div>
                    <div class="milestone_no_issues" style="display: none;" id="milestone_0_unassigned_filtered"><?php echo __('No issues assigned to this milestone matches selected filters'); ?></div>
                    <div class="milestone_error_issues" style="display: none;" id="milestone_0_initialize_error"><?php echo __('The issue list could not be loaded'); ?></div>
                </div>
            </div>
        </div> */ ?>
    </div>
</div>
<script type="text/javascript">
    require(['domReady', 'pachno/index'], function (domReady, Pachno) {
        domReady(function () {
            Pachno.Project.Planning.Whiteboard.initialize({dragdrop: <?php echo ($pachno_user->canAssignScrumUserStories($selected_project)) ? 'true' : 'false'; ?>});
        });
    });
</script>
<div id="moving_issue_workflow_transition"></div>
