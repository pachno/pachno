<?php

    use pachno\core\entities\Dashboard;
    use pachno\core\entities\Project;
    use pachno\core\entities\User;
    use pachno\core\framework\Response;

    /**
     * @var Response $pachno_response
     * @var User $pachno_user
     * @var Dashboard $dashboard
     * @var Project $selected_project
     */

    $pachno_response->setTitle(__('"%project_name" project dashboard', array('%project_name' => $selected_project->getName())));
    $pachno_response->addFeed(make_url('project_timeline', array('project_key' => $selected_project->getKey(), 'format' => 'rss')), __('"%project_name" project timeline', array('%project_name' => $selected_project->getName())));
?>
<div class="content-with-sidebar">
    <?php include_component('project/sidebar', ['dashboard' => $dashboard]); ?>
    <div id="project_planning" class="project_info_container">
        <?php \pachno\core\framework\Event::createNew('core', 'project_dashboard_top')->trigger(); ?>
        <?php if (!$dashboard instanceof Dashboard && $pachno_user->canEditProjectDetails($selected_project)) : ?>
                <div style="text-align: center; padding: 40px;">
                    <p class="content faded_out"><?php echo __("This dashboard doesn't contain any views."); ?></p>
                    <br>
                    <form action="<?php echo make_url('project_dashboard', array('project_key' => $selected_project->getKey())); ?>" method="post">
                        <input type="hidden" name="setup_default_dashboard" value="1">
                        <input type="submit" value="<?php echo __('Setup project dashboard'); ?>" class="button button-green" style="font-size: 1.1em; padding: 5px !important;">
                    </form>
                </div>
            <?php else: ?>
                <?php include_component($dashboard->getLayout(), compact('dashboard')); ?>
        <?php endif; ?>
        <?php \pachno\core\framework\Event::createNew('core', 'project_dashboard_bottom')->trigger(); ?>
        <br style="clear: both;">
    </div>
</div>
