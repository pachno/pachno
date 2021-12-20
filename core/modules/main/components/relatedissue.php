<?php

    /**
 * @var \pachno\core\entities\Issue $issue
 */

?>
<div class="<?php if ($issue->isClosed()) echo 'closed'; ?> related-issue" id="related_issue_<?= $issue->getID(); ?>" data-issue data-issue-id="<?= $issue->getID(); ?>">
    <span class="issue-state <?= $issue->isClosed() ? 'closed' : 'open'; ?>"><?= $issue->isClosed() ? __('Closed') : __('Open'); ?></span>
    <span class="count-badge"><?= $issue->getFormattedIssueNo(true); ?></span>
    <a class="issue-title <?php if ($backdrop) echo 'trigger-backdrop'; ?>" href="<?= $link_url; ?>" <?= $link_data; ?> title="<?= \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()); ?>"><?= \pachno\core\framework\Context::getI18n()->decodeUTF8($issue->getTitle()); ?></a>
    <?php if ($issue->getNumberOfFiles()): ?>
        <span class="information"><?= fa_image_tag('paperclip'); ?><span><?= $issue->getNumberOfFiles(); ?></span></span>
    <?php endif; ?>
    <?php if ($issue->getNumberOfUserComments()): ?>
        <span class="information"><?= fa_image_tag('comment'); ?><span><?= $issue->getNumberOfUserComments(); ?></span></span>
    <?php endif; ?>
    <?php if ($issue->isAssigned()): ?>
        <?php if ($issue->getAssignee() instanceof \pachno\core\entities\User): ?>
            <?php include_component('main/userdropdown', ['user' => $issue->getAssignee(), 'show_name' => false]); ?>
        <?php else: ?>
            <?php include_component('main/teamdropdown', ['team' => $issue->getAssignee()]); ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php include_component('main/statusbadge', ['status' => $issue->getStatus()]); ?>
    <div class="dropper-container">
        <a title="<?php echo __('Show more actions'); ?>" class="button icon secondary dropper dynamic_menu_link" data-id="<?php echo $issue->getID(); ?>" id="more_actions_<?php echo $issue->getID(); ?>_button" href="javascript:void(0);"><?= fa_image_tag('ellipsis-v'); ?></a>
        <?php include_component('main/issuemoreactions', ['issue' => $issue, 'multi' => false, 'dynamic' => true, 'mode' => 'from-bottom']); ?>
    </div>
    <?php /* if (isset($related_issue) &&$related_issue->canAddRelatedIssues()): ?>
        <button class="button secondary icon" data-url="<?= make_url('viewissue_remove_related_issue', ['project_key' => $related_issue->getProject()->getKey(), 'issue_id' => $related_issue->getID(), 'related_issue_id' => $issue->getID()]); ?>">
            <?= fa_image_tag('times', ['class' => 'icon']); ?>
        </button>
    <?php endif; */ ?>
</div>
