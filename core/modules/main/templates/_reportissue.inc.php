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

    /**
     * @var string[][] $errors
     * @var string[][] $permission_errors
     * @var Issuetype[] $issuetypes
     * @var Milestone[] $milestones
     * @var Milestone $selected_milestones
     * @var Issue $issue
     * @var Issuetype[] $issuetypes
     * @var Issuetype $selected_issuetype
     */

?>
<?php if (!empty($errors) || !(empty($permission_errors))): ?>
    <div class="message-box type-error">
        <?= fa_image_tag('exclamation-triangle'); ?>
        <span class="message">
            <?php foreach ($errors as $key => $error): ?>
                <?php if (is_array($error)): ?>
                    <?php foreach ($error as $suberror): ?>
                        <?= $suberror; ?>
                    <?php endforeach; ?>
                <?php elseif (is_bool($error)): ?>
                    <?php if ($key == 'title' || in_array($key, Datatype::getAvailableFields(true)) || in_array($key, ['pain_bug_type', 'pain_likelihood', 'pain_effect'])): ?>
                        <?php

                            switch ($key)
                            {
                                case 'title':
                                    echo __('You have to specify a title');
                                    break;
                                case 'description':
                                    echo __('You have to enter a description in the "%description" field', ['%description' => __('Description')]);
                                    break;
                                case 'shortname':
                                    echo __('You have to enter a label in the "%issue_label" field', ['%issue_label' => __('Issue label')]);
                                    break;
                                case 'reproduction_steps':
                                    echo __('You have to enter something in the "%steps_to_reproduce" field', ['%steps_to_reproduce' => __('Steps to reproduce')]);
                                    break;
                                case 'edition':
                                    echo __("Please specify a valid edition");
                                    break;
                                case 'build':
                                    echo __("Please specify a valid version / release");
                                    break;
                                case 'component':
                                    echo __("Please specify a valid component");
                                    break;
                                case 'category':
                                    echo __("Please specify a valid category");
                                    break;
                                case 'status':
                                    echo __("Please specify a valid status");
                                    break;
                                case 'priority':
                                    echo __("Please specify a valid priority");
                                    break;
                                case 'reproducability':
                                    echo __("Please specify a valid reproducability");
                                    break;
                                case 'severity':
                                    echo __("Please specify a valid severity");
                                    break;
                                case 'resolution':
                                    echo __("Please specify a valid resolution");
                                    break;
                                case 'milestone':
                                    echo __("Please specify a valid milestone");
                                    break;
                                case 'estimated_time':
                                    echo __("Please enter a valid estimate");
                                    break;
                                case 'spent_time':
                                    echo __("Please enter time already spent working on this issue");
                                    break;
                                case 'percent_complete':
                                    echo __("Please enter how many percent complete the issue already is");
                                    break;
                                case 'pain_bug_type':
                                    echo __("Please enter a valid triaged bug type");
                                    break;
                                case 'pain_likelihood':
                                    echo __("Please enter a valid triaged likelihood");
                                    break;
                                case 'pain_effect':
                                    echo __("Please enter a valid triaged effect");
                                    break;
                                default:
                                    echo __("Please triage the reported issue, so the user pain score can be properly calculated");
                                    break;
                            }

                        ?>
                    <?php elseif (CustomDatatype::doesKeyExist($key)): ?>
                        <?= __('Required field "%field_name" is missing or invalid', array('%field_name' => CustomDatatype::getByKey($key)->getDescription())); ?>
                    <?php else:

                        $event = new Event('core', 'reportissue.validationerror', $key);
                        $event->setReturnValue($key);
                        $event->triggerUntilProcessed();
                        echo __('A validation error occured: %error', array('%error' => $event->getReturnValue()));

                    ?>
                    <?php endif; ?>
                <?php else: ?>
                    <?= $error; ?>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php foreach ($permission_errors as $key => $p_error): ?>
                <?php if (is_array($p_error)): ?>
                    <?php foreach ($p_error as $p_suberror): ?>
                        <?= $p_suberror; ?>
                    <?php endforeach; ?>
                <?php elseif (is_bool($p_error)): ?>
                    <?php if (in_array($key, Datatype::getAvailableFields(true))): ?>
                        <?php

                            switch ($key)
                            {
                                case 'description':
                                    echo __("You don't have access to enter a description");
                                    break;
                                case 'shortname':
                                    echo __("You don't have access to enter an issue label");
                                    break;
                                case 'reproduction_steps':
                                    echo __("You don't have access to enter steps to reproduce");
                                    break;
                                case 'edition':
                                    echo __("You don't have access to add edition information");
                                    break;
                                case 'build':
                                    echo __("You don't have access to enter release information");
                                    break;
                                case 'component':
                                    echo __("You don't have access to enter component information");
                                    break;
                                case 'category':
                                    echo __("You don't have access to specify a category");
                                    break;
                                case 'status':
                                    echo __("You don't have access to specify a status");
                                    break;
                                case 'priority':
                                    echo __("You don't have access to specify a priority");
                                    break;
                                case 'reproducability':
                                    echo __("You don't have access to specify reproducability");
                                    break;
                                case 'severity':
                                    echo __("You don't have access to specify a severity");
                                    break;
                                case 'resolution':
                                    echo __("You don't have access to specify a resolution");
                                    break;
                                case 'estimated_time':
                                    echo __("You don't have access to estimate the issue");
                                    break;
                                case 'spent_time':
                                    echo __("You don't have access to specify time already spent working on the issue");
                                    break;
                                case 'percent_complete':
                                    echo __("You don't have access to specify how many percent complete the issue is");
                                    break;
                            }

                        ?>
                    <?php else: ?>
                        <?= __('You don\'t have access to enter "%field_name"', array('%field_name' => CustomDatatype::getByKey($key)->getDescription())); ?>
                    <?php endif; ?>
                <?php else: ?>
                    <?= $p_error; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </span>
    </div>
