<?php

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
        <li id="percent_complete_field"<?php if (!$issue->isPercentCompletedVisible()): ?> style="display: none;"<?php endif; ?>>
            <div class="label" id="percent_complete_header"><?= __('Progress'); ?></div>
            <div class="value" id="percent_complete_content">
                <?php if ($issue->canEditPercentage()): ?>
                    <a href="javascript:void(0);" class="dropper dropdown_link" title="<?= __('Click to set percent completed'); ?>"><?= image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                    <ul class="popup_box more_actions_dropdown with-header" id="percent_complete_change">
                        <li class="header"><?= __('Set percent completed'); ?></li>
                        <li><a href="javascript:void(0);" onclick="Pachno.Issues.Field.setPercent('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'percent_complete', 'percent' => 0)); ?>', 'set');"><?= __('Clear percent completed'); ?></a></li>
                        <li class="list-item separator"></li>
                        <li class="nohover">
                            <form id="percent_complete_form" method="post" accept-charset="<?= Context::getI18n()->getCharset(); ?>" action="" onsubmit="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'percent_complete')); ?>', 'percent_complete', 'percent_complete');return false;">
                                <label for="set_percent"><?= __('Percent complete'); ?></label>&nbsp;<input type="text" style="width: 40px;" name="percent" id="set_percent" value="<?= $issue->getPercentCompleted(); ?>">&percnt;
                                <input type="submit" value="<?= __('Set'); ?>">
                            </form>
                        </li>
                        <li id="percent_complete_spinning" style="margin-top: 3px; display: none;"><?= image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                    </ul>
                <?php endif; ?>
                <div id="issue_percent_complete" title="<?= __('%percentage % completed', array('%percentage' => $issue->getPercentCompleted())); ?>">
                    <?php include_component('main/percentbar', array('percent' => $issue->getPercentCompleted(), 'height' => 4)); ?>
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
<div class="fields-list-container">
    <div class="header">
        <span class="icon"><?= fa_image_tag('users'); ?></span>
        <span class="name"><?= __('People involved'); ?></span>
    </div>
    <ul class="issue_details fields-list" id="issue_details_fieldslist_people">
        <li id="owned_by_field" style="<?php if (!$issue->isOwnedByVisible()): ?> display: none;<?php endif; ?>">
            <div class="label" id="owned_by_header"><?= __('Owned by'); ?></div>
            <div class="value dropper-container" id="owned_by_content">
                <div class="value-container dropper">
                    <div style="width: 170px; display: <?php if ($issue->isOwned()): ?>inline<?php else: ?>none<?php endif; ?>;" id="owned_by_name">
                        <?php if ($issue->getOwner() instanceof User): ?>
                            <?= include_component('main/userdropdown', array('user' => $issue->getOwner())); ?>
                        <?php elseif ($issue->getOwner() instanceof Team): ?>
                            <?= include_component('main/teamdropdown', array('team' => $issue->getOwner())); ?>
                        <?php endif; ?>
                    </div>
                    <span class="no-value" id="no_owned_by"<?php if ($issue->isOwned()): ?> style="display: none;"<?php endif; ?>><?= __('Not owned by anyone'); ?></span>
                    <?php if ($issue->isUpdateable() && $issue->canEditOwner()): ?>
                        <?= fa_image_tag('angle-down', ['class' => 'dropdown-indicator']); ?>
                    <?php endif; ?>
                </div>
                <?php if ($issue->isUpdateable() && $issue->canEditOwner()): ?>
                    <div class="dropdown-container">
                        <?php include_component('main/identifiableselector', array(    'html_id'             => 'owned_by_change',
                                                                                'header'             => __('Change issue owner'),
                                                                                'callback'             => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'user', 'value' => '%identifiable_value')) . "', 'owned_by');",
                                                                                'team_callback'         => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value')) . "', 'owned_by');",
                                                                                'teamup_callback'     => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value', 'teamup' => true)) . "', 'owned_by');",
                                                                                'clear_link_text'    => __('Clear current owner'),
                                                                                'base_id'            => 'owned_by',
                                                                                'include_teams'        => true,
                                                                                'absolute'            => true)); ?>
                    </div>
                <?php endif; ?>
            </div>
        </li>
        <li id="assigned_to_field">
            <div class="label" id="assigned_to_header"><?= __('Assigned to'); ?></div>
            <div class="value dropper-container" id="assigned_to_content">
                <div class="value-container <?php if ($issue->canEditAssignee() && $issue->isUpdateable()): ?>dropper<?php endif; ?>">
                    <div id="assigned_to_name">
                        <?php if ($issue->getAssignee() instanceof User): ?>
                            <?= include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
                        <?php elseif ($issue->getAssignee() instanceof Team): ?>
                            <?= include_component('main/teamdropdown', array('team' => $issue->getAssignee())); ?>
                        <?php endif; ?>
                    </div>
                    <span class="no-value" id="no_assigned_to"<?php if ($issue->isAssigned()): ?> style="display: none;"<?php endif; ?>><?= __('Not assigned to anyone'); ?></span>
                    <?php if ($issue->canEditAssignee() && $issue->isUpdateable()): ?>
                        <?= fa_image_tag('angle-down', ['class' => 'dropdown-indicator']); ?>
                    <?php endif; ?>
                </div>
                <?php if ($issue->canEditAssignee() && $issue->isEditable()): ?>
                    <div class="dropdown-container">
                        <?php include_component('main/identifiableselector', array(    'html_id'             => 'assigned_to_change',
                            'header'             => __('Assign this issue'),
                            'callback'             => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'user', 'value' => '%identifiable_value')) . "', 'assigned_to');",
                            'team_callback'         => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'team', 'value' => '%identifiable_value')) . "', 'assigned_to');",
                            'teamup_callback'     => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'team', 'value' => '%identifiable_value', 'teamup' => true)) . "', 'assigned_to');",
                            'clear_link_text'    => __('Clear current assignee'),
                            'base_id'            => 'assigned_to',
                            'include_teams'        => true,
                            'absolute'            => true)); ?>
                    </div>
                <?php endif; ?>
            </div>
        </li>
        <li id="subscribers_field">
            <div class="label" id="subscribers_header">
                <?= __('Subscribers'); ?>
            </div>
            <div class="value">
                <a href="javascript:void(0)" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', array('key' => 'issue_subscribers', 'issue_id' => $issue->getID())); ?>');"><?= __('%number_of subscriber(s)', array('%number_of' => '<span id="subscribers_field_count">'.count($issue->getSubscribers()).'</span>')); ?></a>
            </div>
            <div class="tooltip from-above"><?= __('Click here to show the list of subscribers'); ?></div>
        </li>
    </ul>
