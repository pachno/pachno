<?php

    use pachno\core\entities\AgileBoard;
    use pachno\core\entities\Milestone;
    use pachno\core\entities\Project;
    use pachno\core\framework\Response;
    
    /**
     * @var AgileBoard $board
     * @var Milestone $selected_milestone
     * @var Project $selected_project
     * @var Response $pachno_response
     */

    $pachno_response->setTitle(__('"%project_name" agile whiteboard', ['%project_name' => $selected_project->getName()]));

?>
<div class="content-with-sidebar">
    <?php include_component('project/sidebar', ['dashboard' => __('Releases'), 'collapsed' => true]); ?>
    <div id="project_planning" class="project_info_container boards-container whiteboard <?php if ($board->getType() == AgileBoard::TYPE_GENERIC) echo 'type-generic'; if ($board->getType() == AgileBoard::TYPE_SCRUM) echo 'type-scrum'; if ($board->getType() == AgileBoard::TYPE_KANBAN) echo 'type-kanban'; ?>" data-last-refreshed="<?= time(); ?>" data-poll-url="<?= make_url('agile_poll', ['project_key' => $selected_project->getKey(), 'board_id' => $board->getID(), 'mode' => 'whiteboard']); ?>" data-board-id="<?= $board->getID(); ?>">
        <div class="planning_indicator" id="planning_indicator"><?= image_tag('spinning_30.gif'); ?></div>
        <div class="top-search-filters-container" id="project_planning_action_strip">
            <div class="header">
                <div class="name-container">
                    <span class="board-name"><?= $board->getName(); ?></span>
                </div>
                <div class="stripe-container">
                    <div class="stripe"></div>
                </div>
                <div class="fancy-tabs">
                    <a class="tab" href="<?= make_url('agile_board', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID()]); ?>">
                        <span class="icon"><?= fa_image_tag('stream'); ?></span>
                        <span class="name label-generic"><?= __('Planning'); ?></span>
                        <span class="name label-scrum"><?= __('Backlog'); ?></span>
                        <span class="name label-kanban"><?= __('Backlog'); ?></span>
                    </a>
                    <a class="tab selected" href="<?= make_url('agile_whiteboard', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID()]); ?>">
                        <span class="icon"><?= fa_image_tag('columns'); ?></span>
                        <span class="name label-generic"><?= __('Whiteboard'); ?></span>
                        <span class="name label-scrum"><?= __('Ongoing sprint'); ?></span>
                        <span class="name label-kanban"><?= __('Ongoing work'); ?></span>
                    </a>
                </div>
            </div>
            <div class="search-and-filters-strip">
                <div class="search-strip">
                    <div class="fancy-dropdown-container filter from-left">
                        <div class="fancy-dropdown shadeable" data-default-label="<?= __('Not selected'); ?>">
                            <label><span class="label-generic"><?= __('Milestone'); ?></span><span class="label-scrum"><?= __('Sprint'); ?></span><span class="label-kanban"><?= __('Milestone'); ?></span></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode" id="selected_milestone_input" data-status-url="<?= make_url('agile_whiteboardmilestonestatus', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID(), 'mode' => 'getmilestonestatus']); ?>">
                                <input type="radio" name="selected_milestone" id="selected_milestone_0_generic" class="fancy-checkbox" value="0" checked>
                                <label for="selected_milestone_0_generic" class="list-item">
                                    <span class="icon"><?= fa_image_tag('money-check'); ?></span>
                                    <span class="name value"><?= __('Issues not assigned to a milestone'); ?></span>
                                </label>
                                <div class="list-item separator"></div>
                                <div class="list-item separator label-kanban" id="milestone-list-separator"></div>
                                <div class="list-item disabled" id="milestone-list-no-milestones" style="<?php if (count($board->getMilestones())) echo 'display: none;'; ?>">
                                    <span class="icon"><?= fa_image_tag('info-circle'); ?></span>
                                    <span class="name value"><?= __('There are no milestones'); ?></span>
                                </div>
                                <?php foreach ($board->getMilestones() as $milestone): ?>
                                    <?php include_component('agile/milestonelistitem', ['milestone' => $milestone, 'board' => $board, 'selected_milestone' => $selected_milestone]); ?>
                                <?php endforeach; ?>
                                <div class="list-item separator"></div>
                                <a class="list-item label-generic label-kanban" href="<?= make_url('agile_board', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID()]); ?>">
                                    <?= fa_image_tag('stream', ['class' => 'icon']); ?>
                                    <span class="name"><?= __('Plan a milestone from the backlog'); ?></span>
                                </a>
                                <a class="list-item label-scrum" href="<?= make_url('agile_board', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID()]); ?>">
                                    <?= fa_image_tag('stream', ['class' => 'icon']); ?>
                                    <span class="name"><?= __('Plan a sprint from the backlog'); ?></span>
                                </a>
                                <a class="list-item trigger-backdrop" href="javascript:void(0);" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'agilemilestone', 'project_id' => $board->getProject()->getID(), 'board_id' => $board->getID()]); ?>">
                                    <?= fa_image_tag('plus-square', ['class' => 'icon']); ?>
                                    <span class="name"><span class="label-generic"><?= __('Create a new milestone'); ?></span><span class="label-kanban"><?= __('Create a new milestone'); ?></span><span class="label-scrum"><?= __('Create an empty sprint'); ?></span></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <input type="search" class="planning_filter_title shadeable" id="planning_filter_title_input" disabled placeholder="<?= __('Filter issues by title'); ?>">
                    <div class="avatar-list" id="board-assignees-list"></div>
                    <button class="button secondary icon trigger-backdrop settings" type="button" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'agileboard', 'project_id' => $board->getProject()->getID(), 'board_id' => $board->getID()]); ?>" data-docked-backdrop="right"><?= fa_image_tag('cog'); ?></button>
                </div>
            </div>
        </div>
        <div id="planning_whiteboard" class="whiteboard-columns-container <?php if (!count($board->getColumns())) echo 'initialized'; ?>">
            <div class="planning_indicator" id="whiteboard_indicator"><?= image_tag('spinning_30.gif'); ?></div>
            <div id="milestone-issues-unhandled" class="message-box type-warning hidden">
                <?= fa_image_tag('exclamation-circle', ['class' => 'icon']); ?>
                <span class="message"><span><?= __('%number_of_issues in this milestone are not visible because their status is not assigned to a column', ['%number_of_issues' => '<span class="count-badge">' . __('%number_of_issues issue(s)', ['%number_of_issues' => '<span class="number_of_issues"></span>']) . '</span>']); ?></span></span>
            </div>
            <div id="milestone-issues-unconfigured" class="message-box type-warning hidden">
                <?= fa_image_tag('exclamation-circle', ['class' => 'icon']); ?>
                <span class="message"><span><?= __('%number_of_issues in this milestone are not visible because of the way the board is configured', ['%number_of_issues' => '<span class="count-badge">' . __('%number_of_issues issue(s)', ['%number_of_issues' => '<span class="number_of_issues"></span>']) . '</span>']); ?></span></span>
                <span class="actions">
                    <button class="button secondary icon trigger-backdrop settings" type="button" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'agileboard', 'project_id' => $board->getProject()->getID(), 'board_id' => $board->getID()]); ?>" data-docked-backdrop="right"><?= __('Configure board'); ?></button>
                </span>
            </div>
            <div id="onboarding-no-milestones" class="onboarding hidden">
                <div class="image-container">
                    <?= image_tag('/unthemed/onboarding_no_milestones.png', [], true); ?>
                </div>
                <div class="helper-text">
                    <div class="title"><?= __('Get started by creating a sprint'); ?></div>
                    <span><?= __('Work is organized in sprints to help you manage workload and progress'); ?></span>
                </div>
                <div class="button-container">
                    <button class="button primary dropper">
                        <?= fa_image_tag('columns', ['class' => 'icon']); ?>
                        <span><?= __('Set up the first new sprint'); ?></span>
                        <span class="icon toggler"><?= fa_image_tag('angle-down'); ?></span>
                    </button>
                    <div class="dropdown-container from-bottom from-center">
                        <div class="list-mode">
                            <a class="list-item" href="<?= make_url('agile_board', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID()]); ?>">
                                <?= fa_image_tag('stream', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Plan a sprint from the backlog'); ?></span>
                            </a>
                            <a class="list-item trigger-backdrop" href="javascript:void(0);" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'agilemilestone', 'project_id' => $board->getProject()->getID(), 'board_id' => $board->getID()]); ?>">
                                <?= fa_image_tag('plus-square', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Create an empty sprint'); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div id="onboarding-no-active-sprint" class="onboarding hidden">
                <div class="image-container">
                    <?= image_tag('/unthemed/onboarding_no_active_sprint.png', [], true); ?>
                </div>
                <div class="helper-text">
                    <div class="title"><?= __('There is no active sprint for today'); ?></div>
                    <span><?= __('Create a sprint for this period to add cards and columns'); ?></span>
                </div>
                <div class="button-container">
                    <div class="dropper-container">
                        <button class="button primary dropper">
                            <?= fa_image_tag('columns', ['class' => 'icon']); ?>
                            <span><?= __('Set up a new sprint'); ?></span>
                            <span class="icon toggler"><?= fa_image_tag('angle-down'); ?></span>
                        </button>
                        <div class="dropdown-container from-bottom from-center">
                            <div class="list-mode">
                                <a class="list-item multiline" href="<?= make_url('agile_board', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID()]); ?>">
                                    <?= fa_image_tag('stream', ['class' => 'icon']); ?>
                                    <span class="name"><?= __('Set up a sprint from the backlog'); ?></span>
                                </a>
                                <a class="list-item trigger-backdrop" href="javascript:void(0);" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'agilemilestone', 'project_id' => $board->getProject()->getID(), 'board_id' => $board->getID()]); ?>">
                                    <?= fa_image_tag('plus-square', ['class' => 'icon']); ?>
                                    <span class="name"><?= __('Create a sprint'); ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="onboarding-no-board-columns" class="onboarding hidden">
                <div class="image-container">
                    <?= image_tag('/unthemed/board-no-columns.png', [], true); ?>
                </div>
                <div class="helper-text">
                    <div class="title"><?= __('Columns help you visualize your work'); ?></div>
                    <span><?= __('Add one or more columns to drag-and-drop organize tasks'); ?></span>
                </div>
                <div class="button-container" id="add-first-column-button-container">
                    <button class="button primary trigger-whiteboard-toggle-add-first-column">
                        <?= fa_image_tag('columns', ['class' => 'icon']); ?>
                        <span><?= __('Add a column'); ?></span>
                    </button>
                </div>
                <div class="content-container">
                    <div class="card form-container">
                        <form method="POST" action="<?= make_url('agile_whiteboardcolumn', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID(), 'column_id' => 0]); ?>" id="add-first-column-form" data-simple-submit>
                            <div class="form-row">
                                <input type="hidden" name="milestone_id" value="0" id="add_first_column_milestone_id" class="add_column_milestone_id">
                                <input type="text" name="name" id="first-column-name" placeholder="<?= __('Give your column a name, like "Todo"'); ?>">
                            </div>
                            <div class="form-row">
                                <label><?= __('Status for this column'); ?></label>
                                <div class="fancy-dropdown-container from-bottom">
                                    <div class="fancy-dropdown">
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <?php foreach ($board->getProject()->getAvailableStatuses() as $index => $status): ?>
                                                <input type="checkbox" value="<?= $status->getID(); ?>" name="status_ids[<?= $status->getID(); ?>]" id="add_first_column_status_<?= $status->getID(); ?>" class="fancy-checkbox" <?php if ($index == 0) echo 'checked'; ?>>
                                                <label for="add_first_column_status_<?= $status->getID(); ?>" class="list-item">
                                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                    <span class="name value"><?= __($status->getName()); ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row submit-container">
                                <button type="button" class="secondary trigger-whiteboard-toggle-add-first-column"><?= __('Cancel'); ?></button>
                                <button type="submit" class="primary secondary highlight">
                                    <span class="name"><?= __('Save'); ?></span>
                                    <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="whiteboard-columns <?= ($board->usesSwimlanes()) ? ' swimlanes' : ' no-swimlanes'; ?>" id="whiteboard" style="display: none;" data-simplebar>
                <div class="header" id="whiteboard-content">
                    <div class="row headers">
                        <div class="columns-container" id="whiteboard-headers-columns">
                            <div class="columns">
                                <?php foreach ($board->getColumns() as $column): ?>
                                    <?php include_component('agile/boardcolumnheader', compact('column')); ?>
                                <?php endforeach; ?>
                                <?php include_component('agile/addboardcolumnheader', ['board' => $board]); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php /* <div class="project_left_container" id="board-backlog-container">
            <div class="project_left">
                <div id="milestone_0" class="milestone_box" data-milestone-id="0" data-issues-url="<?= make_url('agile_milestoneissues', array('project_key' => $board->getProject()->getKey(), 'milestone_id' => 0, 'board_id' => $board->getID())); ?>" data-assign-issue-url="<?= make_url('agile_assignmilestone', array('project_key' => $board->getProject()->getKey(), 'milestone_id' => 0)); ?>" data-backlog-search="<?= ($board->usesAutogeneratedSearchBacklog()) ? 'predefined_'.$board->getAutogeneratedSearch() : 'saved_'.$board->getBacklogSearchObject()->getID(); ?>">
                    <div class="planning_indicator" id="milestone_0_indicator" style="display: none;"><?= image_tag('spinning_30.gif'); ?></div>
                    <div class="header backlog" id="milestone_0_header">
                        <div class="milestone_basic_container">
                            <span class="milestone_name"><?= __('Backlog'); ?></span>
                            <div class="backlog_toggler dynamic_menu_link" onclick="$('#project_planning').toggleClass('left_toggled');" title="<?= __('Click to toggle the show / hide the backlog'); ?>"><?= image_tag('icon_sidebar_collapse.png'); ?></div>
                        </div>
                        <div class="milestone_counts_container">
                            <table>
                                <tr>
                                    <td id="milestone_0_issues_count">-</td>
                                    <td id="milestone_0_points_count" class="issue_estimates">-</td>
                                    <td id="milestone_0_hours_count" class="issue_estimates">-</td>
                                </tr>
                                <tr>
                                    <td><?= __('Issues'); ?></td>
                                    <td class="issue_estimates"><?= __('Points'); ?></td>
                                    <td class="issue_estimates"><?= __('Hours'); ?></td>
                                </tr>
                            </table>
                        </div>
                        <?= image_tag('spinning_20.gif', array('id' => 'milestone_0_issues_indicator', 'class' => 'milestone_issues_indicator', 'style' => 'display: none;')); ?>
                    </div>
                    <ul id="milestone_0_issues" class="milestone_issues jsortable intersortable <?php //if ($board->getBacklogSearchObject()->getTotalNumberOfIssues() == 0) echo 'empty'; ?>"></ul>
                    <div class="milestone-no-issues" style="display: none;" id="milestone_0_unassigned"><?= __('No issues are assigned to this milestone'); ?></div>
                    <div class="milestone-no-issues" style="display: none;" id="milestone_0_unassigned_filtered"><?= __('No issues assigned to this milestone matches selected filters'); ?></div>
                    <div class="milestone_error_issues" style="display: none;" id="milestone_0_initialize_error"><?= __('The issue list could not be loaded'); ?></div>
                </div>
            </div>
        </div> */ ?>
    </div>
</div>
<script type="text/javascript">
    Pachno.on(Pachno.EVENTS.ready, function () {
        let board;

        board = new Board(<?= json_encode($board->toJSON()); ?>);
        window.currentBoard = board;

        $('body').on('click', 'input[name=selected_milestone]', function () {
            board.updateSelectedMilestone(true);
        })
    });
</script>
<div id="moving_issue_workflow_transition"></div>
