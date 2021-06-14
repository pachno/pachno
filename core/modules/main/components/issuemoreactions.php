<?php

    use pachno\core\entities\Issue;
    use pachno\core\entities\User;

    /**
     * @var Issue $issue
     * @var USer $pachno_user
     */

?>
<?php if (isset($dynamic) && $dynamic == true): ?>
    <?php $moreactions_url = array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID()); ?>
    <?php if (isset($board)) $moreactions_url['board_id'] = $board->getID(); ?>
    <?php if (isset($estimator_mode)) $moreactions_url['estimator_mode'] = $estimator_mode; ?>
    <div class="dropdown-container dynamic_menu" data-menu-url="<?php echo make_url('issue_moreactions', $moreactions_url); ?>" data-dynamic-field-value data-field="menu" data-issue-id="<?= $issue->getId(); ?>">
        <div class="list-mode">
            <div class="list-item disabled">
                <span class="icon"><?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?></span>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="dropdown-container" data-dynamic-field-value data-field="menu" data-issue-id="<?= $issue->getId(); ?>">
        <div class="list-mode" data-simplebar>
            <?php if (!$issue->getProject()->isArchived() && $issue->canEditIssueDetails()): ?>
                <?php if (!$multi && $show_workflow_transitions): ?>
                    <div class="header"><?php echo __('Workflow transition actions'); ?></div>
                    <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
                        <?php foreach ($issue->getAvailableWorkflowTransitions() as $transition): ?>
                            <?php if ($transition->hasTemplate()): ?>
                                <a class="list-item trigger-backdrop" href="javascript:void(0);" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'workflow_transition', 'transition_id' => $transition->getID()])."&project_key=".$issue->getProject()->getKey()."&issue_id=".$issue->getID(); ?>"><span class="name"><?= $transition->getName(); ?></span></a>
                            <?php else: ?>
                                <a class="list-item trigger-workflow-transition" href="javascript:void(0);" data-url="<?= str_replace(['%25project_key%25', '%25issue_id%25'], [$issue->getProject()->getKey(), $issue->getID()], $transition->toJSON(false)['url']); ?>"><span class="name"><?= $transition->getName(); ?></span><span class="icon indicator"><?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?></span></a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if (!$multi): ?>
                        <div class="header"><?php echo __('Additional actions available'); ?></div>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($issue->canEditMilestone()): ?>
                    <?php if ($issue->isOpen()): ?>
                        <a class="trigger-not-blocking list-item <?php if (!$issue->isBlocking()) echo 'hidden'; ?>" data-trigger-issue-update data-field="blocking" data-field-value="0" data-issue-id="<?= $issue->getId(); ?>">
                            <?= fa_image_tag('certificate', ['class' => ['mark_not_blocking icon']]); ?>
                            <span class="name"><?php echo __("Mark as not blocking the next release"); ?></span>
                        </a>
                        <a class="trigger-blocking list-item <?php if ($issue->isBlocking()) echo 'hidden'; ?>" data-trigger-issue-update data-field="blocking" data-field-value="1" data-issue-id="<?= $issue->getId(); ?>">
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
                <?php if ($issue->isUpdateable()): ?>
                    <?php if ($issue->canEditAffectedComponents() || $issue->canEditAffectedBuilds() || $issue->canEditAffectedEditions()): ?>
                        <a id="affected_add_button" class="list-item trigger-backdrop" href="javascript:void(0);" data-url="<?php echo make_url('get_partial_for_backdrop', ['key' => 'issue_add_item', 'issue_id' => $issue->getID()]); ?>">
                            <?php echo fa_image_tag('cubes', ['class' => 'affected_items icon']); ?>
                            <span class="name"><?= __('Add affected item'); ?></span>
                        </a>
                    <?php else: ?>
                        <div class="list-item disabled">
                            <a id="affected_add_button" href="javascript:void(0);" onclick="Pachno.UI.Message.error('<?php echo __('You are not allowed to add an item to this list'); ?>');">
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
                                <?php echo javascript_link_tag(fa_image_tag('list-alt', ['class' => 'icon']).'<span class="name">'.__('Create a new child issue').'</span>', array('class' => 'list-item disabled', 'onclick' => "Pachno.Main.Profile.clearPopupsAndButtons();Pachno.UI.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $issue->getProject()->getId(), 'parent_issue_id' => $issue->getID()))."');", 'title' => __('Create a new child issue'))); ?>
                            <?php elseif ($issue->getIssuetype()->getID() != $board->getTaskIssuetypeID()): ?>
                                <?php echo javascript_link_tag(fa_image_tag('list-alt', ['class' => 'icon']).'<span class="name">'.__('Add a new task').'</span>', array('class' => 'list-item disabled', 'onclick' => "Pachno.UI.Backdrop.show('".make_url('get_partial_for_backdrop', array('key' => 'reportissue', 'project_id' => $issue->getProject()->getId(), 'parent_issue_id' => $issue->getID(), 'issuetype_id' => $board->getTaskIssuetypeID(), 'lock_issuetype' => 1))."');", 'title' => __('Add a new task'))); ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="javascript:void(0);" class="list-item trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'reportissue', 'project_id' => $issue->getProject()->getId(), 'parent_issue_id' => $issue->getID()]); ?>">
                                <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Create a related issue / subtask'); ?></span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($issue->canAddRelatedIssues()): ?>
                        <a href="javascript:void(0)" class="list-item trigger-backdrop" id="relate_to_existing_issue_button" data-url="<?php echo make_url('get_partial_for_backdrop', ['key' => 'relate_issue', 'issue_id' => $issue->getID()]); ?>">
                            <?php echo fa_image_tag('share-alt', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Relate to an existing issue'); ?></span>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if ($issue->canAddRelatedIssues() && $pachno_user->canReportIssues($issue->getProject())): ?>
                        <a class="list-item disabled" href="javascript:void(0)">
                            <?php echo fa_image_tag('list-alt', ['class' => 'icon']); ?>
                            <span class="name"><?= __("Create a new related issue"); ?></span>
                            <span class="tooltip rightie"><?php echo __('This action is not available at this stage in the workflow'); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if ($issue->canAddRelatedIssues()): ?>
                        <a class="list-item disabled">
                            <?php echo fa_image_tag('sign-in-alt', ['class' => 'icon']); ?>
                            <span><?= __("Relate to an existing issue"); ?></span>
                            <span class="tooltip rightie"><?php echo __('This action is not available at this stage in the workflow'); ?></span>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="list-item separator"></div>
                <?php if (!isset($times) || $times): ?>
                    <?php if ($issue->canEditEstimatedTime()): ?>
                        <?php if ($issue->isUpdateable()): ?>
                            <a href="javascript:void(0);" class="list-item disabled" onclick="Pachno.Main.Profile.clearPopupsAndButtons();$('#estimated_time_<?php echo $issue->getID(); ?>_change').toggle('block');" title="<?php echo ($issue->hasEstimatedTime()) ? __('Change estimate') : __('Estimate this issue'); ?>"><?php echo fa_image_tag('clock', ['class' => 'icon']); ?><span class="name"><?= (($issue->hasEstimatedTime()) ? __('Change estimate') : __('Estimate this issue')); ?></span></a>
                        <?php else: ?>
                            <a href="javascript:void(0);" class="list-item disabled"><?php echo fa_image_tag('clock', ['class' => 'icon']); ?><span class="name"><?= __("Change estimate"); ?></span><div class="tooltip rightie"><?php echo __('This action is not available at this stage in the workflow'); ?></div></a>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($issue->canEditSpentTime()): ?>
                    <a href="javascript:void(0)" class="list-item trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', array('key' => 'issue_spenttimes', 'issue_id' => $issue->getID())); ?>"><?php echo fa_image_tag('clock', ['class' => 'icon']); ?><span class="name"><?= __('Log time spent'); ?></span></a>
                <?php endif; ?>
                <?php if ($issue->canEditAccessPolicy()): ?>
                    <div class="list-item separator"></div>
                    <a class="trigger-not-locked list-item <?php if ($issue->isLocked()) echo 'hidden'; ?>" data-trigger-issue-update data-field="locked" data-field-value="1" data-issue-id="<?= $issue->getId(); ?>">
                        <?= fa_image_tag('user-lock', ['class' => ['icon']]); ?>
                        <span class="name"><?php echo __("Restrict access to this issue"); ?></span>
                    </a>
                    <a class="trigger-locked list-item <?php if (!$issue->isLocked()) echo 'hidden'; ?>" data-trigger-issue-update data-field="locked" data-field-value="0" data-issue-id="<?= $issue->getId(); ?>">
                        <?= fa_image_tag('lock-open', ['class' => ['icon']]); ?>
                        <span class="name"><?php echo __("Remove access restrictions for this issue"); ?></span>
                    </a>
                <?php endif; ?>
                <?php if ($issue->canEditIssueDetails()): ?>
                    <a href="javascript:void(0);" class="list-item trigger-backdrop" data-url="<?php echo make_url('get_partial_for_backdrop', ['key' => 'move_issue', 'issue_id' => $issue->getID(), 'multi' => (int) (isset($multi) && $multi)]); ?>">
                        <?php echo fa_image_tag('exchange-alt', ['class' => 'icon']); ?>
                        <span class="name"><?= __("Move issue to another project"); ?></span>
                    </a>
                <?php endif; ?>
                <?php if ($issue->canDeleteIssue()): ?>
                    <div class="list-item separator"></div>
                    <a href="javascript:void(0)" class="list-item disabled danger" onclick="Pachno.Main.Profile.clearPopupsAndButtons();Pachno.UI.Dialog.show('<?php echo __('Permanently delete this issue?'); ?>', '<?php echo __('Are you sure you wish to delete this issue? It will remain in the database for your records, but will not be accessible via Pachno.'); ?>', {yes: {href: '<?php echo make_url('deleteissue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID())); ?><?php if (isset($_SERVER['HTTP_REFERER'])): ?>?referer=<?php echo \pachno\core\framework\Response::escape($_SERVER['HTTP_REFERER']); ?><?php echo ($issue->getMilestone()) ? '#roadmap_milestone_' . $issue->getMilestone()->getID() : ''; endif; ?>' }, no: {click: Pachno.UI.Dialog.dismiss}});"><?php echo fa_image_tag('times', ['class' => 'icon']); ?><span class="name"><?= __("Permanently delete this issue"); ?></span></a>
                <?php endif; ?>
            <?php else: ?>
                <div class="list-item disabled"><span class="name"><?php echo __('No additional actions available'); ?></span></div>
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
