<?php

    use pachno\core\entities\User;
    use pachno\core\framework;

    /**
     * @var framework\Response $pachno_response
     * @var User $pachno_user
     */

    $project = \pachno\core\framework\Context::getCurrentProject();

?>
<div class="header-banner">
    <?php \pachno\core\framework\Event::createNew('core', 'project/templates/projectheader', $project)->trigger(); ?>
        <div class="header-name">
            <div class="image-container">
                <?php echo image_tag($project->getIconName(), ['alt' => $project->getName(), 'class' => 'project-icon', 'data-project-id' => $project->getID()], true); ?>
            </div>
            <span class="name-container">
                <span><?php echo $project->getName(); ?></span>
                <span class="info-container">
                    <?php if ($pachno_response->getPage() == 'project_settings'): ?>
                        <?= fa_image_tag('cog', ['class' => 'icon']); ?><span class="name"><?= __('Project settings'); ?></span>
                    <?php else: ?>
                        <?= ($subpage != '') ? $subpage : __('Project dashboard'); ?>
                    <?php endif; ?>
                </span>
            </span>
        </div>
        <?php \pachno\core\framework\Event::createNew('core', 'project/templates/projectheader/namelabel', $project)->trigger(); ?>
        <?php /* if ($pachno_response->getPage() == 'project_dashboard' && $pachno_user->canEditProjectDetails($selected_project)): ?>
            <div class="project_header_right button-group">
                <a href="javascript:void(0);" id="edit-project-dashboard-button" class="button" onclick="$$('.dashboard').each(function (elm) { elm.toggleClass('editable');});$(this).toggleClass('button-pressed');"><?= fa_image_tag('cog'); ?><span><?= __('Edit dashboard'); ?></span></a>
            </div>
        <?php endif; */ ?>
    <div class="button-group">
        <?php if (isset($custom_back)): ?>
            <a href="<?= $custom_back; ?>" class="button secondary icon back-button">
                <?= fa_image_tag('arrow-left', ['class' => 'icon']); ?>
                <span class="name"><?= __('Back'); ?></span>
            </a>
        <?php elseif (isset($show_back) && $show_back): ?>
            <a href="<?= make_url('project_dashboard', ['project_key' => $project->getKey()]); ?>" class="button secondary icon back-button">
                <?= fa_image_tag('arrow-left', ['class' => 'icon']); ?>
                <span class="name"><?= __('Back'); ?></span>
            </a>
        <?php endif; ?>
        <?php if ($pachno_response->getPage() != 'project_settings' && $pachno_user->canEditProjectDetails(framework\Context::getCurrentProject())): ?>
            <a href="<?= make_url('project_settings', ['project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey()]); ?>" class="button secondary <?php if ($pachno_response->getPage() == 'project_settings') echo 'active'; ?>">
                <?= fa_image_tag('cog', ['class' => 'icon']); ?>
                <span class="name"><?= __('Settings'); ?></span>
            </a>
        <?php endif; ?>
        <?php if (framework\Context::isProjectContext() && !framework\Context::getCurrentProject()->isArchived() && !framework\Context::getCurrentProject()->isLocked() && $pachno_user->canReportIssues(framework\Context::getCurrentProject())): ?>
            <button class="button button-report-issue trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'reportissue', 'project_id' => framework\Context::getCurrentProject()->getId()]); ?>" id="reportissue_button">
                <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                <span><?= __('Report an issue'); ?></span>
            </button>
        <?php endif; ?>
    </div>
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
</div>
