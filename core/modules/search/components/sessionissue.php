<?php
/** @var \pachno\core\entities\Issue $issue
 *  @var \pachno\core\framework\Request $pachno_request
 *  @var \pachno\core\framework\Routing $pachno_routing
 */
?>
<a href="<?= $issue->getUrl(); ?>" title="<?= $issue->getFormattedTitle(true); ?>" class="list-item multiline <?php if ($pachno_routing->getCurrentRoute()->getName() == 'viewissue' && $pachno_request->getParameter('issue_no') == $issue->getFormattedIssueNo(true, false)) echo 'selected'; ?>">
    <span class="icon"><?php if ($issue->hasIssueType()) echo fa_image_tag($issue->getIssueType()->getFontAwesomeIcon(), ['class' => (($issue->hasIssueType()) ? 'issuetype-icon issuetype-' . $issue->getIssueType()->getIcon() : 'issuetype-icon issuetype-unknown')]); ?></span>
    <span class="name">
        <span class="title"><?php echo $issue->getFormattedTitle(true); ?></span>
        <span class="description">
            <span class="status-badge" style="background-color: <?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;color: <?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getTextColor() : '#333'; ?>;"><span><?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? __($issue->getStatus()->getName()) : __('Status not set'); ?></span></span>
            <?php if ($issue->getPriority() instanceof \pachno\core\entities\Priority): ?>
                <span class="priority priority_<?= $issue->getPriority()->getValue(); ?>"><?= fa_image_tag($issue->getPriority()->getFontAwesomeIcon(), [], $issue->getPriority()->getFontAwesomeIconStyle()) . $issue->getPriority()->getName(); ?></span>
            <?php endif; ?>
            <div class="time-tracking-buttons tooltip-container <?php if ($issue->isTimeTrackingCurrentUser()) echo 'tracking'; ?>" data-dynamic-field-value data-field="time_tracking" data-issue-id="<?= $issue->getId(); ?>">
                <div class="tooltip from-above">
                    <?= fa_image_tag('user-clock', ['class' => 'icon']); ?>
                    <span><?= __('Time tracking started at %time', ['%time' => '<span class="time-start-value"></span>']); ?><span class="icon-paused count-badge"><?= __('Paused'); ?></span></span>
                </div>
                <span class="value-container count-badge" data-interactive-timer <?php if ($issue->isTimeTrackingCurrentUser()): ?>data-started-at="<?= $issue->getTimeTrackingCurrentUser()->getEditedAt() * 1000 - $issue->getTimeTrackingCurrentUser()->getElapsedTime() * 1000; ?>"<?php endif; ?>>
                    <?= fa_image_tag('clock', ['class' => 'icon icon-running time-tracking-icon']); ?>
                    <?= fa_image_tag('pause', ['class' => 'icon icon-paused time-tracking-icon']); ?>
                    <span class="value">--:--</span>
                </span>
            </div>
        </span>
    </span>
</a>
