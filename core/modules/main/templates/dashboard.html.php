<?php

    $pachno_response->setTitle(__('Dashboard'));
    $pachno_response->addBreadcrumb(__('Personal dashboard'), make_url('dashboard'));
    $pachno_response->addFeed(make_url('my_reported_issues', ['format' => 'rss']), __('Issues reported by me'));
    $pachno_response->addFeed(make_url('my_assigned_issues', ['format' => 'rss']), __('Open issues assigned to you'));
    $pachno_response->addFeed(make_url('my_teams_assigned_issues', ['format' => 'rss']), __('Open issues assigned to your teams'));

?>
<?php include_component('main/hideableInfoBoxModal', ['key' => 'dashboard_didyouknow', 'title' => __('Get started using Pachno'), 'template' => 'main/profile_dashboard']); ?>
<div class="content-with-sidebar">
    <div id="dashboard_lefthand" class="side_bar <?php echo \pachno\core\framework\Settings::getToggle('dashboard_lefthand') ? ' collapsed' : ''; ?>">
        <?php \pachno\core\framework\Event::createNew('core', 'dashboard_left_top')->trigger(); ?>
        <a class="collapser_link" onclick="Pachno.Main.Dashboard.sidebar('<?php echo make_url('set_toggle_state', ['key' => 'dashboard_lefthand', 'state' => '']); ?>', 'dashboard_lefthand');">
            <?php echo fa_image_tag('chevron-left', ['class' => 'collapser']); ?>
            <?php echo fa_image_tag('chevron-right', ['class' => 'expander']); ?>
        </a>
        <div class="container_divs_wrapper">
            <div class="container_div">
                <?php include_component('main/myfriends'); ?>
            </div>
        </div>
        <?php \pachno\core\framework\Event::createNew('core', 'dashboard_left_bottom')->trigger(); ?>
    </div>
    <div class="main_area">
        <?php \pachno\core\framework\Event::createNew('core', 'dashboard_main_top')->trigger(); ?>
        <?php include_component($dashboard->getLayout(), compact('dashboard')); ?>
        <?php \pachno\core\framework\Event::createNew('core', 'dashboard_main_bottom')->trigger(); ?>
    </div>
</div>
