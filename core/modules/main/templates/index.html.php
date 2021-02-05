<?php

/**
 * @var \pachno\core\entities\Dashboard $dashboard
 */

    $pachno_response->setTitle(__('Dashboard'));
    $pachno_response->addFeed(make_url('my_reported_issues', ['format' => 'rss']), __('Issues reported by me'));
    $pachno_response->addFeed(make_url('my_assigned_issues', ['format' => 'rss']), __('Open issues assigned to you'));
    $pachno_response->addFeed(make_url('my_teams_assigned_issues', ['format' => 'rss']), __('Open issues assigned to your teams'));

?>
<?php include_component('main/hideableInfoBoxModal', ['key' => 'dashboard_didyouknow', 'title' => __('Get started using Pachno'), 'template' => 'main/profile_dashboard']); ?>
<div class="content-with-sidebar">
    <div class="main_area">
        <?php \pachno\core\framework\Event::createNew('core', 'dashboard_main_top')->trigger(); ?>
        <?php include_component($dashboard->getLayout(), compact('dashboard')); ?>
        <?php \pachno\core\framework\Event::createNew('core', 'dashboard_main_bottom')->trigger(); ?>
    </div>
</div>
