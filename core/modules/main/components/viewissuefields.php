<?php

use pachno\core\entities\DatatypeBase;
use pachno\core\framework\Context;
    use pachno\core\framework\Event;
    use pachno\core\framework\I18n;
    use pachno\core\entities\CustomDatatype;
    use pachno\core\entities\Issue;
    use pachno\core\entities\Status;
    use pachno\core\entities\Team;
    use pachno\core\entities\User;
    use pachno\core\helpers\TextParser;

    /**
     * @var Issue $issue
     * @var \pachno\core\framework\Response $pachno_response
     * @var \pachno\core\entities\User $pachno_user
     * @var Status[] $statuses
     * @var \pachno\core\entities\Issuetype[] $issuetypes
     * @var mixed[][] $fields_list
     * @var integer $affected_count
     * @var CustomDatatype[][] $customfields_list
     */
?>
<?php Event::createNew('core', 'viewissue_left_top', $issue)->trigger(); ?>
<div class="fields-list-container">
    <div class="header">
        <span class="icon"><?= fa_image_tag('clipboard-check'); ?></span>
        <span class="name"><?= __('Important details'); ?></span>
    </div>
    <ul class="fields-list" id="issue_details_fieldslist_basics">
        <li id="shortname_field"<?php if (!$issue->isShortnameVisible()): ?> style="display: none;"<?php endif; ?> >
            <div class="label" id="shortname_header">
                <?= __('Issue label'); ?>
            </div>
            <div class="value">
                <?php if ($issue->isEditable() && $issue->canEditShortname()): ?>
                    <a href="javascript:void(0);" class="dropper dropdown_link" title="<?= __('Click to edit issue label'); ?>"><?= image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                    <ul id="shortname_change" class="popup_box more_actions_dropdown with-header">
                        <li class="header"><?= __('Set issue label'); ?></li>
                        <li class="nohover">
                            <form id="shortname_form" action="<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'shortname')); ?>" method="post" onSubmit="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'shortname')) ?>', 'shortname', 'shortname'); return false;">
                                <input type="text" name="shortname_value" value="<?= $issue->getShortname(); ?>" /><?= __('%save or %cancel', array('%save' => '<input type="submit" value="'.__('Save').'">', '%cancel' => '<a href="#" onclick="$(\'shortname_change\').hide(); return false;">'.__('cancel').'</a>')); ?>
                            </form>
                        </li>
                        <li id="shortname_spinning" style="margin-top: 3px; display: none;"><?= image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                        <li id="shortname_change_error" class="error_message" style="display: none;"></li>
                    </ul>
                <?php endif; ?>
                <span id="shortname_name"><?php if ($issue->hasShortname()) echo $issue->getShortname(); ?></span>
                <span class="no-value" id="no_shortname"<?php if ($issue->hasShortname()): ?> style="display: none;"<?php endif; ?>><?= __('No label set'); ?></span>
            </div>
        </li>
        <?php $field = $fields_list['priority']; unset($fields_list['priority']); ?>
        <?php include_component('main/viewissuefield', array('field' => 'priority', 'info' => $field, 'issue' => $issue)); ?>
        <?php $field = $fields_list['resolution']; unset($fields_list['resolution']); ?>
        <?php include_component('main/viewissuefield', array('field' => 'resolution', 'info' => $field, 'issue' => $issue)); ?>
        <?php $field = $fields_list['category']; unset($fields_list['category']); ?>
        <?php include_component('main/viewissuefield', array('field' => 'category', 'info' => $field, 'issue' => $issue)); ?>
        <?php $field = $fields_list['milestone']; unset($fields_list['milestone']); ?>
        <?php include_component('main/viewissuefield', array('field' => 'milestone', 'info' => $field, 'issue' => $issue)); ?>
        <li id="percent_complete_field" class="issue-field <?php if (!$issue->isPercentCompletedVisible()): ?> hidden<?php endif; ?> <?php if ($issue->canEditPercentage()) echo ' editable'; ?>">
            <div class="fancy-dropdown-container">
                <div class="fancy-dropdown">
                    <label><?= __('Progress'); ?></label>
                    <span class="value" data-dynamic-field-value data-field="percent_complete" data-issue-id="<?= $issue->getId(); ?>">
                        <?php include_component('main/percentbar', array('percent' => $issue->getPercentCompleted())); ?>
                    </span>
                    <?php if ($issue->canEditPercentage()): ?>
                        <?php echo fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container">
                            <div class="list-mode">
                                <div class="header"><?= __('Set percent completed'); ?></div>
                                <?php foreach(range(0, 100, 10) as $percentage): ?>
                                    <input type="radio" class="fancy-checkbox" id="issue_<?= $issue->getId(); ?>_fields_percent_complete_<?= $percentage; ?>" name="issues[<?= $issue->getId(); ?>]fields[percent_complete]" value="<?= $percentage; ?>" <?php if ($issue->getPercentCompleted() == $percentage) echo ' checked'; ?> data-trigger-issue-update data-field="percent_complete" data-issue-id="<?= $issue->getId(); ?>">
                                    <label for="issue_<?= $issue->getId(); ?>_fields_percent_complete_<?= $percentage; ?>" class="list-item">
                                        <span class="name"><?= $percentage; ?>%</span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </li>
    </ul>
