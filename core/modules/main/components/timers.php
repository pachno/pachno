<?php

    use pachno\core\entities\IssueSpentTime;

    /**
     * @var IssueSpentTime[] $timers
     */

?>
<div class="notifications dropdown-container list-mode dynamic_menu populate-once" id="user_notifications" data-menu-url="<?= make_url('get_partial_for_backdrop', ['key' => 'notifications']); ?>" data-simplebar>
    <?php if (!count($timers)): ?>
        <div class="onboarding">
            <div class="image-container">
                <?= image_tag('/unthemed/onboarding-no-timers.png', [], true); ?>
            </div>
            <div class="helper-text">
                <?= __('Magnificent!'); ?><br>
                <?= __("You are not time tracking any issues right now"); ?>
            </div>
        </div>
    <?php else: ?>
        <div class="header">
            <span><?= __('Ongoing timers'); ?></span>
        </div>
        <?php foreach ($timers as $timer): ?>
            <?php include_component('main/timer', ['timer' => $timer]); ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
