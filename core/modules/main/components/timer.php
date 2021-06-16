<?php

    use pachno\core\entities\IssueSpentTime;

    /**
     * @var IssueSpentTime $timer
     */

?>
<a href="<?= $timer->getIssue()->getUrl(); ?>" class="list-item multiline" id="timer_sidebar_<?= $timer->getID(); ?>_container" data-timer data-timer-id="<?= $timer->getID(); ?>">
    <span class="icon">
        <?php if ($timer->getIssue()->hasIssueType()) echo fa_image_tag($timer->getIssue()->getIssueType()->getFontAwesomeIcon(), ['class' => (($timer->getIssue()->hasIssueType()) ? 'issuetype-icon issuetype-' . $timer->getIssue()->getIssueType()->getIcon() : 'issuetype-icon issuetype-unknown')]); ?>
    </span>
    <span class="name">
        <span class="title">
            <?php echo $timer->getIssue()->getFormattedTitle(true); ?>
        </span>
        <span class="information">
            <span class="row">
                <span class="time-tracking-buttons <?php if ($timer->getIssue()->isTimeTrackingCurrentUser()) echo 'tracking'; ?> <?php if ($timer->getIssue()->isTimeTrackingCurrentUser() && $timer->getIssue()->getTimeTrackingCurrentUser()->isPaused()) echo 'paused'; ?>" data-dynamic-field-value data-field="time_tracking" data-issue-id="<?= $timer->getIssue()->getId(); ?>">
                    <span class="item">
                        <?= fa_image_tag('user-clock', ['class' => 'icon time-tracking-icon']); ?>
                        <span><?= __('Started %time', ['%time' => '<span class="time-start-value">'.\pachno\core\framework\Context::getI18n()->formatTime($timer->getStartedAt(), 9).'</span>']); ?><span class="icon-paused count-badge"><?= __('Paused'); ?></span></span>
                    </span>
                    <span class="value-container count-badge" data-interactive-timer <?php if ($timer->getIssue()->isTimeTrackingCurrentUser()): ?>data-started-at="<?= $timer->getIssue()->getTimeTrackingCurrentUser()->getEditedAt() * 1000 - $timer->getIssue()->getTimeTrackingCurrentUser()->getElapsedTime() * 1000; ?>" <?php if ($timer->getIssue()->getTimeTrackingCurrentUser()->isPaused()): ?>data-paused<?php endif; ?><?php endif; ?>>
                        <?= fa_image_tag('clock', ['class' => 'icon icon-running time-tracking-icon']); ?>
                        <?= fa_image_tag('pause', ['class' => 'icon icon-paused time-tracking-icon']); ?>
                        <span class="value"><?= $timer->getFormattedElapsedTimeTotal(); ?></span>
                    </span>
                </span>
            </span>
        </span>
    </span>
</a>
