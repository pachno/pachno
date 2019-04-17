<?php

    $pachno_response->setTitle(__('Dashboard'));
    $pachno_response->addBreadcrumb(__('Personal dashboard'), make_url('dashboard'));
    $pachno_response->addFeed(make_url('my_reported_issues', ['format' => 'rss']), __('Issues reported by me'));
    $pachno_response->addFeed(make_url('my_assigned_issues', ['format' => 'rss']), __('Open issues assigned to you'));
    $pachno_response->addFeed(make_url('my_teams_assigned_issues', ['format' => 'rss']), __('Open issues assigned to your teams'));

?>
<?php include_component('main/hideableInfoBoxModal', ['key' => 'dashboard_didyouknow', 'title' => __('Get started using Pachno'), 'template' => 'main/profile_dashboard']); ?>
<div class="content-with-sidebar">
    <nav class="sidebar <?php echo \pachno\core\framework\Settings::getToggle('dashboard_lefthand') ? ' collapsed' : ''; ?>">
        <?php \pachno\core\framework\Event::createNew('core', 'dashboard_left_top')->trigger(); ?>
        <div class="list-mode">
            <?php include_component('main/myfriends'); ?>
        </div>
        <?php \pachno\core\framework\Event::createNew('core', 'dashboard_left_bottom')->trigger(); ?>
        <div class="collapser list-mode">
            <a class="list-item" href="javascript:void(0);">
                <span class="icon"><?= fa_image_tag('angle-double-left'); ?></span>
                <span class="name"><?= __('Toggle sidebar'); ?></span>
            </a>
        </div>
    </nav>
    <div class="main_area">
        <?php \pachno\core\framework\Event::createNew('core', 'dashboard_main_top')->trigger(); ?>
        <?php include_component($dashboard->getLayout(), compact('dashboard')); ?>
        <?php \pachno\core\framework\Event::createNew('core', 'dashboard_main_bottom')->trigger(); ?>
    </div>
</div>
