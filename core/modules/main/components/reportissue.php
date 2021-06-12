<?php

    use \pachno\core\entities;
    use pachno\core\entities\Article;
    use pachno\core\entities\Build;
    use pachno\core\entities\Category;
    use pachno\core\entities\Client;
    use pachno\core\entities\Component;
    use pachno\core\entities\CustomDatatype;
    use pachno\core\entities\CustomDatatypeOption;
    use pachno\core\entities\Datatype;
    use pachno\core\entities\Edition;
    use pachno\core\entities\Issue;
    use pachno\core\entities\Issuetype;
    use pachno\core\entities\Milestone;
    use pachno\core\entities\Priority;
    use pachno\core\entities\Project;
    use pachno\core\entities\Reproducability;
    use pachno\core\entities\Resolution;
    use pachno\core\entities\Severity;
    use pachno\core\entities\Status;
    use pachno\core\entities\Team;
    use pachno\core\framework\Context;
    use pachno\core\framework\Event;
    use pachno\core\framework\Settings;

    /**
     * @var string[][] $errors
     * @var string[][] $permission_errors
     * @var Issuetype[] $issuetypes
     * @var Milestone[] $milestones
     * @var Milestone $selected_milestone
     * @var Issue $issue
     * @var Issue $parent_issue
     * @var Issuetype[] $issuetypes
     * @var Issuetype $selected_issuetype
     * @var Status $selected_status
     * @var Status[] $selected_statuses
     * @var Status[] $statuses
     * @var Priority $selected_priority
     * @var Priority[] $selected_priorities
     * @var Priority[] $priorities
     * @var Category $selected_category
     * @var Category[] $selected_categories
     * @var Category[] $categories
     * @var Severity $selected_severity
     * @var Severity[] $selected_severities
     * @var Severity[] $severities
     * @var entities\AgileBoard $board
     */