<?php elseif ($issue instanceof Issue): ?>
    <div class="message-box type-info" id="report_issue_reported_issue_details">
        <div class="message">
            <?= __('The following issue was reported: %link_to_issue', ['%link_to_issue' => link_tag(make_url('viewissue', ['project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()]), $issue->getFormattedIssueNo(true) . ' - ' . $issue->getTitle())]); ?>
        </div>
        <div class="actions">
            <a class="button primary" id="report_issue_report_another_button" onclick="[$(this), $('#report_issue_form'), $('#report_more_here'), $('#report_form'), $('#issuetype_list'), $('#report_issue_reported_issue_details')].each(function (el) { Element.toggle(el, 'block'); });$('#reportissue_container').removeClass('medium');$('#reportissue_container').addClass('large');"><?= __('Report another issue'); ?></a>
        </div>
    </div>
<?php endif; ?>

<div class="form-container" id="report_form_issue_type_selector">
    <?php if (count($issuetypes) > 0): ?>
        <div class="form-row" id="issuetype_list" <?php if ($selected_issuetype instanceof Issuetype) echo 'style="display: none;"'; ?>>
            <div class="list-mode">
                <?php if ($introarticle instanceof Article): ?>
                    <?php include_component('publish/articledisplay', array('article' => $introarticle, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
                <?php endif; ?>
                <?php foreach ($issuetypes as $issuetype): ?>
                    <?php if (!$selected_project->getIssuetypeScheme()->isIssuetypeReportable($issuetype)) continue; ?>
                    <?php if (isset($board) && $issuetype->getID() == $board->getEpicIssuetypeID()) continue; ?>
                    <a class="list-item" data-key="<?= $issuetype->getKey(); ?>" data-id="<?= $issuetype->getID(); ?>" href="javascript:void(0);">
                        <?= fa_image_tag($issuetype->getFontAwesomeIcon(), ['class' => 'icon issuetype-icon issuetype-' . $issuetype->getType()]); ?>
                        <span class="name"><?= __('Choose %issuetype_name', array('%issuetype_name' => $issuetype->getName())); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div id="report_more_here" class="form-row" <?php if ($selected_issuetype instanceof Issuetype && $selected_project instanceof Project): ?> style="display: none;"<?php endif; ?>>
            <span id="issuetype_description_help" class="helper-text"><?= __("Hold your mouse over an issuetype to see what it's used for"); ?></span>
        </div>
        <script type="text/javascript">
            require(['domReady', 'pachno/index', 'jquery'], function (domReady, pachno_index_js, $) {
                domReady(function () {
                    var issueDescriptions = {
                        <?php foreach ($issuetypes as $issuetype): ?>
                        <?php if (!$selected_project->getIssuetypeScheme()->isIssuetypeReportable($issuetype) && !$pachno_request->isAjaxCall()) continue; ?>
                        "<?= $issuetype->getKey(); ?>" : "<?= addslashes(html_entity_decode($issuetype->getDescription(),ENT_QUOTES)); ?>",
                        <?php endforeach; ?>
                    };

                    var cachedHelp = $("#issuetype_description_help").text();

                    $("#issuetype_list a").each(function() {
                        var issueType = $(this);
                        var issueKey = issueType.attr("data-key");

                        issueType
                            .click(function() {
                                document.getElementById('report_issue_issue_type_' + issueType.attr("data-id")).checked = true;
                                $('#reportissue_container').addClass('huge');
                                $('#reportissue_container').removeClass('large');
                                Pachno.Issues.updateFields('<?= make_url('getreportissuefields', array('project_key' => $selected_project->getKey())); ?>');
                            })
                            .mouseover(function() {
                                $('#issuetype_description_help').text(issueDescriptions[issueKey]);
                            })
                            .mouseout(function() {
                                $('#issuetype_description_help').text(cachedHelp);
                            });
                    });
                });
            });
        </script>
    <?php endif; ?>
</div>
<div class="form-container" id="report_form" style="display: none;">
    <form action="<?= make_url('project_reportissue', array('project_key' => $selected_project->getKey())); ?>" method="post" accept-charset="<?= Context::getI18n()->getCharset(); ?>" <?php if ($pachno_request->isAjaxCall()): ?>onsubmit="Pachno.Main.submitIssue('<?= make_url('project_reportissue', array('project_key' => $selected_project->getKey(), 'return_format' => 'planning')); ?>');return false;" id="report_issue_form" style="<?php if (isset($issue) && $issue instanceof Issue) echo 'display: none;'; ?>"<?php endif; ?>>
        <input type="hidden" name="project_id" id="project_id" value="<?= $selected_project->getID(); ?>">
        <?php if (isset($selected_milestone) || isset($selected_build) || isset($parent_issue)): ?>
            <div class="message-box type-info">
                <div class="message">
                    <?php if (isset($selected_milestone)): ?>
                        <?= __('You are adding an issue to %milestone_name', array('%milestone_name' => '<b>'.$selected_milestone->getName().'</b>')); ?>
                        <input type="hidden" name="milestone_id" id="reportissue_selected_milestone_id" value="<?= $selected_milestone->getID(); ?>">
                        <input type="hidden" name="milestone_fixed" value="1">
                    <?php endif; ?>
                    <?php if (isset($parent_issue)): ?>
                        <?= __('Issues you create will be child issues of %related_issue_title', array('%related_issue_title' => '<b>'.$parent_issue->getFormattedTitle().'</b>')); ?>
                        <input type="hidden" name="parent_issue_id" id="reportissue_parent_issue_id" value="<?= $parent_issue->getID(); ?>">
                        <?php if ($issue instanceof Issue): ?>
                        <script>
                            require(['domReady', 'pachno/index'], function (domReady, Pachno) {
                                domReady(function () {
                                    Pachno.Issues.refreshRelatedIssues('<?= make_url('viewissue_related_issues', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $parent_issue->getID())); ?>');
                                });
                            });
                        </script>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (isset($selected_build)): ?>
                        <?= __('You are adding an issue to release %release_name', array('%release_name' => '<b>'.$selected_build->getName().'</b>')); ?>
                        <input type="hidden" name="build_id" id="reportissue_selected_build_id" value="<?= $selected_build->getID(); ?>">
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (count($issuetypes) > 0): ?>
            <?php if ($reporthelparticle instanceof Article): ?>
                <?php include_component('publish/articledisplay', array('article' => $reporthelparticle, 'show_title' => false, 'show_details' => false, 'show_actions' => false, 'embedded' => true)); ?>
            <?php endif; ?>
            <div class="form-row">
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown" data-default-label="<?= __('Select an issue type'); ?>">
                        <label><?= __('Issue type'); ?></label>
                        <span class="value"></span>
                        <?php if (!isset($locked_issuetype) || !$locked_issuetype): ?>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <?php foreach ($issuetypes as $issuetype): ?>
                                    <?php if (!$selected_project->getIssuetypeScheme()->isIssuetypeReportable($issuetype)) continue; ?>
                                    <input type="radio" class="fancy-checkbox" id="report_issue_issue_type_<?= $issuetype->getId(); ?>" name="issuetype_id" value="<?= $issuetype->getId(); ?>" <?php if ($selected_issuetype instanceof Issuetype && $selected_issuetype->getID() == $issuetype->getID()) echo 'checked'; ?> onchange="Pachno.Issues.updateFields('<?= make_url('getreportissuefields', array('project_key' => $selected_project->getKey())); ?>');">
                                    <label for="report_issue_issue_type_<?= $issuetype->getId(); ?>" class="list-item multiline">
                                        <span class="icon"><?= fa_image_tag($issuetype->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $issuetype->getIcon()]); ?></span>
                                        <span class="name">
                                            <span class="title value"><?= $issuetype->getName(); ?></span>
                                            <span class="description"><?= $issuetype->getDescription(); ?></span>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-row <?php if (array_key_exists('title', $errors)): ?> invalid<?php endif; ?>">
                <input type="text" name="title" id="report_issue_title_input" class="name-input-enhance" value="<?php if (isset($title) && trim($title) != '') echo htmlspecialchars($title); ?>" placeholder="<?= __('Enter a short, but descriptive summary of the issue here'); ?>">
                <label for="report_issue_title_input" class="required"><span>* </span><?= __('Short summary'); ?></label>
            </div>
            <div class="form-row additional_information" id="status_div" style="display: none;">
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                        <label id="status_label"><?php echo __('Select initial status'); ?></label>
                        <span class="value"></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <?php foreach ($statuses as $status): ?>
                                <input type="radio" value="<?php echo $status->getID(); ?>" name="status_id" id="report_issue_status_id_<?php echo $status->getID(); ?>" class="fancy-checkbox" <?php if ($selected_status instanceof Datatype && $selected_status->getID() == $status->getID()) echo ' checked'; ?>>
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
            <div class="form-row <?php if (array_key_exists('shortname', $errors)): ?> invalid<?php endif; ?>" id="shortname_div" style="display: none;">
                <input type="text" name="shortname" id="shortname" class="shortname" value="<?php if (isset($shortname) && trim($shortname) != '') echo htmlspecialchars($shortname); ?>" placeholder="<?= __('Enter a very short label for the issue here'); ?>">
                <label for="shortname" id="shortname_label"><span>* </span><?= __('Issue label'); ?></label>
            </div>
            <div id="report_issue_more_options_indicator" class="form-row" style="display: none;">
                <div class="helper-text">
                    <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                    <span><?= __('Checking fields, please wait'); ?>...</span>
                </div>
            </div>
            <div class="form-row <?php if (array_key_exists('description', $errors)) echo 'invalid'; ?>" id="description_div">
                <label for="report_issue_description_input" id="description_label"><?= __('Description'); ?></label>
                <?php include_component('main/textarea', ['area_name' => 'description', 'target_type' => 'project', 'target_id' => $selected_project->getID(), 'height' => '300px', 'width' => '990px', 'syntax' => $pachno_user->getPreferredIssuesSyntax(true), 'value' => ((isset($selected_description)) ? $selected_description : null)]); ?>
                <div class="helper-text"><?= __('Describe the issue in as much detail as possible. More is better.'); ?></div>
            </div>
            <div class="form-row <?php if (array_key_exists('reproduction_steps', $errors)) echo 'invalid'; ?>" id="reproduction_steps_div">
                <label for="report_issue_reproduction_steps_input" id="reproduction_steps_label"><?= __('Reproduction steps'); ?></label>
                <?php include_component('main/textarea', ['area_name' => 'reproduction_steps', 'target_type' => 'project', 'target_id' => $selected_project->getID(), 'height' => '300px', 'width' => '990px', 'syntax' => $pachno_user->getPreferredIssuesSyntax(true), 'value' => ((isset($selected_reproduction_steps)) ? $selected_reproduction_steps : null)]); ?>
                <div class="helper-text">
                    <?= __('Enter the steps necessary to reproduce the issue, as detailed as possible.'); ?>
                </div>
            </div>
            <?php if ($canupload): ?>
                <?php include_component('main/uploader', array('mode' => 'issue')); ?>
            <?php endif; ?>
            <div class="form-row additional_information <?php if (array_key_exists('edition', $errors)): ?>invalid<?php endif; ?>" id="edition_div" style="display: none;">
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                        <label id="edition_label"><span>* </span><?php echo __('Affected edition'); ?></label>
                        <span class="value"></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode"></div>
                    </div>
                </div>
                <div class="helper-text"><?= __("Select which edition of the product you're using"); ?></div>
            </div>
            <div class="form-row additional_information <?php if (array_key_exists('build', $errors)): ?>invalid<?php endif; ?>" id="build_div" style="display: none;">
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                        <label id="build_label"><span>* </span><?php echo __('Affected release'); ?></label>
                        <span class="value"></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode"></div>
                    </div>
                </div>
                <div class="helper-text"><?= __("Select which release you're using"); ?></div>
            </div>
            <div class="form-row additional_information <?php if (array_key_exists('component', $errors)): ?>invalid<?php endif; ?>" id="component_div" style="display: none;">
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                        <label id="component_label"><span>* </span><?php echo __('Affected component'); ?></label>
                        <span class="value"></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode"></div>
                    </div>
                </div>
                <div class="helper-text"><?= __("Choose the component where this issue occurs"); ?></div>
            </div>
            <div class="form-row additional_information" id="estimated_time_div" style="display: none;">
                <input name="estimated_time" id="estimated_time_id" class="number" placeholder="<?= __('Enter an estimate here'); ?>">
                <label for="estimated_time_id" id="estimated_time_label">
                    <?= fa_image_tag('clock', ['class' => 'icon']); ?>
                    <?= __('Estimated time'); ?>
                </label>
                <div class="helper-text"><?= __('Type in your estimate here. Use keywords such as "points", "minutes", "hours", "days", "weeks" and "months" to describe your estimate'); ?></div>
            </div>
            <div class="form-row additional_information" id="spent_time_div" style="display: none;">
                <input name="spent_time" id="spent_time_id" class="number" placeholder="<?= __('Enter an estimate here'); ?>">
                <label for="spent_time_id" id="spent_time_label">
                    <?= fa_image_tag('clock', ['class' => 'icon']); ?>
                    <?= __('Spent time'); ?>
                </label>
                <div class="helper-text"><?= __('Enter time spent on this issue here. Use keywords such as "points", "minutes", "hours", "days", "weeks" and "months" to describe your effort'); ?></div>
            </div>
            <div class="form-row additional_information" id="percentage_div" style="display: none;">
                <input name="percentage" id="percentage_id" class="number" placeholder="<?= __('Enter an estimate here'); ?>">
                <label for="percentage_id" id="percentage_label">
                    <?= fa_image_tag('percent', ['class' => 'icon']); ?>
                    <?= __('Percentage completed'); ?>
                </label>
                <div class="helper-text"><?= __('Enter time spent on this issue here. Use keywords such as "points", "minutes", "hours", "days", "weeks" and "months" to describe your effort'); ?></div>
            </div>
            <div class="form-row additional_information <?php if (array_key_exists('category', $errors)): ?>invalid<?php endif; ?>" id="category_div" style="display: none;">
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                        <label id="category_label"><?php echo __('Select category'); ?></label>
                        <span class="value"></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <input type="radio" value="" name="category_id" id="report_issue_category_id_0" class="fancy-checkbox" <?php if (!$selected_category instanceof Category) echo ' checked'; ?>>
                            <label for="report_issue_category_id_0" class="list-item">
                                <span class="name value"><?= __('Not selected'); ?></span>
                            </label>
                            <?php foreach ($categories as $category): ?>
                                <?php if (!$category->hasAccess()) continue; ?>
                                <input type="radio" value="<?php echo $category->getID(); ?>" name="category_id" id="report_issue_category_id_<?php echo $category->getID(); ?>" class="fancy-checkbox" <?php if ($selected_category instanceof Datatype && $selected_category->getID() == $category->getID()) echo ' checked'; ?>>
                                <label for="report_issue_category_id_<?php echo $category->getID(); ?>" class="list-item">
                                    <span class="name value"><?php echo __($category->getName()); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row additional_information <?php if (array_key_exists('resolution', $errors)): ?>invalid<?php endif; ?>" id="resolution_div" style="display: none;">
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                        <label id="resolution_label"><?php echo __('Select resolution'); ?></label>
                        <span class="value"></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <input type="radio" value="" name="resolution_id" id="report_issue_resolution_id_0" class="fancy-checkbox" <?php if (!$selected_resolution instanceof Resolution) echo ' checked'; ?>>
                            <label for="report_issue_resolution_id_0" class="list-item">
                                <span class="name value"><?= __('Not selected'); ?></span>
                            </label>
                            <?php foreach ($resolutions as $resolution): ?>
                                <input type="radio" value="<?php echo $resolution->getID(); ?>" name="resolution_id" id="report_issue_resolution_id_<?php echo $resolution->getID(); ?>" class="fancy-checkbox" <?php if ($selected_resolution instanceof Datatype && $selected_resolution->getID() == $resolution->getID()) echo ' checked'; ?>>
                                <label for="report_issue_resolution_id_<?php echo $resolution->getID(); ?>" class="list-item">
                                    <span class="name value"><?php echo __($resolution->getName()); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row additional_information <?php if (array_key_exists('reproducability', $errors)): ?>invalid<?php endif; ?>" id="reproducability_div" style="display: none;">
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                        <label id="reproducability_label"><?php echo __('Select reproducability'); ?></label>
                        <span class="value"></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <input type="radio" value="" name="reproducability_id" id="report_issue_reproducability_id_0" class="fancy-checkbox" <?php if (!$selected_reproducability instanceof Reproducability) echo ' checked'; ?>>
                            <label for="report_issue_reproducability_id_0" class="list-item">
                                <span class="name value"><?= __('Not selected'); ?></span>
                            </label>
                            <?php foreach ($reproducabilities as $reproducability): ?>
                                <input type="radio" value="<?php echo $reproducability->getID(); ?>" name="reproducability_id" id="report_issue_reproducability_id_<?php echo $reproducability->getID(); ?>" class="fancy-checkbox" <?php if ($selected_reproducability instanceof Datatype && $selected_reproducability->getID() == $reproducability->getID()) echo ' checked'; ?>>
                                <label for="report_issue_reproducability_id_<?php echo $reproducability->getID(); ?>" class="list-item">
                                    <span class="name value"><?php echo __($reproducability->getName()); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row additional_information <?php if (array_key_exists('priority', $errors)): ?>invalid<?php endif; ?>" id="priority_div" style="display: none;">
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                        <label id="priority_label"><?php echo __('Select priority'); ?></label>
                        <span class="value"></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <input type="radio" value="" name="priority_id" id="report_issue_priority_id_0" class="fancy-checkbox" <?php if (!$selected_priority instanceof Priority) echo ' checked'; ?>>
                            <label for="report_issue_priority_id_0" class="list-item">
                                <span class="name value"><?= __('Not selected'); ?></span>
                            </label>
                            <?php foreach ($priorities as $priority): ?>
                                <input type="radio" value="<?php echo $priority->getID(); ?>" name="priority_id" id="report_issue_priority_id_<?php echo $priority->getID(); ?>" class="fancy-checkbox" <?php if ($selected_priority instanceof Datatype && $selected_priority->getID() == $priority->getID()) echo ' checked'; ?>>
                                <label for="report_issue_priority_id_<?php echo $priority->getID(); ?>" class="list-item">
                                    <span class="name value"><?php echo __($priority->getName()); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row additional_information <?php if (array_key_exists('severity', $errors)): ?>invalid<?php endif; ?>" id="severity_div" style="display: none;">
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                        <label id="severity_label"><?php echo __('Select severity'); ?></label>
                        <span class="value"></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <input type="radio" value="" name="severity_id" id="report_issue_severity_id_0" class="fancy-checkbox" <?php if (!$selected_severity instanceof Severity) echo ' checked'; ?>>
                            <label for="report_issue_severity_id_0" class="list-item">
                                <span class="name value"><?= __('Not selected'); ?></span>
                            </label>
                            <?php foreach ($severities as $severity): ?>
                                <input type="radio" value="<?php echo $severity->getID(); ?>" name="severity_id" id="report_issue_severity_id_<?php echo $severity->getID(); ?>" class="fancy-checkbox" <?php if ($selected_severity instanceof Datatype && $selected_severity->getID() == $severity->getID()) echo ' checked'; ?>>
                                <label for="report_issue_severity_id_<?php echo $severity->getID(); ?>" class="list-item">
                                    <span class="name value"><?php echo __($severity->getName()); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row additional_information <?php if (array_key_exists('milestone', $errors)): ?>invalid<?php endif; ?>" id="milestone_div" style="display: none;">
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown" data-default-label="<?= __('Not selected'); ?>">
                        <label id="milestone_label"><?php echo __('Select milestone'); ?></label>
                        <span class="value"></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <input type="radio" value="" name="milestone_id" id="report_issue_milestone_id_0" class="fancy-checkbox" <?php if (!$selected_milestone instanceof Milestone) echo ' checked'; ?>>
                            <label for="report_issue_milestone_id_0" class="list-item">
                                <span class="name value"><?= __('Not selected'); ?></span>
                            </label>
                            <?php foreach ($milestones as $milestone): ?>
                                <?php if ($milestone->isClosed()) continue; ?>
                                <input type="radio" value="<?php echo $milestone->getID(); ?>" name="milestone_id" id="report_issue_milestone_id_<?php echo $milestone->getID(); ?>" class="fancy-checkbox" <?php if ($selected_milestone instanceof Milestone && $selected_milestone->getID() == $milestone->getID()) echo ' checked'; ?>>
                                <label for="report_issue_milestone_id_<?php echo $milestone->getID(); ?>" class="list-item">
                                    <span class="name value"><?php echo __($milestone->getName()); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php /*<table id="pain_bug_type_div" style="display: none;" class="additional_information<?php if (array_key_exists('pain_bug_type', $errors)): ?> reportissue_error<?php endif; ?>">
                <tr>
                    <td style="width: 180px;"><label for="pain_bug_type_id" id="pain_bug_type_label"><span>* </span><?= __('Triaging: Bug type'); ?></label></td>
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
                    <td style="width: 180px;"><label for="pain_likelihood_id" id="pain_likelihood_label"><span>* </span><?= __('Triaging: Likelihood'); ?></label></td>
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
                    <td style="width: 180px;"><label for="pain_effect_id" id="pain_effect_label"><span>* </span><?= __('Triaging: Effect'); ?></label></td>
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
                <table id="<?= $customdatatype->getKey(); ?>_div" style="display: none;" class="additional_information<?php if (array_key_exists($customdatatype->getKey(), $errors)): ?> reportissue_error<?php endif; ?>">
                    <tr>
                        <?php if ($customdatatype->getType() == CustomDatatype::DATE_PICKER || $customdatatype->getType() == CustomDatatype::DATETIME_PICKER): ?>
                            <td style="width: 180px;"><label for="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_label"><span>* </span><?= __($customdatatype->getDescription()); ?></label></td>
                            <td style="width: 326px;position: relative;" class="report_issue_help faded_out dark">
                                <a href="javascript:void(0);" class="dropper dropdown_link"><?= image_tag('tabmenu_dropdown.png', array('class' => 'dropdown')); ?></a>
                                <ul class="popup_box more_actions_dropdown" id="<?= $customdatatype->getKey(); ?>_change">
                                    <li class="header"><?= __($customdatatype->getDescription()); ?></li>
                                    <li>
                                        <a href="javascript:void(0);" onclick="$('#<?= $customdatatype->getKey(); ?>_name').hide();$('#<?= $customdatatype->getKey(); ?>_value').value = '';$('#no_<?= $customdatatype->getKey(); ?>').show();"><?= __('Clear this field'); ?></a>
                                    </li>
                                    <li class="separator"></li>
                                    <li id="customfield_<?= $customdatatype->getKey(); ?>_calendar_container" style="padding: 0;"></li>
                                    <?php if ($customdatatype->getType() == CustomDatatype::DATETIME_PICKER): ?>
                                        <li class="nohover">
                                            <label><?= __('Time'); ?></label>
                                            <input type="text" id="customfield_<?= $customdatatype->getKey(); ?>_hour" value="00" style="width: 20px; font-size: 0.9em; text-align: center;">&nbsp;:&nbsp;
                                            <input type="text" id="customfield_<?= $customdatatype->getKey(); ?>_minute" value="00" style="width: 20px; font-size: 0.9em; text-align: center;">
                                        </li>
                                    <?php endif; ?>
                                    <script type="text/javascript">
                                        require(['domReady', 'pachno/index', 'calendarview'], function (domReady, pachno_index_js, Calendar) {
                                            domReady(function () {
                                                Calendar.setup({
                                                    dateField: '<?= $customdatatype->getKey(); ?>_name',
                                                    parentElement: 'customfield_<?= $customdatatype->getKey(); ?>_calendar_container',
                                                    valueCallback: function(element, date) {
                                                        <?php if ($customdatatype->getType() == CustomDatatype::DATETIME_PICKER) { ?>
                                                            var value = date.setHours(parseInt($('#customfield_<?= $customdatatype->getKey(); ?>_hour').value));
                                                            var date  = new Date(value);
                                                            var value = Math.floor(date.setMinutes(parseInt($('#customfield_<?= $customdatatype->getKey(); ?>_minute').value)) / 1000);
                                                            $('#<?= $customdatatype->getKey(); ?>_name').dataset.dateStr = $('#<?= $customdatatype->getKey(); ?>_name').innerText;
                                                            $('#<?= $customdatatype->getKey(); ?>_name').update(
                                                                $('#<?= $customdatatype->getKey(); ?>_name').dataset.dateStr + ' '
                                                                + parseInt($('#customfield_<?= $customdatatype->getKey(); ?>_hour').value) + ':'
                                                                + parseInt($('#customfield_<?= $customdatatype->getKey(); ?>_minute').value)
                                                            );
                                                        <?php } else { ?>
                                                            var value = Math.floor(date.getTime() / 1000);
                                                        <?php } ?>
                                                        $('#<?= $customdatatype->getKey(); ?>_name').show();
                                                        $('#<?= $customdatatype->getKey(); ?>_value').value = value;
                                                        $('#no_<?= $customdatatype->getKey(); ?>').hide();
                                                    }
                                                });
                                                <?php if ($customdatatype->getType() == CustomDatatype::DATETIME_PICKER): ?>
                                                    Event.observe($('#customfield_<?= $customdatatype->getKey(); ?>_hour'), 'change', function (event) {
                                                        var value = parseInt($('#<?= $customdatatype->getKey(); ?>_value').value);
                                                        var hours = parseInt(this.value);
                                                        if (value <= 0 || hours < 0 || hours > 24) return;
                                                        var date = new Date(value * 1000);
                                                        $('#<?= $customdatatype->getKey(); ?>_value').value = date.setHours(parseInt(this.value)) / 1000;
                                                        $('#<?= $customdatatype->getKey(); ?>_name').update(
                                                            $('#<?= $customdatatype->getKey(); ?>_name').dataset.dateStr + ' '
                                                            + parseInt($('#customfield_<?= $customdatatype->getKey(); ?>_hour').value) + ':'
                                                            + parseInt($('#customfield_<?= $customdatatype->getKey(); ?>_minute').value)
                                                        );
                                                    });
                                                    Event.observe($('#customfield_<?= $customdatatype->getKey(); ?>_minute'), 'change', function (event) {
                                                        var value = parseInt($('#<?= $customdatatype->getKey(); ?>_value').value);
                                                        var minutes = parseInt(this.value);
                                                        if (value <= 0 || minutes < 0 || minutes > 60) return;
                                                        var date = new Date(value * 1000);
                                                        $('#<?= $customdatatype->getKey(); ?>_value').value = date.setMinutes(parseInt(this.value)) / 1000;
                                                        $('#<?= $customdatatype->getKey(); ?>_name').update(
                                                            $('#<?= $customdatatype->getKey(); ?>_name').dataset.dateStr + ' '
                                                            + parseInt($('#customfield_<?= $customdatatype->getKey(); ?>_hour').value) + ':'
                                                            + parseInt($('#customfield_<?= $customdatatype->getKey(); ?>_minute').value)
                                                        );
                                                    });
                                                <?php endif; ?>
                                            });
                                        });
                                    </script>
                                </ul>
                                <span id="<?= $customdatatype->getKey(); ?>_name" style="display: none;"><?= __('Not set'); ?></span><span class="faded_out" id="no_<?= $customdatatype->getKey(); ?>"><?= __('Not set'); ?></span>
                                <input type="hidden" name="<?= $customdatatype->getKey(); ?>_value" id="<?= $customdatatype->getKey(); ?>_value" />
                            </td>
                        <?php else: ?>
                            <td style="width: 180px;"><label for="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_label"><span>* </span><?= __($customdatatype->getDescription()); ?></label></td>
                            <td class="report_issue_help faded_out dark"><?= __($customdatatype->getInstructions()); ?></td>
                        <?php endif; ?>
                    <tr>
                        <td colspan="2" style="padding-top: 5px;" class="editor_container">
                            <?php
                                switch ($customdatatype->getType())
                                {
                                    case CustomDatatype::DROPDOWN_CHOICE_TEXT: ?>
                                        <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                            <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof CustomDatatypeOption) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                            <?php foreach ($customdatatype->getOptions() as $option): ?>
                                            <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof CustomDatatypeOption && $selected_customdatatype[$customdatatype->getKey()]->getID() == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php
                                        break;
                                    case CustomDatatype::EDITIONS_CHOICE: ?>
                                        <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                            <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof Edition) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                            <?php if ($selected_project instanceof Project): ?>
                                                <?php foreach ($selected_project->getEditions() as $option): ?>
                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <?php
                                        break;
                                    case CustomDatatype::STATUS_CHOICE: ?>
                                        <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                            <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof Status) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                            <?php foreach (Status::getAll() as $option): ?>
                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php
                                        break;
                                    case CustomDatatype::TEAM_CHOICE: ?>
                                        <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                            <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof Team) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                            <?php foreach (Team::getAll() as $option): ?>
                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php
                                        break;
                                    case CustomDatatype::CLIENT_CHOICE: ?>
                                        <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                            <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof Client) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                            <?php foreach (Client::getAll() as $option): ?>
                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php
                                        break;
                                    case CustomDatatype::COMPONENTS_CHOICE: ?>
                                        <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                            <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof Component) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                            <?php if ($selected_project instanceof Project): ?>
                                                <?php foreach ($selected_project->getComponents() as $option): ?>
                                                    <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <?php
                                        break;
                                    case CustomDatatype::RELEASES_CHOICE: ?>
                                        <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id" style="width: 100%;">
                                            <option value=""<?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof Build) echo ' selected'; ?>><?= __('Not specified'); ?></option>
                                            <?php if ($selected_project instanceof Project): ?>
                                                <?php foreach ($selected_project->getBuilds() as $option): ?>
                                                    <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <?php
                                        break;
                                    case CustomDatatype::RADIO_CHOICE: ?>
                                        <input type="radio" name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_0" value="" <?php if (!$selected_customdatatype[$customdatatype->getKey()] instanceof CustomDatatypeOption): ?> selected<?php endif; ?> /> <label for="<?= $customdatatype->getKey(); ?>_0"><?= __('Not specified'); ?></label><br>
                                        <?php foreach ($customdatatype->getOptions() as $option): ?>
                                            <input type="radio" name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_<?= $option->getID(); ?>" value="<?= $option->getID(); ?>" <?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof CustomDatatypeOption && $selected_customdatatype[$customdatatype->getKey()]->getID() == $option->getID()): ?> selected<?php endif; ?> /> <label for="<?= $customdatatype->getKey(); ?>_<?= $option->getID(); ?>"><?= $option->getName(); ?></label><br>
                                        <?php endforeach; ?>
                                        <?php
                                        break;
                                    case CustomDatatype::INPUT_TEXT:
                                        ?>
                                        <input type="text" name="<?= $customdatatype->getKey(); ?>_value" value="<?= $selected_customdatatype[$customdatatype->getKey()]; ?>" id="<?= $customdatatype->getKey(); ?>_value" /><br>
                                        <?php
                                        break;
                                    case CustomDatatype::INPUT_TEXTAREA_SMALL:
                                    case CustomDatatype::INPUT_TEXTAREA_MAIN:
                                        ?>
                                        <?php include_component('main/textarea', array('area_name' => $customdatatype->getKey().'_value', 'target_type' => 'project', 'target_id' => $selected_project->getID(), 'area_id' => $customdatatype->getKey().'_value', 'height' => '75px', 'width' => '100%', 'hide_hint' => true, 'syntax' => $pachno_user->getPreferredIssuesSyntax(true), 'value' => $selected_customdatatype[$customdatatype->getKey()])); ?>
                                        <?php
                                        break;
                                    case CustomDatatype::DATE_PICKER:
                                    case CustomDatatype::DATETIME_PICKER:
                                        ?>

                                        <?php
                                        break;
                                }
                            ?>
                        </td>
                    </tr>
                </table>
            <?php endforeach; ?>
            <?php if ($selected_issuetype != null && $selected_project != null): ?>
                <script type="text/javascript">
                    require(['domReady', 'pachno/index'], function (domReady, Pachno) {
                        domReady(function () {
                            Pachno.Issues.updateFields('<?= make_url('getreportissuefields', array('project_key' => $selected_project->getKey())); ?>');
                        });
                    });
                </script>
            <?php endif; ?>
            <?php Event::createNew('core', 'reportissue.prefile')->trigger(); ?>
            <?php if ($selected_project instanceof Project && $selected_project->permissionCheck('canlockandeditlockedissues')): ?>
                <div class="report-issue-custom-access-check">
                    <div class="report-issue-custom-access-container" style="display:none;">
                        <input type="radio" name="issue_access" id="issue_access_public" onchange="Pachno.Issues.ACL.toggle_checkboxes(this, '', 'public');" value="public"<?php if ($selected_project->getIssuesLockType() === Project::ISSUES_LOCK_TYPE_PUBLIC) echo ' checked'; ?>><label for="issue_access_public"><?= __('Available to anyone with access to project'); ?></label><br>
                        <input type="radio" name="issue_access" id="issue_access_public_category" onchange="Pachno.Issues.ACL.toggle_checkboxes(this, '', 'public_category');" value="public_category"<?php if ($selected_project->getIssuesLockType() === Project::ISSUES_LOCK_TYPE_PUBLIC_CATEGORY) echo ' checked'; ?>><label for="issue_access_public_category"><?= __('Available to anyone with access to project, category and those listed below'); ?></label><br>
                        <input type="radio" name="issue_access" id="issue_access_restricted" onchange="Pachno.Issues.ACL.toggle_checkboxes(this, '', 'restricted');" value="restricted"<?php if ($selected_project->getIssuesLockType() === Project::ISSUES_LOCK_TYPE_RESTRICTED) echo ' checked'; ?>><label for="issue_access_restricted"><?= __('Available only to you and those listed below'); ?></label><br>
                        <script>
                            require(['domReady', 'jquery'], function (domReady, jQuery) {
                                domReady(function () { $('#input[name=issue_access]').trigger('change'); });
                            });
                        </script>
                        <?php image_tag('spinning_16.gif', array('id' => 'acl_indicator_', 'style' => '')); ?>
                        <div id="acl-users-teams-selector" style="display: none;">
                            <h4 style="margin-top: 10px;">
                                <?= javascript_link_tag(__('Add a user or team'), array('onclick' => "$('#popup_find_acl_').toggle('block');", 'style' => 'float: right;', 'class' => 'button')); ?>
                                <?= __('Users or teams who can see this issue'); ?>
                            </h4>
                            <?php include_component('main/identifiableselector', array(    'html_id'             => "popup_find_acl_",
                                                                                      'header'             => __('Give someone access to this issue'),
                                                                                      'callback'             => "Pachno.Issues.ACL.addTarget('" . make_url('getacl_formentry', array('identifiable_type' => 'user', 'identifiable_value' => '%identifiable_value')) . "', '');",
                                                                                      'team_callback'     => "Pachno.Issues.ACL.addTarget('" . make_url('getacl_formentry', array('identifiable_type' => 'team', 'identifiable_value' => '%identifiable_value')) . "', '');",
                                                                                      'base_id'            => "popup_find_acl_",
                                                                                      'include_teams'        => true,
                                                                                      'allow_clear'        => false,
                                                                                      'absolute'            => true,
                                                                                      'use_form'            => false)); ?>
                        </div>
                        <div id="acl__public" style="display: none;">
                            <ul class="issue_access_list simple-list" id="issue__public_category_access_list" style="display: none;">
                                <li id="issue__public_category_access_list_none" class="faded_out" style="<?php if (count($al_items)): ?>display: none; <?php endif; ?>padding: 5px;"><?= __('Noone else can see this issue'); ?></li>
                                <?php foreach ($al_items as $item): ?>
                                    <?php include_component('main/issueaclformentry', array('target' => $item['target'])); ?>
                                <?php endforeach; ?>
                            </ul>
                            <div style="text-align: right;">
                                <input id="issue_access_public_category_input" type="hidden" name="public_category" disabled>
                            </div>
                        </div>
                        <div id="acl__restricted" style="display: none;">
                            <ul class="issue_access_list simple-list" id="issue__restricted_access_list">
                                <li id="issue__restricted_access_list_none" class="faded_out" style="<?php if (count($al_items)): ?>display: none; <?php endif; ?>padding: 5px;"><?= __('Noone else can see this issue'); ?></li>
                                <?php foreach ($al_items as $item): ?>
                                    <?php include_component('main/issueaclformentry', array('target' => $item['target'])); ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-row submit-container">
                <input type="checkbox" name="custom_issue_access" id="report-issue-custom-access-checkbox" class="fancy-checkbox" onchange="Pachno.Issues.ACL.toggle_custom_access(this);" value="1"><label for="report-issue-custom-access-checkbox" class="button secondary icon"><?= fa_image_tag('user-lock', ['class' => 'checked']) . fa_image_tag('lock-open', ['class' => 'unchecked']); ?></label>
                <button type="submit" class="button primary" id="report_issue_submit_button">
                    <span><?= __('File issue'); ?></span>
                    <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator', 'id' => 'report_issue_indicator']); ?>
                </button>
            </div>
            <div id="reportissue_extrafields">
                <div class="form-row" id="milestone_additional" style="display: none;">
                    <div class="fancy-dropdown-container" id="milestone_additional_div">
                        <div class="fancy-dropdown">
                            <label><?php echo __('Milestone'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container from-bottom list-mode">
                                <input type="radio" value="" name="milestone_id" id="report_issue_milestone_id_0" class="fancy-checkbox" <?php if (!$selected_milestone instanceof Milestone) echo ' checked'; ?>>
                                <label for="report_issue_milestone_id_0" class="list-item">
                                    <span class="name value"><?php echo __('Not specified'); ?></span>
                                </label>
                                <?php foreach ($milestones as $milestone): ?>
                                    <?php if ($milestone->isClosed() && (!isset($selected_milestone) || $selected_milestone->getId() != $milestone->getId())) continue; ?>
                                    <input type="radio" value="<?php echo $milestone->getID(); ?>" name="milestone_id" id="report_issue_milestone_id_<?php echo $milestone->getID(); ?>" class="fancy-checkbox" <?php if ($selected_milestone instanceof Milestone && $selected_milestone->getID() == $milestone->getID()) echo ' checked'; ?>>
                                    <label for="report_issue_milestone_id_<?php echo $milestone->getID(); ?>" class="list-item">
                                        <span class="name value"><?php echo __($milestone->getName()); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="remove-button" onclick="$('#milestone_link').show();$('#milestone_additional_div').hide();document.querySelectorAll('#report_issue_form input[name=milestone_id]').forEach((elm) => { elm.checked = false; });"><?= fa_image_tag('undo-alt'); ?></button>
                        </div>
                    </div>
                </div>
                <div class="form-row" id="category_additional" style="display: none;">
                    <div class="fancy-dropdown-container" id="category_additional_div">
                        <div class="fancy-dropdown">
                            <label><?php echo __('Category'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container from-bottom list-mode">
                                <input type="radio" value="" name="category_id" id="report_issue_category_id_0" class="fancy-checkbox" <?php if (!$selected_category instanceof Category) echo ' checked'; ?>>
                                <label for="report_issue_category_id_0" class="list-item">
                                    <span class="name value"><?php echo __('Not specified'); ?></span>
                                </label>
                                <?php foreach ($categories as $category): ?>
                                    <?php if (!$category->hasAccess()) continue; ?>
                                    <input type="radio" value="<?php echo $category->getID(); ?>" name="category_id" id="report_issue_category_id_<?php echo $category->getID(); ?>" class="fancy-checkbox" <?php if ($selected_category instanceof Category && $selected_category->getID() == $category->getID()) echo ' checked'; ?>>
                                    <label for="report_issue_category_id_<?php echo $category->getID(); ?>" class="list-item">
                                        <span class="name value"><?php echo __($category->getName()); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="remove-button" onclick="$('#category_link').show();$('#category_additional_div').hide();document.querySelectorAll('#report_issue_form input[name=category_id]').forEach((elm) => { elm.checked = false; });"><?= fa_image_tag('undo-alt'); ?></button>
                        </div>
                    </div>
                </div>
                <div class="form-row" id="percent_complete_additional" style="display: none;">
                    <?= fa_image_tag('clock', ['class' => 'icon']); ?>
                    <div id="percent_complete_additional_div">
                        <input name="percent_complete" id="percent_complete_id_additional" class="number" placeholder="<?= __('Enter a percentage here'); ?>">
                        <label for="percent_complete_id_additional"><?= __('Percent complete'); ?></label>
                    </div>
                </div>
                <div class="form-row" id="priority_additional" style="display: none;">
                    <div class="fancy-dropdown-container" id="priority_additional_div">
                        <div class="fancy-dropdown">
                            <label><?php echo __('Priority'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container from-bottom list-mode">
                                <input type="radio" value="" name="priority_id" id="report_issue_priority_id_0" class="fancy-checkbox" <?php if (!$selected_priority instanceof Priority) echo ' checked'; ?>>
                                <label for="report_issue_priority_id_0" class="list-item">
                                    <span class="name value"><?php echo __('Not specified'); ?></span>
                                </label>
                                <?php foreach ($categories as $priority): ?>
                                    <?php if (!$priority->hasAccess()) continue; ?>
                                    <input type="radio" value="<?php echo $priority->getID(); ?>" name="priority_id" id="report_issue_priority_id_<?php echo $priority->getID(); ?>" class="fancy-checkbox" <?php if ($selected_priority instanceof Priority && $selected_priority->getID() == $priority->getID()) echo ' checked'; ?>>
                                    <label for="report_issue_priority_id_<?php echo $priority->getID(); ?>" class="list-item">
                                        <span class="name value"><?php echo __($priority->getName()); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="remove-button" onclick="$('#priority_link').show();$('#priority_additional_div').hide();document.querySelectorAll('#report_issue_form input[name=priority_id]').forEach((elm) => { elm.checked = false; });"><?= fa_image_tag('undo-alt'); ?></button>
                        </div>
                    </div>
                </div>
                <div class="form-row" id="reproducability_additional" style="display: none;">
                    <div class="fancy-dropdown-container" id="reproducability_additional_div">
                        <div class="fancy-dropdown">
                            <label><?php echo __('Reproducability'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container from-bottom list-mode">
                                <input type="radio" value="" name="reproducability_id" id="report_issue_reproducability_id_0" class="fancy-checkbox" <?php if (!$selected_reproducability instanceof Reproducability) echo ' checked'; ?>>
                                <label for="report_issue_reproducability_id_0" class="list-item">
                                    <span class="name value"><?php echo __('Not specified'); ?></span>
                                </label>
                                <?php foreach ($categories as $reproducability): ?>
                                    <?php if (!$reproducability->hasAccess()) continue; ?>
                                    <input type="radio" value="<?php echo $reproducability->getID(); ?>" name="reproducability_id" id="report_issue_reproducability_id_<?php echo $reproducability->getID(); ?>" class="fancy-checkbox" <?php if ($selected_reproducability instanceof Reproducability && $selected_reproducability->getID() == $reproducability->getID()) echo ' checked'; ?>>
                                    <label for="report_issue_reproducability_id_<?php echo $reproducability->getID(); ?>" class="list-item">
                                        <span class="name value"><?php echo __($reproducability->getName()); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="remove-button" onclick="$('#reproducability_link').show();$('#reproducability_additional_div').hide();document.querySelectorAll('#report_issue_form input[name=reproducability_id]').forEach((elm) => { elm.checked = false; });"><?= fa_image_tag('undo-alt'); ?></button>
                        </div>
                    </div>
                </div>
                <div class="form-row" id="resolution_additional" style="display: none;">
                    <div class="fancy-dropdown-container" id="resolution_additional_div">
                        <div class="fancy-dropdown">
                            <label><?php echo __('Resolution'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container from-bottom list-mode">
                                <input type="radio" value="" name="resolution_id" id="report_issue_resolution_id_0" class="fancy-checkbox" <?php if (!$selected_resolution instanceof Resolution) echo ' checked'; ?>>
                                <label for="report_issue_resolution_id_0" class="list-item">
                                    <span class="name value"><?php echo __('Not specified'); ?></span>
                                </label>
                                <?php foreach ($categories as $resolution): ?>
                                    <?php if (!$resolution->hasAccess()) continue; ?>
                                    <input type="radio" value="<?php echo $resolution->getID(); ?>" name="resolution_id" id="report_issue_resolution_id_<?php echo $resolution->getID(); ?>" class="fancy-checkbox" <?php if ($selected_resolution instanceof Resolution && $selected_resolution->getID() == $resolution->getID()) echo ' checked'; ?>>
                                    <label for="report_issue_resolution_id_<?php echo $resolution->getID(); ?>" class="list-item">
                                        <span class="name value"><?php echo __($resolution->getName()); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="remove-button" onclick="$('#resolution_link').show();$('#resolution_additional_div').hide();document.querySelectorAll('#report_issue_form input[name=resolution_id]').forEach((elm) => { elm.checked = false; });"><?= fa_image_tag('undo-alt'); ?></button>
                        </div>
                    </div>
                </div>
                <div class="form-row" id="severity_additional" style="display: none;">
                    <div class="fancy-dropdown-container" id="severity_additional_div">
                        <div class="fancy-dropdown">
                            <label><?php echo __('Severity'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container from-bottom list-mode">
                                <input type="radio" value="" name="severity_id" id="report_issue_severity_id_0" class="fancy-checkbox" <?php if (!$selected_severity instanceof Severity) echo ' checked'; ?>>
                                <label for="report_issue_severity_id_0" class="list-item">
                                    <span class="name value"><?php echo __('Not specified'); ?></span>
                                </label>
                                <?php foreach ($categories as $severity): ?>
                                    <?php if (!$severity->hasAccess()) continue; ?>
                                    <input type="radio" value="<?php echo $severity->getID(); ?>" name="severity_id" id="report_issue_severity_id_<?php echo $severity->getID(); ?>" class="fancy-checkbox" <?php if ($selected_severity instanceof Severity && $selected_severity->getID() == $severity->getID()) echo ' checked'; ?>>
                                    <label for="report_issue_severity_id_<?php echo $severity->getID(); ?>" class="list-item">
                                        <span class="name value"><?php echo __($severity->getName()); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="remove-button" onclick="$('#severity_link').show();$('#severity_additional_div').hide();document.querySelectorAll('#report_issue_form input[name=severity_id]').forEach((elm) => { elm.checked = false; });"><?= fa_image_tag('undo-alt'); ?></button>
                        </div>
                    </div>
                </div>
                <?php foreach (CustomDatatype::getAll() as $customdatatype): ?>
                    <div class="form-row" id="<?= $customdatatype->getKey(); ?>_additional" style="display: none;">
                        <?= image_tag('icon_customdatatype.png'); ?>
                        <div id="<?= $customdatatype->getKey(); ?>_link"<?php if ($selected_customdatatype[$customdatatype->getKey()] !== null): ?> style="display: none;"<?php endif; ?>><a href="javascript:void(0);" onclick="$('#<?= $customdatatype->getKey(); ?>_link').hide();$('#<?= $customdatatype->getKey(); ?>_additional_div').show();"><?= __($customdatatype->getDescription()); ?></a></div>
                        <div id="<?= $customdatatype->getKey(); ?>_additional_div"<?php if ($selected_customdatatype[$customdatatype->getKey()] === null): ?> style="display: none;"<?php endif; ?> class="editor_container">
                            <?php
                                switch ($customdatatype->getType())
                                {
                                    case CustomDatatype::DROPDOWN_CHOICE_TEXT:
                                        ?>
                                        <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional">
                                            <?php foreach ($customdatatype->getOptions() as $option): ?>
                                            <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof CustomDatatypeOption && $selected_customdatatype[$customdatatype->getKey()]->getID() == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php
                                        break;
                                    case CustomDatatype::EDITIONS_CHOICE:
                                        ?>
                                        <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional">
                                            <?php if ($selected_project instanceof Project): ?>
                                                <?php foreach ($selected_project->getEditions() as $option): ?>
                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <?php
                                        break;
                                    case CustomDatatype::STATUS_CHOICE:
                                        ?>
                                        <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional">
                                            <?php foreach (Status::getAll() as $option): ?>
                                            <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php
                                        break;
                                    case CustomDatatype::TEAM_CHOICE:
                                        ?>
                                        <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional">
                                            <?php foreach (Team::getAll() as $option): ?>
                                            <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php
                                        break;
                                    case CustomDatatype::CLIENT_CHOICE:
                                        ?>
                                        <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional">
                                            <?php foreach (Client::getAll() as $option): ?>
                                            <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php
                                        break;
                                    case CustomDatatype::COMPONENTS_CHOICE:
                                        ?>
                                        <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional">
                                            <?php if ($selected_project instanceof Project): ?>
                                                <?php foreach ($selected_project->getComponents() as $option): ?>
                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <?php
                                        break;
                                    case CustomDatatype::RELEASES_CHOICE:
                                        ?>
                                        <select name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional">
                                            <?php if ($selected_project instanceof Project): ?>
                                                <?php foreach ($selected_project->getBuilds() as $option): ?>
                                                <option value="<?= $option->getID(); ?>"<?php if ($selected_customdatatype[$customdatatype->getKey()] == $option->getID()): ?> selected<?php endif; ?>><?= $option->getName(); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <?php
                                        break;
                                    case CustomDatatype::RADIO_CHOICE:
                                        ?>
                                        <label for="<?= $customdatatype->getKey(); ?>_id_additional"><?= $customdatatype->getDescription(); ?></label>
                                        <br>
                                        <?php foreach ($customdatatype->getOptions() as $option): ?>
                                            <input type="radio" name="<?= $customdatatype->getKey(); ?>_id" id="<?= $customdatatype->getKey(); ?>_id_additional" value="<?= $option->getID(); ?>" <?php if ($selected_customdatatype[$customdatatype->getKey()] instanceof CustomDatatypeOption && $selected_customdatatype[$customdatatype->getKey()]->getID() == $option->getID()): ?> selected<?php endif; ?> /> <?= $option->getName(); ?><br>
                                        <?php
                                        endforeach;
                                        break;
                                    case CustomDatatype::INPUT_TEXT:
                                        ?>
                                        <input type="text" name="<?= $customdatatype->getKey(); ?>_value" class="field_additional" value="<?= $selected_customdatatype[$customdatatype->getKey()]; ?>" id="<?= $customdatatype->getKey(); ?>_value_additional" />
                                        <?php
                                        break;
                                    case CustomDatatype::INPUT_TEXTAREA_SMALL:
                                    case CustomDatatype::INPUT_TEXTAREA_MAIN:
                                        ?>
                                        <label for="<?= $customdatatype->getKey(); ?>_value_additional"><?= $customdatatype->getDescription(); ?></label>
                                        <br>
                                        <?php include_component('main/textarea', array('area_name' => $customdatatype->getKey().'_value', 'target_type' => 'project', 'target_id' => $selected_project->getID(), 'area_id' => $customdatatype->getKey().'_value_additional', 'height' => '125px', 'hide_hint' => true, 'width' => '100%', 'value' => $selected_customdatatype[$customdatatype->getKey()])); ?>
                                        <?php
                                        break;
                                }
                                if (!$customdatatype->hasCustomOptions())
                                {
                                    ?>
                                    <a href="javascript:void(0);" class="img" onclick="$('#<?= $customdatatype->getKey(); ?>_link').show();$('#<?= $customdatatype->getKey(); ?>_additional_div').hide();$('#<?= $customdatatype->getKey(); ?>_value_additional').setValue('');"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <a href="javascript:void(0);" class="img" onclick="$('#<?= $customdatatype->getKey(); ?>_link').show();$('#<?= $customdatatype->getKey(); ?>_additional_div').hide();$('#<?= $customdatatype->getKey(); ?>_id_additional').setValue(0);"><?= fa_image_tag('undo-alt', ['style' => 'float: none; margin-left: 5px;'], 'fas'); ?></a>
                                    <?php
                                }
                                ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php Event::createNew('core', 'reportissue.listfields')->trigger(); ?>
            </div>
        <?php endif; ?>
    </form>
</div>
