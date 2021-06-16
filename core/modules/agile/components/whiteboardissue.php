<?php

    use pachno\core\entities\BoardColumn;
    use pachno\core\entities\Issue;

    /**
     * @var Issue $issue
     * @var BoardColumn $column
     */

?>
<div <?php if (!isset($fake) || !$fake): ?> id="whiteboard_issue_<?php echo $issue->getID(); ?>"<?php endif; ?> class="whiteboard-issue <?php if ($issue->isClosed()) echo 'issue_closed'; ?> <?php if ($issue->isBlocking()) echo 'blocking'; ?>" data-issue-id="<?php echo $issue->getID(); ?>" data-status-id="<?php echo $issue->getStatus()->getID(); ?>" data-last-updated="<?php echo $issue->getLastUpdatedTime(); ?>" data-column-id="<?php echo $column->getID(); ?>">
    <div class="planning_indicator" id="issue_<?php echo $issue->getID(); ?>_indicator" style="display: none;"><?php echo image_tag('spinning_16.gif'); ?></div>
    <?php //include_component('agile/colorpicker', array('issue' => $issue)); ?>
    <div class="issue_estimates">
        <div class="issue_estimate points" style="<?php if (!$issue->getEstimatedPoints() && !$issue->getSpentPoints()) echo 'display: none;'; ?>"><?php if ($issue->getSpentPoints()): ?><span title="<?php echo __('Spent points'); ?>"><?php echo $issue->getSpentPoints(); ?></span>/<?php endif; ?><span title="<?php echo __('Estimated points'); ?>"><?php echo $issue->getEstimatedPoints(); ?></span></div>
        <div class="issue_estimate hours" style="<?php if (!$issue->getEstimatedHoursAndMinutes() && !$issue->getSpentHoursAndMinutes()) echo 'display: none;'; ?>"><?php if ($issue->getSpentHoursAndMinutes()): ?><span title="<?php echo __('Spent hours'); ?>"><?php echo $issue->getSpentHoursAndMinutes(); ?></span>/<?php endif; ?><span title="<?php echo __('Estimated hours'); ?>"><?php echo $issue->getEstimatedHoursAndMinutes(); ?></span></div>
    </div>
    <?php if ($issue->getPriority() instanceof \pachno\core\entities\Priority): ?>
        <div class="priority priority_<?php echo ($issue->getPriority() instanceof \pachno\core\entities\Priority) ? $issue->getPriority()->getValue() : 0; ?>" title="<?php echo ($issue->getPriority() instanceof \pachno\core\entities\Priority) ? __($issue->getPriority()->getName()) : __('Priority not set'); ?>"><?php echo ($issue->getPriority() instanceof \pachno\core\entities\Priority) ? $issue->getPriority()->getAbbreviation() : '-'; ?></div>
    <?php endif; ?>
    <div class="issue-header">
        <span class="issue-number"><?php echo $issue->getFormattedIssueNo(true); ?></span>
        <span class="issue-title"><?php echo $issue->getTitle(); ?></span>
        <?php if (isset($swimlane)): ?>
            <div class="dropper-container">
                <button title="<?php echo __('Show more actions'); ?>" class="button icon dropper" type="button"><?php echo fa_image_tag('ellipsis-v'); ?></button>
                <?php include_component('main/issuemoreactions', array('issue' => $issue, 'multi' => true, 'dynamic' => true, 'estimator_mode' => 'left')); ?>
            </div>
        <?php endif; ?>
    </div>
    <?php /* if ($issue->hasChildIssues()): ?>
         link_tag($issue->getUrl(), $issue->getFormattedTitle(true, false), array('title' => $issue->getFormattedTitle(), 'target' => '_blank', 'class' => 'issue_header'))
        <ol class="child-issues">
            <?php foreach ($issue->getChildIssues() as $child_issue): ?>
                <li title="<?php echo __e($child_issue->getFormattedTitle()); ?>" class="<?php if ($child_issue->isClosed()) echo 'closed'; ?>"><?php echo link_tag($chile_issue->getUrl(), $child_issue->getFormattedTitle(true), array('title' => $child_issue->getFormattedTitle(), 'target' => '_blank')); ?></li>
            <?php endforeach; ?>
        </ol>
    <?php endif; */ ?>
    <?php /* $issue_custom_fields_of_type = array_filter($issue->getCustomFieldsOfTypes(array(\pachno\core\entities\CustomDatatype::DATE_PICKER, \pachno\core\entities\CustomDatatype::DATETIME_PICKER))); ?>
    <?php if (count($issue->getBuilds()) || count($issue->getComponents()) || (isset($swimlane) && $swimlane->getBoard()->getEpicIssuetypeID() && $issue->hasParentIssuetype($swimlane->getBoard()->getEpicIssuetypeID()) && count(array_filter($issue->getParentIssues(), function($parent) use($swimlane) { return $parent->getIssueType()->getID() == $swimlane->getBoard()->getEpicIssuetypeID(); })))): ?>
        <div class="issue_info<?php if (isset($swimlane) && $swimlane->getBoard()->hasIssueFieldValues() && count(array_filter(array_keys($issue_custom_fields_of_type), function($custom_field_key) use($swimlane) { return $swimlane->getBoard()->hasIssueFieldValue($custom_field_key); }))) echo ' issue_info_top'; ?>">
            <?php foreach ($issue->getBuilds() as $details): ?>
                <div class="issue_release"><?php echo $details['build']->getVersion(); ?></div>
            <?php endforeach; ?>
            <?php foreach ($issue->getComponents() as $details): ?>
                <div class="issue_component"><?php echo $details['component']->getName(); ?></div>
            <?php endforeach; ?>
            <?php if (isset($swimlane)): ?>
                <?php if ($swimlane->getBoard()->getEpicIssuetypeID() && $issue->hasParentIssuetype($swimlane->getBoard()->getEpicIssuetypeID())): ?>
                    <?php foreach ($issue->getParentIssues() as $parent): ?>
                        <?php if ($parent->getIssueType()->getID() == $swimlane->getBoard()->getEpicIssuetypeID()): ?>
                            <?php echo link_tag($parent->getUrl(), $parent->getShortname(), array('title' => $parent->getFormattedTitle(), 'target' => '_blank', 'class' => 'epic_badge', 'style' => 'background-color: ' . $parent->getCoverColor().'; color: ' . $parent->getAgileTextColor(), 'data-parent-epic-id' => $parent->getID())); ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($swimlane) && $swimlane->getBoard()->hasIssueFieldValues() && count(array_filter(array_keys($issue_custom_fields_of_type), function($custom_field_key) use($swimlane) { return $swimlane->getBoard()->hasIssueFieldValue($custom_field_key); }))): ?>
        <div class="issue_info
        <?php if (count($issue->getBuilds()) || count($issue->getComponents()) || (isset($swimlane) && $swimlane->getBoard()->getEpicIssuetypeID() && $issue->hasParentIssuetype($swimlane->getBoard()->getEpicIssuetypeID()) && count(array_filter($issue->getParentIssues(), function($parent) use($swimlane) { return $parent->getIssueType()->getID() == $swimlane->getBoard()->getEpicIssuetypeID(); })))) echo ' issue_info_middle'; ?>">
            <?php if ($swimlane->getBoard()->hasIssueFieldValues()): ?>
                <?php foreach ($issue_custom_fields_of_type as $key => $value): ?>
                    <?php if (!$swimlane->getBoard()->hasIssueFieldValue($key)) continue; ?>
                    <div class="issue_component issue_date" title="<?php echo \pachno\core\entities\CustomDatatype::getByKey($key)->getDescription(); ?>"><?php echo \pachno\core\framework\Context::getI18n()->formatTime($value, 14); ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; */ ?>
    <div class="issue-info">
        <?php if ($issue->getNumberOfUserComments()): ?>
            <div class="comments-badge">
                <?php echo fa_image_tag('comments', [], 'far') .'<span>'. $issue->getNumberOfUserComments() .'</span>'; ?>
            </div>
        <?php endif; ?>
        <?php if ($issue->getNumberOfFiles()): ?>
            <div class="attachments-badge">
                <?php echo fa_image_tag('paperclip') .'<span>'. $issue->getNumberOfFiles() .'</span>'; ?>
            </div>
        <?php endif; ?>
        <?php echo image_tag('icon_block.png', array('class' => 'blocking', 'title' => __('This issue is marked as a blocker'))); ?>
        <?php if (count($column->getStatusIds()) > 1 && $issue->getStatus() instanceof \pachno\core\entities\Datatype): ?>
            <div class="status-badge" style="background-color: <?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;" title="<?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getName() : __('Unknown'); ?>">&nbsp;&nbsp;&nbsp;</div>
        <?php endif; ?>
        <?php if ($issue->isAssigned()): ?>
            <?php if ($issue->getAssignee() instanceof \pachno\core\entities\User): ?>
                <a href="javascript:void(0);" onclick="Pachno.UI.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'usercard', 'user_id' => $issue->getAssignee()->getID())); ?>');"><?php echo image_tag($issue->getAssignee()->getAvatarURL(), ['alt' => ' ', 'class' => 'avatar'], true); ?></a>
            <?php else: ?>
                <?php include_component('main/teamdropdown', array('team' => $issue->getAssignee(), 'size' => 'large', 'displayname' => '')); ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