</div>
<div class="fields-list-container" id="issue_details_fieldslist_pain_container" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
    <div class="header">
        <span class="icon"><?= fa_image_tag('list-ol'); ?></span>
        <span class="name"><?= __('User pain'); ?></span>
    </div>
    <ul class="issue_details fields-list" id="issue_details_fieldslist_pain">
        <li id="pain_bug_type_field" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
            <div class="label" id="pain_bug_type_header"><?= __('Type of bug'); ?></div>
            <div class="value" id="pain_bug_type_content">
                <?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
                    <a href="javascript:void(0);" class="dropper dropdown_link" title="<?= __('Click to triage type of bug'); ?>"><?= image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                    <ul class="popup_box more_actions_dropdown with-header" id="pain_bug_type_change">
                        <li class="header"><?= __('Triage bug type'); ?></li>
                        <li>
                            <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_bug_type', 'pain_bug_type_id' => 0)); ?>', 'pain_bug_type');"><?= __('Clear bug type'); ?></a>
                        </li>
                        <li class="list-item separator"></li>
                        <?php foreach (Issue::getPainTypesOrLabel('pain_bug_type') as $choice_id => $choice): ?>
                            <li>
                                <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_bug_type', 'pain_bug_type_id' => $choice_id)); ?>', 'pain_bug_type');"><?= $choice; ?></a>
                            </li>
                        <?php endforeach; ?>
                        <li id="pain_bug_type_spinning" style="margin-top: 3px; display: none;"><?= image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                        <li id="pain_bug_type_change_error" class="error_message" style="display: none;"></li>
                    </ul>
                <?php endif; ?>
                <span id="pain_bug_type_name"<?php if (!$issue->hasPainBugType()): ?> style="display: none;"<?php endif; ?>>
                    <?= ($issue->hasPainBugType()) ? $issue->getPainBugTypeLabel() : ''; ?>
                </span>
                <span class="no-value" id="no_pain_bug_type"<?php if ($issue->hasPainBugType()): ?> style="display: none;"<?php endif; ?>><?= __('Not triaged'); ?></span>
            </div>
        </li>
        <li id="pain_likelihood_field" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
            <div class="label" id="pain_likelihood_header"><?= __('Likelihood'); ?></div>
            <div class="value" id="pain_likelihood_content">
                <?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
                    <a href="javascript:void(0);" class="dropper dropdown_link" title="<?= __('Click to triage likelihood'); ?>"><?= image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                    <ul class="popup_box more_actions_dropdown with-header" id="pain_likelihood_change">
                        <li class="header"><?= __('Triage likelihood'); ?></li>
                        <li>
                            <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_likelihood', 'pain_likelihood_id' => 0)); ?>', 'pain_likelihood');"><?= __('Clear likelihood'); ?></a>
                        </li>
                        <li class="list-item separator"></li>
                        <?php foreach (Issue::getPainTypesOrLabel('pain_likelihood') as $choice_id => $choice): ?>
                            <li>
                                <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_likelihood', 'pain_likelihood_id' => $choice_id)); ?>', 'pain_likelihood');"><?= $choice; ?></a>
                            </li>
                        <?php endforeach; ?>
                        <li id="pain_likelihood_spinning" style="margin-top: 3px; display: none;"><?= image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                        <li id="pain_likelihood_change_error" class="error_message" style="display: none;"></li>
                    </ul>
                <?php endif; ?>
                <span id="pain_likelihood_name"<?php if (!$issue->hasPainLikelihood()): ?> style="display: none;"<?php endif; ?>>
                    <?= ($issue->hasPainLikelihood()) ? $issue->getPainLikelihoodLabel() : ''; ?>
                </span>
                <span class="no-value" id="no_pain_likelihood"<?php if ($issue->hasPainLikelihood()): ?> style="display: none;"<?php endif; ?>><?= __('Not triaged'); ?></span>
            </div>
        </li>
        <li id="pain_effect_field" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
            <div class="label" id="pain_effect_header"><?= __('Effect'); ?></div>
            <div class="value" id="pain_effect_content">
                <?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
                    <a href="javascript:void(0);" class="dropper dropdown_link" title="<?= __('Click to triage effect'); ?>"><?= image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                    <ul class="popup_box more_actions_dropdown with-header" id="pain_effect_change">
                        <li class="header"><?= __('Triage effect'); ?></li>
                        <li>
                            <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_effect', 'pain_effect_id' => 0)); ?>', 'pain_effect');"><?= __('Clear effect'); ?></a>
                        </li>
                        <li class="list-item separator"></li>
                        <?php foreach (Issue::getPainTypesOrLabel('pain_effect') as $choice_id => $choice): ?>
                            <li>
                                <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_effect', 'pain_effect_id' => $choice_id)); ?>', 'pain_effect');"><?= $choice; ?></a>
                            </li>
                        <?php endforeach; ?>
                        <li id="pain_effect_spinning" style="margin-top: 3px; display: none;"><?= image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                        <li id="pain_effect_change_error" class="error_message" style="display: none;"></li>
                    </ul>
                <?php endif; ?>
                <span id="pain_effect_name"<?php if (!$issue->hasPainEffect()): ?> style="display: none;"<?php endif; ?>>
                    <?= ($issue->hasPainEffect()) ? $issue->getPainEffectLabel() : ''; ?>
                </span>
                <span class="no-value" id="no_pain_effect"<?php if ($issue->hasPainEffect()): ?> style="display: none;"<?php endif; ?>><?= __('Not triaged'); ?></span>
            </div>
        </li>
    </ul>