?>
<div class="form-container">
    <?php if (!empty($errors) || !(empty($permission_errors))): ?>
        <?php include_component('main/reportissueerrors', ['errors' => $errors, 'permission_errors' => $permission_errors]); ?>
    <?php elseif ($issue instanceof Issue): ?>
        <div class="message-box type-info" id="report_issue_reported_issue_details">
            <div class="message">
                <?= __('The following issue was reported: %link_to_issue', ['%link_to_issue' => link_tag($issue->getUrl(), $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle())]); ?>
            </div>
            <div class="actions">
                <a class="button primary" id="report_issue_report_another_button"
                   onclick="[$(this), $('#report_issue_form'), $('#report_more_here'), $('#report_form'), $('#issuetype_list'), $('#report_issue_reported_issue_details')].each(function (el) { Element.toggle(el, 'block'); });$('#reportissue_container').removeClass('medium');$('#reportissue_container').addClass('large');"><?= __('Report another issue'); ?></a>
            </div>
        </div>
    <?php endif; ?>
    <div class="content <?php if ($selected_issuetype instanceof Issuetype) echo 'hidden'; ?> no-sidebar"
         id="report_form_issue_type_selector">
        <?php if (count($issuetypes) > 0): ?>
            <div class="form-row" id="issuetype_list">
                <div class="list-mode">
                    <?php foreach ($issuetypes as $issuetype): ?>
                        <?php if ($parent_issue instanceof Issue && $issuetype->getID() === $parent_issue->getIssueType()->getID()) continue; ?>
                        <?php if (!$selected_project->getIssuetypeScheme()->isIssuetypeReportable($issuetype)) continue; ?>
                        <?php if (isset($board) && $issuetype->getID() == $board->getEpicIssuetypeID()) continue; ?>
                        <?php if (isset($selected_issuetype) && $selected_issuetype instanceof Issuetype && $issuetype->getID() !== $selected_issuetype->getID()) continue; ?>
                        <a class="list-item multiline" data-key="<?= $issuetype->getKey(); ?>" data-id="<?= $issuetype->getID(); ?>" href="javascript:void(0);">
                            <?= fa_image_tag($issuetype->getFontAwesomeIcon(), ['class' => 'icon issuetype-icon issuetype-' . $issuetype->getType()]); ?>
                            <span class="name">
                                <span class="title"><?= __('Create a new %issuetype_name', array('%issuetype_name' => $issuetype->getName())); ?></span>
                                <span class="description"><?= $issuetype->getDescription(); ?></span>
                            </span>
                            <?php if (isset($board) && $board->isIssuetypeSwimlaneIdentifier($issuetype)): ?>
                                <span class="icon tooltip-container">
                                    <?= fa_image_tag('stream', ['class' => 'icon']); ?>
                                    <span class="tooltip from-right">
                                        <?= __('Issues with this issue type will become swimlanes on the current board'); ?>
                                    </span>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="content hidden no-sidebar" id="issue-reported-confirmation">
        <div class="form">
            <div class="form-row header"><?= __('The following issue was created'); ?></div>
            <div class="form-row">
                <div class="list-mode">
                    <a href="#" class="list-item" id="reported-issue-container" target="_blank"></a>
                </div>
            </div>
            <div class="form-row submit-container">
                <button class="secondary closer"><?= __('Done'); ?></button>
                <button class="button primary restart-reportissue-form"><?= __('Add another'); ?></button>
            </div>
        </div>
    </div>
    <div class="form-container <?php if (!$selected_issuetype instanceof Issuetype) echo 'hidden'; ?>" id="report_form" data-fields-url="<?= make_url('getreportissuefields', array('project_key' => $selected_project->getKey())); ?>">
        <form action="<?= make_url('project_reportissue', array('project_key' => $selected_project->getKey())); ?>" method="post" accept-charset="<?= Context::getI18n()->getCharset(); ?>" id="report_issue_form" data-simple-submit>
            <div class="form-row content-with-sidebar-container">
                <div class="content">
                    <input type="hidden" name="project_id" id="project_id" value="<?= $selected_project->getID(); ?>">
                    <?php if (count($issuetypes) > 0): ?>
                        <div class="form-row <?php if (isset($selected_issuetype) && $selected_issuetype instanceof Issuetype) echo 'locked'; ?>">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Select an issue type'); ?>">
                                    <label>
                                        <span><?= __('Issue type'); ?></span>
                                        <?= fa_image_tag('lock', ['class' => 'icon locked']); ?>
                                    </label>
                                    <span class="value"></span>
                                    <?php if (!isset($locked_issuetype) || !$locked_issuetype): ?>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <?php foreach ($issuetypes as $issuetype): ?>
                                                <?php if ($parent_issue instanceof Issue && $issuetype->getID() === $parent_issue->getIssueType()->getID()) continue; ?>
                                                <?php if (!$selected_project->getIssuetypeScheme()->isIssuetypeReportable($issuetype)) continue; ?>
                                                <?php if (isset($board) && $issuetype->getID() == $board->getEpicIssuetypeID()) continue; ?>
                                                <?php if (isset($selected_issuetype) && $selected_issuetype instanceof Issuetype && $issuetype->getID() !== $selected_issuetype->getID()) continue; ?>
                                                <input type="radio" class="fancy-checkbox report-issue-type-selector" id="report_issue_issue_type_<?= $issuetype->getId(); ?>" name="issuetype_id" value="<?= $issuetype->getId(); ?>" <?php if ($selected_issuetype instanceof Issuetype && $selected_issuetype->getID() == $issuetype->getID()) echo 'checked'; ?>>
                                                <label for="report_issue_issue_type_<?= $issuetype->getId(); ?>" class="list-item multiline">
                                                    <span class="icon"><?= fa_image_tag($issuetype->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $issuetype->getIcon()]); ?></span>
                                                    <span class="name">
                                                        <span class="title value"><?= $issuetype->getName(); ?></span>
                                                        <span class="description"><?= $issuetype->getDescription(); ?></span>
                                                    </span>
                                                    <?php if (isset($board) && $board->isIssuetypeSwimlaneIdentifier($issuetype)): ?>
                                                        <span class="icon tooltip-container">
                                                            <?= fa_image_tag('stream', ['class' => 'icon']); ?>
                                                            <span class="tooltip from-right">
                                                                <?= __('Issues with this issue type will become swimlanes on the current board'); ?>
                                                            </span>
                                                        </span>
                                                    <?php endif; ?>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-row <?php if (array_key_exists('title', $errors)): ?> invalid<?php endif; ?>">
                            <input type="text" name="title" id="report_issue_title_input" class="name-input-enhance"
                                   value="<?php if (isset($title) && trim($title) != '') echo htmlspecialchars($title); ?>" placeholder="<?= __('Enter a short, but descriptive summary of the issue here'); ?>">
                            <label for="report_issue_title_input"
                                   class="required">
                                <span><?= __('Short summary'); ?></span>
                                <span class="required-indicator">* </span>
                            </label>
                        </div>
                        <div class="form-row hidden <?php if (array_key_exists('shortname', $errors)): ?> invalid<?php endif; ?>"
                             id="shortname_div">
                            <input type="text" name="shortname" id="shortname" class="shortname"
                                   value="<?php if (isset($shortname) && trim($shortname) != '') echo htmlspecialchars($shortname); ?>" placeholder="<?= __('Enter a very short label for the issue here'); ?>">
                            <label for="shortname" id="shortname_label">
                                <span><?= __('Issue label'); ?></span>
                                <span class="required-indicator">* </span>
                            </label>
                        </div>
                        <div id="report_issue_more_options_indicator" class hidden="form-row">
                            <div class="helper-text">
                                <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                                <span><?= __('Checking fields, please wait'); ?>...</span>
                            </div>
                        </div>
                        <div class="form-row <?php if (array_key_exists('description', $errors)) echo 'invalid'; ?>"
                             id="description_div">
                            <label for="report_issue_description_input" id="description_label">
                                <span><?= __('Description'); ?></span>
                                <span class="required-indicator">* </span>
                            </label>
                            <?php include_component('main/textarea', ['area_name' => 'description', 'target_type' => 'project', 'target_id' => $selected_project->getID(), 'invisible' => true, 'markuppable' => true, 'syntax' => Settings::SYNTAX_MD, 'value' => ((isset($selected_description)) ? $selected_description : null)]); ?>
                            <div class="helper-text"><?= __('Describe the issue in as much detail as possible. More is better.'); ?></div>
                        </div>
                        <div class="form-row <?php if (array_key_exists('reproduction_steps', $errors)) echo 'invalid'; ?>"
                             id="reproduction_steps_div">
                            <label for="report_issue_reproduction_steps_input" id="reproduction_steps_label">
                                <span><?= __('Reproduction steps'); ?></span>
                                <span class="required-indicator">* </span>
                            </label>
                            <?php include_component('main/textarea', ['area_name' => 'reproduction_steps', 'target_type' => 'project', 'target_id' => $selected_project->getID(), 'invisible' => true, 'markuppable' => true, 'syntax' => Settings::SYNTAX_MD, 'value' => ((isset($selected_reproduction_steps)) ? $selected_reproduction_steps : null)]); ?>
                            <div class="helper-text">
                                <?= __('Enter the steps necessary to reproduce the issue, as detailed as possible.'); ?>
                            </div>
                        </div>
                        <?php if ($canupload): ?>
                            <?php include_component('main/uploader', array('mode' => 'issue')); ?>
                        <?php endif; ?>
                        <?php if ($selected_project instanceof Project && $selected_project->permissionCheck('canlockandeditlockedissues')): ?>
                            <div class="report-issue-custom-access-check">
                                <div class="report-issue-custom-access-container" style="display:none;">
                                    <input type="radio" name="issue_access" id="issue_access_public"
                                           onchange="Pachno.Issues.ACL.toggle_checkboxes(this, '', 'public');"
                                           value="public"<?php if ($selected_project->getIssuesLockType() === Project::ISSUES_LOCK_TYPE_PUBLIC) echo ' checked'; ?>><label
                                            for="issue_access_public"><?= __('Available to anyone with access to project'); ?></label><br>
                                    <input type="radio" name="issue_access" id="issue_access_public_category"
                                           onchange="Pachno.Issues.ACL.toggle_checkboxes(this, '', 'public_category');"
                                           value="public_category"<?php if ($selected_project->getIssuesLockType() === Project::ISSUES_LOCK_TYPE_PUBLIC_CATEGORY) echo ' checked'; ?>><label
                                            for="issue_access_public_category"><?= __('Available to anyone with access to project, category and those listed below'); ?></label><br>
                                    <input type="radio" name="issue_access" id="issue_access_restricted"
                                           onchange="Pachno.Issues.ACL.toggle_checkboxes(this, '', 'restricted');"
                                           value="restricted"<?php if ($selected_project->getIssuesLockType() === Project::ISSUES_LOCK_TYPE_RESTRICTED) echo ' checked'; ?>><label
                                            for="issue_access_restricted"><?= __('Available only to you and those listed below'); ?></label><br>
                                    <script>
                                        // require(['domReady', 'jquery'], function (domReady, jQuery) {
                                        //     domReady(function () { $('#input[name=issue_access]').trigger('change'); });
                                        // });
                                    </script>
                                    <?php image_tag('spinning_16.gif', array('id' => 'acl_indicator_', 'style' => '')); ?>
                                    <div id="acl-users-teams-selector" style="display: none;">
                                        <h4 style="margin-top: 10px;">
                                            <?= javascript_link_tag(__('Add a user or team'), array('onclick' => "$('#popup_find_acl_').toggle('block');", 'style' => 'float: right;', 'class' => 'button')); ?>
                                            <?= __('Users or teams who can see this issue'); ?>
                                        </h4>
                                        <?php /* include_component('main/identifiableselector', array('html_id' => "popup_find_acl_",
                                            'header' => __('Give someone access to this issue'),
                                            'callback' => "Pachno.Issues.ACL.addTarget('" . make_url('getacl_formentry', array('identifiable_type' => 'user', 'identifiable_value' => '%identifiable_value')) . "', '');",
                                            'team_callback' => "Pachno.Issues.ACL.addTarget('" . make_url('getacl_formentry', array('identifiable_type' => 'team', 'identifiable_value' => '%identifiable_value')) . "', '');",
                                            'base_id' => "popup_find_acl_",
                                            'include_teams' => true,
                                            'allow_clear' => false,
                                            'absolute' => true,
                                            'use_form' => false)); */ ?>
                                    </div>
                                    <div id="acl__public" style="display: none;">
                                        <ul class="issue_access_list simple-list" id="issue__public_category_access_list"
                                            style="display: none;">
                                            <li id="issue__public_category_access_list_none" class="faded_out"
                                                style="<?php if (count($al_items)): ?>display: none; <?php endif; ?>padding: 5px;"><?= __('Noone else can see this issue'); ?></li>
                                            <?php foreach ($al_items as $item): ?>
                                                <?php include_component('main/issueaclformentry', array('target' => $item['target'])); ?>
                                            <?php endforeach; ?>
                                        </ul>
                                        <div style="text-align: right;">
                                            <input id="issue_access_public_category_input" type="hidden"
                                                   name="public_category" disabled>
                                        </div>
                                    </div>
                                    <div id="acl__restricted" style="display: none;">
                                        <ul class="issue_access_list simple-list" id="issue__restricted_access_list">
                                            <li id="issue__restricted_access_list_none" class="faded_out"
                                                style="<?php if (count($al_items)): ?>display: none; <?php endif; ?>padding: 5px;"><?= __('Noone else can see this issue'); ?></li>
                                            <?php foreach ($al_items as $item): ?>
                                                <?php include_component('main/issueaclformentry', array('target' => $item['target'])); ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php Event::createNew('core', 'reportissue.listfields')->trigger(); ?>
                    <?php endif; ?>
                </div>
                <div class="sidebar">
                    <div class="row additional-information-container">
                        <div class="form-row hidden <?php if ($selected_statuses) echo ' locked'; ?> additional_information" id="status_div">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                                    <label id="status_label">
                                        <span><?php echo __('Select initial status'); ?></span>
                                        <span class="required-indicator">* </span>
                                        <?= fa_image_tag('lock', ['class' => 'icon locked']); ?>
                                    </label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <?php foreach ($statuses as $status): ?>
                                            <?php if ($selected_statuses && !array_key_exists($status->getId(), $selected_statuses)) continue; ?>
                                            <input type="radio" value="<?php echo $status->getID(); ?>" name="status_id" id="report_issue_status_id_<?php echo $status->getID(); ?>" class="fancy-checkbox" <?php if ($selected_statuses || ($selected_status instanceof Datatype && $selected_status->getID() == $status->getID())) echo ' checked'; ?>>
                                            <label for="report_issue_status_id_<?php echo $status->getID(); ?>" class="list-item">
                                                <span class="name">
                                                    <span class="status-badge" style="background-color: <?php echo $status->getColor(); ?>;color: <?php echo $status->getTextColor(); ?>;">
                                                        <span class="value"><?php echo __($status->getName()); ?></span>
                                                    </span>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($parent_issue) && $parent_issue instanceof Issue): ?>
                            <div class="form-row locked additional_information" id="parent_issue_div">
                                <div class="fancy-dropdown-container">
                                    <div class="fancy-dropdown locked">
                                        <label>
                                            <span><?= __('Parent issue'); ?></span>
                                            <?= fa_image_tag('lock', ['class' => 'icon locked']); ?>
                                        </label>
                                        <span class="value">
                                            <?= fa_image_tag(($parent_issue->hasIssueType()) ? $parent_issue->getIssueType()->getFontAwesomeIcon() : 'unknown', ['class' => (($parent_issue->hasIssueType()) ? 'issuetype-icon issuetype-' . $parent_issue->getIssueType()->getIcon() : 'issuetype-icon issuetype-unknown')]); ?>
                                            <span class="name"><?= $parent_issue->getFormattedTitle(); ?></span>
                                        </span>
                                        <div class="dropdown-container list-mode">
                                            <input type="radio" value="<?= $parent_issue->getID(); ?>" name="parent_issue_id" id="report_issue_parent_issue_id_<?= $parent_issue->getId(); ?>" class="fancy-checkbox" checked>
                                            <label for="report_issue_parent_issue_id_<?= $parent_issue->getId(); ?>" class="list-item">
                                                <?= fa_image_tag(($parent_issue->hasIssueType()) ? $parent_issue->getIssueType()->getFontAwesomeIcon() : 'unknown', ['class' => (($parent_issue->hasIssueType()) ? 'issuetype-icon issuetype-' . $parent_issue->getIssueType()->getIcon() : 'issuetype-icon issuetype-unknown')]); ?>
                                                <span class="name"><?= $parent_issue->getFormattedTitle(); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="form-row hidden additional_information <?php if (array_key_exists('edition', $errors)): ?>invalid<?php endif; ?>" id="edition_div">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                                    <label id="edition_label">
                                        <span><?php echo __('Affected edition'); ?></span>
                                        <span class="required-indicator">* </span>
                                    </label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode"></div>
                                </div>
                            </div>
                            <div class="helper-text"><?= __("Select which edition of the product you're using"); ?></div>
                        </div>
                        <div class="form-row hidden additional_information <?php if (array_key_exists('build', $errors)): ?>invalid<?php endif; ?>" id="build_div">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                                    <label id="build_label">
                                        <span><?php echo __('Affected release'); ?></span>
                                        <span class="required-indicator">* </span>
                                    </label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode"></div>
                                </div>
                            </div>
                            <div class="helper-text"><?= __("Select which release you're using"); ?></div>
                        </div>
                        <div class="form-row hidden additional_information <?php if (array_key_exists('component', $errors)): ?>invalid<?php endif; ?>" id="component_div">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                                    <label id="component_label">
                                        <span><?php echo __('Affected component'); ?></span>
                                        <span class="required-indicator">* </span>
                                    </label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode"></div>
                                </div>
                            </div>
                            <div class="helper-text"><?= __("Choose the component where this issue occurs"); ?></div>
                        </div>
                        <div class="form-row hidden additional_information" id="estimated_time_div">
                            <input name="estimated_time" id="estimated_time_id" class="number" placeholder="<?= __('Enter an estimate here'); ?>">
                            <label for="estimated_time_id" id="estimated_time_label">
                                <?= fa_image_tag('clock', ['class' => 'icon']); ?>
                                <?= __('Estimated time'); ?>
                            </label>
                            <div class="helper-text"><?= __('Type in your estimate here. Use keywords such as "points", "minutes", "hours", "days", "weeks" and "months" to describe your estimate'); ?></div>
                        </div>
                        <div class="form-row hidden additional_information" id="spent_time_div">
                            <input name="spent_time" id="spent_time_id" class="number" placeholder="<?= __('Enter an estimate here'); ?>">
                            <label for="spent_time_id" id="spent_time_label">
                                <?= fa_image_tag('clock', ['class' => 'icon']); ?>
                                <?= __('Spent time'); ?>
                            </label>
                            <div class="helper-text"><?= __('Enter time spent on this issue here. Use keywords such as "points", "minutes", "hours", "days", "weeks" and "months" to describe your effort'); ?></div>
                        </div>
                        <div class="form-row hidden additional_information" id="percentage_div">
                            <input name="percentage" id="percentage_id" class="number" placeholder="<?= __('Enter an estimate here'); ?>">
                            <label for="percentage_id" id="percentage_label">
                                <?= fa_image_tag('percent', ['class' => 'icon']); ?>
                                <?= __('Percentage completed'); ?>
                            </label>
                            <div class="helper-text"><?= __('Enter time spent on this issue here. Use keywords such as "points", "minutes", "hours", "days", "weeks" and "months" to describe your effort'); ?></div>
                        </div>
                        <div class="form-row additional_information <?= ($selected_categories || $selected_category) ? 'locked' : 'hidden'; ?>" id="category_div">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                                    <label id="category_label">
                                        <span><?php echo __('Select category'); ?></span>
                                        <?= fa_image_tag('lock', ['class' => 'icon locked']); ?>
                                    </label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <?php if (!$selected_category instanceof Category && !$selected_categories): ?>
                                            <input type="radio" value="" name="category_id" id="report_issue_category_id_0"
                                                   class="fancy-checkbox" checked>
                                            <label for="report_issue_category_id_0" class="list-item">
                                                <span class="name value"><?= __('Not selected'); ?></span>
                                            </label>
                                        <?php endif; ?>
                                        <?php foreach ($categories as $category): ?>
                                            <?php if ($selected_categories && !array_key_exists($category->getId(), $selected_categories)) continue; ?>
                                            <input type="radio" value="<?php echo $category->getID(); ?>" name="category_id"
                                                   id="report_issue_category_id_<?php echo $category->getID(); ?>"
                                                   class="fancy-checkbox" <?php if (($selected_category instanceof Datatype && $selected_category->getID() == $category->getID()) || $selected_categories) echo ' checked'; ?>>
                                            <label for="report_issue_category_id_<?php echo $category->getID(); ?>"
                                                   class="list-item">
                                                <span class="name value"><?php echo __($category->getName()); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row hidden additional_information <?php if (array_key_exists('resolution', $errors)): ?>invalid<?php endif; ?>" id="resolution_div">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                                    <label id="resolution_label"><?php echo __('Select resolution'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <input type="radio" value="" name="resolution_id" id="report_issue_resolution_id_0"
                                               class="fancy-checkbox" <?php if (!$selected_resolution instanceof Resolution) echo ' checked'; ?>>
                                        <label for="report_issue_resolution_id_0" class="list-item">
                                            <span class="name value"><?= __('Not selected'); ?></span>
                                        </label>
                                        <?php foreach ($resolutions as $resolution): ?>
                                            <input type="radio" value="<?php echo $resolution->getID(); ?>"
                                                   name="resolution_id"
                                                   id="report_issue_resolution_id_<?php echo $resolution->getID(); ?>"
                                                   class="fancy-checkbox" <?php if ($selected_resolution instanceof Datatype && $selected_resolution->getID() == $resolution->getID()) echo ' checked'; ?>>
                                            <label for="report_issue_resolution_id_<?php echo $resolution->getID(); ?>"
                                                   class="list-item">
                                                <span class="name value"><?php echo __($resolution->getName()); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row hidden additional_information <?php if (array_key_exists('reproducability', $errors)): ?>invalid<?php endif; ?>" id="reproducability_div">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                                    <label id="reproducability_label"><?php echo __('Select reproducability'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <input type="radio" value="" name="reproducability_id"
                                               id="report_issue_reproducability_id_0"
                                               class="fancy-checkbox" <?php if (!$selected_reproducability instanceof Reproducability) echo ' checked'; ?>>
                                        <label for="report_issue_reproducability_id_0" class="list-item">
                                            <span class="name value"><?= __('Not selected'); ?></span>
                                        </label>
                                        <?php foreach ($reproducabilities as $reproducability): ?>
                                            <input type="radio" value="<?php echo $reproducability->getID(); ?>"
                                                   name="reproducability_id"
                                                   id="report_issue_reproducability_id_<?php echo $reproducability->getID(); ?>"
                                                   class="fancy-checkbox" <?php if ($selected_reproducability instanceof Datatype && $selected_reproducability->getID() == $reproducability->getID()) echo ' checked'; ?>>
                                            <label for="report_issue_reproducability_id_<?php echo $reproducability->getID(); ?>"
                                                   class="list-item">
                                                <span class="name value"><?php echo __($reproducability->getName()); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row additional_information <?= ($selected_priorities || $selected_priority) ? 'locked' : 'hidden'; ?>" id="priority_div">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                                    <label id="priority_label">
                                        <span><?php echo __('Select priority'); ?></span>
                                        <?= fa_image_tag('lock', ['class' => 'icon locked']); ?>
                                    </label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <?php if (!$selected_priority instanceof Priority && !$selected_priorities): ?>
                                            <input type="radio" value="" name="priority_id" id="report_issue_priority_id_0"
                                                   class="fancy-checkbox" checked>
                                            <label for="report_issue_priority_id_0" class="list-item">
                                                <span class="name value"><?= __('Not selected'); ?></span>
                                            </label>
                                        <?php endif; ?>
                                        <?php foreach ($priorities as $priority): ?>
                                            <?php if ($selected_priorities && !array_key_exists($priority->getId(), $selected_priorities)) continue; ?>
                                            <input type="radio" value="<?php echo $priority->getID(); ?>" name="priority_id"
                                                   id="report_issue_priority_id_<?php echo $priority->getID(); ?>"
                                                   class="fancy-checkbox" <?php if (($selected_priority instanceof Datatype && $selected_priority->getID() == $priority->getID())|| $selected_priorities) echo ' checked'; ?>>
                                            <label for="report_issue_priority_id_<?php echo $priority->getID(); ?>"
                                                   class="list-item">
                                                <span class="name value"><?php echo __($priority->getName()); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row additional_information <?= ($selected_severities || $selected_severity) ? 'locked' : 'hidden'; ?>" id="severity_div">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                                    <label id="severity_label">
                                        <span><?php echo __('Select severity'); ?></span>
                                        <?= fa_image_tag('lock', ['class' => 'icon locked']); ?>
                                    </label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <?php if (!$selected_severity instanceof Severity && !$selected_severities): ?>
                                            <input type="radio" value="" name="severity_id" id="report_issue_severity_id_0"
                                                   class="fancy-checkbox" checked>
                                            <label for="report_issue_severity_id_0" class="list-item">
                                                <span class="name value"><?= __('Not selected'); ?></span>
                                            </label>
                                        <?php endif; ?>
                                        <?php foreach ($severities as $severity): ?>
                                            <?php if ($selected_severities && !array_key_exists($severity->getId(), $selected_severities)) continue; ?>
                                            <input type="radio" value="<?php echo $severity->getID(); ?>" name="severity_id"
                                                   id="report_issue_severity_id_<?php echo $severity->getID(); ?>"
                                                   class="fancy-checkbox" <?php if (($selected_severity instanceof Datatype && $selected_severity->getID() == $severity->getID()) || $selected_severities) echo ' checked'; ?>>
                                            <label for="report_issue_severity_id_<?php echo $severity->getID(); ?>"
                                                   class="list-item">
                                                <span class="name value"><?php echo __($severity->getName()); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row hidden <?php if ($selected_milestone instanceof Milestone) echo ' locked'; ?> additional_information <?php if (array_key_exists('milestone', $errors)): ?>invalid<?php endif; ?>" id="milestone_div">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                                    <label id="milestone_label">
                                        <span><?php echo __('Select milestone'); ?></span>
                                        <?= fa_image_tag('lock', ['class' => 'icon locked']); ?>
                                    </label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <input type="radio" value="" name="milestone_id"
                                               id="report_issue_milestone_id_0"
                                               class="fancy-checkbox" <?php if (!$selected_milestone instanceof Milestone) echo ' checked'; ?>>
                                        <label for="report_issue_milestone_id_0" class="list-item">
                                            <span class="name value"><?= __('Not selected'); ?></span>
                                        </label>
                                        <?php foreach ($milestones as $milestone): ?>
                                            <?php if ((!$selected_milestone instanceof Milestone || $selected_milestone->getID() !== $milestone->getID()) && $milestone->isClosed()) continue; ?>
                                            <input type="radio" value="<?php echo $milestone->getID(); ?>"
                                                   name="milestone_id"
                                                   id="report_issue_milestone_id_<?php echo $milestone->getID(); ?>"
                                                   class="fancy-checkbox" <?php if ($selected_milestone instanceof Milestone && $selected_milestone->getID() == $milestone->getID()) echo ' checked'; ?>>
                                            <label for="report_issue_milestone_id_<?php echo $milestone->getID(); ?>"
                                                   class="list-item">
                                                <span class="name value"><?php echo __($milestone->getName()); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php /*<table id="pain_bug_type_div" style="display: none;" class="additional_information<?php if (array_key_exists('pain_bug_type', $errors)): ?> reportissue_error<?php endif; ?>">
                            <tr>
                                <td style="width: 180px;"><label for="pain_bug_type_id" id="pain_bug_type_label"><span class="required-indicator">* </span><?= __('Triaging: Bug type'); ?></label></td>
                                <td class="report_issue_help faded_out dark"><?= __("What type of bug is this?"); ?></td>
                            <tr>
                                <td colspan="2" style="padding-top: 5px;">
                                    <select name="pain_bug_type_id" id="pain_bug_type_id" style="width: 100%;">
                                        <option value=""<?php if (!$selected_pain_bug_type) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                        <?php foreach (Issue::getPainTypesOrLabel('pain_bug_type') as $choice_id => $choice): ?>
                                            <option value="<?= $choice_id; ?>"<?php if ($selected_pain_bug_type == $choice_id): ?> selected<?php endif; ?>><?= $choice; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <table id="pain_likelihood_div" style="display: none;" class="additional_information<?php if (array_key_exists('pain_likelihood', $errors)): ?> reportissue_error<?php endif; ?>">
                            <tr>
                                <td style="width: 180px;"><label for="pain_likelihood_id" id="pain_likelihood_label"><span class="required-indicator">* </span><?= __('Triaging: Likelihood'); ?></label></td>
                                <td class="report_issue_help faded_out dark"><?= __("How likely are users to experience the bug?"); ?></td>
                            <tr>
                                <td colspan="2" style="padding-top: 5px;">
                                    <select name="pain_likelihood_id" id="pain_likelihood_id" style="width: 100%;">
                                        <option value=""<?php if (!$selected_pain_likelihood) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                        <?php foreach (Issue::getPainTypesOrLabel('pain_likelihood') as $choice_id => $choice): ?>
                                            <option value="<?= $choice_id; ?>"<?php if ($selected_pain_likelihood == $choice_id): ?> selected<?php endif; ?>><?= $choice; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <table id="pain_effect_div" style="display: none;" class="additional_information<?php if (array_key_exists('pain_effect', $errors)): ?> reportissue_error<?php endif; ?>">
                            <tr>
                                <td style="width: 180px;"><label for="pain_effect_id" id="pain_effect_label"><span class="required-indicator">* </span><?= __('Triaging: Effect'); ?></label></td>
                                <td class="report_issue_help faded_out dark"><?= __("Of the people who experience the bug, how badly does it affect their experience?"); ?></td>
                            <tr>
                                <td colspan="2" style="padding-top: 5px;">
                                    <select name="pain_effect_id" id="pain_effect_id" style="width: 100%;">
                                        <option value=""<?php if (!$selected_pain_effect) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                        <?php foreach (Issue::getPainTypesOrLabel('pain_effect') as $choice_id => $choice): ?>
                                            <option value="<?= $choice_id; ?>"<?php if ($selected_pain_effect == $choice_id): ?> selected<?php endif; ?>><?= $choice; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        </table> */ ?>
                        <?php foreach (CustomDatatype::getAll() as $field => $customdatatype): ?>
                            <table id="<?= $customdatatype->getKey(); ?>_div" style="display: none;"
                                   class="additional_information<?php if (array_key_exists($customdatatype->getKey(), $errors)): ?> reportissue_error<?php endif; ?>">
                                <tr>
                                    <?php if ($customdatatype->getType() == entities\DatatypeBase::DATE_PICKER || $customdatatype->getType() == entities\DatatypeBase::DATETIME_PICKER): ?>
                                        <td style="width: 180px;"><label for="<?= $customdatatype->getKey(); ?>_id"
                                                                         id="<?= $customdatatype->getKey(); ?>_label"><span class="required-indicator">* </span><?= __($customdatatype->getDescription()); ?>
                                            </label></td>
                                        <td style="width: 326px;position: relative;"
                                            class="report_issue_help faded_out dark">
                                            <a href="javascript:void(0);"
                                               class="dropper dropdown_link"><?= image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                                            <ul class="popup_box more_actions_dropdown"
                                                id="<?= $customdatatype->getKey(); ?>_change">
                                                <li class="header"><?= __($customdatatype->getDescription()); ?></li>
                                                <li>
                                                    <a href="javascript:void(0);"
                                                       onclick="$('#<?= $customdatatype->getKey(); ?>_name').hide();$('#<?= $customdatatype->getKey(); ?>_value').value = '';$('#no_<?= $customdatatype->getKey(); ?>').show();"><?= __('Clear this field'); ?></a>
                                                </li>
                                                <li class="separator"></li>
                                                <li id="customfield_<?= $customdatatype->getKey(); ?>_calendar_container"
                                                    style="padding: 0;"></li>
                                                <?php if ($customdatatype->getType() == entities\DatatypeBase::DATETIME_PICKER): ?>
                                                    <li class="nohover">
                                                        <label><?= __('Time'); ?></label>
                                                        <input type="text"
                                                               id="customfield_<?= $customdatatype->getKey(); ?>_hour"
                                                               value="00"
                                                               style="width: 20px; font-size: 0.9em; text-align: center;">&nbsp;:&nbsp;
                                                        <input type="text"
                                                               id="customfield_<?= $customdatatype->getKey(); ?>_minute"
                                                               value="00"
                                                               style="width: 20px; font-size: 0.9em; text-align: center;">
                                                    </li>
                                                <?php endif; ?>
                                                <script type="text/javascript">
                                                    //require(['domReady', 'pachno/index', 'calendarview'], function (domReady, pachno_index_js, Calendar) {
                                                    //    domReady(function () {
                                                    //        Calendar.setup({
                                                    //            dateField: '<?//= $customdatatype->getKey(); ?>//_name',
                                                    //            parentElement: 'customfield_<?//= $customdatatype->getKey(); ?>//_calendar_container',
                                                    //            valueCallback: function(element, date) {
                                                    //                <?php //if ($customdatatype->getType() == CustomDatatype::DATETIME_PICKER) { ?>
                                                    //                    var value = date.setHours(parseInt($('#customfield_<?//= $customdatatype->getKey(); ?>//_hour').value));
                                                    //                    var date  = new Date(value);
                                                    //                    var value = Math.floor(date.setMinutes(parseInt($('#customfield_<?//= $customdatatype->getKey(); ?>//_minute').value)) / 1000);
                                                    //                    $('#<?//= $customdatatype->getKey(); ?>//_name').dataset.dateStr = $('#<?//= $customdatatype->getKey(); ?>//_name').innerText;
                                                    //                    $('#<?//= $customdatatype->getKey(); ?>//_name').update(
                                                    //                        $('#<?//= $customdatatype->getKey(); ?>//_name').dataset.dateStr + ' '
                                                    //                        + parseInt($('#customfield_<?//= $customdatatype->getKey(); ?>//_hour').value) + ':'
                                                    //                        + parseInt($('#customfield_<?//= $customdatatype->getKey(); ?>//_minute').value)
                                                    //                    );
                                                    //                <?php //} else { ?>
                                                    //                    var value = Math.floor(date.getTime() / 1000);
                                                    //                <?php //} ?>
                                                    //                $('#<?//= $customdatatype->getKey(); ?>//_name').show();
                                                    //                $('#<?//= $customdatatype->getKey(); ?>//_value').value = value;
                                                    //                $('#no_<?//= $customdatatype->getKey(); ?>//').hide();
                                                    //            }
                                                    //        });
                                                    //        <?php //if ($customdatatype->getType() == CustomDatatype::DATETIME_PICKER): ?>
                                                    //            Event.observe($('#customfield_<?//= $customdatatype->getKey(); ?>//_hour'), 'change', function (event) {
                                                    //                var value = parseInt($('#<?//= $customdatatype->getKey(); ?>//_value').value);
                                                    //                var hours = parseInt(this.value);
                                                    //                if (value <= 0 || hours < 0 || hours > 24) return;
                                                    //                var date = new Date(value * 1000);
                                                    //                $('#<?//= $customdatatype->getKey(); ?>//_value').value = date.setHours(parseInt(this.value)) / 1000;
                                                    //                $('#<?//= $customdatatype->getKey(); ?>//_name').update(
                                                    //                    $('#<?//= $customdatatype->getKey(); ?>//_name').dataset.dateStr + ' '
                                                    //                    + parseInt($('#customfield_<?//= $customdatatype->getKey(); ?>//_hour').value) + ':'
                                                    //                    + parseInt($('#customfield_<?//= $customdatatype->getKey(); ?>//_minute').value)
                                                    //                );
                                                    //            });
                                                    //            Event.observe($('#customfield_<?//= $customdatatype->getKey(); ?>//_minute'), 'change', function (event) {
                                                    //                var value = parseInt($('#<?//= $customdatatype->getKey(); ?>//_value').value);
                                                    //                var minutes = parseInt(this.value);
                                                    //                if (value <= 0 || minutes < 0 || minutes > 60) return;
                                                    //                var date = new Date(value * 1000);
                                                    //                $('#<?//= $customdatatype->getKey(); ?>//_value').value = date.setMinutes(parseInt(this.value)) / 1000;
                                                    //                $('#<?//= $customdatatype->getKey(); ?>//_name').update(
                                                    //                    $('#<?//= $customdatatype->getKey(); ?>//_name').dataset.dateStr + ' '
                                                    //                    + parseInt($('#customfield_<?//= $customdatatype->getKey(); ?>//_hour').value) + ':'
                                                    //                    + parseInt($('#customfield_<?//= $customdatatype->getKey(); ?>//_minute').value)
                                                    //                );
                                                    //            });
                                                    //        <?php //endif; ?>
                                                    //    });
                                                    //});
                                                </script>
                                            </ul>
                                            <span id="<?= $customdatatype->getKey(); ?>_name"
                                                  style="display: none;"><?= __('Not set'); ?></span><span class="faded_out"
                                                                                                           id="no_<?= $customdatatype->getKey(); ?>"><?= __('Not set'); ?></span>
                                            <input type="hidden" name="<?= $customdatatype->getKey(); ?>_value"
                                                   id="<?= $customdatatype->getKey(); ?>_value"/>
                                        </td>
                                    <?php else: ?>
                                        <td style="width: 180px;"><label for="<?= $customdatatype->getKey(); ?>_id"
                                                                         id="<?= $customdatatype->getKey(); ?>_label"><span class="required-indicator">* </span><?= __($customdatatype->getDescription()); ?>
                                            </label></td>
                                        <td class="report_issue_help faded_out dark"><?= __($customdatatype->getInstructions()); ?></td>
                                    <?php endif; ?>
                                <tr>
                                    <td colspan="2" style="padding-top: 5px;" class="editor_container">
                                        <?php
                                            switch ($customdatatype->getType()) {
                                                case entities\DatatypeBase::DROPDOWN_CHOICE_TEXT: ?>
                                                    <select name="<?= $customdatatype->getKey(); ?>_id"
                                                            id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                                        <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof CustomDatatypeOption) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                                        <?php foreach ($customdatatype->getOptions() as $option): ?>
                                                            <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof CustomDatatypeOption && $selected_customdatatype[$customdatatype->getKey()]->getID() == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php
                                                    break;
                                                case entities\DatatypeBase::EDITIONS_CHOICE: ?>
                                                    <select name="<?= $customdatatype->getKey(); ?>_id"
                                                            id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                                        <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof Edition) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                                        <?php if ($selected_project instanceof Project): ?>
                                                            <?php foreach ($selected_project->getEditions() as $option): ?>
                                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                    <?php
                                                    break;
                                                case entities\DatatypeBase::STATUS_CHOICE: ?>
                                                    <select name="<?= $customdatatype->getKey(); ?>_id"
                                                            id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                                        <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof Status) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                                        <?php foreach (Status::getAll() as $option): ?>
                                                            <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php
                                                    break;
                                                case entities\DatatypeBase::TEAM_CHOICE: ?>
                                                    <select name="<?= $customdatatype->getKey(); ?>_id"
                                                            id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                                        <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof Team) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                                        <?php foreach (Team::getAll() as $option): ?>
                                                            <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php
                                                    break;
                                                case entities\DatatypeBase::CLIENT_CHOICE: ?>
                                                    <select name="<?= $customdatatype->getKey(); ?>_id"
                                                            id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                                        <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof Client) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                                        <?php foreach (Client::getAll() as $option): ?>
                                                            <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php
                                                    break;
                                                case entities\DatatypeBase::COMPONENTS_CHOICE: ?>
                                                    <select name="<?= $customdatatype->getKey(); ?>_id"
                                                            id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                                        <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof Component) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                                        <?php if ($selected_project instanceof Project): ?>
                                                            <?php foreach ($selected_project->getComponents() as $option): ?>
                                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                    <?php
                                                    break;
                                                case entities\DatatypeBase::RELEASES_CHOICE: ?>
                                                    <select name="<?= $customdatatype->getKey(); ?>_id"
                                                            id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                                        <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof Build) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                                        <?php if ($selected_project instanceof Project): ?>
                                                            <?php foreach ($selected_project->getBuilds() as $option): ?>
                                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </select>
                                                    <?php
                                                    break;
                                                case entities\DatatypeBase::RADIO_CHOICE: ?>
                                                    <input type="radio" name="<?= $customdatatype->getKey(); ?>_id"
                                                           id="<?= $customdatatype->getKey(); ?>_0"
                                                           value="" <?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof CustomDatatypeOption): ?> selected<?php endif; ?> />
                                                    <label for="<?= $customdatatype->getKey(); ?>_0"><?= __('Not specified'); ?></label>
                                                    <br>
                                                    <?php foreach ($customdatatype->getOptions() as $option): ?>
                                                        <input type="radio" name="<?= $customdatatype->getKey(); ?>_id"
                                                               id="<?= $customdatatype->getKey(); ?>_<?= $option->getID(); ?>"
                                                               value="<?= $option->getID(); ?>" <?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof CustomDatatypeOption && $selected_customdatatype[$customdatatype->getKey()]->getID() == $option->getID()): ?> selected<?php endif; ?> />
                                                        <label for="<?= $customdatatype->getKey(); ?>_<?= $option->getID(); ?>"><?= $option->getName(); ?></label>
                                                        <br>
                                                    <?php endforeach; ?>
                                                    <?php
                                                    break;
                                                case entities\DatatypeBase::INPUT_TEXT:
                                                    ?>
                                                    <input type="text" name="<?= $customdatatype->getKey(); ?>_value"
                                                           value="<?= $selected_customdatatype[$customdatatype->getKey()]; ?>"
                                                           id="<?= $customdatatype->getKey(); ?>_value"/><br>
                                                    <?php
                                                    break;
                                                case entities\DatatypeBase::INPUT_TEXTAREA_SMALL:
                                                case entities\DatatypeBase::INPUT_TEXTAREA_MAIN:
                                                    ?>
                                                    <?php include_component('main/textarea', array('area_name' => $customdatatype->getKey() . '_value', 'target_type' => 'project', 'target_id' => $selected_project->getID(), 'area_id' => $customdatatype->getKey() . '_value', 'height' => '75px', 'width' => '100%', 'hide_hint' => true, 'syntax' => $pachno_user->getPreferredIssuesSyntax(true), 'value' => $selected_customdatatype[$customdatatype->getKey()])); ?>
                                                    <?php
                                                    break;
                                                case entities\DatatypeBase::DATE_PICKER:
                                                case entities\DatatypeBase::DATETIME_PICKER:
                                                    ?>

                                                    <?php
                                                    break;
                                            }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        <?php endforeach; ?>
                        <?php Event::createNew('core', 'reportissue.prefile')->trigger(); ?>
                    </div>
                </div>
            </div>
            <div class="form-row submit-container">
                <?php /* <span class="count-badge"><?= __('Disabled in this release'); ?></span>
                <input type="checkbox" name="custom_issue_access" id="report-issue-custom-access-checkbox"
                       class="fancy-checkbox" onchange="Pachno.Issues.ACL.toggle_custom_access(this);"
                       disabled
                       value="1"><label for="report-issue-custom-access-checkbox"
                                        class="button secondary icon"><?= fa_image_tag('user-lock', ['class' => 'checked']) . fa_image_tag('lock-open', ['class' => 'unchecked']); ?></label> */?>
                <button type="submit" class="button primary" id="report_issue_submit_button">
                    <span><?= __('File issue'); ?></span>
                    <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator', 'id' => 'report_issue_indicator']); ?>
                </button>
            </div>
        </form>
    </div>
</div>
