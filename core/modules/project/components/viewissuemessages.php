<?php

    use pachno\core\entities\Issue;

    /**
     * @var Issue $issue
     */

?>
<div id="issue-messages-container" class="messages-container">
    <div class="message-box type-error hidden" id="viewissue_error">
    </div>
    <div id="blocking_field" class="message-box type-error <?php if (!$issue->isBlocking()) echo 'hidden'; ?>" data-dynamic-field-value data-field="blocking" data-issue-id="<?= $issue->getId(); ?>">
        <?= fa_image_tag('exclamation-circle', ['class' => 'icon']); ?>
        <span class="message"><?= __('This issue is blocking the next release'); ?></span>
    </div>
    <div class="message-box type-info <?php if (!$issue->isDuplicate()) echo 'hidden'; ?>" data-dynamic-field-value data-field="duplicate-message" data-issue-id="<?= $issue->getId(); ?>">
        <?php if ($issue->isDuplicate()): ?>
            <?php echo fa_image_tag('info-circle', ['class' => 'icon']); ?>
            <span class="content"><?php echo __('This issue is a duplicate of issue %link_to_duplicate_issue', array('%link_to_duplicate_issue' => link_tag($issue->getUrl(), $issue->getDuplicateOf()->getFormattedIssueNo(true)) . ' - "' . $issue->getDuplicateOf()->getTitle() . '"')); ?></span>
        <?php endif; ?>
    </div>
    <div class="message-box type-info <?php if (!$issue->isClosed()) echo 'hidden'; ?>" data-dynamic-field-value data-field="closed-message" data-issue-id="<?= $issue->getId(); ?>" data-message="<?php echo __('This issue has been closed with status "%status_name" and resolution "%resolution".'); ?>" data-unknown="<?= __('Not determined'); ?>">
        <?php echo fa_image_tag('info-circle', ['class' => 'icon']); ?>
        <span class="content"><?php echo __('This issue has been closed with status "%status_name" and resolution "%resolution".', ['%status_name' => (($issue->getStatus() instanceof \pachno\core\entities\Status) ? $issue->getStatus()->getName() : __('Not determined')), '%resolution' => (($issue->getResolution() instanceof \pachno\core\entities\Resolution) ? $issue->getResolution()->getName() : __('Not determined'))]); ?></span>
    </div>
    <?php if ($issue->getProject()->isArchived()): ?>
        <div class="message-box type-important">
            <?php echo fa_image_tag('info-triangle', ['class' => 'icon']); ?>
            <span class="content"><?php echo __('The project this issue belongs to has been archived, and so this issue is now read only'); ?></span>
        </div>
    <?php endif; ?>
</div>
