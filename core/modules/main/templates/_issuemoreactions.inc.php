<?php if (isset($dynamic) && $dynamic == true): ?>
    <?php $moreactions_url = array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID()); ?>
    <?php if (isset($board)) $moreactions_url['board_id'] = $board->getID(); ?>
    <?php if (isset($estimator_mode)) $moreactions_url['estimator_mode'] = $estimator_mode; ?>
    <div class="dropdown-container dynamic_menu" data-menu-url="<?php echo make_url('issue_moreactions', $moreactions_url); ?>">
        <div class="list-mode">
            <div class="list-item disabled">
                <span class="icon"><?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?></span>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="dropdown-container">
        <div class="list-mode">
            <?php if (!$issue->getProject()->isArchived() && $issue->canEditIssueDetails()): ?>
                <?php if (!isset($multi) || !$multi): ?>
                    <div class="header"><?php echo __('Workflow transition actions'); ?></div>
                    <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
                        <?php foreach ($issue->getAvailableWorkflowTransitions() as $transition): ?>
                            <?php if ($transition->hasTemplate()): ?>
                                <?php echo javascript_link_tag('<span class="name">'.$transition->getName().'</span>', array('class' => 'list-item', 'onclick' => "Pachno.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'workflow_transition', 'transition_id' => $transition->getID()))."&project_key=".$issue->getProject()->getKey()."&issue_id=".$issue->getID()."');")); ?>
                            <?php else: ?>
                                <?php echo javascript_link_tag(fa_image_tag('spinner', array('style' => 'display: none;', 'id' => 'transition_working_'.$transition->getID().'_indicator', 'class' => 'fa-spin icon')).'<span class="name">'.$transition->getName().'</span>', array('class' => 'list-item', 'onclick' => "Pachno.Search.interactiveWorkflowTransition('".make_url('transition_issues', array('project_key' => $issue->getProject()->getKey(), 'transition_id' => $transition->getID()))."&issue_ids[]=".$issue->getID()."', ".$transition->getID().");")); ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (!isset($multi) || !$multi): ?>
                    <div class="header"><?php echo __('Additional actions available'); ?></div>
                <?php endif; ?>
                <?php if ($issue->canEditMilestone()): ?>
                    <?php if ($issue->isOpen()): ?>
                        <a class="list-item" onclick="Pachno.Issues.toggleBlocking('<?= make_url('unblock', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getId())); ?>', '<?= $issue->getID(); ?>');" id="more_actions_mark_notblocking_link_<?php echo $issue->getID(); ?>"<?php if (!$issue->isBlocking()): ?> style="display: none;"<?php endif; ?>>
                            <?= fa_image_tag('certificate', ['class' => ['mark_not_blocking icon']]); ?>
                            <span class="name"><?php echo __("Mark as not blocking the next release"); ?></span>
                        </a>
                        <a class="list-item" onclick="Pachno.Issues.toggleBlocking('<?= make_url('unblock', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getId())); ?>', '<?= $issue->getID(); ?>');" id="more_actions_mark_blocking_link_<?php echo $issue->getID(); ?>"<?php if ($issue->isBlocking()): ?> style="display: none;"<?php endif; ?>>
                            <?= fa_image_tag('certificate', ['class' => ['mark_blocking icon']]); ?>
                            <span class="name"><?php echo __("Mark as blocking the next release"); ?></span>
                        </a>
                    <?php else: ?>
                        <div class="list-item disabled" id="more_actions_mark_notblocking_link_<?php echo $issue->getID(); ?>"<?php if (!$issue->isBlocking()): ?> style="display: none;"<?php endif; ?>>
                            <?= fa_image_tag('certificate', ['class' => ['mark_not_blocking icon']]); ?>
                            <span class="name"><?= __("Mark as not blocking the next release"); ?></span>
                            <span class="tooltip rightie"><?php echo __('This action is not available when this issue is closed'); ?></span>
                        </div>
                        <div class="list-item disabled" id="more_actions_mark_blocking_link_<?php echo $issue->getID(); ?>"<?php if ($issue->isBlocking()): ?> style="display: none;"<?php endif; ?>>
                            <?= fa_image_tag('certificate', ['class' => ['mark_blocking icon']]); ?>
                            <span class="name"><?= __("Mark as blocking the next release"); ?></span>
                            <span class="tooltip rightie"><?php echo __('This action is not available when this issue is closed'); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="list-item separator"></div>
                <?php endif; ?>
                <?php if ((!isset($multi) || !$multi) && $issue->isUpdateable() && $issue->canAttachLinks()): ?>
                    <?php if ($issue->canAttachLinks()): ?>
                        <a href="javascript:void(0);" class="list-item" id="attach_link_button" onclick="Pachno.Main.Profile.clearPopupsAndButtons();Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'attachlink', 'issue_id' => $issue->getID())); ?>');">
                            <?php echo fa_image_tag('link', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Attach a link'); ?></span>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($issue->isUpdateable() && \pachno\core\framework\Settings::isUploadsEnabled() && $issue->canAttachFiles()): ?>
                    <?php if (\pachno\core\framework\Settings::isUploadsEnabled() && $issue->canAttachFiles()): ?>
                        <a href="javascript:void(0);" class="list-item" id="attach_file_button" onclick="Pachno.Main.Profile.clearPopupsAndButtons();Pachno.Main.showUploader('<?php echo make_url('get_partial_for_backdrop', array('key' => 'uploader', 'mode' => 'issue', 'issue_id' => $issue->getID())); ?>');">
                            <?php echo fa_image_tag('paperclip', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Attach a file'); ?></span>
                        </a>
                    <?php else: ?>
                        <a href="javascript:void(0);" class="list-item" id="attach_file_button" onclick="Pachno.Main.Helpers.Message.error('<?php echo __('File uploads are not enabled'); ?>', '<?php echo __('Before you can upload attachments, file uploads needs to be activated'); ?>');">
                            <?php echo fa_image_tag('paperclip', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Attach a file'); ?></span>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($issue->isUpdateable()): ?>
                    <?php if ($issue->canEditAffectedComponents() || $issue->canEditAffectedBuilds() || $issue->canEditAffectedEditions()): ?>
                        <a id="affected_add_button" class="list-item" href="javascript:void(0);" onclick="Pachno.Main.Profile.clearPopupsAndButtons();Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_add_item', 'issue_id' => $issue->getID())); ?>');">
                            <?php echo fa_image_tag('cubes', ['class' => 'affected_items icon']); ?>
                            <span class="name"><?= __('Add affected item'); ?></span>
                        </a>
                    <?php else: ?>
                        <div class="list-item disabled">
                            <a id="affected_add_button" href="javascript:void(0);" onclick="Pachno.Main.Helpers.Message.error('<?php echo __('You are not allowed to add an item to this list'); ?>');">
                                <?php echo fa_image_tag('cubes', ['class' => 'affected_items icon']); ?>
                                <span class="name"><?= __('Add affected item'); ?></span>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php elseif ($issue->canEditAffectedComponents() || $issue->canEditAffectedBuilds() || $issue->canEditAffectedEditions()): ?>
                    <div class="list-item disabled">
                        <a href="javascript:void(0);">
                            <?php echo fa_image_tag('cubes', ['class' => 'affected_items']); ?>
                            <span class="name"><?= __("Add affected item"); ?></span>
                        </a>
                        <span class="tooltip rightie"><?php echo __('This action is not available when this issue is closed'); ?></span>
                    </div>
                <?php endif; ?>
                <div class="list-item separator"></div>
                <?php if ($issue->isUpdateable()): ?>
                    <?php if ($issue->canAddRelatedIssues() && $pachno_user->canReportIssues($issue->getProject())): ?>
                        <?php if (isset($board)): ?>
                            <?php if (!$board->getTaskIssuetypeID()): ?>
                                <?php echo javascript_link_tag(fa_image_tag('list-alt', ['class' => 'icon']).'<span class="name">'.__('Create a new child issue').'</span>', array('class' => 'list-item', 'onclick' => "Pachno.Main.Profile.clearPopupsAndButtons();Pachno.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $issue->getProject()->getId(), 'parent_issue_id' => $issue->getID()))."');", 'title' => __('Create a new child issue'))); ?>
                            <?php elseif ($issue->getIssuetype()->getID() != $board->getTaskIssuetypeID()): ?>
                                <?php echo javascript_link_tag(fa_image_tag('list-alt', ['class' => 'icon']).'<span class="name">'.__('Add a new task').'</span>', array('class' => 'list-item', 'onclick' => "Pachno.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $issue->getProject()->getId(), 'parent_issue_id' => $issue->getID(), 'issuetype_id' => $board->getTaskIssuetypeID(), 'lock_issuetype' => 1))."');", 'title' => __('Add a new task'))); ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php echo javascript_link_tag(fa_image_tag('list-alt', ['class' => 'icon']).'<span class="name">'.__('Create a new related issue').'</span>', array('class' => 'list-item', 'onclick' => "Pachno.Main.Profile.clearPopupsAndButtons();Pachno.Main.Helpers.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $issue->getProject()->getId(), 'parent_issue_id' => $issue->getID()))."');", 'title' => __('Create a new child issue'))); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($issue->canAddRelatedIssues()): ?>
                        <a href="javascript:void(0)" class="list-item" id="relate_to_existing_issue_button" onclick="Pachno.Main.Profile.clearPopupsAndButtons();Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'relate_issue', 'issue_id' => $issue->getID())); ?>');">
                            <?php echo fa_image_tag('share-alt', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Add a relation for this issue'); ?></span>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if ($issue->canAddRelatedIssues() && $pachno_user->canReportIssues($issue->getProject())): ?>
                        <div class="list-item disabled">
                            <a href="javascript:void(0);"><?php echo fa_image_tag('list-alt').__("Create a new related issue"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available at this stage in the workflow'); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if ($issue->canAddRelatedIssues()): ?>
                        <div class="list-item disabled"><a href="javascript:void(0);"><?php echo fa_image_tag('sign-in-alt').__("Relate to an existing issue"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available at this stage in the workflow'); ?></div></div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (!isset($times) || $times): ?>
                    <div class="list-item separator"></div>
                    <?php if ($issue->canEditEstimatedTime()): ?>
                        <?php if ($issue->isUpdateable()): ?>
                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Main.Profile.clearPopupsAndButtons();$('estimated_time_<?php echo $issue->getID(); ?>_change').toggle('block');" title="<?php echo ($issue->hasEstimatedTime()) ? __('Change estimate') : __('Estimate this issue'); ?>"><?php echo fa_image_tag('clock').(($issue->hasEstimatedTime()) ? __('Change estimate') : __('Estimate this issue')); ?></a>
                        <?php else: ?>
                            <div class="list-item disabled"><a href="javascript:void(0);"><?php echo fa_image_tag('clock').__("Change estimate"); ?></a><div class="tooltip rightie"><?php echo __('This action is not available at this stage in the workflow'); ?></div></div>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($issue->canEditSpentTime()): ?>
                    <a href="javascript:void(0)" class="list-item" onclick="Pachno.Main.Profile.clearPopupsAndButtons();Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_spenttimes', 'issue_id' => $issue->getID(), 'initial_view' => 'entry')); ?>');"><?php echo fa_image_tag('clock').__('Log time spent'); ?></a>
                <?php endif; ?>
                <?php if ($issue->canEditAccessPolicy()): ?>
                    <div class="list-item separator"></div>
                    <a href="javascript:void(0);" class="list-item" onclick="Pachno.Main.Profile.clearPopupsAndButtons();Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_permissions', 'issue_id' => $issue->getID())); ?>');"><?php echo fa_image_tag('lock', ['class' => 'access_policy']).__("Update issue access policy"); ?></a>
                    <a href="javascript:void(0);" class="list-item" onclick="Pachno.Main.Profile.clearPopupsAndButtons();Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'issue_subscribers', 'issue_id' => $issue->getID())); ?>');"><?php echo fa_image_tag('star', ['class' => 'subscriber_list']).__("Manage issue subscribers"); ?></a>
                <?php endif; ?>
                <?php if ($issue->canEditIssueDetails()): ?>
                    <a href="javascript:void(0);" class="list-item" onclick="Pachno.Main.Profile.clearPopupsAndButtons();Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'move_issue', 'issue_id' => $issue->getID(), 'multi' => (int) (isset($multi) && $multi))); ?>');"><?php echo fa_image_tag('exchange-alt').__("Move issue to another project"); ?></a>
                <?php endif; ?>
                <?php if ($issue->canDeleteIssue()): ?>
                    <div class="list-item separator"></div>
                    <div class="list-item delete"><a href="javascript:void(0)" onclick="Pachno.Main.Profile.clearPopupsAndButtons();Pachno.Main.Helpers.Dialog.show('<?php echo __('Permanently delete this issue?'); ?>', '<?php echo __('Are you sure you wish to delete this issue? It will remain in the database for your records, but will not be accessible via Pachno.'); ?>', {yes: {href: '<?php echo make_url('deleteissue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?><?php if (isset($_SERVER['HTTP_REFERER'])): ?>?referer=<?php echo \pachno\core\framework\Response::escape($_SERVER['HTTP_REFERER']); ?><?php echo ($issue->getMilestone()) ? '#roadmap_milestone_' . $issue->getMilestone()->getID() : ''; endif; ?>' }, no: {click: Pachno.Main.Helpers.Dialog.dismiss}});"><?php echo fa_image_tag('times').__("Permanently delete this issue"); ?></a></div>
                <?php endif; ?>
            <?php else: ?>
                <div class="list-item disabled"><a href="#"><?php echo __('No additional actions available'); ?></a></div>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!isset($times) || $times): ?>
        <?php if ($issue->canEditEstimatedTime()): ?>
            <?php $estimator_params = array('issue' => $issue, 'field' => 'estimated_time', 'instant_save' => true); ?>
            <?php if (isset($estimator_mode)) $estimator_params['mode'] = $estimator_mode; ?>
            <?php if (isset($board)): ?>
                <?php include_component('main/issueestimator', array_merge($estimator_params, compact('board'))); ?>
            <?php else: ?>
                <?php include_component('main/issueestimator', $estimator_params); ?>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
