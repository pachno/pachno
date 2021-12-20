<?php

    use pachno\core\framework\Context;
    use pachno\core\entities\Issue;
    use pachno\core\entities\LogItem;

    /**
     * @var \pachno\core\entities\IssueSpentTime[] $timers
     * @var string $prev_date
     * @var integer $prev_timestamp
     * @var Issue $prev_issue
     */

?>
<div class="dashboard-timers">
    <?php if (count($timers) > 0): ?>
        <div class="list-mode">
            <?php foreach ($timers as $timer): ?>
                <?php include_component('main/timer', ['timer' => $timer]); ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="onboarding medium">
            <div class="image-container">
                <?= image_tag('/unthemed/onboarding-no-timers.png', [], true); ?>
            </div>
            <div class="helper-text">
                <?php echo __("Active timers show up here"); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