</div>
<div class="fields-list-container" id="issue_timetracking_container">
    <div class="header">
        <span class="icon"><?= fa_image_tag('clock'); ?></span>
        <span class="name"><?= __('Times and dates'); ?></span>
    </div>
    <ul class="issue_details fields-list" id="issue_details_fieldslist_time">
        <li id="estimated_time_field"<?php if (!$issue->isEstimatedTimeVisible()): ?> style="display: none;"<?php endif; ?>>
            <div class="label" id="estimated_time_header"><?= __('Estimated time'); ?></div>
            <div class="value <?php if ($issue->isUpdateable() && $issue->canEditEstimatedTime()): ?>dropper-container<?php endif; ?>" id="estimated_time_content">
                <div class="value-container <?php if ($issue->isUpdateable() && $issue->canEditEstimatedTime()): ?>dropper<?php endif; ?>">
                    <span id="estimated_time_<?= $issue->getID(); ?>_name"<?php if (!$issue->hasEstimatedTime()): ?> style="display: none;"<?php endif; ?>>
                        <?= Issue::getFormattedTime($issue->getEstimatedTime(true, true)); ?>
                    </span>
                    <span class="no-value" id="no_estimated_time_<?= $issue->getID(); ?>"<?php if ($issue->hasEstimatedTime()): ?> style="display: none;"<?php endif; ?>><?= __('Not estimated'); ?></span>
                    <?php if ($issue->isUpdateable() && $issue->canEditEstimatedTime()): ?>
                        <?= fa_image_tag('angle-down', ['class' => 'dropdown-indicator']); ?>
                    <?php endif; ?>
                </div>
                <?php if ($issue->isUpdateable() && $issue->canEditEstimatedTime()): ?>
                    <?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'estimated_time', 'mode' => 'left')); ?>
                <?php endif; ?>
            </div>
        </li>
        <li id="spent_time_field" style="position: relative;<?php if (!$issue->isSpentTimeVisible()): ?> display: none;<?php endif; ?>">
            <div class="label" id="spent_time_header"><?= __('Time spent'); ?></div>
            <div class="value" id="spent_time_content">
                <div class="value-container">
                    <span id="spent_time_<?= $issue->getID(); ?>_name"<?php if (!$issue->hasSpentTime()): ?> style="display: none;"<?php endif; ?>>
                        <a href="javascript:void(0)" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', array('key' => 'issue_spenttimes', 'issue_id' => $issue->getID())); ?>');" id="spent_time_<?= $issue->getID(); ?>_value"><?= Issue::getFormattedTime($issue->getSpentTime(true, true)); ?></a>
                    </span>
                    <span class="no-value" id="no_spent_time_<?= $issue->getID(); ?>"<?php if ($issue->hasSpentTime()): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0)" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', array('key' => 'issue_spenttimes', 'issue_id' => $issue->getID())); ?>');"><?= __('No time spent'); ?></a></span>
                    <div id="estimated_percentbar"<?php if (!($issue->hasEstimatedTime())): ?> style="display: none;"<?php endif; ?>><?php include_component('main/percentbar', array('percent' => $issue->getEstimatedPercentCompleted(), 'height' => 2)); ?></div>
                </div>
            </div>
            <div class="tooltip from-above"><?= __('Click here to see time logged against this issue'); ?></div>
        </li>
    </ul>
