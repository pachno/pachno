<?php

    use pachno\core\entities\Issue;

    /**
     * @var Issue $issue
     */

?>
<div id="workflow-actions" class="workflow-actions-container">
    <div id="workflow-list" class="workflow-list" data-issue-workflow-transitions-container data-issue-id="<?= $issue->getId(); ?>"></div>
    <div class="time-tracking-buttons tooltip-container <?php if ($issue->isTimeTrackingCurrentUser()) echo 'tracking'; ?>" data-dynamic-field-value data-field="time_tracking" data-issue-id="<?= $issue->getId(); ?>">
        <div class="tooltip from-above">
            <?= fa_image_tag('user-clock', ['class' => 'icon']); ?>
            <span><?= __('Time tracking started at %time', ['%time' => '<span class="time-start-value"></span>']); ?><span class="icon-paused count-badge"><?= __('Paused'); ?></span></span>
        </div>
        <?php if ($issue->canEditSpentTime()): ?>
            <button class="button secondary highlight trigger-start-time-tracking" data-issue-id="<?= $issue->getID(); ?>" data-url="<?= make_url('issue_edittimespent', ['project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'entry_id' => 0]); ?>?is_completed=0">
                <?= fa_image_tag('play-circle', ['class' => 'icon']); ?>
                <span class="name"><?= __('Track time'); ?></span>
            </button>
            <span class="value-container count-badge" data-interactive-timer>
                <?= fa_image_tag('clock', ['class' => 'icon icon-running']); ?>
                <?= fa_image_tag('pause', ['class' => 'icon icon-paused']); ?>
                <span class="value">--:--</span>
            </span>
            <button class="button secondary icon highlight trigger-pause-time-tracking" data-issue-id="<?= $issue->getID(); ?>">
                <?= fa_image_tag('pause', ['class' => 'icon']); ?>
            </button>
            <button class="button secondary icon highlight trigger-resume-time-tracking" data-issue-id="<?= $issue->getID(); ?>">
                <?= fa_image_tag('play', ['class' => 'icon']); ?>
            </button>
            <button class="button secondary icon highlight trigger-stop-time-tracking" data-issue-id="<?= $issue->getID(); ?>">
                <?= fa_image_tag('stop', ['class' => 'icon']); ?>
            </button>
            <button class="button secondary icon highlight danger trigger-cancel-time-tracking" onclick="Pachno.UI.Dialog.show('<?= __('Stop time tracking and discard tracked time?'); ?>', '<?= __('Please confirm that you want to discard the time automatically tracked so far.'); ?>', {yes: {click: function() {Pachno.trigger(Pachno.EVENTS.issue.removeSpentTime, { auto: true, issue_id: <?= $issue->getID(); ?> })}}, no: { click: Pachno.UI.Dialog.dismiss }});">
                <?= fa_image_tag('times', ['class' => 'icon']); ?>
            </button>
        <?php endif; ?>
    </div>
</div>
