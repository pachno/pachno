<?php

    use pachno\core\entities\Issue;

    /**
     * @var Issue $issue
     */

?>
<div class="backdrop_box wide" id="viewissue_add_item_div">
    <div class="backdrop_detail_header">
        <span><?= __('Issue time tracking - time spent'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="helper-text">
            <div class="image-container"><?= image_tag('/unthemed/onboarding_time_tracking.png', [], true); ?></div>
            <span class="message">
                <?= __('The list below shows all time logged against this issue so far.'); ?>
            </span>
        </div>
        <div class="flexible-table" id="timespent_list">
            <div class="row header">
                <div class="column header"><?= __('Date'); ?></div>
                <div class="column header name-container"><?= __('Time logged'); ?></div>
                <div class="column header"><?= __('Logged by'); ?></div>
                <div class="column header"><?= __('Activity'); ?></div>
                <div class="column header actions"><?= __('Actions'); ?></div>
            </div>
            <?php foreach ($issue->getSpentTimes() as $spent_time): ?>
                <?php if ($spent_time->isAutomatic() && !$spent_time->isCompleted()) continue; ?>
                <?php include_component('main/editspenttimeentry', ['entry' => $spent_time, 'issue' => $issue]); ?>
            <?php endforeach; ?>
        </div>
        <div class="flexible-table">
            <?php include_component('main/editspenttimeentry', ['issue' => $issue, 'save' => true]); ?>
        </div>
    </div>
</div>
<?php if (isset($initial_view) && $initial_view == 'entry'): ?>
<script>
    $('#issue_<?= $issue->getID(); ?>_timeentry').focus();
</script>
<?php endif; ?>
