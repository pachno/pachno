<?php

    /**
     * @var \pachno\core\entities\Issue $issue
     * @var \pachno\core\entities\User $pachno_user
     */

?>
<div id="issue_<?php echo $issue->getID(); ?>" class="viewissue-container <?php if ($issue->isBlocking()) echo ' blocking'; ?>">
    <?php include_component('project/viewissueheader', ['issue' => $issue]); ?>
    <?php include_component('project/viewissuemessages', compact('issue')); ?>
    <div id="issue-container" class="issue-card">
        <div id="issue-main-container" class="issue-card-main">
            <div class="card-header">
                <?php include_component('project/issuefieldissuetype', ['issue' => $issue]); ?>
                <?php include_component('project/viewissueworkflowbuttons', ['issue' => $issue]); ?>
                <div class="dropper-container">
                    <button class="dropper button secondary icon" id="more_actions_<?php echo $issue->getID(); ?>_button"><?= fa_image_tag('ellipsis-v'); ?></button>
                    <?php include_component('main/issuemoreactions', array('issue' => $issue, 'times' => false, 'show_workflow_transitions' => false)); ?>
                </div>
            </div>
            <?php \pachno\core\framework\Event::createNew('core', 'viewissue::afterWorkflowButtons', $issue)->trigger(); ?>
            <div class="issue-details">
                <div id="description_field"<?php if (!$issue->isDescriptionVisible()): ?> style="display: none;"<?php endif; ?> class="fields-list-container viewissue_description">
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
                                <?php echo __('%cancel or %save', ['%save' => '<input class="button" type="submit" value="'.__('Save').'">', '%cancel' => javascript_link_tag(__('Cancel'), ['onclick' => "$('#description_edit').style.display = '';$('#description_change').hide();".(($issue->getDescription() != '') ? "$('#description_name').show();" : "$('#no_description').show();")."return false;"])]); ?>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
                <div id="reproduction_steps_field"<?php if (!$issue->isReproductionStepsVisible()): ?> style="display: none;"<?php endif; ?> class="fields-list-container">
                    <div class="header" id="reproduction_steps_header">
                        <?php if (false && $issue->isEditable() && $issue->canEditReproductionSteps()): ?>
                            <?php echo fa_image_tag('edit', ['class' => 'dropdown', 'id' => 'reproduction_steps_edit', 'onclick' => "$('#reproduction_steps_change').show(); $('#reproduction_steps_name').hide(); $('#no_reproduction_steps').hide();", 'title' => __('Click here to edit reproduction steps')]); ?>
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
                                    <?php echo __('%cancel or %save', ['%save' => '<input class="button" type="submit" value="'.__('Save').'">', '%cancel' => javascript_link_tag(__('Cancel'), ['onclick' => "$('#reproduction_steps_change').hide();".(($issue->getReproductionSteps() != '') ? "$('#reproduction_steps_name').show();" : "$('#no_reproduction_steps').show();")."return false;"])]); ?>
                                </div>
                            </form>
                            <?php echo image_tag('spinning_16.gif', ['style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'reproduction_steps_spinning']); ?>
                            <div id="reproduction_steps_change_error" class="error_message" style="display: none;"></div>
                        </div>
                    <?php endif; ?>
                </div>
                <div id="viewissue_attached_information_container" class="fields-list-container">
                    <div class="header">
                        <span class="icon"><?= fa_image_tag('paperclip'); ?></span>
                        <span class="name"><?php echo __('Attachments'); ?><span id="viewissue_uploaded_attachments_count" class="count-badge"><?= (count($issue->getLinks()) + count($issue->getFiles())); ?></span></span>
                    </div>
                    <div id="viewissue_attached_information" class="attachments-list content">
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
                </div>
                <div id="viewissue_related_information_container" class="fields-list-container <?php if (!$issue->countChildIssues()) echo 'not-visible'; ?>">
                    <div class="header">
                        <span class="icon"><?= fa_image_tag('list-alt', [], 'far'); ?></span>
                        <span class="name"><?= __('Child issues'); ?><span id="viewissue_related_issues_count" class="count-badge"><?= $issue->countChildIssues(); ?></span></span>
                    </div>
                    <div id="viewissue_related_information" class="related-issues content">
                        <?php include_component('main/relatedissues', array('issue' => $issue)); ?>
                    </div>
                </div>
                <?php include_component('main/issuemaincustomfields', ['issue' => $issue]); ?>
                <?php /*
                        <fieldset class="todos" id="viewissue_todos_container">
                            <div id="viewissue_todos">
                                <?php include_component('main/todos', compact('issue')); ?>
                            </div>
                            <div id="todo_add" class="todo_add todo_editor" style="<?php if (!(isset($todo_error) && $todo_error)): ?>display: none; <?php endif; ?>margin-top: 5px;">
                                <div class="backdrop_detail_header">
                                    <span><?php echo __('Create a todo'); ?></span>
                                    <?= javascript_link_tag(fa_image_tag('times'), ['onclick' => "$('#todo_add').hide();$('#todo_add_button').show();", 'class' => 'closer']); ?>
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
            <?php /*
                    <div class="fancy-tabs" id="viewissue_activity">
                        <?php \pachno\core\framework\Event::createNew('core', 'viewissue_before_tabs', $issue)->trigger(); ?>
                        <a id="tab_viewissue_history" class="tab" href="javascript:void(0);" onclick="Pachno.UI.tabSwitcher('tab_viewissue_history', 'viewissue_activity');">
                            <?= fa_image_tag('history', ['class' => 'icon']); ?>
                            <span class="name"><?= __('History'); ?></span>
                        </a>
                    </div>
                    <div id="viewissue_activity_panes" class="fancypanes">
                        <?php \pachno\core\framework\Event::createNew('core', 'viewissue_after_tabs', $issue)->trigger(); ?>
                        <div id="tab_viewissue_history_pane" style="display:none;">
                            <div class="viewissue_history">
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
                            </div>
                        </div>
                    </div> */ ?>
        </div>
        <div class="issue-fields">
            <?php include_component('main/viewissuefields', ['issue' => $issue]); ?>
        </div>
    </div>
    <div class="comments" id="viewissue_comments_container">
        <div class="comments-header-strip">
            <div class="dropper-container">
                <button class="dropper secondary icon">
                    <?php echo fa_image_tag('spinner', ['class' => 'fa-spin', 'style' => 'display: none;', 'id' => 'comments_loading_indicator']); ?>
                    <?= fa_image_tag('cog'); ?>
                </button>
                <div class="dropdown-container">
                    <div class="list-mode">
                        <a href="javascript:void(0);" class="list-item" id="comments_show_system_comments_toggle" onclick="$$('#comments_box .system_comment').each(function (elm) { $(elm).toggle(); });">
                            <span class="icon"><?= fa_image_tag('comment-slash'); ?></span>
                            <span class="name"><?php echo __('Toggle system-generated comments'); ?></span>
                        </a>
                        <a href="javascript:void(0);" class="list-item" onclick="Pachno.Main.Comment.toggleOrder('<?= \pachno\core\entities\Comment::TYPE_ISSUE; ?>', '<?= $issue->getID(); ?>');">
                            <span class="icon"><?= fa_image_tag('arrows-alt-v'); ?></span>
                            <span class="name"><?php echo __('Sort comments in opposite direction'); ?></span>
                        </a>
                    </div>
                </div>
            </div>
            <?php if ($pachno_user->canPostComments() && ((\pachno\core\framework\Context::isProjectContext() && !\pachno\core\framework\Context::getCurrentProject()->isArchived()) || !\pachno\core\framework\Context::isProjectContext())): ?>
                <button class="button primary" id="comment_add_button" onclick="Pachno.Main.Comment.showPost();"><?php echo __('Post comment'); ?></button>
            <?php endif; ?>
        </div>
        <div id="viewissue_comments">
            <?php include_component('main/comments', ['target_id' => $issue->getID(), 'mentionable_target_type' => 'issue', 'target_type' => \pachno\core\entities\Comment::TYPE_ISSUE, 'show_button' => false, 'comment_count_div' => 'viewissue_comment_count', 'save_changes_checked' => false, 'issue' => $issue]); ?>
        </div>
    </div>
</div>