<?php

    use pachno\core\framework\Context;
    use pachno\core\entities\Issue;
    use pachno\core\entities\LogItem;

    /**
     * @var LogItem[] $log_items
     * @var string $prev_date
     * @var integer $prev_timestamp
     * @var Issue $prev_issue
     */

?>
<div class="dashboard-recent-activities">
    <?php if (count($log_items) > 0): ?>
        <div class="recent-activities">
            <?php foreach ($log_items as $log_item): ?>
                <?php if (!$log_item->isVisible()) continue; ?>
                <?php $date = Context::getI18n()->formatTime($log_item->getTime(), 5); ?>
                <?php if ($date != $prev_date): ?>
                    <div class="date-header">
                        <span class="icon"><?= fa_image_tag('dot-circle', [], 'far'); ?></span>
                        <span class="date"><?php echo $date; ?></span>
                    </div>
                <?php endif; ?>
                <?php include_component('main/logitem', [
                        'item' => $log_item,
                        'include_project' => true,
                        'include_issue_title' => $prev_issue != $log_item->getTarget(),
                        'include_time' => true
                    ]); ?>
                <?php $prev_date = $date; ?>
                <?php $prev_timestamp = $log_item->getTime(); ?>
                <?php $prev_issue = $log_item->getTarget(); ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="onboarding medium">
            <div class="image-container">
                <?= image_tag('/unthemed/onboarding-recent-activities.png', [], true); ?>
            </div>
            <div class="helper-text">
                <?php echo __("Changes to issues, commits and other actions show up here"); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
