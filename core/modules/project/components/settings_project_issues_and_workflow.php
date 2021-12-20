<?php

    use pachno\core\framework\Context;
    use pachno\core\framework\Settings;
    use pachno\core\modules\publish\Publish;
    use pachno\core\entities\Project;

    /**
     * @var \pachno\core\entities\Project $project
     * @var int $access_level
     */

?>
<div class="fancy-tabs tab-switcher" id="project-issues-and-workflow-menu">
    <a id="tab_project_workflow" class="tab selected tab-switcher-trigger" data-tab-target="workflow"><?= fa_image_tag('code-branch', ['class' => 'icon']); ?><span><?= __('Workflow'); ?></span></a>
    <a id="tab_project_issues" class="tab tab-switcher-trigger" data-tab-target="issues"><?= fa_image_tag('file-alt', ['class' => 'icon']); ?><span><?= __('Issues'); ?></span></a>
</div>
<div id="project-issues-and-workflow-menu_panes" class="form-container">
    <div id="tab_project_issues_pane" style="display: none;" data-tab-id="issues">
        <?php if ($access_level == Settings::ACCESS_FULL): ?>
        <form
            accept-charset="<?= Context::getI18n()->getCharset(); ?>"
            data-submit-project-settings
            data-project-id="<?= $project->getID(); ?>"
            action="<?= make_url('configure_project_settings', ['project_id' => $project->getID()]); ?>"
            method="post"
            id="project_issuetypes_and_schemes"
            data-interactive-form
        >
            <?php endif; ?>
            <div class="form-row">
                <h3>
                    <span><?= __('Issue type scheme'); ?></span>
                    <a href="<?= make_url('configure_issuetypes_schemes'); ?>" target="_blank" class="button secondary"><?= fa_image_tag('cog', ['class' => 'icon']) ?><span><?= __('Configure issue type schemes'); ?></span></a>
                </h3>
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown">
                        <label><?= __('Issue type scheme'); ?></label>
                        <span class="value"></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <?php foreach (\pachno\core\entities\IssuetypeScheme::getAll() as $issuetype_scheme): ?>
                                <input type="radio" class="fancy-checkbox" id="issuetype_scheme_checkbox_<?= $issuetype_scheme->getID(); ?>" name="issuetype_scheme" value="<?= $issuetype_scheme->getID(); ?>" <?php if ($project->getIssuetypeScheme()->getID() == $issuetype_scheme->getID()) echo 'checked'; ?>>
                                <label for="issuetype_scheme_checkbox_<?= $issuetype_scheme->getID(); ?>" class="list-item multiline">
                                    <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span class="name">
                                <span class="title value"><?= $issuetype_scheme->getName(); ?></span>
                                <span class="description">
                                    <?php foreach ($issuetype_scheme->getIssuetypes() as $issue_type): ?>
                                        <span class="status-badge" style="background-color: #FFF; color: #333;">
                                        <?php echo fa_image_tag($issue_type->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $issue_type->getIcon()]); ?>
                                        <span class="name"><?php echo __($issue_type->getName()); ?></span>
                                    </span>
                                    <?php endforeach; ?>
                                </span>
                            </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="helper-text"><?php echo __('The issue type scheme setting controls which issue types are available for this project.'); ?></div>
            </div>
            <div class="column">
                <div class="form-row">
                    <label for="use_prefix_yes"><?php echo __('Prefix issue numbers'); ?></label>
                    <div class="fancy-label-select">
                        <input name="use_prefix" class="fancy-checkbox" id="use_prefix_yes" type="radio" value="1"<?php if ($project->usePrefix()) echo ' checked'; ?> onchange="$('#project_prefix_input').enable();">
                        <label for="use_prefix_yes"><?= fa_image_tag('check', ['class' => 'checked']) . __('Yes'); ?></label>
                        <input name="use_prefix" class="fancy-checkbox" id="use_prefix_no" type="radio" value="0"<?php if (!$project->usePrefix()) echo ' checked'; ?> onchange="$('#project_prefix_input').disable();">
                        <label for="use_prefix_no"><?= fa_image_tag('check', ['class' => 'checked']) . __('No'); ?></label>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="form-row">
                    <label for="project_prefix_input"><?php echo __('Issue number prefix'); ?></label>
                    <?php if ($access_level == Settings::ACCESS_FULL): ?>
                        <input type="text" class="prefix-and-key" name="prefix" id="project_prefix_input" maxlength="10" value="<?php print $project->getPrefix(); ?>" <?php if (!$project->usePrefix()): ?> disabled<?php endif; ?>>
                    <?php elseif ($project->hasPrefix()): ?>
                        <?php echo $project->getPrefix(); ?>
                    <?php else: ?>
                        <span class="faded_out"><?php echo __('No prefix set'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-row">
                <div class="helper-text"><?php echo __('See %about_issue_prefix for an explanation about issue prefixes', array('%about_issue_prefix' => link_tag(Publish::getArticleLink('AboutIssuePrefixes'), __('about issue prefixes'), array('target' => '_new')))); ?></div>
            </div>
            <div class="form-row">
                <label for="locked_no"><?php echo __('Allow issues to be reported'); ?></label>
                <div class="fancy-label-select">
                    <?php if ($access_level == Settings::ACCESS_FULL): ?>
                        <input type="radio" name="locked" value="0" class="fancy-checkbox" id="locked_yes"<?php if (!$project->isLocked()): ?> checked<?php endif; ?>>
                        <label for="locked_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Yes'); ?></label>
                        <input type="radio" name="locked" value="1" class="fancy-checkbox" id="locked_no"<?php if ($project->isLocked()): ?> checked<?php endif; ?>>
                        <label for="locked_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('No'); ?></label>
                    <?php else: ?>
                        <?php echo (!$project->isLocked()) ? __('Yes') : __('No'); ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-row" style="display: none;">
                <label for="issues_lock_type"><?php echo __('Access policy for new issues'); ?></label>
                <?php if ($access_level == Settings::ACCESS_FULL): ?>
                    <div class="fancy-dropdown-container">
                        <div class="fancy-dropdown">
                            <label><?= __('Access policy'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <input type="radio" class="fancy-checkbox" id="issues_lock_type_checkbox_<?= Project::ISSUES_LOCK_TYPE_PUBLIC; ?>" name="issues_lock_type" value="<?= Project::ISSUES_LOCK_TYPE_PUBLIC; ?>" <?php if ($project->getIssuesLockType() === Project::ISSUES_LOCK_TYPE_PUBLIC) echo 'checked'; ?>>
                                <label for="issues_lock_type_checkbox_<?= Project::ISSUES_LOCK_TYPE_PUBLIC; ?>" class="list-item">
                                    <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span class="name value"><?= __('Available to anyone with access to project'); ?></span>
                                </label>
                                <input type="radio" class="fancy-checkbox" id="issues_lock_type_checkbox_<?= Project::ISSUES_LOCK_TYPE_PUBLIC_CATEGORY; ?>" name="issues_lock_type" value="<?= Project::ISSUES_LOCK_TYPE_PUBLIC_CATEGORY; ?>" <?php if ($project->getIssuesLockType() === Project::ISSUES_LOCK_TYPE_PUBLIC_CATEGORY) echo 'checked'; ?>>
                                <label for="issues_lock_type_checkbox_<?= Project::ISSUES_LOCK_TYPE_PUBLIC_CATEGORY; ?>" class="list-item">
                                    <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span class="name value"><?= __('Available to anyone with access to project and category'); ?></span>
                                </label>
                                <input type="radio" class="fancy-checkbox" id="issues_lock_type_checkbox_<?= Project::ISSUES_LOCK_TYPE_RESTRICTED; ?>" name="issues_lock_type" value="<?= Project::ISSUES_LOCK_TYPE_RESTRICTED; ?>" <?php if ($project->getIssuesLockType() === Project::ISSUES_LOCK_TYPE_RESTRICTED) echo 'checked'; ?>>
                                <label for="issues_lock_type_checkbox_<?= Project::ISSUES_LOCK_TYPE_RESTRICTED; ?>" class="list-item">
                                    <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span class="name value"><?= __("Available only to issue's poster"); ?></span>
                                </label>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php
                    switch ($project->getIssuesLockType()) {
                        case Project::ISSUES_LOCK_TYPE_PUBLIC_CATEGORY:
                            echo __('Available to anyone with access to project and category');
                            break;
                        case Project::ISSUES_LOCK_TYPE_PUBLIC:
                            echo __('Available to anyone with access to project');
                            break;
                        case Project::ISSUES_LOCK_TYPE_RESTRICTED:
                            echo __('Available only to issue\'s poster');
                            break;
                    }
                    ?>
                <?php endif; ?>
            </div>
            <?php \pachno\core\framework\Event::createNew('core', 'project/projectinfo', $project)->trigger(); ?>
            <?php if ($access_level == Settings::ACCESS_FULL): ?>
            <div class="form-row submit-container">
                <button type="submit" class="button primary">
                    <span><?php echo __('Save'); ?></span>
                    <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                </button>
            </div>
        </form>
    <?php endif; ?>
    </div>
    <div id="tab_project_workflow_pane" data-tab-id="workflow">
        <div class="form-row">
            <h3>
                <span><?= __('Workflow scheme'); ?></span>
                <button class="secondary" onclick="Pachno.UI.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_workflow', 'project_id' => $project->getId())); ?>');"><?= fa_image_tag('stream'); ?><span><?php echo __('Change workflow scheme'); ?></span></button>
            </h3>
            <div class="flexible-table">
                <?php include_component('configuration/workflowscheme', ['scheme' => $project->getWorkflowScheme(), 'embed' => true]); ?>
            </div>
        </div>
        <?php if ($access_level == Settings::ACCESS_FULL): ?>
        <form
            accept-charset="<?= Context::getI18n()->getCharset(); ?>"
            data-submit-project-settings
            data-project-id="<?= $project->getID(); ?>"
            action="<?= make_url('configure_project_settings', ['project_id' => $project->getID()]); ?>"
            method="post"
            id="project_issues_and_workflow"
            data-interactive-form
        >
        <?php endif; ?>
            <div class="form-row">
                <label for="strict_workflow_mode_yes"><?php echo __('Strict workflow mode'); ?></label>
                <div class="fancy-label-select">
                    <?php if ($access_level == Settings::ACCESS_FULL): ?>
                        <input type="radio" name="strict_workflow_mode" value="1" class="fancy-checkbox" id="strict_workflow_mode_yes"<?php if ($project->useStrictWorkflowMode()): ?> checked<?php endif; ?>>
                        <label for="strict_workflow_mode_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Yes'); ?></label>
                        <input type="radio" name="strict_workflow_mode" value="0" class="fancy-checkbox" id="strict_workflow_mode_no"<?php if (!$project->useStrictWorkflowMode()): ?> checked<?php endif; ?>>
                        <label for="strict_workflow_mode_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('No'); ?></label>
                    <?php else: ?>
                        <?php echo ($project->useStrictWorkflowMode()) ? __('Yes') : __('No'); ?>
                    <?php endif; ?>
                </div>
                <div class="helper-text"><?php echo __('Whether or not developers must use the configured workflow to change issue status. Choosing "%no" means issues can change status freely without following the configured workflow.', ['%no' => __('No')]); ?></div>
            </div>
            <?php if ($access_level == Settings::ACCESS_FULL): ?>
            <div class="form-row submit-container">
                <button type="submit" class="button primary">
                    <span><?php echo __('Save'); ?></span>
                    <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                </button>
            </div>
        </form>
    <?php endif; ?>
    </div>
</div>