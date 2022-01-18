<?php

    use pachno\core\entities\AgileBoard;
    use pachno\core\entities\Milestone;
    use pachno\core\entities\Project;
    use pachno\core\entities\User;
    use pachno\core\framework\Response;
    
    /**
     * @var AgileBoard $board
     * @var Milestone $selected_milestone
     * @var Project $selected_project
     * @var Response $pachno_response
     * @var User $pachno_user
     */
    
    $pachno_response->setTitle(__('"%project_name" project planning', ['%project_name' => $selected_project->getName()]));

    switch ($board->getType())
    {
        case AgileBoard::TYPE_SCRUM:
            $newmilestonelabel = __('Add new sprint');
            $togglemilestoneslabel = __('Toggle hidden sprints');
            $no_milestones_header = __('Sprints are the cornerstones of agile deliveries');
            $no_milestones_onboarding_text = __('Plan, prioritize and execute with confidence');
            break;
        case AgileBoard::TYPE_GENERIC:
        case AgileBoard::TYPE_KANBAN:
        default:
            $newmilestonelabel = __('New milestone');
            $togglemilestoneslabel = __('Toggle hidden milestones');
            $no_milestones_header = __('Organize what needs to be done with milestones');
            $no_milestones_onboarding_text = __('Plan, prioritize and execute with confidence');
            break;
    }

