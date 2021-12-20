<?php

    /**
     * @var \pachno\core\entities\Issue $issue
     */

?>
<div id="issuetype-field" class="issuetype-field dropper-container">
    <div class="<?php if ($issue->isEditable() && $issue->canEditIssuetype()) echo 'dropper'; ?> issuetype-icon issuetype-<?= ($issue->hasIssueType()) ? $issue->getIssueType()->getIcon() : 'unknown'; ?>" data-dynamic-field-value data-field="issue_type" data-issue-id="<?= $issue->getId(); ?>">
        <?php if ($issue->hasIssueType()) echo fa_image_tag($issue->getIssueType()->getFontAwesomeIcon(), ['class' => 'icon']); ?>
        <span class="name"><?= __($issue->getIssueType()->getName()); ?></span>
    </div>
    <?php if ($issue->isEditable() && $issue->canEditIssuetype()): ?>
        <div id="issuetype_change" class="dropdown-container from-right">
            <div class="list-mode">
                <div class="header">
                    <span class="name"><?= __('Change issue type'); ?></span>
                </div>
                <?php foreach ($issue->getProject()->getIssuetypeScheme()->getIssuetypes() as $issuetype): ?>
                    <input type="radio" class="fancy-checkbox" name="issuetype_id" id="issue_issuetype_<?= $issuetype->getId(); ?>_radio" value="<?= $issuetype->getId(); ?>" <?php if ($issue->getIssueType() instanceof \pachno\core\entities\Issuetype && $issue->getIssueType()->getID() == $issuetype->getId()) echo ' checked'; ?> data-trigger-issue-update data-field="issuetype" data-issue-id="<?= $issue->getId(); ?>">
                    <label for="issue_issuetype_<?= $issuetype->getId(); ?>_radio" class="list-item">
                        <?php echo fa_image_tag($issuetype->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $issuetype->getIcon()]); ?>
                        <span class="name"><?php echo __($issuetype->getName()); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