</div>
<?php if (count($fields_list) || count($customfields_list)): ?>
    <div class="fields-list-container">
        <div class="header">
            <span class="icon"><?= fa_image_tag('stream'); ?></span>
            <span class="name"><?= __('Other details'); ?></span>
        </div>
        <ul class="issue_details fields-list" id="issue_details_fieldslist">
            <?php foreach ($fields_list as $field => $info): ?>
                <?php include_component('main/viewissuefield', compact('field', 'info', 'issue')); ?>
            <?php endforeach; ?>
            <?php foreach ($customfields_list as $field => $info): ?>
                <?php if ($info['type'] == CustomDatatype::INPUT_TEXTAREA_MAIN): continue; endif; ?>
                <li id="<?= $field; ?>_field" <?php if (!$info['visible']): ?> style="display: none;"<?php endif; ?>>
                    <div class="label" id="<?= $field; ?>_header">
                        <?= $info['title']; ?>
                    </div>
                    <div class="value dropper-container" id="<?= $field; ?>_content">
                        <div class="value-container dropper">
                            <?php
                                switch ($info['type'])
                                {
                                    case CustomDatatype::INPUT_TEXTAREA_SMALL:
                                        ?>
                                        <span id="<?= $field; ?>_name"<?php if (!$info['value']): ?> style="display: none;"<?php endif; ?>>
                                            <?= TextParser::parseText($info['name'], false, null, array('headers' => false)); ?>
                                        </span>
                                        <span class="no-value" id="no_<?= $field; ?>"<?php if ($info['value']): ?> style="display: none;"<?php endif; ?>>
                                            <?= __('Not determined'); ?>
                                        </span>
                                        <?php
                                        break;
                                    case CustomDatatype::USER_CHOICE:
                                        ?>
                                        <span id="<?= $field; ?>_name"<?php if (!$info['value']): ?> style="display: none;"<?php endif; ?>>
                                            <?= include_component('main/userdropdown', array('user' => $info['value'])); ?>
                                        </span>
                                        <span class="no-value" id="no_<?= $field; ?>"<?php if ($info['value']): ?> style="display: none;"<?php endif; ?>>
                                            <?= __('Not determined'); ?>
                                        </span>
                                        <?php
                                        break;
                                    case CustomDatatype::TEAM_CHOICE:
                                        ?>
                                        <span id="<?= $field; ?>_name"<?php if (!$info['value']): ?> style="display: none;"<?php endif; ?>>
                                            <?= include_component('main/teamdropdown', array('team' => $info['identifiable'])); ?>
                                        </span>
                                        <span class="no-value" id="no_<?= $field; ?>"<?php if ($info['value']): ?> style="display: none;"<?php endif; ?>>
                                            <?= __('Not determined'); ?>
                                        </span>
                                        <?php
                                        break;
                                    case CustomDatatype::EDITIONS_CHOICE:
                                    case CustomDatatype::COMPONENTS_CHOICE:
                                    case CustomDatatype::RELEASES_CHOICE:
                                    case CustomDatatype::MILESTONE_CHOICE:
                                    case CustomDatatype::CLIENT_CHOICE:
                                         ?>
                                        <span id="<?= $field; ?>_name"<?php if (!$info['value']): ?> style="display: none;"<?php endif; ?>>
                                            <?= (isset($info['name'])) ? $info['name'] : __('Unknown'); ?>
                                        </span>
                                        <span class="no-value" id="no_<?= $field; ?>"<?php if ($info['value']): ?> style="display: none;"<?php endif; ?>>
                                            <?= __('Not determined'); ?>
                                        </span><?php
                                        break;
                                    case CustomDatatype::STATUS_CHOICE:
                                        $status = null;
                                        $value = null;
                                        $color = '#FFF';
                                        try
                                        {
                                            $status = new Status($info['name']);
                                            $value = $status->getName();
                                            $color = $status->getColor();
                                        }
                                        catch (\Exception $e) { }
                                        ?><span id="<?= $field; ?>_name"<?php if (!$info['value']): ?> style="display: none;"<?php endif; ?>><div class="status-badge" style="background-color: <?= $color; ?>;"><span><?= __($value); ?></span></div></span><span class="no-value" id="no_<?= $field; ?>"<?php if ($info['value']): ?> style="display: none;"<?php endif; ?>><?= __('Not determined'); ?></span><?php
                                        break;
                                    case CustomDatatype::DATE_PICKER:
                                    case CustomDatatype::DATETIME_PICKER:
                                        $pachno_response->addJavascript('calendarview');
                                        if (is_numeric($info['name'])) {
                                            $value = ($info['name']) ? date('Y-m-d' . ($info['type'] == CustomDatatype::DATETIME_PICKER ? ' H:i' : ''), $info['name']) : __('Not set');
                                        } else {
                                            $value = $info['name'];
                                        }
                                        ?><span id="<?= $field; ?>_name"<?php if (!$info['value']): ?> style="display: none;"<?php endif; ?>><?= $value; ?></span><span id="<?= $field; ?>_new_name" style="display: none;"><?= (int) $value; ?></span><span class="no-value" id="no_<?= $field; ?>"<?php if ($info['value']): ?> style="display: none;"<?php endif; ?>><?= __('Not set'); ?></span><?php
                                        break;
                                    default:
                                        if (!isset($info['name'])) {
                                            var_dump($info);
                                        } else {
                                            ?><span id="<?= $field; ?>_name"<?php if (!$info['value']): ?> style="display: none;"<?php endif; ?>><?= (filter_var($info['name'], FILTER_VALIDATE_URL) !== false) ? link_tag($info['name'], $info['name']) : $info['name']; ?></span><span class="no-value" id="no_<?= $field; ?>"<?php if ($info['value']): ?> style="display: none;"<?php endif; ?>><?= __('Not determined'); ?></span><?php
                                            break;
                                        }
                                }
                            ?>
                            <?php if ($issue->isUpdateable() && $issue->canEditCustomFields($field) && $info['editable']): ?>
                                <?= fa_image_tag('angle-down', ['class' => 'dropdown-indicator']); ?>
                            <?php endif; ?>
                        </div>
                        <?php if ($issue->isUpdateable() && $issue->canEditCustomFields($field) && $info['editable']): ?>
                            <div class="dropdown-container">
                                <?php if ($info['type'] == CustomDatatype::USER_CHOICE): ?>
                                    <?php include_component('main/identifiableselector', array(
                                        'html_id'             => $field.'_change',
                                        'header'             => __('Select a user'),
                                        'callback'             => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, 'identifiable_type' => 'user', $field.'_value' => '%identifiable_value')) . "', '".$field."');",
                                        'clear_link_text'    => __('Clear currently selected user'),
                                        'base_id'            => $field,
                                        'include_teams'        => false,
                                        'absolute'            => true)); ?>
                                <?php elseif ($info['type'] == CustomDatatype::TEAM_CHOICE): ?>
                                    <?php include_component('main/identifiableselector', array(
                                        'html_id'             => $field.'_change',
                                        'header'             => __('Select a team'),
                                        'callback'             => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, 'identifiable_type' => 'team', $field.'_value' => '%identifiable_value')) . "', '".$field."');",
                                        'clear_link_text'    => __('Clear currently selected team'),
                                        'base_id'            => $field,
                                        'include_teams'        => true,
                                        'include_users'        => false,
                                        'absolute'            => true)); ?>
                                <?php elseif ($info['type'] == CustomDatatype::CLIENT_CHOICE): ?>
                                    <?php include_component('main/identifiableselector', array(
                                        'html_id'             => $field.'_change',
                                        'header'             => __('Select a client'),
                                        'callback'             => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, 'identifiable_type' => 'client', $field.'_value' => '%identifiable_value')) . "', '".$field."');",
                                        'clear_link_text'    => __('Clear currently selected client'),
                                        'base_id'            => $field,
                                        'include_clients'    => true,
                                        'include_teams'        => false,
                                        'include_users'        => false,
                                        'absolute'            => true)); ?>
                                <?php else: ?>
                                    <div class="list-mode" id="<?= $field; ?>_change">
                                        <div class="header"><?= $info['change_header']; ?></div>
                                        <?php if (array_key_exists('choices', $info) && is_array($info['choices'])): ?>
                                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?= $field; ?>');">
                                                <span class="name"><?= $info['clear']; ?></span>
                                            </a>
                                            <div class="list-item separator"></div>
                                            <?php foreach ($info['choices'] ?: array() as $choice): ?>
                                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?= $field; ?>');">
                                                <span class="icon"><?= fa_image_tag('list'); ?></span>
                                                <span class="name"><?= __($choice->getName()); ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                        <?php elseif ($info['type'] == CustomDatatype::DATE_PICKER || $info['type'] == CustomDatatype::DATETIME_PICKER): ?>
                                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?= $field; ?>');">
                                                <span class="name"><?= $info['clear']; ?></span>
                                            </a>
                                            <div class="list-item separator"></div>
                                            <div class="list-item" id="customfield_<?= $field; ?>_calendar_container" style="padding: 0;"></div>
                                        <?php if ($info['type'] == CustomDatatype::DATETIME_PICKER): ?>
                                            <form id="customfield_<?= $field; ?>_form" method="post" class="list-item" accept-charset="<?= Context::getI18n()->getCharset(); ?>" action="" onsubmit="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>', '<?= $field; ?>', 'customfield_<?= $field; ?>');return false;">
                                                <div class="header"><?= __('Time'); ?></div>
                                                <input type="text" id="customfield_<?= $field; ?>_hour" value="00" style="width: 20px; font-size: 0.9em; text-align: center;">&nbsp;:&nbsp;
                                                <input type="text" id="customfield_<?= $field; ?>_minute" value="00" style="width: 20px; font-size: 0.9em; text-align: center;">
                                                <input type="hidden" name="<?= $field; ?>_value" value="<?= (int) $info['name'] - I18n::getTimezoneOffset(); ?>" id="<?= $field; ?>_value" />
                                                <input type="submit" class="button secondary" value="<?= __('Update'); ?>">
                                            </form>
                                        <?php endif; ?>
                                            <script type="text/javascript">
                                                require(['domReady', 'pachno/index', 'calendarview'], function (domReady, pachno_index_js, Calendar) {
                                                    domReady(function () {
                                                        Calendar.setup({
                                                            dateField: '<?= $field; ?>_new_name',
                                                            parentElement: 'customfield_<?= $field; ?>_calendar_container',
                                                            valueCallback: function(element, date) {
                                                                <?php if ($info['type'] == CustomDatatype::DATETIME_PICKER): ?>
                                                                var value = date.setUTCHours(parseInt($('customfield_<?= $field; ?>_hour').value));
                                                                var date  = new Date(value);
                                                                var value = Math.floor(date.setUTCMinutes(parseInt($('customfield_<?= $field; ?>_minute').value)) / 1000);
                                                                <?php else: ?>
                                                                var value = Math.floor(date.getTime() / 1000);
                                                                Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>?<?= $field; ?>_value='+value, '<?= $field; ?>');
                                                                <?php endif; ?>
                                                                $('<?= $field; ?>_value').value = value;
                                                            }
                                                        });
                                                        <?php if ($info['type'] == CustomDatatype::DATETIME_PICKER): ?>
                                                        var date = new Date(parseInt($('<?= $field; ?>_value').value) * 1000);
                                                        $('customfield_<?= $field; ?>_hour').value = date.getUTCHours();
                                                        $('customfield_<?= $field; ?>_minute').value = date.getUTCMinutes();
                                                        Event.observe($('customfield_<?= $field; ?>_hour'), 'change', function (event) {
                                                            var value = parseInt($('<?= $field; ?>_value').value);
                                                            var hours = parseInt(this.value);
                                                            if (value <= 0 || hours < 0 || hours > 24) return;
                                                            var date = new Date(value * 1000);
                                                            $('<?= $field; ?>_value').value = date.setUTCHours(parseInt(this.value)) / 1000;
                                                        });
                                                        Event.observe($('customfield_<?= $field; ?>_minute'), 'change', function (event) {
                                                            var value = parseInt($('<?= $field; ?>_value').value);
                                                            var minutes = parseInt(this.value);
                                                            if (value <= 0 || minutes < 0 || minutes > 60) return;
                                                            var date = new Date(value * 1000);
                                                            $('<?= $field; ?>_value').value = date.setUTCMinutes(parseInt(this.value)) / 1000;
                                                        });
                                                        <?php endif; ?>
                                                    });
                                                });
                                            </script>
                                        <?php else: ?>
                                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?= $field; ?>');">
                                                <span class="name"><?= $info['clear']; ?></span>
                                            </a>
                                            <div class="list-item separator"></div>
                                            <?php

                                        switch ($info['type'])
                                        {
                                        case CustomDatatype::EDITIONS_CHOICE:
                                            ?>
                                            <?php foreach ($issue->getProject()->getEditions() as $choice): ?>
                                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?= $field; ?>');">
                                                <span class="icon"><?= fa_image_tag('window-restore'); ?></span>
                                                <span class="name"><?= __($choice->getName()); ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                            <?php
                                            break;
                                        case CustomDatatype::MILESTONE_CHOICE:
                                            ?>
                                            <?php foreach ($issue->getProject()->getMilestonesForIssues() as $choice): ?>
                                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?= $field; ?>');">
                                                <span class="icon"><?= fa_image_tag('chart-line'); ?></span>
                                                <span class="name"><?= __($choice->getName()); ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                            <?php
                                            break;
                                        case CustomDatatype::STATUS_CHOICE:
                                            ?>
                                            <?php foreach (Status::getAll() as $choice): ?>
                                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?= $field; ?>');">
                                                <span class="status-badge" style="background-color: <?= ($choice instanceof Status) ? $choice->getColor() : '#FFF'; ?>;">
                                                    <span id="status_content">&nbsp;&nbsp;</span>
                                                </span>
                                                <?= __($choice->getName()); ?>
                                            </a>
                                        <?php endforeach; ?>
                                            <?php
                                            break;
                                        case CustomDatatype::COMPONENTS_CHOICE:
                                            ?>
                                            <?php foreach ($issue->getProject()->getComponents() as $choice): ?>
                                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?= $field; ?>');">
                                                <span class="icon"><?= fa_image_tag('cube'); ?></span>
                                                <span class="name"><?= __($choice->getName()); ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                            <?php
                                            break;
                                        case CustomDatatype::RELEASES_CHOICE:
                                            ?>
                                            <?php foreach ($issue->getProject()->getBuilds() as $choice): ?>
                                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?= $field; ?>');">
                                                <span class="icon"><?= fa_image_tag('compact-dist'); ?></span>
                                                <span class="name"><?= __($choice->getName()); ?></span>
                                            </a>
                                        <?php endforeach; ?>
                                            <?php
                                            break;
                                        case CustomDatatype::INPUT_TEXT:
                                            ?>
                                            <div class="list-item">
                                                <form id="<?= $field; ?>_form" action="<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>" method="post" onSubmit="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)) ?>', '<?= $field; ?>', '<?= $field; ?>'); return false;">
                                                    <input type="text" name="<?= $field; ?>_value" value="<?= $info['name'] ?>" /><?= __('%save or %cancel', array('%save' => '<input type="submit" value="'.__('Save').'">', '%cancel' => '<a href="#" onclick="$(\''.$field.'_change\').hide(); return false;">'.__('cancel').'</a>')); ?>
                                                </form>
                                            </div>
                                            <?php
                                            break;
                                        case CustomDatatype::INPUT_TEXTAREA_SMALL:
                                            ?>
                                            <div class="list-item">
                                                <form id="<?= $field; ?>_form" action="<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>" method="post" onSubmit="Pachno.Issues.Field.set('<?= make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)) ?>', '<?= $field; ?>', '<?= $field; ?>'); return false;">
                                                    <?php include_component('main/textarea', array('area_name' => $field.'_value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => $field.'_value', 'height' => '100px', 'width' => '100%', 'value' => $info['name'])); ?>
                                                    <br><?= __('%save or %cancel', array('%save' => '<input type="submit" value="'.__('Save').'">', '%cancel' => '<a href="#" onclick="$(\''.$field.'_change\').hide(); return false;">'.__('cancel').'</a>')); ?>
                                                </form>
                                            </div>
                                            <?php
                                            break;
                                        }

                                        endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<?php Event::createNew('core', 'viewissue_left_after_attachments', $issue)->trigger(); ?>
<div class="fields-list-container" id="viewissue_duplicate_issues_container">
    <div class="header">
        <span class="name"><?= __('Duplicate issues %count', ['%count' => '']); ?><span id="viewissue_duplicate_issues_count" class="count-badge"><?= $issue->getNumberOfDuplicateIssues(); ?></span></span>
    </div>
    <div id="viewissue_duplicate_issues">
        <?php include_component('main/duplicateissues', array('issue' => $issue)); ?>
    </div>
</div>
<?php Event::createNew('core', 'viewissue_left_bottom', $issue)->trigger(); ?>
