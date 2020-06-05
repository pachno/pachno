<?php

    /** @var AgileBoard $board */
    use pachno\core\entities\AgileBoard;
    $pachno_response->addBreadcrumb(__('Planning'), make_url('agile_whiteboard', array('project_key' => $selected_project->getKey(), 'board_id' => $board->getId())));
    $pachno_response->setTitle(__('"%project_name" agile whiteboard', array('%project_name' => $selected_project->getName())));

?>
<div class="content-with-sidebar">
    <?php include_component('project/sidebar', ['dashboard' => __('Releases'), 'collapsed' => true]); ?>
    <div id="project_planning" class="project_info_container boards-container whiteboard <?php if ($board->getType() == AgileBoard::TYPE_GENERIC) echo 'type-generic'; if ($board->getType() == AgileBoard::TYPE_SCRUM) echo 'type-scrum'; if ($board->getType() == AgileBoard::TYPE_KANBAN) echo 'type-kanban'; ?>" data-last-refreshed="<?php echo time(); ?>" data-poll-url="<?php echo make_url('agile_poll', array('project_key' => $selected_project->getKey(), 'board_id' => $board->getID(), 'mode' => 'whiteboard')); ?>" data-retrieve-issue-url="<?php echo make_url('agile_retrieveissue', array('project_key' => $selected_project->getKey(), 'board_id' => $board->getID(), 'mode' => 'whiteboard')); ?>" data-board-id="<?php echo $board->getID(); ?>">
        <div class="planning_indicator" id="planning_indicator"><?php echo image_tag('spinning_30.gif'); ?></div>
        <div class="top-search-filters-container" id="project_planning_action_strip">
            <div class="header">
                <div class="fancy-tabs">
                    <a class="tab" href="<?= make_url('agile_board', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>">
                        <span class="icon"><?= fa_image_tag('stream'); ?></span>
                        <span class="name"><?= ($board->getType() == AgileBoard::TYPE_GENERIC) ? __('Planning') : __('Backlog'); ?></span>
                    </a>
                    <a class="tab selected" href="<?= make_url('agile_whiteboard', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>">
                        <span class="icon"><?= fa_image_tag('columns'); ?></span>
                        <span class="name"><?= __('Whiteboard'); ?></span>
                    </a>
                </div>
            </div>
            <div class="search-and-filters-strip">
                <div class="search-strip">
                    <div class="fancy-dropdown-container filter">
                        <div class="fancy-dropdown">
                            <label><?= ($board->getType() == AgileBoard::TYPE_SCRUM) ? __('Sprint') : __('Milestone'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode from-left" id="selected_milestone_input" data-status-url="<?php echo make_url('agile_whiteboardmilestonestatus', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID(), 'mode' => 'getmilestonestatus')); ?>">
                                <div class="list-item disabled" id="milestone-list-no-milestones" style="<?php if (count($board->getMilestones())) echo 'display: none;'; ?>">
                                    <span class="icon"><?= fa_image_tag('info-circle'); ?></span>
                                    <span class="name"><?= __('There are no milestones'); ?></span>
                                </div>
                                <?php if ($board->getType() != AgileBoard::TYPE_SCRUM): ?>
                                    <input type="radio" name="selected_milestone" id="selected_milestone_0" class="fancy-checkbox" checked>
                                    <label for="selected_milestone_0" class="list-item">
                                        <span class="icon"><?= fa_image_tag('money-check'); ?></span>
                                        <span class="name value"><?= __('Any milestones'); ?></span>
                                    </label>
                                <?php endif; ?>
                                <?php foreach ($board->getMilestones() as $milestone): ?>
                                    <input type="radio" name="selected_milestone" id="selected_milestone_<?= $milestone->getId(); ?>" class="fancy-checkbox" <?php if ($selected_milestone instanceof \pachno\core\entities\Milestone && $selected_milestone->getID() == $milestone->getID()) echo 'checked'; ?>>
                                    <label class="list-item multiline" for="selected_milestone_<?= $milestone->getId(); ?>" data-board-value="<?php echo $board->getID(); ?>" onclick="window.location='#<?php echo $milestone->getID(); ?>';">
                                        <span class="icon"><?php echo fa_image_tag('money-check'); ?></span>
                                        <span class="name">
                                            <span class="title value"><?php echo $milestone->getName(); ?></span>
                                            <span class="description">
                                                <span><?php echo __('Start date'); ?></span>
                                                <span><?php echo ($milestone->getStartingDate()) ? \pachno\core\framework\Context::getI18n()->formatTime($milestone->getStartingDate(), 22, true, true) : '-'; ?></span>
                                                <span><?php echo __('End date'); ?></span>
                                                <span><?php echo ($milestone->getScheduledDate()) ? \pachno\core\framework\Context::getI18n()->formatTime($milestone->getScheduledDate(), 22, true, true) : '-'; ?></span>
                                            </span>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <input type="search" class="planning_filter_title" id="planning_filter_title_input" disabled placeholder="<?php echo __('Filter issues by title'); ?>">
                    <?php if ($pachno_user->canManageProject($selected_project)): ?>
                        <div class="edit-mode-buttons">
                            <a class="button secondary highlighted" href="javascript:void(0);" onclick="Pachno.Project.Planning.Whiteboard.addColumn(this);" data-url="<?php echo make_url('agile_whiteboardcolumn', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>">
                                <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                                <span class="name"><?php echo __('Add column'); ?></span>
                            </a>
                            <a class="button primary" href="javascript:void(0);" onclick="Pachno.Project.Planning.Whiteboard.saveColumns();">
                                <?= fa_image_tag('save', ['class' => 'icon']); ?>
                                <span class="name"><?php echo __('Save columns'); ?></span>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="dropper-container settings-dropper">
                        <button class="button secondary icon dropper"><?= fa_image_tag('cog'); ?></button>
                        <div class="dropdown-container">
                            <div class="list-mode">
                                <a class="list-item" href="javascript:void(0);" onclick="Pachno.UI.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'agileboard', 'project_id' => $board->getProject()->getID(), 'board_id' => $board->getID())); ?>');" title="<?php echo __('Edit this board'); ?>">
                                    <span class="icon"><?php echo fa_image_tag('edit'); ?></span>
                                    <span class="name"><?= __('Edit this board'); ?></span>
                                </a>
                                <a class="list-item" href="javascript:void(0);" onclick="Pachno.Project.Planning.Whiteboard.toggleEditMode();">
                                    <span class="icon"><?php echo fa_image_tag('columns'); ?></span>
                                    <span class="name"><?= __('Manage columns'); ?></span>
                                </a>
                                <span class="list-item separator"></span>
                                <a class="list-item" href="javascript:void(0);" onclick="$('#main_container').toggleClass('fullscreen');">
                                    <span class="icon"><?php echo fa_image_tag('arrows-alt'); ?></span>
                                    <span class="name"><?= __('Toggle fullscreen mode'); ?></span>
                                </a>
                                <span class="list-item separator"></span>
                                <div class="header"><?= __('Card display'); ?></div>
                                <input type="radio" class="fancy-checkbox" name="card_mode" value="simple" checked id="card_mode_simple">
                                <label for="card_mode_simple" class="list-item" onclick="Pachno.Project.Planning.Whiteboard.setViewMode(this, 'simple');">
                                    <span class="icon"><?php echo fa_image_tag('list'); ?></span>
                                    <span class="name"><?= __('Summary card view'); ?></span>
                                </label>
                                <input type="radio" class="fancy-checkbox" name="card_mode" value="detailed" checked id="card_mode_detailed">
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
        <div id="planning_whiteboard" class="whiteboard-columns-container <?php if (!count($board->getColumns())) echo 'initialized'; ?>">
            <div class="planning_indicator" id="whiteboard_indicator"><?php echo image_tag('spinning_30.gif'); ?></div>
            <?php if ($pachno_user->canManageProject($selected_project)): ?>
                <form id="planning_whiteboard_columns_form" class="whiteboard-columns" action="<?php echo make_url('agile_whiteboard', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>">
                    <div class="row" id="planning_whiteboard_columns_form_row">
                        <?php foreach ($board->getColumns() as $column): ?>
                            <?php include_component('agile/editboardcolumn', compact('column')); ?>
                        <?php endforeach; ?>
                    </div>
                </form>
            <?php endif; ?>
            <?php if (!count($board->getColumns())): ?>
                <div id="onboarding-no-board-columns" class="onboarding">
                    <div class="image-container">
                        <?= image_tag('/unthemed/board-no-columns.png', [], true); ?>
                    </div>
                    <div class="helper-text">
                        <?= __('Columns help you visualize your work'); ?><br>
                        <?= __('Add one or more columns to drag-and-drop organize tasks'); ?>
                    </div>
                    <div class="button-container">
                        <button class="button primary" onclick="Pachno.Project.Planning.Whiteboard.toggleEditMode();">
                            <?= fa_image_tag('columns', ['class' => 'icon']); ?>
                            <span><?= __('Add columns'); ?></span>
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="whiteboard-columns <?php echo ($board->usesSwimlanes()) ? ' swimlanes' : ' no-swimlanes'; ?>" id="whiteboard" data-whiteboard-url="<?php echo make_url('agile_whiteboardissues', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>" data-swimlane-type="<?php echo $board->getSwimlaneType(); ?>">
                    <div class="header" id="whiteboard-headers-placeholder">
                        <div class="row">
                            <?php foreach ($board->getColumns() as $column): ?>
                                <div class="column">&nbsp;</div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php if (count($board->getColumns())): ?>
                        <div class="header" id="whiteboard-headers">
                            <div class="row">
                                <?php foreach ($board->getColumns() as $column): ?>
                                    <?php include_component('agile/boardcolumnheader', compact('column')); ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php /* <div class="project_left_container" id="board-backlog-container">
            <div class="project_left">
                <div id="milestone_0" class="milestone_box" data-milestone-id="0" data-issues-url="<?php echo make_url('agile_milestoneissues', array('project_key' => $board->getProject()->getKey(), 'milestone_id' => 0, 'board_id' => $board->getID())); ?>" data-assign-issue-url="<?php echo make_url('agile_assignmilestone', array('project_key' => $board->getProject()->getKey(), 'milestone_id' => 0)); ?>" data-backlog-search="<?php echo ($board->usesAutogeneratedSearchBacklog()) ? 'predefined_'.$board->getAutogeneratedSearch() : 'saved_'.$board->getBacklogSearchObject()->getID(); ?>">
                    <div class="planning_indicator" id="milestone_0_indicator" style="display: none;"><?php echo image_tag('spinning_30.gif'); ?></div>
                    <div class="header backlog" id="milestone_0_header">
                        <div class="milestone_basic_container">
                            <span class="milestone_name"><?php echo __('Backlog'); ?></span>
                            <div class="backlog_toggler dynamic_menu_link" onclick="$('#project_planning').toggleClass('left_toggled');" title="<?php echo __('Click to toggle the show / hide the backlog'); ?>"><?php echo image_tag('icon_sidebar_collapse.png'); ?></div>
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
                    <div class="milestone-no-issues" style="display: none;" id="milestone_0_unassigned"><?php echo __('No issues are assigned to this milestone'); ?></div>
                    <div class="milestone-no-issues" style="display: none;" id="milestone_0_unassigned_filtered"><?php echo __('No issues assigned to this milestone matches selected filters'); ?></div>
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
