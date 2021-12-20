<?php

/**
 * @var \pachno\core\entities\SavedSearch $search_object
 */

use pachno\core\entities\Category;
use pachno\core\entities\common\Identifiable;
use pachno\core\entities\common\Timeable;
use pachno\core\entities\CustomDatatype;
use pachno\core\entities\CustomDatatypeOption;
use pachno\core\entities\Datatype;
use pachno\core\entities\DatatypeBase;
use pachno\core\entities\Issue;
use pachno\core\entities\Milestone;
use pachno\core\entities\Priority;
use pachno\core\entities\Reproducability;
use pachno\core\entities\Resolution;
use pachno\core\entities\Severity;
use pachno\core\entities\Status;
use pachno\core\entities\tables\Issues;
use pachno\core\entities\tables\IssueSpentTimes;
use pachno\core\entities\User;
use pachno\core\framework\Context;
use pachno\core\modules\search\controllers\Main;

$current_count = 0;
$current_estimated_time = Timeable::getZeroedUnitsWithPoints();
$current_spent_time = $current_estimated_time;
?>
<div class="results_container results_normal flexible-table">
<?php foreach ($search_object->getIssues() as $issue):
    // shows only issues with permissions, useful when if we're including subprojects
    if (!$issue->hasAccess())
        continue;

    list ($showtablestart, $showheader, $prevgroup_id, $groupby_description) = Main::resultGrouping($issue, $search_object->getGroupBy(), $current_count, $prevgroup_id);
    if (($showtablestart || $showheader) && $current_count > 0):
        echo '</div>';
        $current_count = 0;
        $current_estimated_time = Timeable::getZeroedUnitsWithPoints();
        $current_spent_time = $current_estimated_time;
    endif;
    $current_count++;
    $estimate = $issue->getEstimatedTime();
    $spenttime = $issue->getSpentTime(true, true);
    foreach ($current_estimated_time as $key => $value) $current_estimated_time[$key] += $estimate[$key];
    foreach ($current_spent_time as $key => $value) $current_spent_time[$key] += ($spenttime[$key]);
    if ($showheader):