?>
<div class="content-with-sidebar">
    <?php include_component('project/sidebar', ['dashboard' => __('Releases'), 'collapsed' => true]); ?>
    <div id="project_planning" class="board-backlog-container <?php if ($board->getType() == AgileBoard::TYPE_GENERIC) echo 'type-generic'; if ($board->getType() == AgileBoard::TYPE_SCRUM) echo 'type-scrum'; if ($board->getType() == AgileBoard::TYPE_KANBAN) echo 'type-kanban'; ?>" data-last-refreshed="<?php echo time(); ?>" data-poll-url="<?php echo make_url('agile_poll', ['project_key' => $selected_project->getKey(), 'board_id' => $board->getID(), 'mode' => 'planning']); ?>" data-board-id="<?php echo $board->getID(); ?>">
        <div class="top-search-filters-container" id="project_planning_action_strip">
            <div class="header">
                <div class="name-container">
                    <span class="board-name"><?= $board->getName(); ?></span>
                </div>
                <div class="stripe-container">
                    <div class="stripe"></div>
                </div>
                <div class="fancy-tabs">
                    <a class="tab selected" href="<?= make_url('agile_board', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID()]); ?>">
                        <span class="icon"><?= fa_image_tag('stream'); ?></span>
                        <span class="name label-generic"><?= __('Planning'); ?></span>
                        <span class="name label-scrum"><?= __('Backlog'); ?></span>
                        <span class="name label-kanban"><?= __('Backlog'); ?></span>
                    </a>
                    <a class="tab" href="<?= make_url('agile_whiteboard', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID()]); ?>">
                        <span class="icon"><?= fa_image_tag('columns'); ?></span>
                        <span class="name label-generic"><?= __('Whiteboard'); ?></span>
                        <span class="name label-scrum"><?= __('Ongoing sprint'); ?></span>
                        <span class="name label-kanban"><?= __('Ongoing work'); ?></span>
                    </a>
                </div>
            </div>
            <div class="search-and-filters-strip">
                <div class="search-strip" id="project_planning_action_strip">
                    <input type="search" class="planning_filter_title" id="planning_filter_title_input" disabled placeholder="<?php echo __('Filter issues by title'); ?>">
                    <?php /* if ($board->getProject()->isBuildsEnabled()): ?>
                        <a class="button" id="releases_toggler_button" style="display: none;" href="javascript:void(0);" onclick="$(this).toggleClass('button-pressed');$('#builds-list').toggleClass('expanded');"><?php echo __('Releases'); ?></a>
                    <?php endif; ?>
                    <?php if ($board->getEpicIssuetypeID()): ?>
                        <button class="button" id="epics_toggler_button" style="display: none;" onclick="$(this).toggleClass('button-pressed');$('#epics-list').toggleClass('expanded');" disabled><?php echo __('Epics'); ?></button>
                    <?php endif; */ ?>
                    <button class="button trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'agilemilestone', 'project_id' => $board->getProject()->getId(), 'board_id' => $board->getID()]); ?>">
                        <span class="name label-generic label-kanban"><?= __('New milestone'); ?></span>
                        <span class="name label-scrum"><?= __('Add new sprint'); ?></span>
                    </button>
                    <?php echo image_tag('spinning_16.gif', ['id' => 'retrieve_indicator', 'class' => 'indicator', 'style' => 'display: none;']); ?>
                    <?php if ($pachno_user->canManageProjectReleases($selected_project)): ?>
                        <div class="dropper-container settings">
                            <?php echo fa_image_tag('ellipsis-v', ['class' => 'dropper', 'id' => 'planning_board_settings_gear']); ?>
                            <div class="dropdown-container">
                                <div class="list-mode">
                                    <a class="list-item trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'agileboard', 'project_id' => $board->getProject()->getID(), 'board_id' => $board->getID()]); ?>" data-docked-backdrop="right">
                                        <span class="icon"><?= fa_image_tag('cog'); ?></span>
                                        <span class="name"><?= __('Board settings'); ?></span>
                                    </a>
                                    <div class="list-item separator"></div>
                                    <input type="checkbox" class="fancy-checkbox trigger-toggle-closed-issues" name="show_closed_issues" id="board-show-closed-issues-checkbox">
                                    <label for="board-show-closed-issues-checkbox" class="list-item toggler">
                                        <span class="icon"><?= fa_image_tag('toggle-on', ['class' => 'checked']) . fa_image_tag('toggle-off', ['class' => 'unchecked']); ?></span>
                                        <span class="name"><?= __('Show closed issues'); ?></span>
                                    </label>
                                    <input type="checkbox" class="fancy-checkbox" name="show_hidden_milestones" id="board-show-hidden-milestones-checkbox" onchange="$('#planning_container').toggleClass('show-unavailable');Pachno.Main.Profile.clearPopupsAndButtons();">
                                    <label for="board-show-hidden-milestones-checkbox" class="list-item toggler">
                                        <span class="icon"><?= fa_image_tag('toggle-on', ['class' => 'checked']) . fa_image_tag('toggle-off', ['class' => 'unchecked']); ?></span>
                                        <span class="name"><?= $togglemilestoneslabel; ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="planning-container" id="planning_container">
            <?php if ($board->getProject()->isBuildsEnabled()): ?>
                <ul id="builds-list" data-releases-url="<?php echo make_url('agile_getreleases', ['project_key' => $selected_project->getKey(), 'board_id' => $board->getID()]); ?>"></ul>
            <?php endif; ?>
            <?php if ($board->getEpicIssuetypeID()): ?>
                <ul id="epics-list" data-epics-url="<?php echo make_url('agile_getepics', ['project_key' => $selected_project->getKey(), 'board_id' => $board->getID()]); ?>"></ul>
            <?php endif; ?>
            <div id="milestones-list" class="milestones-list jsortable"></div>
            <div id="no_milestones" style="display: none;">
                <?= image_tag('/unthemed/navigation/turn.png', ['id' => 'indicate-button'], true); ?>
                <div class="onboarding large">
                    <div class="image-container"><?= image_tag('/unthemed/backlog-no-milestones.png', [], true); ?></div>
                    <div class="helper-text">
                        <?= $no_milestones_header; ?><br>
                        <?= $no_milestones_onboarding_text; ?>
                    </div>
                </div>
            </div>
            <div id="board-backlog-container"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    Pachno.on(Pachno.EVENTS.ready, function () {
        let backlog;

        backlog = new Backlog(<?= json_encode($board->toJSON()); ?>);
        window.currentBacklog = backlog;

        // $('body').on('click', 'input[name=selected_milestone]', function () {
        //     board.updateSelectedMilestone(true);
        // })
    });
</script>
<?php /* if ($pachno_user->isPlanningTutorialEnabled()): ?>
    <?php include_component('main/tutorial_planning', compact('board')); ?>
<?php endif; */ ?>
