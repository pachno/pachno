<?php

    /** @var \pachno\core\entities\LogItem[][] $activities */

?>
<?php if (count($activities)): ?>
    <div class="recent-activities">
        <?php foreach ($activities as $timestamp => $activities_array): ?>
            <?php $date = \pachno\core\framework\Context::getI18n()->formatTime($timestamp, 5); ?>
            <?php if ($date != $prev_date): ?>
                <div class="date-header">
                    <span class="icon"><?= fa_image_tag('dot-circle', [], 'far'); ?></span>
                    <span class="date"><?php echo $date; ?></span>
                </div>
            <?php endif; ?>
            <?php $prev_issue = isset($prev_issue) ? $prev_issue : null; ?>
            <?php foreach ($activities_array as $log_item): ?>
                <?php include_component('main/logitem', [
                    'item' => $log_item,
                    'include_time' => true,
                    'include_user' => true,
                    'include_details' => true,
                    'include_issue_title' => $prev_issue != $log_item->getTarget()
                ]); ?>
                <?php $prev_timestamp = $timestamp; ?>
                <?php $prev_issue = $log_item->getTarget() ?: null; ?>
            <?php endforeach; ?>
            <?php $prev_date = $date; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
