<?php

    use pachno\core\entities\DashboardView;
    use pachno\core\entities\User;
    use pachno\core\framework;

    /**
     * @var framework\Response $pachno_response
     * @var framework\Routing $pachno_routing
     * @var User $pachno_user
     */

    $project = \pachno\core\framework\Context::getCurrentProject();
//    $report_issue_primary_class = (in_array($pachno_response->getPage(), ['project_dashboard', 'project_issues'])) ? 'primary' : 'secondary highlight';
    $report_issue_primary_class = 'secondary highlight';

?>
<div class="name-container">
    <span class="image-container">
        <?php echo image_tag($project->getIconName(), ['alt' => $project->getName(), 'class' => 'project-icon', 'data-project-id' => $project->getID()], true); ?>
    </span>
    <span class="header">
        <?php \pachno\core\framework\Event::createNew('core', 'project/templates/projectheader', $project)->trigger(); ?>
        <span class="name"><?php echo $project->getName(); ?></span>
        <span class="subtitle">
            <?php if ($pachno_response->getPage() == 'project_settings'): ?>
                <?= fa_image_tag('cog', ['class' => 'icon']); ?><span class="name"><?= __('Project settings'); ?></span>
            <?php else: ?>
                <?= $pagename; ?>
            <?php endif; ?>
        </span>
        <?php \pachno\core\framework\Event::createNew('core', 'project/templates/projectheader/namelabel', $project)->trigger(); ?>
    </span>
</div>
<div class="spacer"></div>
<?php if ($pachno_response->getPage() !== 'project_dashboard'): ?>
    <div class="action-container">
        <div class="dropper-container">
            <button class="dropper button secondary icon"><?php echo fa_image_tag('ellipsis-v', ['class' => 'icon']); ?></button>
            <div class="dropdown-container">
                <div class="list-mode">
                    <a href="<?= make_url('project_settings', ['project_key' => $project->getKey()]); ?>" class="list-item">
                        <?= fa_image_tag('cog', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Configure project'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php \pachno\core\framework\Event::createNew('core', 'project/templates/projectheader/after-spacer', $project)->trigger(); ?>
<div class="action-container">
    <?php if ($pachno_response->getPage() === 'project_dashboard'): ?>
        <?= javascript_link_tag(fa_image_tag('edit', ['class' => 'icon']) . '<span class="name">' . __('Customize dashboard') . '</span>', ['title' => __('Customize dashboard'), 'onclick' => "Pachno.UI.Backdrop.show('" . make_url('get_partial_for_backdrop', ['key' => 'dashboard_config', 'dashboard_id' => $project->getDefaultDashboard()->getID(), 'target_type' => DashboardView::TYPE_PROJECT, 'previous_route']) . "');", 'class' => 'button secondary']); ?>
        <?php if ($pachno_user->canEditProjectDetails($project)): ?>
            <a href="<?= make_url('project_settings', ['project_key' => $project->getKey()]); ?>" class="button secondary">
                <?= fa_image_tag('cog', ['class' => 'icon']); ?>
                <span class="name"><?= __('Settings'); ?></span>
            </a>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (framework\Context::isProjectContext() && !$project->isArchived() && !$project->isLocked() && $pachno_user->canReportIssues($project)): ?>
        <button class="button button-report-issue trigger-backdrop <?= $report_issue_primary_class; ?>" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'reportissue', 'project_id' => $project->getId()]); ?>" id="reportissue_button">
            <?= fa_image_tag('plus', ['class' => 'icon']); ?>
            <span><?= __('Report an issue'); ?></span>
        </button>
    <?php endif; ?>
    <?php if ($pachno_response->getPage() == 'project_roadmap' && $pachno_user->canManageProjectReleases($project)): ?>
        <button class="button primary trigger-backdrop" data-url="<?php echo make_url('get_partial_for_backdrop', ['key' => 'milestone', 'project_id' => $project->getID()]); ?>');"><?= fa_image_tag('plus-square'); ?><span><?= __('Create milestone'); ?></span></button>
    <?php endif; ?>
    <?php if ($pachno_response->getPage() == 'project_releases' && $pachno_user->canEditProjectDetails($project)): ?>
        <a href="javascript:void(0);" class="button primary trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'project_build', 'project_id' => $project->getId()]); ?>">
            <span><?php echo __('Create new release'); ?></span>
        </a>
    <?php endif; ?>
    <?php \pachno\core\framework\Event::createNew('core', 'project_header_buttons', $project)->trigger(); ?>
</div>
<?php \pachno\core\framework\Event::createNew('core', 'project_header_sections', $project)->trigger(); ?>
<?php if ($pachno_response->getPage() == 'project_summary'): ?>
    <div class="project_header_right button-group">
        <?php \pachno\core\framework\Event::createNew('core', 'project_header_buttons')->trigger(); ?>
        <?php if ($project->hasDownloads() && $pachno_response->getPage() != 'project_releases'): ?>
            <?php echo link_tag(make_url('project_releases', ['project_key' => $project->getKey()]), image_tag('icon_download.png').__('Download'), ['class' => 'button button-orange']); ?>
        <?php endif; ?>
        <?php if ($project->hasParent()): ?>
            <?php echo link_tag(make_url('project_dashboard', ['project_key' => $project->getParent()->getKey()]), image_tag($project->getParent()->getIconName(), ['style' => 'width: 16px; height: 16px;'], true) . __('Up to %parent', array('%parent' => $project->getParent()->getName())), array('class' => 'button')); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>
