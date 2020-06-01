<?php

    use pachno\core\entities\DashboardView;

    if (!isset($selected_project))
    {
        $selected_project = \pachno\core\framework\Context::getCurrentProject();
    }

    if (!isset($submenu)): $submenu = false;
    endif;
?>
<?php if ($pachno_user->hasProjectPageAccess('project_dashboard', $selected_project)): ?>
    <a href="<?= make_url('project_dashboard', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())); ?>" class="list-item <?php if ($pachno_response->getPage() == 'project_dashboard') echo 'selected'; ?>"><span class="name"><?= __('Dashboard'); ?></span></a>
    <?php \pachno\core\framework\Event::createNew('core', 'project_sidebar_links_dashboard')->trigger(array('submenu' => $submenu)); ?>
    <?php if (!($submenu) && $pachno_response->getPage() == 'project_dashboard' && $pachno_user->canEditProjectDetails($selected_project)): ?>
        <ul class="simple-list">
            <li><?= javascript_link_tag('<span>' . __('Customize') . '</span>', array('title' => __('Customize'), 'onclick' => "Pachno.UI.Backdrop.show('" . make_url('get_partial_for_backdrop', array('key' => 'dashboard_config', 'tid' => $selected_project->getID(), 'target_type' => DashboardView::TYPE_PROJECT, 'previous_route')) . "');")); ?></li>
        </ul>
    <?php endif; ?>
<?php endif; ?>
<?php if ($pachno_user->hasProjectPageAccess('project_releases', $selected_project) && $selected_project->isBuildsEnabled()): ?>
    <a href="<?= make_url('project_releases', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())); ?>" class="list-item <?php if ($pachno_response->getPage() == 'project_releases') echo 'selected'; ?>"><span class="name"><?= __('Releases'); ?></span></a>
    <?php \pachno\core\framework\Event::createNew('core', 'project_sidebar_links_releases')->trigger(array('submenu' => $submenu)); ?>
<?php endif; ?>
<?php if ($pachno_user->hasProjectPageAccess('project_roadmap', $selected_project)): ?>
    <a href="<?= make_url('project_roadmap', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())); ?>" class="list-item <?php if ($pachno_response->getPage() == 'project_roadmap') echo 'selected'; ?>"><span class="name"><?=  __('Roadmap'); ?></span></a>
    <?php \pachno\core\framework\Event::createNew('core', 'project_sidebar_links_roadmap')->trigger(array('submenu' => $submenu)); ?>
<?php endif; ?>
<?php if ($pachno_user->hasProjectPageAccess('project_team', $selected_project)): ?>
    <a href="<?= make_url('project_team', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())); ?>" class="list-item <?php if ($pachno_response->getPage() == 'project_team') echo 'selected'; ?>"><span class="name"><?= __('Team overview'); ?></span></a>
    <?php \pachno\core\framework\Event::createNew('core', 'project_sidebar_links_team')->trigger(array('submenu' => $submenu)); ?>
<?php endif; ?>
<?php if ($pachno_user->hasProjectPageAccess('project_statistics', $selected_project)): ?>
    <a href="<?= make_url('project_statistics', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())); ?>" class="list-item <?php if ($pachno_response->getPage() == 'project_statistics') echo 'selected'; ?>"><span class="name"><?=  __('Statistics'); ?></span></a>
    <?php if (!($submenu) && $pachno_response->getPage() == 'project_statistics'): ?>
        <div class="submenu list-mode">
            <div class="header"><?= __('Number of issues per:'); ?></div>
            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Project.Statistics.get('<?= make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_state')); ?>');"><?= __('%number_of_issues_per State (open / closed)', array('%number_of_issues_per' => '')); ?></a>
            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Project.Statistics.get('<?= make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_category')); ?>');"><?= __('%number_of_issues_per Category', array('%number_of_issues_per' => '')); ?></a>
            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Project.Statistics.get('<?= make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_priority')); ?>');"><?= __('%number_of_issues_per Priority level', array('%number_of_issues_per' => '')); ?></a>
            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Project.Statistics.get('<?= make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_resolution')); ?>');"><?= __('%number_of_issues_per Resolution', array('%number_of_issues_per' => '')); ?></a>
            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Project.Statistics.get('<?= make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_reproducability')); ?>');"><?= __('%number_of_issues_per Reproducability', array('%number_of_issues_per' => '')); ?></a>
            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Project.Statistics.get('<?= make_url('project_statistics_imagesets', array('project_key' => $selected_project->getKey(), 'set' => 'issues_per_status')); ?>');"><?= __('%number_of_issues_per Status type', array('%number_of_issues_per' => '')); ?></a>
        </div>
        <?php \pachno\core\framework\Event::createNew('core', 'projectstatistics_links', $selected_project)->trigger(); ?>
    <?php endif; ?>
    <?php \pachno\core\framework\Event::createNew('core', 'project_sidebar_links_statistics')->trigger(array('submenu' => $submenu)); ?>
<?php endif; ?>
<?php if ($pachno_user->hasProjectPageAccess('project_timeline', $selected_project)): ?>
    <a href="<?= make_url('project_timeline_important', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())); ?>" class="list-item <?php if ($pachno_response->getPage() == 'project_timeline') echo 'selected'; ?>"><span class="name"><?= __('Timeline'); ?></span></a>
    <?php \pachno\core\framework\Event::createNew('core', 'project_sidebar_links_timeline')->trigger(array('submenu' => $submenu)); ?>
<?php endif; ?>
<?php $event = \pachno\core\framework\Event::createNew('core', 'project_sidebar_links')->trigger(array('submenu' => $submenu)); ?>
<?php foreach ($event->getReturnList() as $menuitem): ?>
    <a href="<?= $menuitem['url']; ?>" class="list-item">
        <span clasS="name"><?= $menuitem['title']; ?></span>
    </a>
<?php endforeach; ?>