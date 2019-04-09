<?php /** @var \pachno\core\entities\User $pachno_user */ ?>
<?php /** @var \pachno\core\framework\Response $pachno_response */ ?>
<?php if ($issue instanceof \pachno\core\entities\Issue): ?>
    <?php

        $pachno_response->addBreadcrumb(__('Issues'), make_url('project_issues', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())));
        $pachno_response->addBreadcrumb($issue->getFormattedIssueNo(true, true), make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())));
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
        <?php include_component('project/sidebar'); ?>
        <div id="issue_<?php echo $issue->getID(); ?>" class="viewissue_container cf <?php if ($issue->isBlocking()) echo ' blocking'; ?>">
                <?php include_component('project/viewissueheader', ['issue' => $issue]); ?>
                <div id="issue_info_container">
                    <div class="issue_info error<?php if (isset($issue_unsaved)): ?> active<?php endif; ?>" id="viewissue_unsaved"<?php if (!isset($issue_unsaved)): ?> style="display: none;"<?php endif; ?>>
                        <div class="header"><?php echo __('Could not save your changes'); ?></div>
                    </div>
                    <div class="issue_info error<?php if ($issue->hasMergeErrors()): ?> active<?php endif; ?>" id="viewissue_merge_errors"<?php if (!$issue->hasMergeErrors()): ?> style="display: none;"<?php endif; ?>>
                        <div class="header"><?php echo __('This issue has been changed since you started editing it'); ?></div>
                        <div class="content"><?php echo __('Data that has been changed is highlighted in red below. Undo your changes to see the updated information'); ?></div>
                    </div>

                    <div class="issue_info important" id="viewissue_changed" <?php if (!$issue->hasUnsavedChanges()): ?>style="display: none;"<?php endif; ?>>
                        <form action="<?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>" method="post">
                            <div class="buttons">
                                <input class="button" type="submit" value="<?php echo __('Save changes'); ?>">
                                <button class="button" onclick="$('comment_add_button').hide(); $('comment_add').show();$('comment_save_changes').checked = true;$('comment_bodybox').focus();return false;"><?php echo __('Add comment and save changes'); ?></button>
                            </div>
                            <input type="hidden" name="issue_action" value="save">
                        </form>
                        <?php echo __("You have changed this issue, but haven't saved your changes yet. To save it, press the %save_changes button to the right", array('%save_changes' => '<b>' . __("Save changes") . '</b>')); ?>
                    </div>
                    <?php if (isset($error) && $error): ?>
                        <div class="issue_info error" id="viewissue_error">
                            <?php if ($error == 'transition_error'): ?>
                                <div class="header"><?php echo __('There was an error trying to move this issue to the next step in the workflow'); ?></div>
                                <div class="content" style="text-align: left;">
                                    <?php include_component('main/issue_transition_error'); ?>
                                </div>
                            <?php else: ?>
                                <div class="header"><?php echo __('There was an error trying to save changes to this issue'); ?></div>
                                <div class="content">
                                    <?php if (isset($workflow_error) && $workflow_error): ?>
                                        <?php echo __('No workflow step matches this issue after changes are saved. Please either use the workflow action buttons, or make sure your changes are valid within the current project workflow for this issue type.'); ?>
                                    <?php else: ?>
                                        <?php echo $error; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($issue_saved)): ?>
                        <div class="issue_info successful" id="viewissue_saved">
                            <?php echo __('Your changes have been saved'); ?>
                            <div class="buttons">
                                <button class="button" onclick="$('viewissue_saved').hide();"><?php echo __('OK'); ?></button>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($issue_message)): ?>
                        <div class="issue_info successful" id="viewissue_saved">
                            <?php echo $issue_message; ?>
                            <div class="buttons">
                                <button class="button" onclick="$('viewissue_saved').hide();"><?php echo __('OK'); ?></button>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($issue_file_uploaded)): ?>
                        <div class="issue_info successful" id="viewissue_saved">
                            <?php echo __('The file was attached to this issue'); ?>
                            <div class="buttons">
                                <button class="button" onclick="$('viewissue_saved').hide();"><?php echo __('OK'); ?></button>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($issue->isBeingWorkedOn() && $issue->isOpen()): ?>
                        <div class="issue_info information" id="viewissue_being_worked_on">
                            <?php if ($issue->getUserWorkingOnIssue()->getID() == $pachno_user->getID()): ?>
                                <?php echo __('You have been working on this issue since %time', array('%time' => \pachno\core\framework\Context::getI18n()->formatTime($issue->getWorkedOnSince(), 6))); ?>
                            <?php elseif ($issue->getAssignee() instanceof \pachno\core\entities\Team): ?>
                                <?php echo __('%teamname has been working on this issue since %time', array('%teamname' => $issue->getAssignee()->getName(), '%time' => \pachno\core\framework\Context::getI18n()->formatTime($issue->getWorkedOnSince(), 6))); ?>
                            <?php else: ?>
                                <?php echo __('%user has been working on this issue since %time', array('%user' => $issue->getUserWorkingOnIssue()->getNameWithUsername(), '%time' => \pachno\core\framework\Context::getI18n()->formatTime($issue->getWorkedOnSince(), 6))); ?>
                            <?php endif; ?>
                            <div class="buttons">
                                <button class="button" onclick="$('viewissue_being_worked_on').hide();"><?php echo __('OK'); ?></button>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="issue_info error" id="blocking_div"<?php if (!$issue->isBlocking()): ?> style="display: none;"<?php endif; ?>>
                        <?php echo __('This issue is blocking the next release'); ?>
                    </div>
                    <?php if ($issue->isDuplicate()): ?>
                        <div class="issue_info information" id="viewissue_duplicate">
                            <?php echo fa_image_tag('info-circle'); ?>
                            <?php echo __('This issue is a duplicate of issue %link_to_duplicate_issue', array('%link_to_duplicate_issue' => link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getDuplicateOf()->getFormattedIssueNo())), $issue->getDuplicateOf()->getFormattedIssueNo(true)) . ' - "' . $issue->getDuplicateOf()->getTitle() . '"')); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($issue->isClosed()): ?>
                        <div class="issue_info information" id="viewissue_closed">
                            <?php echo fa_image_tag('info-circle'); ?>
                            <?php echo __('This issue has been closed with status "%status_name" and resolution "%resolution".', array('%status_name' => (($issue->getStatus() instanceof \pachno\core\entities\Status) ? $issue->getStatus()->getName() : __('Not determined')), '%resolution' => (($issue->getResolution() instanceof \pachno\core\entities\Resolution) ? $issue->getResolution()->getName() : __('Not determined')))); ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($issue->getProject()->isArchived()): ?>
                        <div class="issue_info important" id="viewissue_archived">
                            <?php echo image_tag('icon_important.png', array('style' => 'float: left; margin: 0 5px 0 5px;')); ?>
                            <?php echo __('The project this issue belongs to has been archived, and so this issue is now read only'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div id="viewissue_left_box_top">
              <div id="workflow_actions">
                <ul class="workflow_actions simple_list">
                  <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
                    <?php $cc = 1; $num_transitions = count($issue->getAvailableWorkflowTransitions()); ?>
                    <?php foreach ($issue->getAvailableWorkflowTransitions() as $transition): ?>
                      <li class="workflow">
                        <div class="tooltip from-above rightie">
                          <?php echo $transition->getDescription(); ?>
                        </div>
                        <?php if ($transition->hasTemplate()): ?>
                          <input class="button<?php if ($cc == 1): ?> first<?php endif; if ($cc == $num_transitions): ?> last<?php endif; ?>" type="button" value="<?php echo $transition->getName(); ?>" onclick="Pachno.Issues.showWorkflowTransition(<?php echo $transition->getID(); ?>);">
                        <?php else: ?>
                          <form action="<?php echo make_url('transition_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'transition_id' => $transition->getID())); ?>" method="post">
                            <input type="submit" class="button<?php if ($cc == 1): ?> first<?php endif; if ($cc == $num_transitions): ?> last<?php endif; ?>" value="<?php echo $transition->getName(); ?>">
                          </form>
                        <?php endif; ?>
                      </li>
                      <?php $cc++; ?>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  <li class="more_actions">
                    <input class="dropper button first last" id="more_actions_<?php echo $issue->getID(); ?>_button" type="button" value="<?php echo ($issue->isWorkflowTransitionsAvailable()) ? __('More actions') : __('Actions'); ?>">
                    <?php include_component('main/issuemoreactions', array('issue' => $issue, 'times' => false)); ?>
                  </li>
                </ul>
              </div>
                <div id="issue_view">
                    <div id="issue_details_container">
                        <div id="issue_details">
                            <div class="collapser_link" onclick="$('issue_details_container').toggleClassName('collapsed');$('issue_main_container').toggleClassName('uncollapsed');">
                                <a href="javascript:void(0);" class="image">
                                    <?php echo fa_image_tag('arrow-left', ['class' => 'collapser']); ?>
                                    <?php echo fa_image_tag('arrow-right', ['class' => 'expander']); ?>
                                </a>
                            </div>
                            <div class="issue_details_fieldsets_wrapper"><?php include_component('main/issuedetailslisteditable', array('issue' => $issue)); ?></div>
                            <div style="clear: both; margin-bottom: 5px;"> </div>
                        </div>
                    </div>
                    <div id="issue_main_container">
                        <div class="issue_main" id="issue_main">
                            <?php \pachno\core\framework\Event::createNew('core', 'viewissue_right_top', $issue)->trigger(); ?>
                            <fieldset id="description_field"<?php if (!$issue->isDescriptionVisible()): ?> style="display: none;"<?php endif; ?> class="viewissue_description<?php if ($issue->isDescriptionChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isDescriptionMerged()): ?> issue_detail_unmerged<?php endif; ?> hoverable">
                                <legend id="description_header">
                                    <?php if ($issue->isEditable() && $issue->canEditDescription()): ?>
                                        <a href="javascript:void(0);" onclick="Pachno.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'description')); ?>', 'description');" title="<?php echo __('Undo this change'); ?>"><?php echo fa_image_tag('undo-alt', ['class' => 'undo'], 'fas'); ?></a> <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'description_undo_spinning')); ?>
                                        <?php echo fa_image_tag('edit', array('class' => 'dropdown', 'id' => 'description_edit', 'onclick' => "$('description_edit').show('inline'); $('description_change').show(); $('description_name').hide(); $('no_description').hide();", 'title' => __('Click here to edit description'))); ?>
                                    <?php endif; ?>
                                    <?php echo __('Description'); ?>
                                </legend>
                                <div id="description_content">
                                    <div class="faded_out" id="no_description" <?php if ($issue->getDescription() != ''):?> style="display: none;" <?php endif; ?>><?php echo __('Nothing entered.'); ?></div>
                                    <div id="description_name" class="issue_inline_description">
                                        <?php if ($issue->getDescription()): ?>
                                            <?php echo $issue->getParsedDescription(array('issue' => $issue)); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($issue->isEditable() && $issue->canEditDescription()): ?>
                                    <div id="description_change" style="display: none;" class="editor_container">
                                        <form id="description_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'description')); ?>" method="post" onSubmit="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'description')) ?>', 'description'); return false;">
                                            <?php include_component('main/textarea', array('area_name' => 'value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => 'description_form_value', 'syntax' => \pachno\core\framework\Settings::getSyntaxClass($issue->getDescriptionSyntax()), 'height' => '250px', 'width' => '100%', 'value' => htmlentities($issue->getDescription(), ENT_COMPAT, \pachno\core\framework\Context::getI18n()->getCharset()))); ?>
                                            <div class="textarea_save_container">
                                                <?php echo __('%cancel or %save', array('%save' => '<input class="button" type="submit" value="'.__('Save').'">', '%cancel' => javascript_link_tag(__('Cancel'), array('onclick' => "$('description_edit').style.display = '';$('description_change').hide();".(($issue->getDescription() != '') ? "$('description_name').show();" : "$('no_description').show();")."return false;")))); ?>
                                            </div>
                                        </form>
                                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'description_spinning')); ?>
                                        <div id="description_change_error" class="error_message" style="display: none;"></div>
                                    </div>
                                <?php endif; ?>
                            </fieldset>
                            <fieldset id="reproduction_steps_field"<?php if (!$issue->isReproductionStepsVisible()): ?> style="display: none;"<?php endif; ?> class="hoverable<?php if ($issue->isReproduction_StepsChanged()): ?> issue_detail_changed<?php endif; ?><?php if (!$issue->isReproduction_StepsMerged()): ?> issue_detail_unmerged<?php endif; ?>">
                                <legend id="reproduction_steps_header">
                                    <?php if ($issue->isEditable() && $issue->canEditReproductionSteps()): ?>
                                        <a href="javascript:void(0);" onclick="Pachno.Issues.Field.revert('<?php echo make_url('issue_revertfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'reproduction_steps')); ?>', 'reproduction_steps');" title="<?php echo __('Undo this change'); ?>"><?php echo fa_image_tag('undo-alt', ['class' => 'undo'], 'fas'); ?></a> <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'reproduction_steps_undo_spinning')); ?>
                                        <?php echo fa_image_tag('edit', array('class' => 'dropdown', 'id' => 'reproduction_steps_edit', 'onclick' => "$('reproduction_steps_change').show(); $('reproduction_steps_name').hide(); $('no_reproduction_steps').hide();", 'title' => __('Click here to edit reproduction steps'))); ?>
                                    <?php endif; ?>
                                    <?php echo __('Steps to reproduce this issue'); ?>
                                </legend>
                                <div id="reproduction_steps_content">
                                    <div class="faded_out" id="no_reproduction_steps" <?php if ($issue->getReproductionSteps() != ''):?> style="display: none;" <?php endif; ?>><?php echo __('Nothing entered.'); ?></div>
                                    <div id="reproduction_steps_name" class="issue_inline_description">
                                        <?php if ($issue->getReproductionSteps()): ?>
                                            <?php echo $issue->getParsedReproductionSteps(array('issue' => $issue)); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($issue->isEditable() && $issue->canEditReproductionSteps()): ?>
                                    <div id="reproduction_steps_change" style="display: none;" class="editor_container">
                                        <form id="reproduction_steps_form" action="<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'reproduction_steps')); ?>" method="post" onSubmit="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'reproduction_steps')) ?>', 'reproduction_steps'); return false;">
                                            <?php include_component('main/textarea', array('area_name' => 'value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => 'reproduction_steps_form_value', 'syntax' => \pachno\core\framework\Settings::getSyntaxClass($issue->getReproductionStepsSyntax()), 'height' => '250px', 'width' => '100%', 'value' => htmlentities($issue->getReproductionSteps(), ENT_COMPAT, \pachno\core\framework\Context::getI18n()->getCharset()))); ?>
                                            <div class="textarea_save_container">
                                                <?php echo __('%cancel or %save', array('%save' => '<input class="button" type="submit" value="'.__('Save').'">', '%cancel' => javascript_link_tag(__('Cancel'), array('onclick' => "$('reproduction_steps_change').hide();".(($issue->getReproductionSteps() != '') ? "$('reproduction_steps_name').show();" : "$('no_reproduction_steps').show();")."return false;")))); ?>
                                            </div>
                                        </form>
                                        <?php echo image_tag('spinning_16.gif', array('style' => 'display: none; float: left; margin-right: 5px;', 'id' => 'reproduction_steps_spinning')); ?>
                                        <div id="reproduction_steps_change_error" class="error_message" style="display: none;"></div>
                                    </div>
                                <?php endif; ?>
                            </fieldset>
                            <?php include_component('main/issuemaincustomfields', array('issue' => $issue)); ?>
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
                                        <form id="todo_form" accept-charset="<?php echo mb_strtoupper(\pachno\core\framework\Context::getI18n()->getCharset()); ?>" action="<?php echo make_url('todo_add', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>" method="post" onSubmit="Pachno.Issues.addTodo('<?php echo make_url('todo_add', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?>');return false;">
                                            <label for="todo_bodybox"><?php echo __('Todo'); ?></label><br />
                                            <?php include_component('main/textarea', array('area_name' => 'todo_body', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => 'todo_bodybox', 'height' => '250px', 'width' => '100%', 'syntax' => $pachno_user->getPreferredCommentsSyntax(true), 'value' => ((isset($todo_error) && $todo_error) ? $todo_error : ''))); ?>
                                            <input type="hidden" name="forward_url" value="<?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false); ?>">
                                            <div id="todo_add_controls" class="backdrop_details_submit">
                                                <span class="explanation"></span>
                                                <div class="submit_container"><button type="submit" class="button"><?= image_tag('spinning_16.gif', ['id' => 'todo_add_indicator', 'style' => 'display: none;']) . __('Create todo'); ?></button></div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </fieldset>
                            <?php \pachno\core\framework\Event::createNew('core', 'viewissue_right_bottom', $issue)->trigger(); ?>
                            <div class="fancytabs" id="viewissue_activity">
                                <a id="tab_viewissue_comments" class="tab selected" href="javascript:void(0);" onclick="Pachno.Main.Helpers.tabSwitcher('tab_viewissue_comments', 'viewissue_activity');">
                                    <?= fa_image_tag('comments', ['class' => 'icon']); ?>
                                    <span class="name"><?= __('Comments %count', array('%count' => '<span id="viewissue_comment_count" class="count-badge">' . $issue->countComments() . '</span>')); ?></span>
                                </a>
                                <?php \pachno\core\framework\Event::createNew('core', 'viewissue_before_tabs', $issue)->trigger(); ?>
                                <a id="tab_viewissue_history" class="tab" href="javascript:void(0);" onclick="Pachno.Main.Helpers.tabSwitcher('tab_viewissue_history', 'viewissue_activity');">
                                    <?= fa_image_tag('history', ['class' => 'icon']); ?>
                                    <span class="name"><?= __('History'); ?></span>
                                </a>
                            </div>
                            <div id="viewissue_activity_panes">
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
                                                <ul class="simple_list button_container" id="add_comment_button_container">
                                                    <li id="comment_add_button"><input class="button first last" type="button" onclick="Pachno.Main.Comment.showPost();" value="<?php echo __('Post comment'); ?>"></li>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                        <div id="viewissue_comments">
                                            <?php include_component('main/comments', array('target_id' => $issue->getID(), 'mentionable_target_type' => 'issue', 'target_type' => \pachno\core\entities\Comment::TYPE_ISSUE, 'show_button' => false, 'comment_count_div' => 'viewissue_comment_count', 'save_changes_checked' => $issue->hasUnsavedChanges(), 'issue' => $issue, 'forward_url' => make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo()), false))); ?>
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
                </div>
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
