<?php

    use pachno\core\entities\AgileBoard;

    $selected_columns = $milestone->getProject()->getPlanningColumns($pachno_user);
    $all_columns = $milestone->getProject()->getIssueFields(false, array('status', 'milestone', 'resolution', 'assignee', 'user_pain'));

    if (isset($board))
    {
        switch ($board->getType())
        {
            case AgileBoard::TYPE_GENERIC:
            case AgileBoard::TYPE_KANBAN:
                $noissueslabel = __('No issues are assigned to this milestone');
                $noissuesfilteredlabel = __('No issues assigned to this milestone matches selected filters');
                break;
            case AgileBoard::TYPE_SCRUM:
                $noissueslabel = __('There are no issues in this sprint');
                $noissuesfilteredlabel = __('No issues in this sprint matches selected filters');
                break;
        }
    }

?>
<div id="milestone_<?php echo $milestone->getID(); ?>" class="milestone-box <?php echo ($milestone->isVisibleRoadmap()) ? ' available' : ' unavailable'; ?> <?php echo ($milestone->isReached()) ? 'closed' : 'open'; ?>" data-milestone-id="<?php echo $milestone->getID(); ?>" <?php if (isset($board)): ?> data-issues-url="<?php echo make_url('agile_milestoneissues', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID(), 'board_id' => $board->getID())); ?>" data-assign-issue-url="<?php echo make_url('agile_assignmilestone', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID())); ?>" <?php else: ?> data-issues-url="<?php echo make_url('agile_milestoneissues', array('project_key' => $milestone->getProject()->getKey(), 'milestone_id' => $milestone->getID(), 'board_id' => '0')); ?>" <?php endif; ?>>
    <div class="planning_indicator" id="milestone_<?php echo $milestone->getID(); ?>_indicator" style="display: none;"><?php echo image_tag('spinning_30.gif'); ?></div>
    <?php include_component('agile/milestoneboxheader', compact('milestone', 'include_counts', 'include_buttons', 'board')); ?>
    <div id="milestone_<?php echo $milestone->getID(); ?>_issues" class="milestone-issues jsortable intersortable collapsed <?php if ($milestone->countIssues() == 0) echo 'empty'; ?>"></div>
    <?php if (isset($board)): ?>
        <div class="milestone_no_issues" style="<?php if ($milestone->countIssues() > 0): ?> display: none;<?php endif; ?>" id="milestone_<?php echo $milestone->getID(); ?>_unassigned"><?php echo $noissueslabel; ?></div>
        <div class="milestone_no_issues" style="display: none;" id="milestone_<?php echo $milestone->getID(); ?>_unassigned_filtered"><?php echo $noissuesfilteredlabel; ?></div>
        <div class="milestone_error_issues" style="display: none;" id="milestone_0_initialize_error"><?php echo __('The issue list could not be loaded'); ?></div>
    <?php else: ?>
        <div class="milestone_no_issues" style="<?php if ($milestone->countIssues() > 0): ?> display: none;<?php endif; ?>" id="milestone_<?php echo $milestone->getID(); ?>_unassigned"><?php echo __('No issues are assigned to this milestone'); ?></div>
    <?php endif; ?>
</div>
