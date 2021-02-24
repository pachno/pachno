<?php

    use pachno\core\entities\Issue;
    use pachno\core\entities\File;

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
        <?php endif; ?>
        <div class="indicator issue-update-indicator" data-issue-id="<?= $issue->getID(); ?>">
            <?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?>
        </div>
        <a class="button secondary highlight" href="<?= $issue->getUrl(); ?>" target="_blank"><span><?= __('Go to issue'); ?></span><?= fa_image_tag('external-link-alt', ['class' => 'icon']); ?></a>
        <?php include_component('project/issuefavorite', array('issue' => $issue)); ?>
        <button class="closer"><?= fa_image_tag('times'); ?></button>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content with-sidebar sidebar-right">
        <div class="content <?php if ($issue->getCoverImageFile() instanceof File) echo 'with-cover'; ?>" data-dynamic-field-value data-field="cover_image_toggle" data-issue-id="<?= $issue->getId(); ?>">
            <div id="title-field" class="title-container" style="<?php if ($issue->getCoverImageFile() instanceof File) echo "background-image: url('{$issue->getCoverImageFile()->getURL()}');"; ?>" data-dynamic-field-value data-field="cover_image" data-issue-id="<?= $issue->getId(); ?>">
                <div id="title_content" class="title-content">
                    <?php if ($issue->getParentIssues()): ?>
                        <span class="title-crumbs">
                            <?php include_component('project/issueparent_crumbs', ['issue' => $issue, 'backdrop' => true]); ?>
                        </span>
                    <?php endif; ?>
                    <div id="title-name" class="title-name" title="<?php echo \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()); ?>">
                        <?= fa_image_tag(($issue->hasIssueType()) ? $issue->getIssueType()->getFontAwesomeIcon() : 'unknown', ['class' => (($issue->hasIssueType()) ? 'issuetype-icon issuetype-' . $issue->getIssueType()->getIcon() : 'issuetype-icon issuetype-unknown')]); ?>
                        <span><?= $issue->getFormattedIssueNo(true); ?>&nbsp;&ndash;&nbsp;</span>
                        <input type="text" name="title" value="<?= $issue->getTitle(); ?>" class="invisible" id="issue_<?= $issue->getID(); ?>_title_input" data-trigger-save-on-blur data-field="title" data-issue-id="<?= $issue->getId(); ?>">
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                    </div>
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
            <?php include_component('project/issuedetails', ['issue' => $issue, 'backdrop' => true]); ?>
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