</div>
<div id="viewissue_affected_container" class="fields-list-container <?= (!$affected_count) ? 'not-visible' : ''; ?>">
    <div class="header">
        <span class="name"><?= __('Affected by this issue %count', ['%count' => '']); ?><span id="viewissue_affects_count" class="count-badge"><?= $affected_count; ?></span></span>
    </div>
    <div id="viewissue_affected">
        <?php include_component('main/issueaffected', array('issue' => $issue)); ?>
    </div>
</div>
<div class="fields-list-container" id="issue_timetracking_container">
    <div class="header">
        <span class="icon"><?= fa_image_tag('clock'); ?></span>
        <span class="name"><?= __('Times and dates'); ?></span>
    </div>
    <ul class="issue_details fields-list" id="issue_details_fieldslist_time">
        <li id="estimated_time_field" class="issue-field <?php if (!$issue->isEstimatedTimeVisible()): ?> hidden<?php endif; ?> <?php if ($issue->isUpdateable() && $issue->canEditEstimatedTime()) echo 'editable'; ?>">
            <div id="estimated_time_content" class="value fancy-dropdown-container">
                <div class="fancy-dropdown">
                    <label><?= __('Estimated time'); ?></label>
                    <span class="value" data-dynamic-field-value data-field="estimated_time" data-issue-id="<?= $issue->getId(); ?>">
                        <?= ($issue->hasEstimatedTime()) ? Issue::getFormattedTime($issue->getEstimatedTime(true, true)) : __('Not estimated'); ?>
                    </span>
                    <?php if ($issue->isUpdateable() && $issue->canEditEstimatedTime()): ?>
                        <?php echo fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </li>
        <li id="spent_time_field" class="<?php if (!$issue->isSpentTimeVisible()): ?> hidden<?php endif; ?> <?php if ($issue->canEditEstimatedTime()) echo 'trigger-backdrop'; ?>" data-url="<?= make_url('get_partial_for_backdrop', array('key' => 'issue_spenttimes', 'issue_id' => $issue->getID())); ?>">
            <div id="estimated_time_content" class="field-container <?php if ($issue->canEditEstimatedTime()) echo 'editable'; ?>">
                <span class="label"><?= __('Time spent'); ?></span>
                <span class="value" data-dynamic-field-value data-field="spent_time" data-issue-id="<?= $issue->getId(); ?>">
                    <span><?= ($issue->hasEstimatedTime()) ? Issue::getFormattedTime($issue->getSpentTime(true, true)) : __('Not estimated'); ?></span>
                </span>
            </div>
        </li>
        <?php foreach ($customfields_list as $field => $info): ?>
            <?php if (!in_array($info['type'], [DatatypeBase::DATE_PICKER, DatatypeBase::DATETIME_PICKER])) continue; ?>
            <?php include_component('main/viewissuecustomfield', compact('field', 'info', 'issue')); ?>
        <?php endforeach; ?>
    </ul>
