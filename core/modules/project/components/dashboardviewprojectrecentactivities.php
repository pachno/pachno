<?php

    use pachno\core\entities\LogItem;
    use pachno\core\entities\Permission;
    use pachno\core\entities\tables\Permissions;
    use pachno\core\entities\User;
    use pachno\core\framework\Context;

    /**
     * @var User $pachno_user
     * @var LogItem[] $recent_activities
     */

?>
<div class="dashboard_project_recent_activities">
    <?php if (count($recent_activities) > 0): ?>
        <?php include_component('project/timeline', array('activities' => $recent_activities)); ?>
    <?php else: ?>
        <div class="onboarding unthemed">
            <div class="image-container"><?= image_tag('/unthemed/no-recent-activities.png', [], true); ?></div>
            <div class="helper-text">
                <?php echo __('As soon as something important happens it will appear here.'); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
