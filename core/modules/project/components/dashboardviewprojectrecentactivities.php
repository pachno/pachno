<?php

    use pachno\core\entities\LogItem;
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
<div class="button-container">
    <?php if ($pachno_user->hasProjectPermission(Permissions::PERMISSION_PROJECT_ACCESS_DASHBOARD, Context::getCurrentProject())): ?>
        <?php echo link_tag(make_url('project_timeline_important', array('project_key' => Context::getCurrentProject()->getKey())), __('Show timeline for important events'), array('class' => 'button secondary', 'title' => __('Show more'))); ?>
        <?php echo link_tag(make_url('project_timeline', array('project_key' => Context::getCurrentProject()->getKey())), fa_image_tag('stream', ['class' => 'icon']).'<span>'.__('Show complete timeline').'</span>', array('class' => 'button secondary highlight', 'title' => __('Show more'))); ?>
    <?php endif; ?>
</div>