?>
        <h5 class="<?php if ($search_object->getGroupby() == 'priority' && $issue->getPriority() instanceof Priority) echo 'priority_' . $issue->getPriority()->getItemdata(); ?>">
            <?php if ($search_object->getGroupBy() == 'issuetype'): ?>
                <?php echo fa_image_tag((($issue->hasIssueType()) ? $issue->getIssueType()->getFontAwesomeIcon() : 'question'), ['class' => (($issue->hasIssueType()) ? 'issuetype-icon issuetype-' . $issue->getIssueType()->getIcon() : 'issuetype-icon issuetype-unknown'),  'title' => (($issue->hasIssueType()) ? $issue->getIssueType()->getName() : __('Unknown issuetype'))]); ?>
            <?php elseif ($search_object->getGroupBy() == 'priority'): ?>
                <?php echo fa_image_tag((($issue->getPriority() instanceof Priority) ? $issue->getPriority()->getFontAwesomeIcon() : 'question'), ['title' => (($issue->getPriority() instanceof Priority) ? $issue->getPriority()->getName() : __('Unknown priority'))], (($issue->getPriority() instanceof Priority) ? $issue->getPriority()->getFontAwesomeIconStyle() : 'fas')); ?>
            <?php endif; ?>
            <?php echo $groupby_description; ?>
        </h5>
    <?php endif; ?>
    <?php if ($showtablestart): ?>
        <div class="results_body row-container">
            <div class="row header">
                <div class="column header invisible info-icons"></div>
                <?php if (!$pachno_user->isGuest() && $actionable): ?>
                    <div class="column header nosort sca_action_selector"><input type="checkbox" id="results_issue_all_checkbox" class="fancy-checkbox"><label for="results_issue_all_checkbox"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?></label></div>
                <?php endif; ?>
                <?php if (!Context::isProjectContext() && $show_project == true): ?>
                    <div class="column header"><?php echo __('Project'); ?></div>
                <?php endif; ?>
                <div data-sort-direction="<?php echo $search_object->getSortDirection(Issues::ISSUE_TYPE); ?>" data-sort-field="<?php echo Issues::ISSUE_TYPE; ?>" class="column header sc_issuetype result_issue <?php if ($dir = $search_object->getSortDirection(Issues::ISSUE_TYPE)) echo "sort_{$dir}"; ?>"><span><?php echo __('Issue'); ?></span><?= fa_image_tag('angle-down', ['class' => 'sort-indicator-asc']) . fa_image_tag('angle-down', ['class' => 'sort-indicator-desc']); ?></div>
                <div data-sort-direction="<?php echo $search_object->getSortDirection(Issues::ASSIGNEE_USER); ?>" data-sort-field="<?php echo Issues::ASSIGNEE_USER; ?>" class="column header sc_assigned_to <?php if ($dir = $search_object->getSortDirection(Issues::ASSIGNEE_USER)) echo "sort_{$dir}"; ?> <?php if (!in_array('assigned_to', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Assigned to'); ?></span></div>
                <div data-sort-direction="<?php echo $search_object->getSortDirection(Issues::POSTED_BY); ?>" data-sort-field="<?php echo Issues::POSTED_BY; ?>" class="column header sc_posted_by <?php if ($dir = $search_object->getSortDirection(Issues::POSTED_BY)) echo "sort_{$dir}"; ?> <?php if (!in_array('posted_by', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Posted by'); ?></span></div>
                <div data-sort-direction="<?php echo $search_object->getSortDirection(Issues::STATUS); ?>" data-sort-field="<?php echo Issues::STATUS; ?>" class="column header sc_status <?php if ($dir = $search_object->getSortDirection(Issues::STATUS)) echo "sort_{$dir}"; ?> <?php if (!in_array('status', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Status'); ?></span></div>
                <div data-sort-direction="<?php echo $search_object->getSortDirection(Issues::RESOLUTION); ?>" data-sort-field="<?php echo Issues::RESOLUTION; ?>" class="column header sc_resolution <?php if ($dir = $search_object->getSortDirection(Issues::RESOLUTION)) echo "sort_{$dir}"; ?> <?php if (!in_array('resolution', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Resolution'); ?></span></div>
                <div data-sort-direction="<?php echo $search_object->getSortDirection(Issues::CATEGORY); ?>" data-sort-field="<?php echo Issues::CATEGORY; ?>" class="column header sc_category <?php if ($dir = $search_object->getSortDirection(Issues::CATEGORY)) echo "sort_{$dir}"; ?> <?php if (!in_array('category', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Category'); ?></span></div>
                <div data-sort-direction="<?php echo $search_object->getSortDirection(Issues::SEVERITY); ?>" data-sort-field="<?php echo Issues::SEVERITY; ?>" class="column header sc_severity <?php if ($dir = $search_object->getSortDirection(Issues::SEVERITY)) echo "sort_{$dir}"; ?> <?php if (!in_array('severity', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Severity'); ?></span></div>
                <div data-sort-direction="<?php echo $search_object->getSortDirection(Issues::PERCENT_COMPLETE); ?>" data-sort-field="<?php echo Issues::PERCENT_COMPLETE; ?>" class="column header sc_percent_complete <?php if ($dir = $search_object->getSortDirection(Issues::PERCENT_COMPLETE)) echo "sort_{$dir}"; ?> <?php if (!in_array('percent_complete', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('% completed'); ?></span></div>
                <div data-sort-direction="<?php echo $search_object->getSortDirection(Issues::REPRODUCABILITY); ?>" data-sort-field="<?php echo Issues::REPRODUCABILITY; ?>" class="column header sc_reproducability <?php if ($dir = $search_object->getSortDirection(Issues::REPRODUCABILITY)) echo "sort_{$dir}"; ?> <?php if (!in_array('reproducability', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Reproducability'); ?></span><?= fa_image_tag('angle-down', ['class' => 'sort-indicator-asc']) . fa_image_tag('angle-down', ['class' => 'sort-indicator-desc']); ?></div>
                <div data-sort-direction="<?php echo $search_object->getSortDirection(Issues::PRIORITY); ?>" data-sort-field="<?php echo Issues::PRIORITY; ?>" class="column header sc_priority <?php if ($dir = $search_object->getSortDirection(Issues::PRIORITY)) echo "sort_{$dir}"; ?> <?php if (!in_array('priority', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Priority'); ?></span><?= fa_image_tag('angle-down', ['class' => 'sort-indicator-asc']) . fa_image_tag('angle-down', ['class' => 'sort-indicator-desc']); ?></div>
                <div class="column header sc_components nosort <?php if (!in_array('components', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Component(s)'); ?></span><?= fa_image_tag('angle-down', ['class' => 'sort-indicator-asc']) . fa_image_tag('angle-down', ['class' => 'sort-indicator-desc']); ?></div>
                <div data-sort-direction="<?php echo $search_object->getSortDirection(Issues::MILESTONE); ?>" data-sort-field="<?php echo Issues::MILESTONE; ?>" class="column header sc_milestone <?php if ($dir = $search_object->getSortDirection(Issues::MILESTONE)) echo "sort_{$dir}"; ?> <?php if (!in_array('milestone', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Milestone'); ?></span><?= fa_image_tag('angle-down', ['class' => 'sort-indicator-asc']) . fa_image_tag('angle-down', ['class' => 'sort-indicator-desc']); ?></div>
                <div class="column header sc_estimated_time nosort sc_datetime <?php if (!in_array('estimated_time', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Estimate'); ?></span><?= fa_image_tag('angle-down', ['class' => 'sort-indicator-asc']) . fa_image_tag('angle-down', ['class' => 'sort-indicator-desc']); ?></div>
                <div class="column header sc_spent_time nosort sc_datetime <?php if (!in_array('spent_time', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Time spent'); ?></span><?= fa_image_tag('angle-down', ['class' => 'sort-indicator-asc']) . fa_image_tag('angle-down', ['class' => 'sort-indicator-desc']); ?></div>
                <div data-sort-direction="<?php echo $search_object->getSortDirection(Issues::LAST_UPDATED); ?>" data-sort-field="<?php echo Issues::LAST_UPDATED; ?>" class="column header sc_last_updated <?php if ($dir = $search_object->getSortDirection(Issues::LAST_UPDATED)) echo "sort_{$dir}"; ?> sc_datetime <?php if (!in_array('last_updated', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Last updated'); ?></span><?= fa_image_tag('angle-down', ['class' => 'sort-indicator-asc']) . fa_image_tag('angle-down', ['class' => 'sort-indicator-desc']); ?></div>
                <div data-sort-direction="<?php echo $search_object->getSortDirection(Issues::POSTED); ?>" data-sort-field="<?php echo Issues::POSTED; ?>" class="column header sc_posted <?php if ($dir = $search_object->getSortDirection(Issues::POSTED)) echo "sort_{$dir}"; ?> sc_datetime <?php if (!in_array('posted', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Posted at'); ?></span><?= fa_image_tag('angle-down', ['class' => 'sort-indicator-asc']) . fa_image_tag('angle-down', ['class' => 'sort-indicator-desc']); ?></div>
                <div data-sort-direction="<?php echo $search_object->getSortDirection(IssueSpentTimes::EDITED_AT); ?>" data-sort-field="<?php echo IssueSpentTimes::EDITED_AT; ?>" class="column header sc_time_spent <?php if ($dir = $search_object->getSortDirection(IssueSpentTimes::EDITED_AT)) echo "sort_{$dir}"; ?> sc_datetime <?php if (!in_array('time_spent', $visible_columns)) echo 'hidden'; ?>"><span><?php echo __('Time spent at'); ?></span><?= fa_image_tag('angle-down', ['class' => 'sort-indicator-asc']) . fa_image_tag('angle-down', ['class' => 'sort-indicator-desc']); ?></div>
                <?php foreach ($custom_columns as $column): ?>
                    <div data-sort-direction="<?php echo $search_object->getSortDirection($column->getKey()); ?>" data-sort-field="<?php echo $column->getKey(); ?>" class="column header sc_<?php echo $column->getKey(); ?> <?php if ($dir = $search_object->getSortDirection($column->getKey())) echo "sort_{$dir}"; ?> <?php if ($column->getType() == DatatypeBase::DATE_PICKER || $column->getType() == DatatypeBase::DATETIME_PICKER) echo 'sc_datetime'; ?> <?php if (!in_array($column->getKey(), $visible_columns)) echo 'hidden'; ?>"><span><?php echo __($column->getName()); ?></span><?= fa_image_tag('angle-down', ['class' => 'sort-indicator-asc']) . fa_image_tag('angle-down', ['class' => 'sort-indicator-desc']); ?></div>
                <?php endforeach; ?>
                <?php if (!$pachno_user->isGuest() && $actionable): ?>
                    <div class="column header sc_actions nosort">&nbsp;</div>
                <?php endif; ?>
            </div>
    <?php endif; ?>
    <div class="row <?php if ($issue->isClosed()): ?> closed<?php endif; ?><?php if ($issue->isBlocking()): ?> blocking<?php endif; ?><?php if ($issue->isLocked()): ?> locked<?php endif; ?> priority_<?php echo ($issue->getPriority() instanceof Priority) ? $issue->getPriority()->getValue() : 0; ?>" id="issue_<?php echo $issue->getID(); ?>">
        <div class="column info-icons invisible">
            <?php if ($issue->getNumberOfUserComments()): ?>
                <?= fa_image_tag('comment', [], 'far'); ?>
            <?php endif; ?>
            <?php if ($issue->getNumberOfFiles()): ?>
                <?php echo fa_image_tag('paperclip', array('title' => __('This issue has %num attachments', array('%num' => $issue->getNumberOfFiles())))); ?>
            <?php endif; ?>
            <?php if ($issue->isLocked()): ?>
                <?php echo fa_image_tag('lock', array('title' => __('Access to this issue is restricted'))); ?>
            <?php endif; ?>
        </div>
        <?php if (!$pachno_user->isGuest() && $actionable): ?>
            <div class="column sca_actions">
                <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
                    <input type="checkbox" class="fancy-checkbox" name="update_issue[<?php echo $issue->getID(); ?>]" value="<?php echo $issue->getID(); ?>" id="update_issue_<?= $issue->getID(); ?>_checkbox"><label for="update_issue_<?= $issue->getID(); ?>_checkbox"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?></label>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php if (!Context::isProjectContext() && $show_project == true): ?>
        <div class="column "><?php echo link_tag(make_url('project_issues', array('project_key' => $issue->getProject()->getKey())), $issue->getProject()->getName()); ?></div>
    <?php endif; ?>
        <div class="column result_issue">
            <?php $title_visible = (in_array('title', $visible_columns)) ? '' : ' style="display: none;'; ?>
            <a class="issue_link" href="<?php echo $issue->getUrl(); ?>">
                <?php echo fa_image_tag((($issue->hasIssueType()) ? $issue->getIssueType()->getFontAwesomeIcon() : 'unknown'), ['class' => (($issue->hasIssueType()) ? 'issuetype-icon issuetype-' . $issue->getIssueType()->getIcon() : 'issuetype-icon issuetype-unknown'), 'title' => (($issue->hasIssueType()) ? $issue->getIssueType()->getName() : __('Unknown issuetype'))]); ?>
                <span class="issue_no"><?php echo $issue->getFormattedIssueNo(true); ?></span>
                <span class="sc_title"<?php echo $title_visible; ?>><span class="sc_dash"> - </span><?php echo $issue->getTitle(); ?></span>
            </a>
        </div>
        <div class="column sc_assigned_to<?php if (!$issue->isAssigned()): ?> faded_out<?php endif; ?> <?php if (!in_array('assigned_to', $visible_columns)) echo 'hidden'; ?>">
            <?php if ($issue->isAssigned()): ?>
                <?php if ($issue->getAssignee() instanceof User): ?>
                    <?php include_component('main/userdropdown', ['user' => $issue->getAssignee(), 'size' => 'small']); ?>
                <?php else: ?>
                    <?php include_component('main/teamdropdown', ['team' => $issue->getAssignee(), 'size' => 'small']); ?>
                <?php endif; ?>
            <?php else: ?>
                -
            <?php endif; ?>
        </div>
        <div class="column sc_posted_by<?php if (!$issue->isPostedBy()): ?> faded_out<?php endif; ?> <?php if (!in_array('posted_by', $visible_columns)) echo 'hidden'; ?>">
            <?php if ($issue->isPostedBy()): ?>
                <?php include_component('main/userdropdown', ['user' => $issue->getPostedBy(), 'size' => 'small']); ?>
            <?php else: ?>
                -
            <?php endif; ?>
        </div>
        <div class="column sc_status<?php if (!$issue->getStatus() instanceof Datatype): ?> faded_out<?php endif; ?> <?php if (!in_array('status', $visible_columns)) echo 'hidden'; ?>">
            <?php if ($issue->getStatus() instanceof Datatype): ?>
                <div class="sc_status_color status-badge" data-dynamic-field-value data-field="status" data-issue-id="<?= $issue->getId(); ?>" style="background-color: <?php echo ($issue->getStatus() instanceof Datatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>; color: <?php echo $issue->getStatus()->getTextColor(); ?>;"><span><?php echo $issue->getStatus()->getName(); ?></span></div>
            <?php else: ?>
                -
            <?php endif; ?>
        </div>
        <div class="column sc_resolution<?php if (!$issue->getResolution() instanceof Resolution): ?> faded_out<?php endif; ?> <?php if (!in_array('resolution', $visible_columns)) echo 'hidden'; ?>" data-dynamic-field-value data-field="resolution" data-issue-id="<?= $issue->getId(); ?>" data-unknown="-">
            <?php echo ($issue->getResolution() instanceof Resolution) ? mb_strtoupper($issue->getResolution()->getName()) : '-'; ?>
        </div>
        <div class="column sc_category<?php if (!$issue->getCategory() instanceof Category): ?> faded_out<?php endif; ?> <?php if (!in_array('category', $visible_columns)) echo 'hidden'; ?>">
            <?php echo ($issue->getCategory() instanceof Category) ? $issue->getCategory()->getName() : '-'; ?>
        </div>
        <div class="column sc_severity<?php if (!$issue->getSeverity() instanceof Severity): ?> faded_out<?php endif; ?> <?php if (!in_array('severity', $visible_columns)) echo 'hidden'; ?>">
            <?php echo ($issue->getSeverity() instanceof Severity) ? $issue->getSeverity()->getName() : '-'; ?>
        </div>
        <div class="column smaller sc_percent_complete <?php if (!in_array('percent_complete', $visible_columns)) echo 'hidden'; ?>">
            <span style="display: none;"><?php echo $issue->getPercentCompleted(); ?></span><?php include_component('main/percentbar', array('percent' => $issue->getPercentCompleted(), 'height' => 15)) ?>
        </div>
        <div class="column sc_reproducability<?php if (!$issue->getReproducability() instanceof Reproducability): ?> faded_out<?php endif; ?> <?php if (!in_array('reproducability', $visible_columns)) echo 'hidden'; ?>">
            <?php echo ($issue->getReproducability() instanceof Reproducability) ? $issue->getReproducability()->getName() : '-'; ?>
        </div>
        <div class="column sc_priority<?php if (!$issue->getPriority() instanceof Priority): ?> faded_out<?php endif; ?> <?php if (!in_array('priority', $visible_columns)) echo 'hidden'; ?>">
            <?php echo ($issue->getPriority() instanceof Priority) ? fa_image_tag($issue->getPriority()->getFontAwesomeIcon(), [], $issue->getPriority()->getFontAwesomeIconStyle()) . '<span>'.$issue->getPriority()->getName().'</span>' : '-'; ?>
        </div>
        <?php $component_names = $issue->getComponentNames(); ?>
        <div class="column sc_components<?php if (!count($component_names)): ?> faded_out<?php endif; ?> <?php if (!in_array('components', $visible_columns)) echo 'hidden'; ?>">
            <?php echo (count($component_names)) ? join(', ', $component_names) : '-'; ?>
        </div>
        <div class="column sc_milestone<?php if (!$issue->getMilestone() instanceof Milestone): ?> faded_out<?php endif; ?> <?php if (!in_array('milestone', $visible_columns)) echo 'hidden'; ?>">
            <?php // echo ($issue->getMilestone() instanceof Milestone) ? link_tag(make_url('project_milestone_details', array('project_key' => $issue->getProject()->getKey(), 'milestone_id' => $issue->getMilestone()->getID())), $issue->getMilestone()->getName()) : '-'; ?>
            <?php echo ($issue->getMilestone() instanceof Milestone) ? $issue->getMilestone()->getName() : '-'; ?>
        </div>
        <div class="column sc_estimated_time<?php if (!$issue->hasEstimatedTime()): ?> faded_out<?php endif; ?> <?php if (!in_array('estimated_time', $visible_columns)) echo 'hidden'; ?>">
            <?php echo (!$issue->hasEstimatedTime()) ? '-' : Issue::getFormattedTime($issue->getEstimatedTime()); ?>
        </div>
        <div class="column sc_spent_time<?php if (!$issue->hasSpentTime()): ?> faded_out<?php endif; ?> <?php if (!in_array('spent_time', $visible_columns)) echo 'hidden'; ?>">
            <?php echo (!$issue->hasSpentTime() || !$issue->isSpentTimeVisible()) ? '-' : Issue::getFormattedTime($issue->getSpentTime(true, true)); ?>
        </div>
        <div class="column smaller sc_last_updated <?php if (!in_array('last_updated', $visible_columns)) echo 'hidden'; ?>" title="<?php echo Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 21); ?>"><?php echo Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 20); ?></div>
        <div class="column smaller sc_posted <?php if (!in_array('posted', $visible_columns)) echo 'hidden'; ?>" title="<?php echo Context::getI18n()->formatTime($issue->getPosted(), 21); ?>"><?php echo Context::getI18n()->formatTime($issue->getPosted(), 20); ?></div>
        <div class="column smaller sc_time_spent <?php if (!in_array('time_spent', $visible_columns)) echo 'hidden'; ?>" title="<?php echo $issue->getSumsSpentTime(); ?>"><?php echo $issue->getSumsSpentTime(); ?></div>
        <?php foreach ($custom_columns as $column): ?>
            <div class="column smaller sc_<?php echo $column->getKey(); ?> <?php if (!in_array($column->getKey(), $visible_columns)) echo 'hidden'; ?>"><?php
                $value = $issue->getCustomField($column->getKey());
                switch ($column->getType()) {
                    case DatatypeBase::DATE_PICKER:
                        echo Context::getI18n()->formatTime($value, 20);
                        break;
                    case DatatypeBase::DROPDOWN_CHOICE_TEXT:
                    case DatatypeBase::RADIO_CHOICE:
                    echo ($value instanceof CustomDatatypeOption) ? $value->getValue() : '';
                        break;
                    case DatatypeBase::INPUT_TEXT:
                    case DatatypeBase::INPUT_TEXTAREA_MAIN:
                    case DatatypeBase::INPUT_TEXTAREA_SMALL:
                        echo $value;
                        break;
                    case DatatypeBase::STATUS_CHOICE:
                        if ($value instanceof Status):
                            ?><div class="sc_status_color status-badge" style="background-color: <?php echo $value->getColor(); ?>;"><span class="sc_status_name" style="color: <?php echo $value->getTextColor(); ?>;"><?php echo $value->getName(); ?></span></div><?php
                        endif;
                        break;
                    case DatatypeBase::CLIENT_CHOICE:
                    case DatatypeBase::COMPONENTS_CHOICE:
                    case DatatypeBase::EDITIONS_CHOICE:
                    case DatatypeBase::MILESTONE_CHOICE:
                    case DatatypeBase::RELEASES_CHOICE:
                    case DatatypeBase::TEAM_CHOICE:
                    case DatatypeBase::USER_CHOICE:
                        echo ($value instanceof Identifiable) ? $value->getName() : '';
                        break;
                    case DatatypeBase::DATETIME_PICKER:
                        echo (is_numeric($value)) ? Context::getI18n()->formatTime($value, 25) : $value;
                        break;
                }
            ?></div>
        <?php endforeach; ?>
        <?php if (!$pachno_user->isGuest() && $actionable): ?>
            <div class="column sc_actions">
                <div class="dropper-container">
                    <a title="<?php echo __('Show more actions'); ?>" class="button icon secondary dropper dynamic_menu_link" data-id="<?php echo $issue->getID(); ?>" id="more_actions_<?php echo $issue->getID(); ?>_button" href="javascript:void(0);"><?= fa_image_tag('ellipsis-v'); ?></a>
                    <?php include_component('main/issuemoreactions', array('issue' => $issue, 'multi' => true, 'dynamic' => true)); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
<?php if ($current_count > 0): ?>
    </div>
<?php endif; ?>
</div>
