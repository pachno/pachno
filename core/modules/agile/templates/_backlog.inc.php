<?php

use pachno\core\entities\AgileBoard;

/** @var AgileBoard $board */

$new_milestone_label = ($board->getType() == AgileBoard::TYPE_SCRUM) ? __('Create new sprint') : __('Create new milestone');

?>
<div class="new_milestone_marker" id="new_backlog_milestone_marker">
    <div class="draggable">
        <div class="milestone-counts-container">
            <div class="count">
                <span id="new_backlog_milestone_issues_count">0</span>
                <span><?php echo __('Issues'); ?></span>
            </div>
            <div class="count">
                <span id="new_backlog_milestone_points_count" class="issue_estimates estimated_points">0</span>
                <span class="issue_estimates estimated_points"><?php echo __('Points'); ?></span>
            </div>
            <div class="count">
                <span id="new_backlog_milestone_hours_count" class="issue_estimates estimated_hours">0</span>
                <span class="issue_estimates estimated_hours"><?php echo __('Hours'); ?></span>
            </div>
        </div>
        <?php echo javascript_link_tag($new_milestone_label, array('class' => 'button', 'onclick' => "Pachno.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'agilemilestone', 'project_id' => $board->getProject()->getId(), 'board_id' => $board->getID()))."', Pachno.Project.Planning.updateNewMilestoneIssues);")); ?>
    </div>
</div>
<?php foreach ($board->getBacklogSearchObject()->getIssues() as $issue): ?>
    <?php if ($issue->getMilestone() instanceof pachno\core\entities\Milestone) continue; ?>
    <?php /*if ($issue->isChildIssue()): ?>
        <?php foreach ($issue->getParentIssues() as $parent): ?>
            <?php if ($parent->getIssueType()->getID() != $board->getEpicIssuetypeID()) continue 2; ?>
        <?php endforeach; ?>
        <?php include_component('agile/milestoneissue', array('issue' => $issue, 'board' => $board)); ?>
    <?php else: */ ?>
        <?php include_component('agile/milestoneissue', array('issue' => $issue, 'board' => $board)); ?>
    <?php //endif; ?>
<?php endforeach; ?>
