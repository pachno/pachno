<?php /** @var \pachno\core\entities\User $pachno_user */ ?>
<?php /** @var \pachno\core\framework\Response $pachno_response */ ?>
<?php if ($issue instanceof \pachno\core\entities\Issue): ?>
    <?php

        $pachno_response->addBreadcrumb(__('Issues'), make_url('project_issues', ['project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey()]));
        $pachno_response->addBreadcrumb($issue->getFormattedIssueNo(true, true), make_url('viewissue', ['project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()]));
        $pachno_response->setTitle('['.(($issue->isClosed()) ? mb_strtoupper(__('Closed')) : mb_strtoupper(__('Open'))) .'] ' . $issue->getFormattedIssueNo(true) . ' - ' . \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()));

    ?>
    <?php \pachno\core\framework\Event::createNew('core', 'viewissue_top', $issue)->trigger(); ?>
    <div id="issuetype_indicator_fullpage" style="display: none;" class="fullpage_backdrop">
        <div style="position: absolute; top: 45%; left: 40%; z-index: 100001; color: #FFF; font-size: 15px; font-weight: bold;">
            <?php echo image_tag('spinning_32.gif'); ?><br>
            <?php echo __('Please wait while updating issue type'); ?>...
        </div>
    </div>
    <div class="content-with-sidebar">
        <?php include_component('project/sidebar', ['collapsed' => true]); ?>
        <div id="issue_<?php echo $issue->getID(); ?>" class="viewissue-container <?php if ($issue->isBlocking()) echo ' blocking'; ?>">
            <?php include_component('project/viewissueheader', ['issue' => $issue]); ?>
            <?php include_component('project/issuemessages', compact('issue', 'error', 'issue_unsaved', 'workflow_message', 'issue_message', 'issue_file_uploaded')); ?>
            <div id="issue-container" class="issue-card">
                <div id="issue-main-container" class="issue-card-main">
                    <div class="card-header">
                        <div id="issuetype-field" class="issuetype-field dropper-container">
                            <span id="issuetype_content" class="<?php if ($issue->isEditable() && $issue->canEditIssuetype()) echo 'dropper'; ?> issuetype-icon issuetype-<?= ($issue->hasIssueType()) ? $issue->getIssueType()->getIcon() : 'unknown'; ?>">
                                <?php if ($issue->hasIssueType()) echo fa_image_tag($issue->getIssueType()->getFontAwesomeIcon(), ['class' => 'icon']); ?>
                                <span class="name"><?= __($issue->getIssueType()->getName()); ?></span>
                            </span>
                            <?php if ($issue->isEditable() && $issue->canEditIssuetype()): ?>
                                <div id="issuetype_change" class="dropdown-container">
                                    <ul class="list-mode">
                                        <li class="header"><?php echo __('Change issue type'); ?></li>
                                        <?php foreach ($issuetypes as $issuetype): ?>
                                            <li class="list-item">
                                                <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'issuetype', 'issuetype_id' => $issuetype->getID())); ?>', 'issuetype');">
                                                    <?php echo fa_image_tag($issuetype->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $issuetype->getIcon()]); ?>
                                                    <span class="name"><?php echo __($issuetype->getName()); ?></span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php include_component('project/issueworkflowbuttons', ['issue' => $issue]); ?>
                    </div>
                    <?php \pachno\core\framework\Event::createNew('core', 'viewissue::afterWorkflowButtons', $issue)->trigger(); ?>
                    <div class="issue-details">
                        <fieldset id="description_field"<?php if (!$issue->isDescriptionVisible()): ?> style="display: none;"<?php endif; ?> class="viewissue_description<?php if ($issue->isDescriptionChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isDescriptionMerged()): ?> issue_detail_unmerged<?php endif; ?> hoverable">
                            <div class="header" id="description_header">
                                <span class="icon"><?= fa_image_tag('align-left'); ?></span>
                                <span class="name"><?php echo __('Description'); ?></span>
                            </div>
                            <div id="description_name" class="content">
                                <?php if ($issue->getDescription()): ?>
                                    <?php echo $issue->getParsedDescription(['issue' => $issue]); ?>
                                <?php endif; ?>
                            </div>
                            <?php if ($issue->isEditable() && $issue->canEditDescription()): ?>
                                <form class="viewissue-form" id="description_form" action="<?php echo make_url('viewissue', ['project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()]); ?>" method="post" id="description_change" style="display: none;" class="editor_container">
                                    <?php include_component('main/textarea', ['area_name' => 'value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => 'description_form_value', 'syntax' => \pachno\core\framework\Settings::getSyntaxClass($issue->getDescriptionSyntax()), 'height' => '250px', 'width' => '100%', 'value' => htmlentities($issue->getDescription(), ENT_COMPAT, \pachno\core\framework\Context::getI18n()->getCharset())]); ?>
                                    <div class="textarea_save_container">
                                        <?php echo __('%cancel or %save', ['%save' => '<input class="button" type="submit" value="'.__('Save').'">', '%cancel' => javascript_link_tag(__('Cancel'), ['onclick' => "$('description_edit').style.display = '';$('description_change').hide();".(($issue->getDescription() != '') ? "$('description_name').show();" : "$('no_description').show();")."return false;"])]); ?>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </fieldset>
                        <fieldset id="reproduction_steps_field"<?php if (!$issue->isReproductionStepsVisible()): ?> style="display: none;"<?php endif; ?> class="hoverable<?php if ($issue->isReproduction_StepsChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isReproduction_StepsMerged()): ?> issue_detail_unmerged<?php endif; ?>">
                            <div class="header" id="reproduction_steps_header">
                                <?php if (false && $issue->isEditable() && $issue->canEditReproductionSteps()): ?>
                                    <?php echo fa_image_tag('edit', ['class' => 'dropdown', 'id' => 'reproduction_steps_edit', 'onclick' => "$('reproduction_steps_change').show(); $('reproduction_steps_name').hide(); $('no_reproduction_steps').hide();", 'title' => __('Click here to edit reproduction steps')]); ?>
                                <?php endif; ?>
                                <span class="icon"><?= fa_image_tag('list-ol'); ?></span>
                                <span class="name"><?php echo __('How to reproduce'); ?></span>
                            </div>
                            <div id="reproduction_steps_content" class="content">
                                <div class="faded_out" id="no_reproduction_steps" <?php if ($issue->getReproductionSteps() != ''):?> style="display: none;" <?php endif; ?>><?php echo __('Nothing entered.'); ?></div>
                                <div id="reproduction_steps_name" class="issue_inline_description">
                                    <?php if ($issue->getReproductionSteps()): ?>
                                        <?php echo $issue->getParsedReproductionSteps(['issue' => $issue]); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($issue->isEditable() && $issue->canEditReproductionSteps()): ?>
                                <div id="reproduction_steps_change" style="display: none;" class="editor_container">
                                    <form class="viewissue-form" id="reproduction_steps_form" action="<?php echo make_url('viewissue', ['project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()]); ?>" method="post">
                                        <?php include_component('main/textarea', ['area_name' => 'value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => 'reproduction_steps_form_value', 'syntax' => \pachno\core\framework\Settings::getSyntaxClass($issue->getReproductionStepsSyntax()), 'height' => '250px', 'width' => '100%', 'value' => htmlentities($issue->getReproductionSteps(), ENT_COMPAT, \pachno\core\framework\Context::getI18n()->getCharset())]); ?>
                                        <div class="textarea_save_container">
                                            <?php echo __('%cancel or %save', ['%save' => '<input class="button" type="submit" value="'.__('Save').'">', '%cancel' => javascript_link_tag(__('Cancel'), ['onclick' => "$('reproduction_steps_change').hide();".(($issue->getReproductionSteps() != '') ? "$('reproduction_steps_name').show();" : "$('no_reproduction_steps').show();")."return false;"])]); ?>
                                        </div>
                                    </form>
                                    <?php echo image_tag('spinning_16.gif', ['style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'reproduction_steps_spinning']); ?>
                                    <div id="reproduction_steps_change_error" class="error_message" style="display: none;"></div>
                                </div>
                            <?php endif; ?>
                        </fieldset>
                        <?php include_component('main/issuemaincustomfields', ['issue' => $issue]); ?>
                        <?php /*
                        <fieldset class="todos" id="viewissue_todos_container">
                            <div id="viewissue_todos">
                                <?php include_component('main/todos', compact('issue')); ?>
                            </div>
                            <div id="todo_add" class="todo_add todo_editor" style="<?php if (!(isset($todo_error) && $todo_error)): ?>display: none; <?php endif; ?>margin-top: 5px;">
                                <div class="backdrop_detail_header">
                                    <span><?php echo __('Create a todo'); ?></span>
                                    <?= javascript_link_tag(fa_image_tag('times'), ['onclick' => "$('todo_add').hide();$('todo_add_button').show();", 'class' => 'closer']); ?>
                                </div>
                                <div class="todo_add_main">
                                    <form class="viewissue-form" id="todo_form" accept-charset="<?php echo mb_strtoupper(\pachno\core\framework\Context::getI18n()->getCharset()); ?>" action="<?php echo make_url('todo_add', ['project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()]); ?>" method="post" onSubmit="Pachno.Issues.addTodo('<?php echo make_url('todo_add', ['project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()]); ?>');return false;">
                                        <label for="todo_bodybox"><?php echo __('Todo'); ?></label><br />
                                        <?php include_component('main/textarea', ['area_name' => 'todo_body', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => 'todo_bodybox', 'height' => '250px', 'width' => '100%', 'syntax' => $pachno_user->getPreferredCommentsSyntax(true), 'value' => ((isset($todo_error) && $todo_error) ? $todo_error : '')]); ?>
                                        <input type="hidden" name="forward_url" value="<?php echo make_url('viewissue', ['project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()], false); ?>">
                                        <div id="todo_add_controls" class="backdrop_details_submit">
                                            <span class="explanation"></span>
                                            <div class="submit_container"><button type="submit" class="button"><?= image_tag('spinning_16.gif', ['id' => 'todo_add_indicator', 'style' => 'display: none;']) . __('Create todo'); ?></button></div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </fieldset>
                         */ ?>
                    </div>
                    <?php \pachno\core\framework\Event::createNew('core', 'viewissue::afterMainDetails', $issue)->trigger(); ?>
                    <div class="fancytabs" id="viewissue_activity">
                        <a id="tab_viewissue_comments" class="tab selected" href="javascript:void(0);" onclick="Pachno.Main.Helpers.tabSwitcher('tab_viewissue_comments', 'viewissue_activity');">
                            <?= fa_image_tag('comments', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Comments %count', ['%count' => '<span id="viewissue_comment_count" class="count-badge">' . $issue->countComments() . '</span>']); ?></span>
                        </a>
                        <?php \pachno\core\framework\Event::createNew('core', 'viewissue_before_tabs', $issue)->trigger(); ?>
                        <a id="tab_viewissue_history" class="tab" href="javascript:void(0);" onclick="Pachno.Main.Helpers.tabSwitcher('tab_viewissue_history', 'viewissue_activity');">
                            <?= fa_image_tag('history', ['class' => 'icon']); ?>
                            <span class="name"><?= __('History'); ?></span>
                        </a>
                    </div>
                    <div id="viewissue_activity_panes" class="fancypanes">
                        <div id="tab_viewissue_comments_pane">
                            <fieldset class="comments" id="viewissue_comments_container">
                                <div class="viewissue_comments_header">
                                    <div class="dropper_container">
                                        <?php echo fa_image_tag('spinner', ['class' => 'fa-spin', 'style' => 'display: none;', 'id' => 'comments_loading_indicator']); ?>
                                        <span class="dropper"><?= fa_image_tag('cog') . __('Options'); ?></span>
                                        <ul class="more_actions_dropdown dropdown_box popup_box rightie">
                                            <li><a href="javascript:void(0);" id="comments_show_system_comments_toggle" onclick="$$('#comments_box .system_comment').each(function (elm) { $(elm).toggle(); });"><?php echo __('Toggle system-generated comments'); ?></a></li>
                                            <li><a href="javascript:void(0);" onclick="Pachno.Main.Comment.toggleOrder('<?= \pachno\core\entities\Comment::TYPE_ISSUE; ?>', '<?= $issue->getID(); ?>');"><?php echo __('Sort comments in opposite direction'); ?></a></li>
                                        </ul>
                                    </div>
                                    <?php if ($pachno_user->canPostComments() && ((\pachno\core\framework\Context::isProjectContext() && !\pachno\core\framework\Context::getCurrentProject()->isArchived()) || !\pachno\core\framework\Context::isProjectContext())): ?>
                                        <ul class="simple-list button_container" id="add_comment_button_container">
                                            <li id="comment_add_button"><input class="button first last" type="button" onclick="Pachno.Main.Comment.showPost();" value="<?php echo __('Post comment'); ?>"></li>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                                <div id="viewissue_comments">
                                    <?php include_component('main/comments', ['target_id' => $issue->getID(), 'mentionable_target_type' => 'issue', 'target_type' => \pachno\core\entities\Comment::TYPE_ISSUE, 'show_button' => false, 'comment_count_div' => 'viewissue_comment_count', 'save_changes_checked' => $issue->hasUnsavedChanges(), 'issue' => $issue, 'forward_url' => make_url('viewissue', ['project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()], false)]); ?>
                                </div>
                            </fieldset>
                        </div>
                        <?php \pachno\core\framework\Event::createNew('core', 'viewissue_after_tabs', $issue)->trigger(); ?>
                        <div id="tab_viewissue_history_pane" style="display:none;">
                            <fieldset class="viewissue_history">
                                <div id="viewissue_log_items">
                                    <ul>
                                        <?php $previous_time = null; ?>
                                        <?php $include_user = true; ?>
                                        <?php foreach (array_reverse($issue->getLogEntries()) as $item): ?>
                                            <?php if (!$item instanceof \pachno\core\entities\LogItem) continue; ?>
                                            <?php include_component('main/issuelogitem', compact('item', 'previous_time', 'include_user')); ?>
                                            <?php $previous_time = $item->getTime(); ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <?php include_component('main/issuefields', ['issue' => $issue]); ?>
            </div>
        </div>
    </div>
    <?php include_component('main/issue_workflow_transition', compact('issue')); ?>
    <?php if ($pachno_user->isViewissueTutorialEnabled()): ?>
        <?php //include_component('main/tutorial_viewissue', compact('issue')); ?>
    <?php endif; ?>
<?php elseif (isset($issue_deleted)): ?>
    <div class="greenbox" id="issue_deleted_message">
        <div class="header"><?php echo __("This issue has been deleted"); ?></div>
        <div class="content"><?php echo __("This message will disappear when you reload the page."); ?></div>
    </div>
<?php else: ?>
    <div class="redbox" id="notfound_error">
        <div class="header"><?php echo __("This issue can not be displayed"); ?></div>
        <div class="content"><?php echo __("This issue either does not exist, has been deleted or you do not have permission to view it."); ?></div>
    </div>
<?php endif; ?>
