<?php

    use pachno\core\entities\Issue;
    use pachno\core\framework\Settings;
    use pachno\core\framework\Context;

    /**
     * @var string $set_field_route
     * @var Issue $issue
     */

    $json = $issue->toJSON(true);

?>
<div class="backdrop_box huge" id="issue-card-popup">
    <div class="backdrop_detail_header">
        <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
            <?php include_component('project/viewissueworkflowbuttons', ['issue' => $issue, 'showLockedStatus' => false]); ?>
        <?php else: ?>
            <span class="title-crumbs">
                <?php include_component('project/issueparent_crumbs', array('issue' => $issue)); ?>
            </span>
        <?php endif; ?>
        <div class="indicator issue-update-indicator" data-issue-id="<?= $issue->getID(); ?>">
            <?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?>
        </div>
        <div class="dropper-container">
            <button class="dropper button secondary" id="more_actions_<?= $issue->getID(); ?>_button"><span><?= __('Actions'); ?></span><?= fa_image_tag('chevron-down'); ?></button>
            <?php include_component('main/issuemoreactions', array('issue' => $issue, 'times' => false, 'show_workflow_transitions' => false)); ?>
        </div>
        <?php include_component('project/issuefavorite', array('issue' => $issue)); ?>
        <button class="closer"><?= fa_image_tag('times'); ?></button>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content with-sidebar sidebar-right">
        <div class="content">
            <div id="title-field" class="title-container">
                <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
                    <span class="title-crumbs">
                        <?php include_component('project/issueparent_crumbs', array('issue' => $issue)); ?>
                    </span>
                <?php endif; ?>
                <div id="title_content">
                    <span id="title-name" class="title-name" title="<?php echo \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()); ?>">
                        <?php echo \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()); ?>
                    </span>
                </div>
            </div>
            <div id="status-field" class="dropper-container status-field">
                <div class="status-badge dropper" style="
                        background-color: <?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;
                        color: <?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getTextColor() : '#333'; ?>;
                <?php if (!$issue->getStatus() instanceof \pachno\core\entities\Datatype): ?> display: none;<?php endif; ?>
                        " id="status_<?php echo $issue->getID(); ?>_color">
                    <span id="status_content"><?php if ($issue->getStatus() instanceof \pachno\core\entities\Datatype) echo __($issue->getStatus()->getName()); ?></span>
                </div>
                <?php if ($issue->canEditStatus()): ?>
                    <div class="dropdown-container">
                        <div class="list-mode" id="status_change">
                            <div class="header">
                                <span class="name"><?= __('Change status'); ?></span>
                            </div>
                            <?php foreach ($statuses as $status): ?>
                                <?php if (!$status->canUserSet($pachno_user)) continue; ?>
                                <div class="list-item">
                                    <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'status', 'status_id' => $status->getID())); ?>', 'status');">
                                        <div class="status-badge" style="background-color: <?php echo $status->getColor(); ?>;color: <?php echo $status->getTextColor(); ?>;">
                                            <span><?php echo __($status->getName()); ?></span>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php include_component('project/issuedetails', ['issue' => $issue]); ?>
            <?php include_component('project/issuecomments', ['issue' => $issue]); ?>
        </div>
        <div class="sidebar" id="issue-card-issuefields-container">
            <?php include_component('main/viewissuefields', ['issue' => $issue, 'include_status' => true]); ?>
        </div>
    </div>
</div>
<script>
    Pachno.addIssue(<?= json_encode($json); ?>).updateVisibleValues();
</script>
