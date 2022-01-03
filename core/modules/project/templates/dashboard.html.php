<?php

    use pachno\core\entities\Dashboard;
    use pachno\core\entities\Project;
    use pachno\core\entities\User;
    use pachno\core\framework\Event;
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
        <?php Event::createNew('core', 'project_dashboard_top')->trigger(); ?>
        <?php if ($dashboard instanceof Dashboard): ?>
            <?php include_component($dashboard->getLayout(), compact('dashboard')); ?>
        <?php endif; ?>
        <?php Event::createNew('core', 'project_dashboard_bottom')->trigger(); ?>
    </div>
</div>