</div><div class="fields-list-container">
    <div class="header">
        <span class="icon"><?= fa_image_tag('users'); ?></span>
        <span class="name"><?= __('People involved'); ?></span>
    </div>
    <ul class="issue_details fields-list" id="issue_details_fieldslist_people">
        <li id="posted_by_field" class="issue-field <?php if ($issue->isUpdateable() && $issue->canEditPostedBy()) echo 'editable'; ?>">
            <div class="fancy-dropdown-container">
                <div class="fancy-dropdown" data-default-label="<?= __('Unknown'); ?>">
                    <label><?= __('Posted by'); ?></label>
                    <?= include_component('main/userdropdown', ['user' => $issue->getPostedBy(), 'size' => 'small']); ?>
                    <?php if ($issue->isUpdateable() && $issue->canEditPostedBy()): ?>
                        <?php echo fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container">
                            <?php include_component('main/identifiableselector', array(    'html_id'             => 'posted_by_change',
                                                                                    'header'             => __('Change issue creator'),
                                                                                    'callback'             => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'posted_by', 'identifiable_type' => 'user', 'value' => '%identifiable_value')) . "', 'posted_by');",
                                                                                    'team_callback'         => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'posted_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value')) . "', 'posted_by');",
                                                                                    'teamup_callback'     => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'posted_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value', 'teamup' => true)) . "', 'posted_by');",
                                                                                    'clear_link_text'    => __('Clear current creator'),
                                                                                    'base_id'            => 'posted_by',
                                                                                    'include_teams'        => false,
                                                                                    'absolute'            => false)); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </li>
        <li id="owned_by_field"class="issue-field <?php if ($issue->isUpdateable() && $issue->canEditOwner()) echo 'editable'; ?>">
            <div class="fancy-dropdown-container">
                <div class="fancy-dropdown" data-default-label="<?= __('Unknown'); ?>">
                    <label><?= __('Owned by'); ?></label>
                    <div class="value">
                        <?php if ($issue->getOwner() instanceof User): ?>
                            <?= include_component('main/userdropdown', array('user' => $issue->getOwner())); ?>
                        <?php elseif ($issue->getOwner() instanceof Team): ?>
                            <?= include_component('main/teamdropdown', array('team' => $issue->getOwner())); ?>
                        <?php endif; ?>
                    </div>
                    <?php if ($issue->isUpdateable() && $issue->canEditOwner()): ?>
                        <?php echo fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container">
                            <?php include_component('main/identifiableselector', array(    'html_id'             => 'owned_by_change',
                                                                                    'header'             => __('Change issue creator'),
                                                                                    'callback'             => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'user', 'value' => '%identifiable_value')) . "', 'owned_by');",
                                                                                    'team_callback'         => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value')) . "', 'owned_by');",
                                                                                    'teamup_callback'     => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value', 'teamup' => true)) . "', 'owned_by');",
                                                                                    'clear_link_text'    => __('Clear current creator'),
                                                                                    'base_id'            => 'owned_by',
                                                                                    'include_teams'        => true,
                                                                                    'absolute'            => false)); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </li>
        <li id="assigned_to_field"class="issue-field <?php if ($issue->isUpdateable() && $issue->canEditAssignee()) echo 'editable'; ?>">
            <div class="fancy-dropdown-container">
                <div class="fancy-dropdown" data-default-label="<?= __('Unknown'); ?>">
                    <label><?= __('Assigned to'); ?></label>
                    <div class="value">
                        <?php if ($issue->getAssignee() instanceof User): ?>
                            <?= include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
                        <?php elseif ($issue->getAssignee() instanceof Team): ?>
                            <?= include_component('main/teamdropdown', array('team' => $issue->getAssignee())); ?>
                        <?php endif; ?>
                    </div>
                    <?php if ($issue->isUpdateable() && $issue->canEditAssignee()): ?>
                        <?php echo fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container">
                            <?php include_component('main/identifiableselector', array(    'html_id'             => 'assigned_to_change',
                                                                                    'header'             => __('Change issue creator'),
                                                                                    'callback'             => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'user', 'value' => '%identifiable_value')) . "', 'assigned_to');",
                                                                                    'team_callback'         => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'team', 'value' => '%identifiable_value')) . "', 'assigned_to');",
                                                                                    'teamup_callback'     => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'team', 'value' => '%identifiable_value', 'teamup' => true)) . "', 'assigned_to');",
                                                                                    'clear_link_text'    => __('Clear current creator'),
                                                                                    'base_id'            => 'assigned_to',
                                                                                    'include_teams'        => true,
                                                                                    'absolute'            => false)); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </li>
        <li id="subscribers_field">
            <a class="field-container trigger-backdrop tooltip-container <?php if ($pachno_user->canManageProject($issue->getProject())) echo ' editable'; ?>" href="javascript:void(0);" data-url="<?= make_url('get_partial_for_backdrop', array('key' => 'issue_subscribers', 'issue_id' => $issue->getID())); ?>">
                <span class="label">
                    <?= __('Subscribers'); ?>
                </span>
                <span class="value">
                    <span class="count-badge">
                        <?= fa_image_tag('users', ['class' => 'icon']); ?>
                        <span data-dynamic-value data-field="number_of_subscribers"><?= count($issue->getSubscribers()); ?></span>
                    </span>
                </span>
                <span class="tooltip from-right"><?= __('Click here to show the list of subscribers'); ?></span>
            </a>
        </li>
    </ul>
