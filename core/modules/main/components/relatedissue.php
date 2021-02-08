<a class="<?php if ($issue->isClosed()): ?> closed<?php endif; ?> related-issue" id="related_issue_<?php echo $issue->getID(); ?>"  href="<?php echo make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getFormattedIssueNo())); ?>">
    <span class="count-badge"><?= $issue->getFormattedIssueNo(true); ?></span>
    <span class="issue-state <?php echo $issue->isClosed() ? 'closed' : 'open'; ?>"><?php echo $issue->isClosed() ? __('Closed') : __('Open'); ?></span>
    <span class="issue-title" title="<?php echo \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()); ?>"><?php echo \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()); ?></span>
    <?php if ($issue->isAssigned()): ?>
        <?php if ($issue->getAssignee() instanceof \pachno\core\entities\User): ?>
            <?php include_component('main/userdropdown', ['user' => $issue->getAssignee(), 'show_name' => false]); ?>
        <?php else: ?>
            <?php include_component('main/teamdropdown', ['team' => $issue->getAssignee()]); ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php include_component('main/statusbadge', ['status' => $issue->getStatus()]); ?>
    <?php /* if (isset($related_issue) &&$related_issue->canAddRelatedIssues()): ?>
        <button class="button secondary icon" data-url="<?= make_url('viewissue_remove_related_issue', ['project_key' => $related_issue->getProject()->getKey(), 'issue_id' => $related_issue->getID(), 'related_issue_id' => $issue->getID()]); ?>">
            <?= fa_image_tag('times', ['class' => 'icon']); ?>
        </button>
    <?php endif; */ ?>
</a>
