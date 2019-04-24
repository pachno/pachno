<?php use pachno\core\entities\AgileBoard; ?>
<div class="header <?php if (!$milestone->getID()) echo 'backlog'; ?>" id="milestone_<?= $milestone->getID(); ?>_header">
    <div class="main-details">
        <div class="name-container">
            <?php if (isset($board)): ?>
                <span class="name">
                    <span><?= $milestone->getName(); ?></span>
                    <a href="<?= make_url('agile_whiteboard', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>#<?= $milestone->getID(); ?>" class="button secondary icon"><?= fa_image_tag('columns'); ?></a>
                </span>
            <?php else: ?>
                <span class="milestone_name milestone_virtual_status"><?php include_component('project/milestonevirtualstatusdetails', array('milestone' => $milestone)); ?></span>
            <?php endif; ?>
            <?php if ($milestone->getID() && isset($board)): ?>
                <div class="percentage tooltip-container">
                    <div class="filler" id="milestone_<?= $milestone->getID(); ?>_percentage_filler"></div>
                    <div class="tooltip"><?= ($board->getType() == AgileBoard::TYPE_SCRUM) ? __('Sprint is %percentage% completed', ['%percentage' => $milestone->getPercentComplete()]) : __('Milestone is %percentage% completed', ['%percentage' => $milestone->getPercentComplete()]); ?></div>
                </div>
            <?php endif; ?>
        </div>
        <div class="dates">
            <?= fa_image_tag('calendar-alt', [], 'far'); ?>
            <?php if ($milestone->getID() && ($milestone->getStartingDate() || $milestone->getScheduledDate())): ?>
                <span class="start-date"><?= ($milestone->getStartingDate()) ? \pachno\core\framework\Context::getI18n()->formatTime($milestone->getStartingDate(), 22, true, true) : __('Unplanned'); ?></span>
                <?= fa_image_tag('arrow-alt-circle-right', [], 'far'); ?>
                <span class="end-date"><?= ($milestone->getScheduledDate()) ? \pachno\core\framework\Context::getI18n()->formatTime($milestone->getScheduledDate(), 22, true, true) : __('Unplanned'); ?></span>
            <?php else: ?>
                <span><?= __('Unscheduled'); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="milestone-counts-container">
        <div class="count">
            <span id="milestone_<?= $milestone->getID(); ?>_issues_count">
                <?php if ($include_counts): ?>
                    <?= $milestone->countOpenIssues(); ?><?php if ($milestone->countClosedIssues() > 0) echo ' ('.$milestone->countClosedIssues().')'; ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </span>
            <span><?= __('Issues'); ?></span>
        </div>
        <div class="count">
            <span id="milestone_<?= $milestone->getID(); ?>_points_count" class="issue_estimates"><?= ($include_counts) ? $milestone->getPointsSpent() .' / '. $milestone->getPointsEstimated() : '-'; ?></span>
            <span class="issue_estimates"><?= __('Points'); ?></span>
        </div>
        <div class="count">
            <span id="milestone_<?= $milestone->getID(); ?>_hours_count" class="issue_estimates"><?= ($include_counts) ? $milestone->getHoursAndMinutesSpent(true, true) .' / '. $milestone->getHoursAndMinutesEstimated(true, true) : '-'; ?></span>
            <span class="issue_estimates"><?= __('Hours'); ?></span>
        </div>
    </div>
    <?php if ($include_buttons): ?>
        <div class="actions-container">
            <?php if ($milestone->getID()): ?>
                <input type="checkbox" class="fancycheckbox" name="show_issues" id="milestone-<?= $milestone->getId(); ?>-show-issues-checkbox" onchange="Pachno.Project.Planning.toggleMilestoneIssues(<?= $milestone->getID(); ?>);">
                <label class="button secondary toggle-issues" for="milestone-<?= $milestone->getId(); ?>-show-issues-checkbox">
                    <span class="icon indicator"><?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?></span>
                    <span class="icon"><?= fa_image_tag('toggle-on', ['class' => 'checked']) . fa_image_tag('toggle-off', ['class' => 'unchecked']); ?></span>
                    <span><?= __('Show issues'); ?></span>
                </label>
            <?php endif; ?>
            <div class="dropper-container">
                <button class="dropper secondary icon"><?= fa_image_tag('cog'); ?></button>
                <div class="dropdown-container" id="milestone_<?= $milestone->getID(); ?>_moreactions">
                    <div class="list-mode">
                        <a href="<?= make_url('project_milestone_details', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID())); ?>" class="list-item">
                            <span class="icon"><?= fa_image_tag('columns'); ?></span>
                            <span class="name"><?= __('Show overview'); ?></span>
                        </a>
                        <?php if ($pachno_user->canEditProjectDetails(\pachno\core\framework\Context::getCurrentProject())): ?>
                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', array('key' => 'milestone_finish', 'project_id' => $milestone->getProject()->getId(), 'milestone_id' => $milestone->getID(), 'board_id' => isset($board) ? $board->getID() : '')); ?>');">
                                <span class="icon"><?= fa_image_tag('flag-checkered'); ?></span>
                                <span class="name"><?= __('Mark as finished'); ?></span>
                            </a>
                            <div class="list-item separator"></div>
                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', array('key' => 'agilemilestone', 'project_id' => $milestone->getProject()->getId(), 'milestone_id' => $milestone->getID(), 'board_id' => isset($board) ? $board->getID() : '')); ?>');">
                                <span class="icon"><?= fa_image_tag('edit'); ?></span>
                                <span class="name"><?= __('Edit'); ?></span>
                            </a>
                            <div class="list-item separator"></div>
                            <?php
                                if (isset($board))
                                {
                                    switch ($board->getType())
                                    {
                                        case AgileBoard::TYPE_GENERIC:
                                            echo javascript_link_tag(
                                                '<span class="icon">'.fa_image_tag('times').'</span><span class="name">'.__('Delete').'</span>',
                                                [
                                                    'onclick' => "Pachno.Main.Helpers.Dialog.show('".__('Do you really want to delete this milestone?')."', '".__('Removing this milestone will unassign all issues from this milestone and remove it from all available lists. This action cannot be undone.')."', {yes: {click: function() { Pachno.Project.Milestone.remove('".make_url('agile_milestone', ['project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID()])."', ".$milestone->getID()."); } }, no: {click: Pachno.Main.Helpers.Dialog.dismiss} });",
                                                    'class' => 'list-item'
                                                ]
                                            );
                                            break;
                                        case AgileBoard::TYPE_SCRUM:
                                        case AgileBoard::TYPE_KANBAN:
                                            echo javascript_link_tag(
                                                '<span class="icon">'.fa_image_tag('times').'</span><span class="name">'.__('Delete').'</span>',
                                                [
                                                    'onclick' => "Pachno.Main.Helpers.Dialog.show('".__('Do you really want to delete this sprint?')."', '".__('Deleting this sprint will remove all issues in this sprint and put them in the backlog. This action cannot be undone.')."', {yes: {click: function() { Pachno.Project.Milestone.remove('".make_url('agile_milestone', ['project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID()])."', ".$milestone->getID()."); } }, no: {click: Pachno.Main.Helpers.Dialog.dismiss} });",
                                                    'class' => 'list-item'
                                                ]
                                            );
                                            break;
                                    }
                                }
                            ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?= image_tag('spinning_20.gif', array('id' => 'milestone_'.$milestone->getID().'_issues_indicator', 'class' => 'milestone_issues_indicator', 'style' => 'display: none;')); ?>
</div>
<script>
    setTimeout(function () {
        <?php /* jQuery('#milestone_<?= $milestone->getId(); ?>_percentage_filler').css({ width: '<?= $milestone->getPercentComplete(); ?>%' }); */ ?>
        jQuery('#milestone_<?= $milestone->getId(); ?>_percentage_filler').css({ transform: '<?= ($milestone->getPercentComplete() < 100) ? 'scaleX(0.'.round($milestone->getPercentComplete()).')' : 'scaleX(1)'; ?>' });
    }, 1500);
</script>