</div>
<?php if (count($fields_list) || count($customfields_list)): ?>
    <div class="fields-list-container" id="issue_details_fieldslist">
        <div class="header">
            <span class="icon"><?= fa_image_tag('stream'); ?></span>
            <span class="name"><?= __('Other details'); ?></span>
        </div>
        <ul class="issue_details fields-list">
            <?php foreach ($fields_list as $field => $info): ?>
                <?php include_component('main/viewissuefield', compact('field', 'info', 'issue')); ?>
            <?php endforeach; ?>
            <?php foreach ($customfields_list as $field => $info): ?>
                <?php if (in_array($info['type'], [DatatypeBase::INPUT_TEXTAREA_MAIN, DatatypeBase::DATE_PICKER, DatatypeBase::DATETIME_PICKER])) continue; ?>
                <?php include_component('main/viewissuecustomfield', compact('field', 'info', 'issue')); ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<?php Event::createNew('core', 'viewissue_left_after_attachments', $issue)->trigger(); ?>
<?php if ($issue->getNumberOfDuplicateIssues()): ?>
    <div class="fields-list-container" id="viewissue_duplicate_issues_container">
        <div class="header">
            <span class="name"><?= __('Duplicate issues %count', ['%count' => '']); ?><span id="viewissue_duplicate_issues_count" class="count-badge"><?= $issue->getNumberOfDuplicateIssues(); ?></span></span>
        </div>
        <div id="viewissue_duplicate_issues">
            <?php include_component('main/duplicateissues', array('issue' => $issue)); ?>
        </div>
    </div>
<?php endif; ?>
<?php Event::createNew('core', 'viewissue_left_bottom', $issue)->trigger(); ?>
