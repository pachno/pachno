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
                    <input type="radio" class="fancy-checkbox" name="status_id" id="issue_status_<?= $status->getId(); ?>_radio" value="<?= $status->getId(); ?>" <?php if ($issue->getIssueType() instanceof \pachno\core\entities\Issuetype && $issue->getIssueType()->getID() == $status->getId()) echo ' checked'; ?> data-trigger-issue-update data-field="status" data-issue-id="<?= $issue->getId(); ?>">
                    <label for="issue_status_<?= $status->getId(); ?>_radio" class="list-item">
                        <span class="status-badge" style="background-color: <?php echo $status->getColor(); ?>;color: <?php echo $status->getTextColor(); ?>;">
                            <span><?php echo __($status->getName()); ?></span>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
