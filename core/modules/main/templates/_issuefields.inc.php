<?php
    /**
     * @var \pachno\core\entities\Issue $issue
     * @var \pachno\core\entities\Status[] $statuses
     * @var \pachno\core\entities\Issuetype[] $issuetypes
     */
?>
<div class="issue-fields">
    <?php \pachno\core\framework\Event::createNew('core', 'viewissue_left_top', $issue)->trigger(); ?>
    <fieldset>
        <div class="header" onclick="$('issue_details_fieldslist_basics').toggle();"><?php echo __('Issue basics'); ?></div>
        <ul class="fields-list" id="issue_details_fieldslist_basics">
            <li id="shortname_field"<?php if (!$issue->isShortnameVisible()): ?> style="display: none;"<?php endif; ?> >
                <dl class="viewissue_list">
                    <dt id="shortname_header">
                        <?php echo __('Issue label'); ?>
                    </dt>
                    <dd class="hoverable">
                        <?php if ($issue->isEditable() && $issue->canEditShortname()): ?>
                            <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to edit issue label'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                            <ul id="shortname_change" class="popup_box more_actions_dropdown with-header">
                                <li class="header"><?php echo __('Set issue label'); ?></li>
                                <li class="nohover">
                                    <form id="shortname_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'shortname')); ?>" method="post" onSubmit="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'shortname')) ?>', 'shortname', 'shortname'); return false;">
                                        <input type="text" name="shortname_value" value="<?php echo $issue->getShortname(); ?>" /><?php echo __('%save or %cancel', array('%save' => '<input type="submit" value="'.__('Save').'">', '%cancel' => '<a href="#" onclick="$(\'shortname_change\').hide(); return false;">'.__('cancel').'</a>')); ?>
                                    </form>
                                </li>
                                <li id="shortname_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                                <li id="shortname_change_error" class="error_message" style="display: none;"></li>
                            </ul>
                        <?php endif; ?>
                        <span id="shortname_name"><?php if ($issue->hasShortname()) echo $issue->getShortname(); ?></span>
                        <span class="faded_out" id="no_shortname"<?php if ($issue->hasShortname()): ?> style="display: none;"<?php endif; ?>><?php echo __('No label set'); ?></span>
                    </dd>
                </dl>
            </li>
            <?php $field = $fields_list['category']; unset($fields_list['category']); ?>
            <?php include_component('main/issuefield', array('field' => 'category', 'info' => $field, 'issue' => $issue)); ?>
            <?php $field = $fields_list['milestone']; unset($fields_list['milestone']); ?>
            <?php include_component('main/issuefield', array('field' => 'milestone', 'info' => $field, 'issue' => $issue)); ?>
            <li id="percent_complete_field"<?php if (!$issue->isPercentCompletedVisible()): ?> style="display: none;"<?php endif; ?>>
                <dl class="viewissue_list">
                    <dt id="percent_complete_header"><?php echo __('Progress'); ?></dt>
                    <dd id="percent_complete_content" class="hoverable">
                        <?php if ($issue->canEditPercentage()): ?>
                            <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to set percent completed'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                            <ul class="popup_box more_actions_dropdown with-header" id="percent_complete_change">
                                <li class="header"><?php echo __('Set percent completed'); ?></li>
                                <li><a href="javascript:void(0);" onclick="Pachno.Issues.Field.setPercent('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'percent_complete', 'percent' => 0)); ?>', 'set');"><?php echo __('Clear percent completed'); ?></a></li>
                                <li class="separator"></li>
                                <li class="nohover">
                                    <form id="percent_complete_form" method="post" accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="" onsubmit="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'percent_complete')); ?>', 'percent_complete', 'percent_complete');return false;">
                                        <label for="set_percent"><?php echo __('Percent complete'); ?></label>&nbsp;<input type="text" style="width: 40px;" name="percent" id="set_percent" value="<?php echo $issue->getPercentCompleted(); ?>">&percnt;
                                        <input type="submit" value="<?php echo __('Set'); ?>">
                                    </form>
                                </li>
                                <li id="percent_complete_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                            </ul>
                        <?php endif; ?>
                        <div id="issue_percent_complete" title="<?php echo __('%percentage % completed', array('%percentage' => $issue->getPercentCompleted())); ?>">
                            <?php include_component('main/percentbar', array('percent' => $issue->getPercentCompleted(), 'height' => 4)); ?>
                        </div>
                    </dd>
                </dl>
            </li>
            <?php $field = $fields_list['priority']; unset($fields_list['priority']); ?>
            <?php include_component('main/issuefield', array('field' => 'priority', 'info' => $field, 'issue' => $issue)); ?>
        </ul>
    </fieldset>
    <fieldset id="issue_details_fieldslist_pain_container" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
        <div class="header" onclick="$('issue_details_fieldslist_pain').toggle();"><?php echo __('User pain'); ?></div>
        <ul class="issue_details fields-list" id="issue_details_fieldslist_pain">
            <li id="pain_bug_type_field" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
                <dl class="viewissue_list">
                    <dt id="pain_bug_type_header"><?php echo __('Type of bug'); ?></dt>
                    <dd id="pain_bug_type_content">
                        <?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
                            <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to triage type of bug'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                            <ul class="popup_box more_actions_dropdown with-header" id="pain_bug_type_change">
                                <li class="header"><?php echo __('Triage bug type'); ?></li>
                                <li>
                                    <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_bug_type', 'pain_bug_type_id' => 0)); ?>', 'pain_bug_type');"><?php echo __('Clear bug type'); ?></a>
                                </li>
                                <li class="separator"></li>
                                <?php foreach (\pachno\core\entities\Issue::getPainTypesOrLabel('pain_bug_type') as $choice_id => $choice): ?>
                                    <li>
                                        <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_bug_type', 'pain_bug_type_id' => $choice_id)); ?>', 'pain_bug_type');"><?php echo $choice; ?></a>
                                    </li>
                                <?php endforeach; ?>
                                <li id="pain_bug_type_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                                <li id="pain_bug_type_change_error" class="error_message" style="display: none;"></li>
                            </ul>
                        <?php endif; ?>
                        <span id="pain_bug_type_name"<?php if (!$issue->hasPainBugType()): ?> style="display: none;"<?php endif; ?>>
                            <?php echo ($issue->hasPainBugType()) ? $issue->getPainBugTypeLabel() : ''; ?>
                        </span>
                        <span class="faded_out" id="no_pain_bug_type"<?php if ($issue->hasPainBugType()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not triaged'); ?></span>
                    </dd>
                </dl>
            </li>
            <li id="pain_likelihood_field" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
                <dl class="viewissue_list">
                    <dt id="pain_likelihood_header"><?php echo __('Likelihood'); ?></dt>
                    <dd id="pain_likelihood_content">
                        <?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
                            <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to triage likelihood'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                            <ul class="popup_box more_actions_dropdown with-header" id="pain_likelihood_change">
                                <li class="header"><?php echo __('Triage likelihood'); ?></li>
                                <li>
                                    <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_likelihood', 'pain_likelihood_id' => 0)); ?>', 'pain_likelihood');"><?php echo __('Clear likelihood'); ?></a>
                                </li>
                                <li class="separator"></li>
                                <?php foreach (\pachno\core\entities\Issue::getPainTypesOrLabel('pain_likelihood') as $choice_id => $choice): ?>
                                    <li>
                                        <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_likelihood', 'pain_likelihood_id' => $choice_id)); ?>', 'pain_likelihood');"><?php echo $choice; ?></a>
                                    </li>
                                <?php endforeach; ?>
                                <li id="pain_likelihood_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                                <li id="pain_likelihood_change_error" class="error_message" style="display: none;"></li>
                            </ul>
                        <?php endif; ?>
                        <span id="pain_likelihood_name"<?php if (!$issue->hasPainLikelihood()): ?> style="display: none;"<?php endif; ?>>
                            <?php echo ($issue->hasPainLikelihood()) ? $issue->getPainLikelihoodLabel() : ''; ?>
                        </span>
                        <span class="faded_out" id="no_pain_likelihood"<?php if ($issue->hasPainLikelihood()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not triaged'); ?></span>
                    </dd>
                </dl>
            </li>
            <li id="pain_effect_field" style="<?php if (!$issue->isUserPainVisible()): ?> display: none;<?php endif; ?>">
                <dl class="viewissue_list">
                    <dt id="pain_effect_header"><?php echo __('Effect'); ?></dt>
                    <dd id="pain_effect_content">
                        <?php if ($issue->isUpdateable() && $issue->canEditUserPain()): ?>
                            <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to triage effect'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                            <ul class="popup_box more_actions_dropdown with-header" id="pain_effect_change">
                                <li class="header"><?php echo __('Triage effect'); ?></li>
                                <li>
                                    <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_effect', 'pain_effect_id' => 0)); ?>', 'pain_effect');"><?php echo __('Clear effect'); ?></a>
                                </li>
                                <li class="separator"></li>
                                <?php foreach (\pachno\core\entities\Issue::getPainTypesOrLabel('pain_effect') as $choice_id => $choice): ?>
                                    <li>
                                        <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'pain_effect', 'pain_effect_id' => $choice_id)); ?>', 'pain_effect');"><?php echo $choice; ?></a>
                                    </li>
                                <?php endforeach; ?>
                                <li id="pain_effect_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                                <li id="pain_effect_change_error" class="error_message" style="display: none;"></li>
                            </ul>
                        <?php endif; ?>
                        <span id="pain_effect_name"<?php if (!$issue->hasPainEffect()): ?> style="display: none;"<?php endif; ?>>
                            <?php echo ($issue->hasPainEffect()) ? $issue->getPainEffectLabel() : ''; ?>
                        </span>
                        <span class="faded_out" id="no_pain_effect"<?php if ($issue->hasPainEffect()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not triaged'); ?></span>
                    </dd>
                </dl>
            </li>
        </ul>
    </fieldset>
    <fieldset id="viewissue_affected_container">
        <div class="header">
            <?php echo image_tag('spinning_16.gif', array('style' => 'display: none;', 'id' => 'issue_affected_indicator')) . __('Affected by this issue (%count)', array('%count' => '<span id="viewissue_affects_count">'.$affected_count.'</span>')); ?>
        </div>
        <div id="viewissue_affected">
            <?php include_component('main/issueaffected', array('issue' => $issue)); ?>
        </div>
    </fieldset>
    <fieldset>
        <div class="header" onclick="$('issue_details_fieldslist_people').toggle();"><?php echo __('People involved'); ?></div>
        <ul class="issue_details fields-list" id="issue_details_fieldslist_people">
            <li id="owned_by_field" style="<?php if (!$issue->isOwnedByVisible()): ?> display: none;<?php endif; ?>">
                <dl class="viewissue_list">
                    <dt id="owned_by_header"><?php echo __('Owned by'); ?></dt>
                    <dd id="owned_by_content">
                        <?php if ($issue->isUpdateable() && $issue->canEditOwner()): ?>
                            <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to change owner'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                            <?php include_component('main/identifiableselector', array(    'html_id'             => 'owned_by_change',
                                                                                    'header'             => __('Change issue owner'),
                                                                                    'callback'             => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'user', 'value' => '%identifiable_value')) . "', 'owned_by');",
                                                                                    'team_callback'         => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value')) . "', 'owned_by');",
                                                                                    'teamup_callback'     => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'owned_by', 'identifiable_type' => 'team', 'value' => '%identifiable_value', 'teamup' => true)) . "', 'owned_by');",
                                                                                    'clear_link_text'    => __('Clear current owner'),
                                                                                    'base_id'            => 'owned_by',
                                                                                    'include_teams'        => true,
                                                                                    'absolute'            => true,
                                                                                    'classes'            => 'popup_box more_actions_dropdown')); ?>
                        <?php endif; ?>
                        <div style="width: 170px; display: <?php if ($issue->isOwned()): ?>inline<?php else: ?>none<?php endif; ?>;" id="owned_by_name">
                            <?php if ($issue->getOwner() instanceof \pachno\core\entities\User): ?>
                                <?php echo include_component('main/userdropdown', array('user' => $issue->getOwner())); ?>
                            <?php elseif ($issue->getOwner() instanceof \pachno\core\entities\Team): ?>
                                <?php echo include_component('main/teamdropdown', array('team' => $issue->getOwner())); ?>
                            <?php endif; ?>
                        </div>
                        <span class="faded_out" id="no_owned_by"<?php if ($issue->isOwned()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not owned by anyone'); ?></span>
                    </dd>
                </dl>
            </li>
            <li id="assigned_to_field">
                <dl class="viewissue_list">
                    <dt id="assigned_to_header"><?php echo __('Assigned to'); ?></dt>
                    <dd id="assigned_to_content">
                        <?php if ($issue->canEditAssignee() && $issue->isUpdateable()): ?>
                            <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to change assignee'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                            <?php include_component('main/identifiableselector', array(    'html_id'             => 'assigned_to_change',
                                                                                    'header'             => __('Assign this issue'),
                                                                                    'callback'             => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'user', 'value' => '%identifiable_value')) . "', 'assigned_to');",
                                                                                    'team_callback'         => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'team', 'value' => '%identifiable_value')) . "', 'assigned_to');",
                                                                                    'teamup_callback'     => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'assigned_to', 'identifiable_type' => 'team', 'value' => '%identifiable_value', 'teamup' => true)) . "', 'assigned_to');",
                                                                                    'clear_link_text'    => __('Clear current assignee'),
                                                                                    'base_id'            => 'assigned_to',
                                                                                    'include_teams'        => true,
                                                                                    'absolute'            => true,
                                                                                    'classes'            => 'popup_box more_actions_dropdown')); ?>
                        <?php endif; ?>
                        <div style="width: 170px; display: <?php if ($issue->isAssigned()): ?>inline<?php else: ?>none<?php endif; ?>;" id="assigned_to_name">
                            <?php if ($issue->getAssignee() instanceof \pachno\core\entities\User): ?>
                                <?php echo include_component('main/userdropdown', array('user' => $issue->getAssignee())); ?>
                            <?php elseif ($issue->getAssignee() instanceof \pachno\core\entities\Team): ?>
                                <?php echo include_component('main/teamdropdown', array('team' => $issue->getAssignee())); ?>
                            <?php endif; ?>
                        </div>
                        <span class="faded_out" id="no_assigned_to"<?php if ($issue->isAssigned()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not assigned to anyone'); ?></span>
                    </dd>
                </dl>
                <?php if ($issue->canEditAssignee() && $issue->isEditable()): ?>
                <?php endif; ?>
            </li>
            <li id="subscribers_field">
                <dl class="viewissue_list">
                    <dt id="subscribers_header">
                        <?php echo __('Subscribers'); ?>
                    </dt>
                    <dd class="hoverable">
                        <a href="javascript:void(0)" onclick="Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_subscribers', 'issue_id' => $issue->getID())); ?>');"><?php echo __('%number_of subscriber(s)', array('%number_of' => '<span id="subscribers_field_count">'.count($issue->getSubscribers()).'</span>')); ?></a>
                    </dd>
                </dl>
                <div class="tooltip from-above" style="width: 300px; font-size: 0.9em; margin-top: 10px; margin-left: 17px;"><?php echo __('Click here to show the list of subscribers'); ?></div>
            </li>
        </ul>
    </fieldset>
    <fieldset id="issue_timetracking_container">
        <div class="header" onclick="$('issue_details_fieldslist_time').toggle();"><?php echo __('Times and dates'); ?></div>
        <ul class="issue_details fields-list" id="issue_details_fieldslist_time">
            <li id="posted_at_field">
                <dl class="viewissue_list">
                    <dt id="posted_at_header">
                        <?php echo __('Posted at'); ?>
                    </dt>
                    <dd class="hoverable">
                        <time datetime="<?php echo \pachno\core\framework\Context::getI18n()->formatTime($issue->getPosted(), 24); ?>" title="<?php echo \pachno\core\framework\Context::getI18n()->formatTime($issue->getPosted(), 21); ?>" pubdate><?php echo \pachno\core\framework\Context::getI18n()->formatTime($issue->getPosted(), 20); ?></time>
                    </dd>
                </dl>
            </li>
            <li id="updated_at_field">
                <dl class="viewissue_list">
                    <dt id="updated_at_header">
                        <?php echo __('Last updated'); ?>
                    </dt>
                    <dd class="hoverable">
                        <time datetime="<?php echo \pachno\core\framework\Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 24); ?>" title="<?php echo \pachno\core\framework\Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 21); ?>"><?php echo \pachno\core\framework\Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 20); ?></time>
                    </dd>
                </dl>
            </li>
            <li id="estimated_time_field"<?php if (!$issue->isEstimatedTimeVisible()): ?> style="display: none;"<?php endif; ?>>
                <dl class="viewissue_list">
                    <dt id="estimated_time_header"><?php echo __('Estimated time'); ?></dt>
                    <dd id="estimated_time_content">
                        <?php if ($issue->isUpdateable() && $issue->canEditEstimatedTime()): ?>
                            <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo __('Click to estimate this issue'); ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                            <?php include_component('main/issueestimator', array('issue' => $issue, 'field' => 'estimated_time', 'mode' => 'left')); ?>
                        <?php endif; ?>
                        <span id="estimated_time_<?php echo $issue->getID(); ?>_name"<?php if (!$issue->hasEstimatedTime()): ?> style="display: none;"<?php endif; ?>>
                            <?php echo \pachno\core\entities\Issue::getFormattedTime($issue->getEstimatedTime(true, true)); ?>
                        </span>
                        <span class="faded_out" id="no_estimated_time_<?php echo $issue->getID(); ?>"<?php if ($issue->hasEstimatedTime()): ?> style="display: none;"<?php endif; ?>><?php echo __('Not estimated'); ?></span>
                    </dd>
                </dl>
            </li>
            <li id="spent_time_field" style="position: relative;<?php if (!$issue->isSpentTimeVisible()): ?> display: none;<?php endif; ?>">
                <dl class="viewissue_list">
                    <dt id="spent_time_header"><?php echo __('Time spent'); ?></dt>
                    <dd id="spent_time_content">
                        <span id="spent_time_<?php echo $issue->getID(); ?>_name"<?php if (!$issue->hasSpentTime()): ?> style="display: none;"<?php endif; ?>>
                            <a href="javascript:void(0)" onclick="Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_spenttimes', 'issue_id' => $issue->getID())); ?>');" id="spent_time_<?php echo $issue->getID(); ?>_value"><?php echo \pachno\core\entities\Issue::getFormattedTime($issue->getSpentTime(true, true)); ?></a>
                        </span>
                        <span class="faded_out" id="no_spent_time_<?php echo $issue->getID(); ?>"<?php if ($issue->hasSpentTime()): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0)" onclick="Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_spenttimes', 'issue_id' => $issue->getID())); ?>');"><?php echo __('No time spent'); ?></a></span>
                        <div id="estimated_percentbar"<?php if (!($issue->hasEstimatedTime())): ?> style="display: none;"<?php endif; ?>><?php include_component('main/percentbar', array('percent' => $issue->getEstimatedPercentCompleted(), 'height' => 2)); ?></div>
                    </dd>
                </dl>
                <div class="tooltip from-above" style="width: 300px; font-size: 0.9em; margin-top: 10px; margin-left: 17px;"><?php echo __('Click here to see time logged against this issue'); ?></div>
            </li>
        </ul>
    </fieldset>
    <?php if (count($fields_list) || count($customfields_list)): ?>
        <fieldset>
            <div class="header" onclick="$('issue_details_fieldslist').toggle();"><?php echo __('Issue details'); ?></div>
            <ul class="issue_details fields-list" id="issue_details_fieldslist">
                <?php foreach ($fields_list as $field => $info): ?>
                    <?php include_component('main/issuefield', compact('field', 'info', 'issue')); ?>
                <?php endforeach; ?>
                <?php foreach ($customfields_list as $field => $info): ?>
                    <?php if ($info['type'] == \pachno\core\entities\CustomDatatype::INPUT_TEXTAREA_MAIN): continue; endif; ?>
                    <li id="<?php echo $field; ?>_field" <?php if (!$info['visible']): ?> style="display: none;"<?php endif; ?>>
                        <dl class="viewissue_list">
                            <dt id="<?php echo $field; ?>_header">
                                <?php echo $info['title']; ?>
                            </dt>
                            <dd id="<?php echo $field; ?>_content">
                                <?php if ($issue->isUpdateable() && $issue->canEditCustomFields($field) && $info['editable']): ?>
                                    <a href="javascript:void(0);" class="dropper dropdown_link" title="<?php echo $info['change_tip']; ?>"><?php echo image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                                    <?php if ($info['type'] == \pachno\core\entities\CustomDatatype::USER_CHOICE): ?>
                                        <?php include_component('main/identifiableselector', array(    'html_id'             => $field.'_change',
                                                                                                'header'             => __('Select a user'),
                                                                                                'callback'             => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, 'identifiable_type' => 'user', $field.'_value' => '%identifiable_value')) . "', '".$field."');",
                                                                                                'clear_link_text'    => __('Clear currently selected user'),
                                                                                                'base_id'            => $field,
                                                                                                'include_teams'        => false,
                                                                                                'absolute'            => true,
                                                                                                'classes'            => 'popup_box more_actions_dropdown')); ?>
                                    <?php elseif ($info['type'] == \pachno\core\entities\CustomDatatype::TEAM_CHOICE): ?>
                                        <?php include_component('main/identifiableselector', array(    'html_id'             => $field.'_change',
                                                                                                'header'             => __('Select a team'),
                                                                                                'callback'             => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, 'identifiable_type' => 'team', $field.'_value' => '%identifiable_value')) . "', '".$field."');",
                                                                                                'clear_link_text'    => __('Clear currently selected team'),
                                                                                                'base_id'            => $field,
                                                                                                'include_teams'        => true,
                                                                                                'include_users'        => false,
                                                                                                'absolute'            => true,
                                                                                                'classes'            => 'popup_box more_actions_dropdown')); ?>
                                    <?php elseif ($info['type'] == \pachno\core\entities\CustomDatatype::CLIENT_CHOICE): ?>
                                        <?php include_component('main/identifiableselector', array(    'html_id'             => $field.'_change',
                                                                                                'header'             => __('Select a client'),
                                                                                                'callback'             => "Pachno.Issues.Field.set('" . make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, 'identifiable_type' => 'client', $field.'_value' => '%identifiable_value')) . "', '".$field."');",
                                                                                                'clear_link_text'    => __('Clear currently selected client'),
                                                                                                'base_id'            => $field,
                                                                                                'include_clients'    => true,
                                                                                                'include_teams'        => false,
                                                                                                'include_users'        => false,
                                                                                                'absolute'            => true,
                                                                                                'classes'            => 'popup_box more_actions_dropdown')); ?>
                                    <?php else: ?>
                                        <ul class="popup_box more_actions_dropdown with-header" id="<?php echo $field; ?>_change">
                                            <li class="header"><?php echo $info['change_header']; ?></li>
                                            <li id="<?php echo $field; ?>_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</li>
                                            <li id="<?php echo $field; ?>_change_error" class="error_message" style="display: none;"></li>
                                            <?php if (array_key_exists('choices', $info) && is_array($info['choices'])): ?>
                                                <li>
                                                    <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a>
                                                </li>
                                                <li class="separator"></li>
                                                <?php foreach ($info['choices'] ?: array() as $choice): ?>
                                                    <li>
                                                        <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo image_tag('icon_customdatatype.png').__($choice->getName()); ?></a>
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php elseif ($info['type'] == \pachno\core\entities\CustomDatatype::DATE_PICKER || $info['type'] == \pachno\core\entities\CustomDatatype::DATETIME_PICKER): ?>
                                                <li>
                                                    <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a>
                                                </li>
                                                <li class="separator"></li>
                                                <li id="customfield_<?php echo $field; ?>_calendar_container" style="padding: 0;"></li>
                                                <?php if ($info['type'] == \pachno\core\entities\CustomDatatype::DATETIME_PICKER): ?>
                                                    <li class="nohover">
                                                        <form id="customfield_<?php echo $field; ?>_form" method="post" accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="" onsubmit="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>', '<?php echo $field; ?>', 'customfield_<?php echo $field; ?>');return false;">
                                                            <label><?php echo __('Time'); ?></label>
                                                            <input type="text" id="customfield_<?php echo $field; ?>_hour" value="00" style="width: 20px; font-size: 0.9em; text-align: center;">&nbsp;:&nbsp;
                                                            <input type="text" id="customfield_<?php echo $field; ?>_minute" value="00" style="width: 20px; font-size: 0.9em; text-align: center;">
                                                            <input type="hidden" name="<?php echo $field; ?>_value" value="<?php echo (int) $info['name'] - \pachno\core\framework\I18n::getTimezoneOffset(); ?>" id="<?php echo $field; ?>_value" />
                                                            <input type="submit" value="<?php echo __('Set'); ?>">
                                                        </form>
                                                    </li>
                                                <?php endif; ?>
                                                <script type="text/javascript">
                                                    require(['domReady', 'pachno/index', 'calendarview'], function (domReady, pachno_index_js, Calendar) {
                                                        domReady(function () {
                                                            Calendar.setup({
                                                                dateField: '<?php echo $field; ?>_new_name',
                                                                parentElement: 'customfield_<?php echo $field; ?>_calendar_container',
                                                                valueCallback: function(element, date) {
                                                                    <?php if ($info['type'] == \pachno\core\entities\CustomDatatype::DATETIME_PICKER): ?>
                                                                        var value = date.setUTCHours(parseInt($('customfield_<?php echo $field; ?>_hour').value));
                                                                        var date  = new Date(value);
                                                                        var value = Math.floor(date.setUTCMinutes(parseInt($('customfield_<?php echo $field; ?>_minute').value)) / 1000);
                                                                    <?php else: ?>
                                                                        var value = Math.floor(date.getTime() / 1000);
                                                                        Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>?<?php echo $field; ?>_value='+value, '<?php echo $field; ?>');
                                                                    <?php endif; ?>
                                                                    $('<?php echo $field; ?>_value').value = value;
                                                                }
                                                            });
                                                            <?php if ($info['type'] == \pachno\core\entities\CustomDatatype::DATETIME_PICKER): ?>
                                                                var date = new Date(parseInt($('<?php echo $field; ?>_value').value) * 1000);
                                                                $('customfield_<?php echo $field; ?>_hour').value = date.getUTCHours();
                                                                $('customfield_<?php echo $field; ?>_minute').value = date.getUTCMinutes();
                                                                Event.observe($('customfield_<?php echo $field; ?>_hour'), 'change', function (event) {
                                                                    var value = parseInt($('<?php echo $field; ?>_value').value);
                                                                    var hours = parseInt(this.value);
                                                                    if (value <= 0 || hours < 0 || hours > 24) return;
                                                                    var date = new Date(value * 1000);
                                                                    $('<?php echo $field; ?>_value').value = date.setUTCHours(parseInt(this.value)) / 1000;
                                                                });
                                                                Event.observe($('customfield_<?php echo $field; ?>_minute'), 'change', function (event) {
                                                                    var value = parseInt($('<?php echo $field; ?>_value').value);
                                                                    var minutes = parseInt(this.value);
                                                                    if (value <= 0 || minutes < 0 || minutes > 60) return;
                                                                    var date = new Date(value * 1000);
                                                                    $('<?php echo $field; ?>_value').value = date.setUTCMinutes(parseInt(this.value)) / 1000;
                                                                });
                                                            <?php endif; ?>
                                                        });
                                                    });
                                                </script>
                                            <?php else: ?>
                                                <li>
                                                    <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => "")); ?>', '<?php echo $field; ?>');"><?php echo $info['clear']; ?></a>
                                                </li>
                                                <li class="separator"></li>
                                                <?php

                                                switch ($info['type'])
                                                {
                                                    case \pachno\core\entities\CustomDatatype::EDITIONS_CHOICE:
                                                        ?>
                                                        <?php foreach ($issue->getProject()->getEditions() as $choice): ?>
                                                            <li>
                                                                <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo image_tag('icon_edition.png').__($choice->getName()); ?></a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                        <?php
                                                        break;
                                                    case \pachno\core\entities\CustomDatatype::MILESTONE_CHOICE:
                                                        ?>
                                                        <?php foreach ($issue->getProject()->getMilestonesForIssues() as $choice): ?>
                                                            <li>
                                                                <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo image_tag('icon_milestone.png').__($choice->getName()); ?></a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                        <?php
                                                        break;
                                                    case \pachno\core\entities\CustomDatatype::STATUS_CHOICE:
                                                        ?>
                                                        <?php foreach (\pachno\core\entities\Status::getAll() as $choice): ?>
                                                            <li>
                                                                <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');">
                                                                    <div class="status-badge" style="background-color: <?php echo ($choice instanceof \pachno\core\entities\Status) ? $choice->getColor() : '#FFF'; ?>;">
                                                                        <span id="status_content">&nbsp;&nbsp;</span>
                                                                    </div>
                                                                    <?php echo __($choice->getName()); ?>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                        <?php
                                                        break;
                                                    case \pachno\core\entities\CustomDatatype::COMPONENTS_CHOICE:
                                                        ?>
                                                        <?php foreach ($issue->getProject()->getComponents() as $choice): ?>
                                                            <li>
                                                                <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo image_tag('icon_components.png').$choice->getName(); ?></a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                        <?php
                                                        break;
                                                    case \pachno\core\entities\CustomDatatype::RELEASES_CHOICE:
                                                        ?>
                                                        <?php foreach ($issue->getProject()->getBuilds() as $choice): ?>
                                                            <li>
                                                                <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field, $field . '_value' => $choice->getID())); ?>', '<?php echo $field; ?>');"><?php echo image_tag('icon_build.png').$choice->getName(); ?></a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                        <?php
                                                        break;
                                                    case \pachno\core\entities\CustomDatatype::INPUT_TEXT:
                                                        ?>
                                                        <li class="nohover">
                                                            <form id="<?php echo $field; ?>_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>" method="post" onSubmit="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)) ?>', '<?php echo $field; ?>', '<?php echo $field; ?>'); return false;">
                                                                <input type="text" name="<?php echo $field; ?>_value" value="<?php echo $info['name'] ?>" /><?php echo __('%save or %cancel', array('%save' => '<input type="submit" value="'.__('Save').'">', '%cancel' => '<a href="#" onclick="$(\''.$field.'_change\').hide(); return false;">'.__('cancel').'</a>')); ?>
                                                            </form>
                                                        </li>
                                                        <?php
                                                        break;
                                                    case \pachno\core\entities\CustomDatatype::INPUT_TEXTAREA_SMALL:
                                                        ?>
                                                        <li class="nohover">
                                                            <form id="<?php echo $field; ?>_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)); ?>" method="post" onSubmit="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => $field)) ?>', '<?php echo $field; ?>', '<?php echo $field; ?>'); return false;">
                                                                <?php include_component('main/textarea', array('area_name' => $field.'_value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => $field.'_value', 'height' => '100px', 'width' => '100%', 'value' => $info['name'])); ?>
                                                                <br><?php echo __('%save or %cancel', array('%save' => '<input type="submit" value="'.__('Save').'">', '%cancel' => '<a href="#" onclick="$(\''.$field.'_change\').hide(); return false;">'.__('cancel').'</a>')); ?>
                                                            </form>
                                                        </li>
                                                        <?php
                                                        break;
                                                }

                                            endif; ?>
                                        </ul>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php
                                    switch ($info['type'])
                                    {
                                        case \pachno\core\entities\CustomDatatype::INPUT_TEXTAREA_SMALL:
                                            ?>
                                            <span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>>
                                                <?php echo \pachno\core\helpers\TextParser::parseText($info['name'], false, null, array('headers' => false)); ?>
                                            </span>
                                            <span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>>
                                                <?php echo __('Not determined'); ?>
                                            </span>
                                            <?php
                                            break;
                                        case \pachno\core\entities\CustomDatatype::USER_CHOICE:
                                            ?>
                                            <span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>>
                                                <?php echo include_component('main/userdropdown', array('user' => $info['name'])); ?>
                                            </span>
                                            <span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>>
                                                <?php echo __('Not determined'); ?>
                                            </span>
                                            <?php
                                            break;
                                        case \pachno\core\entities\CustomDatatype::TEAM_CHOICE:
                                            ?>
                                            <span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>>
                                                <?php echo include_component('main/teamdropdown', array('team' => $info['identifiable'])); ?>
                                            </span>
                                            <span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>>
                                                <?php echo __('Not determined'); ?>
                                            </span>
                                            <?php
                                            break;
                                        case \pachno\core\entities\CustomDatatype::EDITIONS_CHOICE:
                                        case \pachno\core\entities\CustomDatatype::COMPONENTS_CHOICE:
                                        case \pachno\core\entities\CustomDatatype::RELEASES_CHOICE:
                                        case \pachno\core\entities\CustomDatatype::MILESTONE_CHOICE:
                                        case \pachno\core\entities\CustomDatatype::CLIENT_CHOICE:
                                             ?>
                                            <span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>>
                                                <?php echo (isset($info['name'])) ? $info['name'] : __('Unknown'); ?>
                                            </span>
                                            <span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>>
                                                <?php echo __('Not determined'); ?>
                                            </span><?php
                                            break;
                                        case \pachno\core\entities\CustomDatatype::STATUS_CHOICE:
                                            $status = null;
                                            $value = null;
                                            $color = '#FFF';
                                            try
                                            {
                                                $status = new \pachno\core\entities\Status($info['name']);
                                                $value = $status->getName();
                                                $color = $status->getColor();
                                            }
                                            catch (\Exception $e) { }
                                            ?><span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>><div class="status-badge" style="background-color: <?php echo $color; ?>;"><span><?php echo __($value); ?></span></div></span><span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not determined'); ?></span><?php
                                            break;
                                        case \pachno\core\entities\CustomDatatype::DATE_PICKER:
                                        case \pachno\core\entities\CustomDatatype::DATETIME_PICKER:
                                            $pachno_response->addJavascript('calendarview');
                                            if (is_numeric($info['name'])) {
                                                $value = ($info['name']) ? date('Y-m-d' . ($info['type'] == \pachno\core\entities\CustomDatatype::DATETIME_PICKER ? ' H:i' : ''), $info['name']) : __('Not set');
                                            } else {
                                                $value = $info['name'];
                                            }
                                            ?><span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>><?php echo $value; ?></span><span id="<?php echo $field; ?>_new_name" style="display: none;"><?php echo (int) $value; ?></span><span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not set'); ?></span><?php
                                            break;
                                        default:
                                            ?><span id="<?php echo $field; ?>_name"<?php if (!$info['name_visible']): ?> style="display: none;"<?php endif; ?>><?php echo (filter_var($info['name'], FILTER_VALIDATE_URL) !== false) ? link_tag($info['name'], $info['name']) : $info['name']; ?></span><span class="faded_out" id="no_<?php echo $field; ?>"<?php if (!$info['noname_visible']): ?> style="display: none;"<?php endif; ?>><?php echo __('Not determined'); ?></span><?php
                                            break;
                                    }
                                ?>
                            </dd>
                        </dl>
                        <div style="clear: both;"> </div>
                        <?php if ($issue->isUpdateable() && $issue->canEditCustomFields()): ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </fieldset>
    <?php endif; ?>
    <fieldset id="viewissue_attached_information_container">
        <div class="header">
            <?php echo __('Attachments (%count)', array('%count' => '<span id="viewissue_uploaded_attachments_count">'.(count($issue->getLinks()) + count($issue->getFiles())).'</span>')); ?>
        </div>
        <div id="viewissue_attached_information">
            <div id="viewissue_no_uploaded_files"<?php if (count($issue->getFiles()) + count($issue->getLinks()) > 0): ?> style="display: none;"<?php endif; ?>><?php echo __('There is nothing attached to this issue'); ?></div>
            <ul class="attached_items" id="viewissue_uploaded_links">
                <?php foreach ($issue->getLinks() as $link_id => $link): ?>
                    <?php include_component('main/attachedlink', array('issue' => $issue, 'link' => $link, 'link_id' => $link['id'])); ?>
                <?php endforeach; ?>
            </ul>
            <ul class="attached_items" id="viewissue_uploaded_files">
                <?php foreach (array_reverse($issue->getFiles()) as $file_id => $file): ?>
                    <?php if (!$file->isImage()): ?>
                        <?php include_component('main/attachedfile', array('base_id' => 'viewissue_files', 'mode' => 'issue', 'issue' => $issue, 'file' => $file)); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php foreach (array_reverse($issue->getFiles()) as $file_id => $file): ?>
                    <?php if ($file->isImage()): ?>
                        <?php include_component('main/attachedfile', array('base_id' => 'viewissue_files', 'mode' => 'issue', 'issue' => $issue, 'file' => $file)); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </fieldset>
    <?php \pachno\core\framework\Event::createNew('core', 'viewissue_left_after_attachments', $issue)->trigger(); ?>
    <fieldset id="viewissue_related_information_container">
        <div class="header">
            <?php echo image_tag('spinning_16.gif', array('style' => 'display: none;', 'id' => 'related_issues_indicator')) . __('Child issues (%count)', array('%count' => '<span id="viewissue_related_issues_count">'.$issue->countChildIssues().'</span>')); ?>
        </div>
        <div id="viewissue_related_information">
            <?php include_component('main/relatedissues', array('issue' => $issue)); ?>
        </div>
    </fieldset>
    <fieldset id="viewissue_duplicate_issues_container">
        <div class="header">
            <?php echo image_tag('spinning_16.gif', array('style' => 'display: none;', 'id' => 'duplicate_issues_indicator')) . __('Duplicate issues (%count)', array('%count' => '<span id="viewissue_duplicate_issues_count">'.$issue->getNumberOfDuplicateIssues().'</span>')); ?>
        </div>
        <div id="viewissue_duplicate_issues">
            <?php include_component('main/duplicateissues', array('issue' => $issue)); ?>
        </div>
    </fieldset>
    <div style="clear: both; margin-bottom: 5px;"> </div>
    <div class="issue_details_detailed_toggler">
        <a href="javascript:void(0);" class="button" onclick="$('issue_details').toggleClassName('detailed');"><?php echo __('Show / hide more details'); ?>&nbsp;&raquo;</a>
    </div>
    <script type="text/javascript">
        var Pachno, jQuery;
        require(['domReady', 'pachno/index', 'jquery', 'jquery.nanoscroller'], function (domReady, pachno_index_js, jquery, nanoscroller) {
            domReady(function () {
                Pachno = pachno_index_js;
                jQuery = jquery;

                Event.observe(window, 'resize', function() {
                    if (document.viewport.getWidth() > 900) {
                        $('issue_details').dataset.resizable = true;
                    } else {
                        $('issue_details').dataset.resizable = undefined;
                    }
                });
            });
        });
    </script>
    <?php \pachno\core\framework\Event::createNew('core', 'viewissue_left_bottom', $issue)->trigger(); ?>
</div>
