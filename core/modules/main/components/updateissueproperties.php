<?php

    use pachno\core\entities\DatatypeBase;
    use pachno\core\entities\Issue;
    use pachno\core\entities\Project;
    use pachno\core\entities\WorkflowTransition;
    use pachno\core\framework\Context;

    /**
     * @var Project $project
     * @var WorkflowTransition $transition
     * @var Issue $issue
     * @var Issue[] $issues
     * @var string $form_url
     * @var string $form_id
     */

?>
<div class="backdrop_box large issuedetailspopup workflow_transition" style="<?php if ($issue instanceof Issue && (!isset($show) || !$show)): ?>display: none;<?php endif; ?>" id="issue_transition_container_<?= $transition->getId(); ?>">
    <div class="backdrop_detail_header">
        <span><?= $transition->getDescription(); ?></span>
        <?php if (false && (($issue instanceof Issue && !$issue->isDuplicate()) || isset($issues)) && $transition->hasAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_DUPLICATE)): ?>
            <a href="javascript:void(0);" class="button secondary icon trigger-duplicate-search" title="<?= __('Mark as duplicate'); ?>"><?= fa_image_tag('search-plus'); ?></a>
        <?php endif; ?>
        <?= fa_image_tag('times', ['class' => 'closer']); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form action="<?= $form_url; ?>" method="post" accept-charset="<?= Context::getI18n()->getCharset(); ?>" id="<?= $form_id; ?>" data-workflow-form data-workflow-transition-id="<?= $transition->getId(); ?>" data-simple-submit>
                <?php if (isset($interactive) && $interactive && $issue instanceof Issue): ?>
                    <input type="hidden" name="issue_ids[<?= $issue->getID(); ?>]" value="<?= $issue->getID(); ?>">
                <?php elseif (!$issue instanceof Issue): ?>
                    <?php foreach ($issues as $issue_id => $i): ?>
                        <input type="hidden" name="issue_ids[<?= $issue_id; ?>]" value="<?= $issue_id; ?>">
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="content">
                    <?php if (!$issue instanceof Issue): ?>
                        <div class="form-row">
                            <div class="helper-text">
                                <div class="message-box type-info"><?= fa_image_tag('info-circle') . __('This transition will be applied to %count selected issues', array('%count' => count($issues))); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($parent_issue)): ?>
                        <div class="form-row locked additional_information" id="parent_issue_div">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown locked">
                                    <label>
                                        <span><?= __('Parent issue'); ?></span>
                                        <?= fa_image_tag('lock', ['class' => 'icon locked']); ?>
                                    </label>
                                    <span class="value">
                                        <?php if ($parent_issue instanceof Issue): ?>
                                            <?= fa_image_tag(($parent_issue->hasIssueType()) ? $parent_issue->getIssueType()->getFontAwesomeIcon() : 'unknown', ['class' => (($parent_issue->hasIssueType()) ? 'issuetype-icon issuetype-' . $parent_issue->getIssueType()->getIcon() : 'issuetype-icon issuetype-unknown')]); ?>
                                            <span class="name"><?= $parent_issue->getFormattedTitle(); ?></span>
                                        <?php else: ?>
                                            <span class="name"><?= __('No parent issue'); ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <div class="dropdown-container list-mode">
                                        <input type="radio" value="<?= ($parent_issue instanceof Issue) ? $parent_issue->getID() : 0; ?>" name="parent_issue_id" id="transition_<?= $transition->getId(); ?>_parent_issue_radio" class="fancy-checkbox" checked>
                                        <label for="transition_<?= $transition->getId(); ?>_parent_issue_radio" class="list-item">
                                            <?php if ($parent_issue instanceof Issue): ?>
                                                <?= fa_image_tag(($parent_issue->hasIssueType()) ? $parent_issue->getIssueType()->getFontAwesomeIcon() : 'unknown', ['class' => (($parent_issue->hasIssueType()) ? 'issuetype-icon issuetype-' . $parent_issue->getIssueType()->getIcon() : 'issuetype-icon issuetype-unknown')]); ?>
                                                <span class="name"><?= $parent_issue->getFormattedTitle(); ?></span>
                                            <?php else: ?>
                                                <span class="name"><?= __('No parent issue'); ?></span>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ((($issue instanceof Issue && $issue->isUpdateable() && $issue->canEditAssignee()) || isset($issues)) && $transition->hasAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE) && !$transition->getAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE)->hasTargetValue()): ?>
                        <div class="form-row" id="transition_popup_assignee_div_<?= $transition->getID(); ?>" style="display: none;">
                            <input type="hidden" name="assignee_id" id="popup_assigned_to_id_<?= $transition->getID(); ?>" value="<?= ($issue instanceof Issue && $issue->hasAssignee() ? $issue->getAssignee()->getID() : 0); ?>">
                            <input type="hidden" name="assignee_type" id="popup_assigned_to_type_<?= $transition->getID(); ?>" value="<?= ($issue instanceof Issue && $issue->hasAssignee() ? ($issue->getAssignee() instanceof \pachno\core\entities\User ? 'user' : 'team') : ''); ?>">
                            <input type="hidden" name="assignee_teamup" id="popup_assigned_to_teamup_<?= $transition->getID(); ?>" value="0">
                            <label for="transition_popup_set_assignee_<?= $transition->getID(); ?>"><?= __('Assignee'); ?></label>
                            <span style="width: 170px; display: <?php if ($issue instanceof Issue && $issue->isAssigned()): ?>inline<?php else: ?>none<?php endif; ?>;" id="popup_assigned_to_name_<?= $transition->getID(); ?>">
                                    <?php if ($issue instanceof Issue): ?>
                                        <?php if ($issue->getAssignee() instanceof \pachno\core\entities\User): ?>
                                            <?= include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
                                        <?php elseif ($issue->getAssignee() instanceof \pachno\core\entities\Team): ?>
                                            <?= include_component('main/teamdropdown', array('team' => $issue->getAssignee())); ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </span>
                            <span class="faded_out" id="popup_no_assigned_to_<?= $transition->getID(); ?>"<?php if ($issue instanceof Issue && $issue->isAssigned()): ?> style="display: none;"<?php endif; ?>><?= __('Not assigned to anyone'); ?></span>
                            <a href="javascript:void(0);" class="dropper" data-target="popup_assigned_to_change_<?= $transition->getID(); ?>" title="<?= __('Click to change assignee'); ?>" style="display: inline-block; float: right; line-height: 1em;"><?= image_tag('tabmenu_dropdown.png', array('style' => 'float: none; margin: 3px;')); ?></a>
                            <div id="popup_assigned_to_name_indicator_<?= $transition->getID(); ?>" style="display: none;"><?= image_tag('spinning_16.gif', array('style' => 'float: right; margin-left: 5px;')); ?></div>
                            <div class="faded_out" id="popup_assigned_to_teamup_info_<?= $transition->getID(); ?>" style="clear: both; display: none;"><?= __('You will be teamed up with this user'); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if ((($issue instanceof Issue && !$issue->isDuplicate()) || isset($issues)) && $transition->hasAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_DUPLICATE)): ?>
                        <div class="duplicate_search form-row" style="display: none;">
                            <?php if ($issue instanceof Issue): ?>
                                <label for="viewissue_find_issue_<?= $transition->getID(); ?>_input"><?= __('Find issue(s)'); ?>&nbsp;</label>
                            <input type="text" name="searchfor" id="viewissue_find_issue_<?= $transition->getID(); ?>_input">
                            <input class="button button-blue" type="button" onclick="Pachno.Issues.findDuplicate($('#duplicate_finder_transition_<?= $transition->getID(); ?>').val(), <?= $transition->getID(); ?>);return false;" value="<?= __('Find'); ?>" id="viewissue_find_issue_<?= $transition->getID(); ?>_submit">
                                <?= image_tag('spinning_20.gif', array('id' => 'viewissue_find_issue_' . $transition->getID() . '_indicator', 'style' => 'display: none;')); ?>
                            <br>
                                <div id="viewissue_<?= $transition->getID(); ?>_duplicate_results"></div>
                            <input type="hidden" name="transition_duplicate_ulr[<?= $transition->getID(); ?>]" id="duplicate_finder_transition_<?= $transition->getID(); ?>" value="<?= make_url('viewissue_find_duplicated_issue', array('project_key' => $project->getKey(), 'issue_id' => $issue->getID())); ?>">
                            <?php if (!$issue instanceof Issue): ?>
                                <script type="text/javascript">
                                    var transition_id = <?= $transition->getID(); ?>;
                                    $('#viewissue_find_issue_' + transition_id + '_input').observe('keypress', function (event) {
                                        if (event.keyCode == Event.KEY_RETURN) {
                                            Pachno.Issues.findDuplicate($('#duplicate_finder_transition_' + transition_id).val(), transition_id);
                                            event.stop();
                                        }
                                    });
                                </script>
                            <?php endif; ?>
                                <div class="faded_out">
                                    <?= __('If you want to mark this issue as duplicate of another, existing issue, find the issue by entering details to search for, in the box above.'); ?>
                                </div>
                            <?php else: ?>
                                <div class="message-box type-warning"><?= fa_image_tag('info-circle') . __('Duplicate search is not available when applying a transition to multiple issues'); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ((($issue instanceof Issue && $issue->canEditStatus()) | isset($issues)) && $transition->hasAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_STATUS) && !$transition->getAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_STATUS)->hasTargetValue()): ?>
                        <div class="form-row">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Pick a status from the list'); ?>">
                                    <label><?= __('Status'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode filter-values-container">
                                        <?php foreach ($statuses as $status): ?>
                                            <?php if (!$transition->hasPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::RULE_STATUS_VALID) || $transition->getPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::RULE_STATUS_VALID)->isValueValid($status)): ?>
                                                <input type="radio" name="status_id" class="fancy-checkbox" value="<?= $status->getID(); ?>" <?php if ($issue instanceof Issue && $issue->getStatus() instanceof \pachno\core\entities\Status && $issue->getStatus()->getID() == $status->getID()) echo 'checked'; ?> id="transition_<?= $transition->getId(); ?>_status_<?= $status->getId(); ?>">
                                                <label for="transition_<?= $transition->getId(); ?>_status_<?= $status->getId(); ?>" class="list-item">
                                                    <span class="status-badge" style="background-color: <?= $status->getColor(); ?>;color: <?= $status->getTextColor(); ?>;">
                                                        <span class="name value"><?= __($status->getName()); ?></span>
                                                    </span>
                                                </label>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ((($issue instanceof Issue && $issue->canEditPriority()) | isset($issues)) && (isset($selected_priorities) || $transition->hasAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_PRIORITY) && !$transition->getAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_PRIORITY)->hasTargetValue())): ?>
                        <div class="form-row <?php if (isset($selected_priorities)) echo 'locked'; ?>">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Pick a priority from the list'); ?>">
                                    <label><?= __('Priority'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode filter-values-container">
                                        <?php if (isset($selected_priorities) && !is_array($selected_priorities)): ?>
                                            <input type="radio" name="priority_id" class="fancy-checkbox" value="0" checked id="transition_<?= $transition->getId(); ?>_priority_0">
                                            <label for="transition_<?= $transition->getId(); ?>_priority_0" class="list-item">
                                                <span class="name value"><?= __('Clear the priority value'); ?></span>
                                            </label>
                                        <?php endif; ?>
                                        <?php foreach ($fields_list['priority']['choices'] as $priority): ?>
                                            <?php if (isset($selected_priorities) && is_array($selected_priorities) && !array_key_exists($priority->getId(), $selected_priorities)) continue; ?>
                                            <?php if (!$transition->hasPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::RULE_PRIORITY_VALID) || $transition->getPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::RULE_PRIORITY_VALID)->isValueValid($priority)): ?>
                                                <input type="radio" name="priority_id" class="fancy-checkbox" value="<?= $priority->getID(); ?>" <?php if (isset($selected_priorities) || ($issue instanceof Issue && $issue->getPriority() instanceof \pachno\core\entities\Priority && $issue->getPriority()->getID() == $priority->getID())) echo 'checked'; ?> id="transition_<?= $transition->getId(); ?>_priority_<?= $priority->getId(); ?>">
                                                <label for="transition_<?= $transition->getId(); ?>_priority_<?= $priority->getId(); ?>" class="list-item">
                                                    <span class="name value"><?= __($priority->getName()); ?></span>
                                                </label>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($issue instanceof Issue && !$issue->isPriorityVisible()): ?>
                            <div class="form-row">
                                <div class="helper-text">
                                    <?= fa_image_tag('info-circle'); ?>
                                    <span><?= __("Priority isn't visible for this issuetype / project combination, unless you specify a value here"); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ((($issue instanceof Issue && $issue->canEditSeverity()) | isset($issues)) && (isset($selected_severities) || $transition->hasAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_SEVERITY) && !$transition->getAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_SEVERITY)->hasTargetValue())): ?>
                        <div class="form-row <?php if (isset($selected_severities)) echo 'locked'; ?>">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Pick a severity from the list'); ?>">
                                    <label><?= __('Severity'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode filter-values-container">
                                        <?php if (isset($selected_severities) && !is_array($selected_severities)): ?>
                                            <input type="radio" name="severity_id" class="fancy-checkbox" value="0" checked id="transition_<?= $transition->getId(); ?>_severity_0">
                                            <label for="transition_<?= $transition->getId(); ?>_severity_0" class="list-item">
                                                <span class="name value"><?= __('Clear the severity value'); ?></span>
                                            </label>
                                        <?php endif; ?>
                                        <?php foreach ($fields_list['severity']['choices'] as $severity): ?>
                                            <?php if (isset($selected_severities) && is_array($selected_severities) && !array_key_exists($severity->getId(), $selected_severities)) continue; ?>
                                            <?php if (!$transition->hasPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::RULE_SEVERITY_VALID) || $transition->getPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::RULE_SEVERITY_VALID)->isValueValid($severity)): ?>
                                                <input type="radio" name="severity_id" class="fancy-checkbox" value="<?= $severity->getID(); ?>" <?php if (isset($selected_severities) || ($issue instanceof Issue && $issue->getSeverity() instanceof \pachno\core\entities\Severity && $issue->getSeverity()->getID() == $severity->getID())) echo 'checked'; ?> id="transition_<?= $transition->getId(); ?>_severity_<?= $severity->getId(); ?>">
                                                <label for="transition_<?= $transition->getId(); ?>_severity_<?= $severity->getId(); ?>" class="list-item">
                                                    <span class="name value"><?= __($severity->getName()); ?></span>
                                                </label>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($issue instanceof Issue && !$issue->isSeverityVisible()): ?>
                            <div class="form-row">
                                <div class="helper-text">
                                    <?= fa_image_tag('info-circle'); ?>
                                    <span><?= __("Severity isn't visible for this issuetype / project combination, unless you specify a value here"); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ((($issue instanceof Issue && $issue->canEditCategory()) | isset($issues)) && (isset($selected_categories) || $transition->hasAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_CATEGORY) && !$transition->getAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_CATEGORY)->hasTargetValue())): ?>
                        <div class="form-row <?php if (isset($selected_categories)) echo 'locked'; ?>">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Pick a category from the list'); ?>">
                                    <label><?= __('Category'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode filter-values-container">
                                        <?php if (isset($selected_categories) && !is_array($selected_categories)): ?>
                                            <input type="radio" name="category_id" class="fancy-checkbox" value="0" checked id="transition_<?= $transition->getId(); ?>_category_0">
                                            <label for="transition_<?= $transition->getId(); ?>_category_0" class="list-item">
                                                <span class="name value"><?= __('Clear the category value'); ?></span>
                                            </label>
                                        <?php endif; ?>
                                        <?php foreach ($fields_list['category']['choices'] as $category): ?>
                                            <?php if (isset($selected_categories) && is_array($selected_categories) && !array_key_exists($category->getId(), $selected_categories)) continue; ?>
                                            <?php if (!$transition->hasPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::RULE_CATEGORY_VALID) || $transition->getPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::RULE_CATEGORY_VALID)->isValueValid($category)): ?>
                                                <input type="radio" name="category_id" class="fancy-checkbox" value="<?= $category->getID(); ?>" <?php if (isset($selected_categories) || ($issue instanceof Issue && $issue->getCategory() instanceof \pachno\core\entities\Category && $issue->getCategory()->getID() == $category->getID())) echo 'checked'; ?> id="transition_<?= $transition->getId(); ?>_category_<?= $category->getId(); ?>">
                                                <label for="transition_<?= $transition->getId(); ?>_category_<?= $category->getId(); ?>" class="list-item">
                                                    <span class="name value"><?= __($category->getName()); ?></span>
                                                </label>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($issue instanceof Issue && !$issue->isCategoryVisible()): ?>
                            <div class="form-row">
                                <div class="helper-text">
                                    <?= fa_image_tag('info-circle'); ?>
                                    <span><?= __("Category isn't visible for this issuetype / project combination, unless you specify a value here"); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ((($issue instanceof Issue && $issue->isUpdateable()) || isset($issues)) && $transition->hasAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_PERCENT) && !$transition->getAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_PERCENT)->hasTargetValue()): ?>
                        <div class="form-row">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Pick a percentage from the list'); ?>">
                                    <label><?= __('Percent complete'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode filter-values-container">
                                        <?php foreach (range(0, 100, 10) as $percentage): ?>
                                            <input type="radio" name="percentage" class="fancy-checkbox" value="<?= $percentage; ?>" <?php if ($issue instanceof Issue && $issue->getPercentCompleted() == $percentage) echo 'checked'; ?> id="transition_<?= $transition->getId(); ?>_percentage_<?= $percentage; ?>">
                                            <label for="transition_<?= $transition->getId(); ?>_percentage_<?= $percentage; ?>" class="list-item">
                                                <span class="name value"><?= $percentage; ?>%</span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (($issue instanceof Issue && (!$issue->isPercentCompletedVisible())) || isset($issues)): ?>
                            <div class="form-row">
                                <div class="helper-text">
                                    <?= fa_image_tag('info-circle'); ?>
                                    <span><?= __("Percent completed isn't visible for this issuetype / project combination, unless you specify a value here"); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ((($issue instanceof Issue && $issue->canEditReproducability()) || isset($issues)) && $transition->hasAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_REPRODUCABILITY) && !$transition->getAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_REPRODUCABILITY)->hasTargetValue()): ?>
                        <div class="form-row">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Pick a reproducability from the list'); ?>">
                                    <label><?= __('Reproducability'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode filter-values-container">
                                        <?php foreach ($fields_list['reproducability']['choices'] as $reproducability): ?>
                                            <?php if (!$transition->hasPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID) || $transition->getPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::RULE_REPRODUCABILITY_VALID)->isValueValid($reproducability)): ?>
                                                <input type="radio" name="reproducability_id" class="fancy-checkbox" value="<?= $reproducability->getID(); ?>" <?php if ($issue instanceof Issue && $issue->getReproducability() instanceof \pachno\core\entities\Reproducability && $issue->getReproducability()->getID() == $reproducability->getID()) echo 'checked'; ?> id="transition_<?= $transition->getId(); ?>_reproducability_<?= $reproducability->getId(); ?>">
                                                <label for="transition_<?= $transition->getId(); ?>_reproducability_<?= $reproducability->getId(); ?>" class="list-item">
                                                    <span class="name value"><?= __($reproducability->getName()); ?></span>
                                                </label>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (($issue instanceof Issue && (!$issue->isReproducabilityVisible())) || isset($issues)): ?>
                            <div class="form-row">
                                <div class="helper-text">
                                    <?= fa_image_tag('info-circle'); ?>
                                    <span><?= __("Reproducability isn't visible for this issuetype / project combination, unless you specify a value here"); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (($issue instanceof Issue || isset($issues)) && $transition->hasAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_RESOLUTION) && !$transition->getAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_RESOLUTION)->hasTargetValue()): ?>
                        <div class="form-row">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Pick a resolution from the list'); ?>">
                                    <label><?= __('Resolution'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode filter-values-container">
                                        <?php foreach ($fields_list['resolution']['choices'] as $resolution): ?>
                                            <?php if (!$transition->hasPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::RULE_RESOLUTION_VALID) || $transition->getPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::RULE_RESOLUTION_VALID)->isValueValid($resolution)): ?>
                                                <input type="radio" name="resolution_id" class="fancy-checkbox" value="<?= $resolution->getID(); ?>" <?php if ($issue instanceof Issue && $issue->getResolution() instanceof \pachno\core\entities\Resolution && $issue->getResolution()->getID() == $resolution->getID()) echo 'checked'; ?> id="transition_<?= $transition->getId(); ?>_resolution_<?= $resolution->getId(); ?>">
                                                <label for="transition_<?= $transition->getId(); ?>_resolution_<?= $resolution->getId(); ?>" class="list-item">
                                                    <span class="name value"><?= __($resolution->getName()); ?></span>
                                                </label>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (($issue instanceof Issue && (!$issue->isResolutionVisible())) || isset($issues)): ?>
                            <div class="form-row">
                                <div class="helper-text">
                                    <?= fa_image_tag('info-circle'); ?>
                                    <span><?= __("Resolution isn't visible for this issuetype / project combination, unless you specify a value here"); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ((($issue instanceof Issue && $issue->canEditMilestone()) | isset($issues)) && $transition->hasAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_SET_MILESTONE)): ?>
                        <div class="form-row">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Pick a milestone from the list'); ?>">
                                    <label><?= __('Milestone'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode filter-values-container">
                                        <?php foreach ($project->getMilestonesForIssues() as $milestone): ?>
                                            <input type="radio" name="milestone_id" class="fancy-checkbox" value="<?= $milestone->getID(); ?>" <?php if ($issue instanceof Issue && $issue->getMilestone() instanceof \pachno\core\entities\Milestone && $issue->getMilestone()->getID() == $milestone->getID()) echo 'checked'; ?> id="transition_<?= $transition->getId(); ?>_milestone_<?= $milestone->getId(); ?>">
                                            <label for="transition_<?= $transition->getId(); ?>_milestone_<?= $milestone->getId(); ?>" class="list-item">
                                                <span class="name value"><?= __($milestone->getName()); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (($issue instanceof Issue && (!$issue->isMilestoneVisible())) || isset($issues)): ?>
                            <div class="form-row">
                                <div class="helper-text">
                                    <?= fa_image_tag('info-circle'); ?>
                                    <span><?= __("Milestone isn't visible for this issuetype / project combination, unless you specify a value here"); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (!empty($customfields_list)): ?>
                        <?php foreach ($customfields_list as $field => $info): ?>
                            <?php if (($issue instanceof Issue || isset($issues)) && $transition->hasAction(\pachno\core\entities\WorkflowTransitionAction::CUSTOMFIELD_SET_PREFIX . $field) && !$transition->getAction(\pachno\core\entities\WorkflowTransitionAction::CUSTOMFIELD_SET_PREFIX . $field)->hasTargetValue()): ?>
                                <div class="form-row" id="transition_popup_<?= $field; ?>_div_<?= $transition->getID(); ?>">
                                    <label for="transition_popup_set_<?= $field; ?>_<?= $transition->getID(); ?>"><?= $info['title']; ?></label>
                                    <?php if (array_key_exists('choices', $info) && is_array($info['choices'])): ?>
                                        <select name="<?= $field; ?>_id" id="transition_popup_set_<?= $field; ?>_<?= $transition->getID(); ?>">
                                            <?php foreach ($info['choices'] ?: array() as $choice): ?>
                                                <?php if (!$transition->hasPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::CUSTOMFIELD_VALIDATE_PREFIX . $field) || $transition->getPostValidationRule(\pachno\core\entities\WorkflowTransitionValidationRule::CUSTOMFIELD_VALIDATE_PREFIX . $field)->isValueValid($choice->getID())): ?>
                                                    <option value="<?= $choice->getID(); ?>"<?php if ($issue instanceof Issue && $issue->getCustomField($field) instanceof \pachno\core\entities\CustomDatatypeOption && $issue->getCustomField($field)->getID() == $choice->getID()): ?> selected<?php endif; ?>><?= __($choice->getName()); ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php elseif ($info['type'] == DatatypeBase::DATE_PICKER || $info['type'] == DatatypeBase::DATETIME_PICKER): ?>
                                        <div id="customfield_<?= $field; ?>_calendar_container"></div>
                                        <script type="text/javascript">
                                            //require(['domReady', 'pachno/index', 'calendarview'], function (domReady, pachno_index_js, Calendar) {
                                            //    domReady(function () {
                                            //        Calendar.setup({
                                            //            dateField: '<?//= $field; ?>//_id',
                                            //            parentElement: 'customfield_<?//= $field; ?>//_calendar_container'
                                            //        });
                                            //    });
                                            //});
                                        </script>
                                    <?php elseif ($info['type'] == DatatypeBase::INPUT_TEXTAREA_SMALL || $info['type'] == DatatypeBase::INPUT_TEXTAREA_MAIN):
                                        include_component('main/textarea', array('area_name' => $field . '_id', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => $field . '_' . $transition->getID(), 'height' => '120px', 'width' => '790px', 'value' => ''));
                                        elseif ($info['type'] == DatatypeBase::INPUT_TEXT): ?>
                                    <input type="text" name="<?= $field; ?>_id" placeholder="<?= $info['name'] ?>">
                                    <?php else: ?>
                                        <select name="<?= $field; ?>_id" id="transition_popup_set_<?= $field; ?>_<?= $transition->getID(); ?>">
                                            <?php

                                                switch ($info['type']) {
                                                    case DatatypeBase::EDITIONS_CHOICE:
                                                        foreach ($project->getEditions() as $choice): ?>
                                                            <option value="<?= $choice->getID(); ?>"<?php if ($issue instanceof Issue && $issue->getCustomField($field) instanceof \pachno\core\entities\Edition && $issue->getCustomField($field)->getID() == $choice->getID()): ?> selected<?php endif; ?>><?= __($choice->getName()); ?></option>
                                                        <?php endforeach;
                                                        break;
                                                    case DatatypeBase::MILESTONE_CHOICE:
                                                        foreach ($project->getMilestones() as $choice): ?>
                                                            <option value="<?= $choice->getID(); ?>"<?php if ($issue instanceof Issue && $issue->getCustomField($field) instanceof \pachno\core\entities\Milestone && $issue->getCustomField($field)->getID() == $choice->getID()): ?> selected<?php endif; ?>><?= __($choice->getName()); ?></option>
                                                        <?php endforeach;
                                                        break;
                                                    case DatatypeBase::STATUS_CHOICE:
                                                        foreach (\pachno\core\entities\Status::getAll() as $choice): ?>
                                                            <option value="<?= $choice->getID(); ?>"<?php if ($issue instanceof Issue && $issue->getCustomField($field) instanceof \pachno\core\entities\Edition && $issue->getCustomField($field)->getID() == $choice->getID()): ?> selected<?php endif; ?>><?= __($choice->getName()); ?></option>
                                                        <?php endforeach;
                                                        break;
                                                    case DatatypeBase::COMPONENTS_CHOICE:
                                                        foreach ($project->getComponents() as $choice): ?>
                                                            <option value="<?= $choice->getID(); ?>"<?php if ($issue instanceof Issue && $issue->getCustomField($field) instanceof \pachno\core\entities\Edition && $issue->getCustomField($field)->getID() == $choice->getID()): ?> selected<?php endif; ?>><?= __($choice->getName()); ?></option>
                                                        <?php endforeach;
                                                        break;
                                                    case DatatypeBase::RELEASES_CHOICE:
                                                        foreach ($project->getBuilds() as $choice): ?>
                                                            <option value="<?= $choice->getID(); ?>"<?php if ($issue instanceof Issue && $issue->getCustomField($field) instanceof \pachno\core\entities\Edition && $issue->getCustomField($field)->getID() == $choice->getID()): ?> selected<?php endif; ?>><?= __($choice->getName()); ?></option>
                                                        <?php endforeach;
                                                        break;
                                                }
                                            ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if ($transition->hasAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_USER_STOP_WORKING)): ?>
                        <?php if ($issue instanceof Issue): ?>
                            <div class="form-row" id="transition_popup_stop_working_div_<?= $transition->getID(); ?>">
                                <label for="transition_popup_set_stop_working"><?= __('Log time spent'); ?></label>
                                <div class="time_logger_summary">
                                    <?php $time_spent = $issue->calculateTimeSpent(); ?>
                                    <input type="radio" <?= (array_sum($time_spent) == 0) ? ' disabled' : ' checked'; ?> name="did" id="transition_popup_set_stop_working_<?= $transition->getID(); ?>" value="something" onchange="$('#transition_popup_set_stop_working_specify_log_div_<?= $transition->getID(); ?>').hide();"><label for="transition_popup_set_stop_working_<?= $transition->getID(); ?>" class="simple"><?= __('Yes'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;<span class="faded_out"><?= __($issue->getTimeLoggerSomethingSummaryText(), array('%minutes' => $time_spent['minutes'], '%hours' => $time_spent['hours'], '%days' => $time_spent['days'], '%weeks' => $time_spent['weeks'])); ?></span><br>
                                    <input type="radio" name="did" id="transition_popup_set_stop_working_specify_log_<?= $transition->getID(); ?>" value="this" onchange="$('#transition_popup_set_stop_working_specify_log_div_<?= $transition->getID(); ?>').show()"><label for="transition_popup_set_stop_working_specify_log_<?= $transition->getID(); ?>" class="simple"><?= __('Yes, let me specify'); ?></label><br>
                                    <input type="radio" <?php if (array_sum($time_spent) == 0) echo ' checked'; ?> name="did" id="transition_popup_set_stop_working_no_log_<?= $transition->getID(); ?>" value="nothing" onchange="$('#transition_popup_set_stop_working_specify_log_div_<?= $transition->getID(); ?>').hide();"><label for="transition_popup_set_stop_working_no_log_<?= $transition->getID(); ?>" class="simple"><?= __('No'); ?></label>
                                </div>
                            </div>
                            <div id="transition_popup_set_stop_working_specify_log_div_<?= $transition->getID(); ?>" class="lightyellowbox issue_timespent_form" style="display: none;">
                                <?php // include_component('main/issuespenttimeentry', compact('issue')); ?>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="did" id="transition_popup_set_stop_working_no_log_<?= $transition->getID(); ?>" value="nothing">
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($issue instanceof Issue || isset($issues)): ?>
                        <div class="form-row">
                            <label for="transition_popup_comment_body"><?= __('Write a comment if you want it to be added'); ?></label><br>
                            <?php include_component('main/textarea', array('area_name' => 'comment_body', 'target_type' => 'issue', 'target_id' => (isset($issue)) ? $issue->getID() : 0, 'area_id' => 'transition_popup_comment_body_' . $transition->getID(), 'height' => '120px', 'width' => '790px', 'value' => '')); ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-row submit-container">
                        <button type="submit" class="workflow_transition_submit_button primary" id="transition_working_<?= $transition->getID(); ?>_submit"><?= image_tag('spinning_16.gif', array('style' => 'display: none;', 'id' => 'transition_working_' . $transition->getID() . '_indicator')) . $transition->getName(); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php if ((($issue instanceof Issue && $issue->canEditAssignee()) || isset($issues)) && $transition->hasAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE) && !$transition->getAction(\pachno\core\entities\WorkflowTransitionAction::ACTION_ASSIGN_ISSUE)->hasTargetValue()): ?>
        <?php include_component('main/identifiableselector', array('html_id' => 'popup_assigned_to_change_' . $transition->getID(),
            'header' => __('Assign this issue'),
            'callback' => "Pachno.Issues.updateWorkflowAssignee('" . make_url('issue_gettempfieldvalue', array('field' => 'assigned_to', 'identifiable_type' => '%identifiable_type', 'value' => '%identifiable_value')) . "', %identifiable_value, %identifiable_type, " . $transition->getID() . ");",
            'teamup_callback' => "Pachno.Issues.updateWorkflowAssigneeTeamup('" . make_url('issue_gettempfieldvalue', array('field' => 'assigned_to', 'identifiable_type' => '%identifiable_type', 'value' => '%identifiable_value')) . "', %identifiable_value, %identifiable_type, " . $transition->getID() . ");",
            'clear_link_text' => __('Clear current assignee'),
            'base_id' => 'popup_assigned_to_' . $transition->getID(),
            'include_teams' => true,
            'allow_clear' => false,
            'style' => array('top' => '68px', 'right' => '5px'),
            'absolute' => true)); ?>
    <?php endif; ?>
</div>
