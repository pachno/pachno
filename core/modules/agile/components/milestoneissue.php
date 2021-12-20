<?php

/** @var \pachno\core\entities\Issue $issue */

// shows only issues with permissions, useful when if we're including subprojects
if (!$issue->hasAccess())
    return;

$parent_prefix = isset($parent_issue) ? 'issue_'.$parent_issue->getID().'_child_' : '';

?>
<div id="<?php echo $parent_prefix ?>issue_<?php echo $issue->getID(); ?>_top_container" class="milestone-issue <?php /* if ($issue->isChildIssue() && !$issue->hasParentIssuetype($board->getEpicIssuetypeID())) echo 'child_issue'; */ ?> <?php if ($issue->isClosed()) echo 'issue_closed'; ?> <?php //if ($issue->hasChildIssues()) echo 'has_child_issues'; ?>" data-issue-id="<?php echo $parent_prefix . $issue->getID(); ?>"<?php

    foreach ($issue->getBuilds() as $details) echo ' data-release-'.$details['build']->getID();
    // foreach ($issue->getParentIssues() as $parent) echo ' data-parent-'.$parent->getID();

?>>
    <div class="planning_indicator" id="<?php echo $parent_prefix; ?>issue_<?php echo $issue->getID(); ?>_indicator" style="display: none;"><?php echo image_tag('spinning_16.gif'); ?></div>
    <div id="<?php echo $parent_prefix .'issue_'. $issue->getID(); ?>" class="issue-container <?php if ($issue->isClosed()) echo 'issue_closed'; ?> <?php if ($issue->isBlocking()) echo 'blocking'; ?> draggable" data-estimated-points="<?php echo $issue->getEstimatedPoints(); ?>" data-estimated-hours="<?php echo $issue->getEstimatedHours(); ?>" data-estimated-minutes="<?php echo $issue->getEstimatedMinutes(); ?>" data-spent-points="<?php echo $issue->getSpentPoints(); ?>" data-spent-hours="<?php echo $issue->getSpentHours(); ?>" data-spent-minutes="<?php echo $issue->getSpentMinutes(); ?>" data-last-updated="<?php echo $issue->getLastUpdatedTime(); ?>">
        <?php include_component('agile/colorpicker', array('issue' => $issue)); ?>
        <div class="priority priority_<?php echo ($issue->getPriority() instanceof \pachno\core\entities\Priority) ? $issue->getPriority()->getValue() : 0; ?>" title="<?php echo ($issue->getPriority() instanceof \pachno\core\entities\Priority) ? __($issue->getPriority()->getName()) : __('Priority not set'); ?>"><?php echo ($issue->getPriority() instanceof \pachno\core\entities\Priority) ? $issue->getPriority()->getAbbreviation() : '-'; ?></div>
        <div class="assignee-container">
            <?php if ($issue->isAssigned()): ?>
                <?php if ($issue->getAssignee() instanceof \pachno\core\entities\User): ?>
                    <a href="javascript:void(0);" onclick="Pachno.UI.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $issue->getAssignee()->getID())); ?>');"><?php echo image_tag($issue->getAssignee()->getAvatarURL(), array('alt' => ' ', 'class' => 'avatar small'), true); ?></a>
                <?php else: ?>
                    <?php include_component('main/teamdropdown', array('team' => $issue->getAssignee(), 'size' => 'large', 'displayname' => '')); ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="status-container">
            <?php if ($issue->getStatus() instanceof \pachno\core\entities\Datatype): ?>
                <div class="status-badge" style="background-color: <?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;" title="<?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getName() : __('Unknown'); ?>">&nbsp;&nbsp;&nbsp;</div>
            <?php endif; ?>
        </div>
        <div class="issue-link-container">
            <div class="issue-link">
                <a href="<?= $issue->getUrl(); ?>" title="<?= $issue->getFormattedTitle(); ?>" target="_blank">
                    <?= fa_image_tag(($issue->hasIssueType()) ? $issue->getIssueType()->getFontAwesomeIcon() : 'unknown', ['class' => (($issue->hasIssueType()) ? 'issuetype-icon issuetype-' . $issue->getIssueType()->getIcon() : 'issuetype-icon issuetype-unknown')]); ?>
                    <span><?= $issue->getFormattedTitle(true); ?></span>
                </a>
            </div>
            <div class="issue-info">
                <?php echo image_tag('icon_block.png', array('class' => 'blocking', 'title' => __('This issue is marked as a blocker'))); ?>
                <?php foreach ($issue->getBuilds() as $details): ?>
                    <div class="issue_release"><?php echo $details['build']->getVersion(); ?></div>
                <?php endforeach; ?>
                <?php foreach ($issue->getComponents() as $details): ?>
                    <div class="issue_component"><?php echo $details['component']->getName(); ?></div>
                <?php endforeach; ?>
                <div class="issue_estimates">
                    <div class="issue_estimate points" style="<?php if (!$issue->getEstimatedPoints() && !$issue->getSpentPoints()) echo 'display: none;'; ?>"><?php if ($issue->getSpentPoints()): ?><span title="<?php echo __('Spent points'); ?>"><?php echo $issue->getSpentPoints(); ?></span>/<?php endif; ?><span title="<?php echo __('Estimated points'); ?>"><?php echo $issue->getEstimatedPoints(); ?></span></div>
                    <div class="issue_estimate hours" style="<?php if (!$issue->getEstimatedHoursAndMinutes() && !$issue->getSpentHoursAndMinutes()) echo 'display: none;'; ?>"><?php if ($issue->getSpentHoursAndMinutes()): ?><span title="<?php echo __('Spent hours'); ?>"><?php echo $issue->getSpentHoursAndMinutes(); ?></span>/<?php endif; ?><span title="<?php echo __('Estimated hours'); ?>"><?php echo $issue->getEstimatedHoursAndMinutes(); ?></span></div>
                </div>
                <?php /* if ($board->getEpicIssuetypeID() && $issue->hasParentIssuetype($board->getEpicIssuetypeID())): ?>
                    <?php foreach ($issue->getParentIssues() as $parent): ?>
                        <?php if ($parent->getIssueType()->getID() == $board->getEpicIssuetypeID()): ?>
                            <div class="epic_badge" style="background-color: <?php echo $parent->getCoverColor(); ?>; color: <?php echo $parent->getAgileTextColor(); ?>" data-parent-epic-id="<?php echo $parent_prefix . $parent->getID(); ?>"><?php echo $parent->getShortname(); ?></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; */ ?>
            </div>
        </div>
        <div class="issue_more_actions_link_container dropper-container">
            <a title="<?php echo __('Show more actions'); ?>" class="dropper dynamic_menu_link" data-id="<?php echo $parent_prefix . $issue->getID(); ?>" id="<?php echo $parent_prefix; ?>more_actions_<?php echo $issue->getID(); ?>_button" href="javascript:void(0);"><?php echo fa_image_tag('ellipsis-v'); ?></a>
            <?php include_component('main/issuemoreactions', array('issue' => $issue, 'multi' => true, 'dynamic' => true, 'board' => $board)); ?>
        </div>
    </div>
    <?php /* <ul class="child_issues_container" id="<?php echo $parent_prefix; ?>child_issues_<?php echo $issue->getID(); ?>_container">
        <?php foreach ($issue->getChildIssues() as $child_issue): ?>
            <?php if ($issue->isChildIssue() && !$issue->hasParentIssuetype($board->getEpicIssuetypeID())) continue; ?>
            <?php include_component('agile/milestoneissue', array('issue' => $child_issue, 'parent_issue' => $issue, 'board' => $board)); ?>
        <?php endforeach; ?>
    </ul> */ ?>
</div>
