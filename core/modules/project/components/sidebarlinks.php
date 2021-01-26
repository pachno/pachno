<?php

    use pachno\core\framework;
    use pachno\core\framework\Context;

    /**
     * @var framework\Response $pachno_response
     * @var \pachno\core\entities\User $pachno_user
     */

    $selected_project = framework\Context::getCurrentProject();

?>
<a href="<?= make_url('project_dashboard', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())); ?>" class="list-item <?php if ($pachno_response->getPage() == 'project_dashboard') echo 'selected'; ?>">
    <?= fa_image_tag('columns', ['class' => 'icon']); ?>
    <span class="name"><?= __('Dashboard'); ?></span>
</a>
<?php \pachno\core\framework\Event::createNew('core', 'project_sidebar_links_dashboard')->trigger(array('submenu' => false)); ?>
<?php if ($pachno_user->hasProjectPageAccess('project_releases', $selected_project) && $selected_project->isBuildsEnabled()): ?>
    <a href="<?= make_url('project_releases', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())); ?>" class="list-item <?php if ($pachno_response->getPage() == 'project_releases') echo 'selected'; ?>">
        <?= fa_image_tag('box', ['class' => 'icon']); ?>
        <span class="name"><?= __('Releases'); ?></span>
    </a>
    <?php \pachno\core\framework\Event::createNew('core', 'project_sidebar_links_releases')->trigger(array('submenu' => false)); ?>
<?php endif; ?>
<?php if ($pachno_user->canSearchForIssues()): ?>
    <div class="list-item <?php if (in_array($pachno_response->getPage(), ['project_issues', 'viewissue'])) echo 'selected'; ?>">
        <a href="<?= make_url('project_issues', ['project_key' => Context::getCurrentProject()->getKey()]); ?>">
            <?= fa_image_tag('file-alt', ['class' => 'icon']); ?>
            <span class="name"><?= __('Issues'); ?></span>
        </a>
        <div class="dropper-container pop-out-expander">
            <?= fa_image_tag('angle-right', ['class' => 'dropper']); ?>
            <div class="dropdown-container interactive_filters_list list-mode from-left slide-out">
                <a class="list-item" href="javascript:void(0);">
                    <span class="icon"><?= fa_image_tag('angle-double-left'); ?></span>
                    <span class="name"><?= __('Back'); ?></span>
                </a>
                <?php include_component('project/searchmenu'); ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php framework\Event::createNew('core', 'templates/header::projectmenulinks', Context::getCurrentProject())->trigger(); ?>
<div class="header">
    <span class="name"><?= __('More information'); ?></span>
</div>
<?php if ($pachno_user->hasProjectPageAccess('project_roadmap', $selected_project)): ?>
    <a href="<?= make_url('project_roadmap', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())); ?>" class="list-item <?php if ($pachno_response->getPage() == 'project_roadmap') echo 'selected'; ?>">
        <?= fa_image_tag('road', ['class' => 'icon']); ?>
        <span class="name"><?=  __('Roadmap'); ?></span>
    </a>
    <?php \pachno\core\framework\Event::createNew('core', 'project_sidebar_links_roadmap')->trigger(array('submenu' => false)); ?>
<?php endif; ?>
<?php if ($pachno_user->hasProjectPageAccess('project_team', $selected_project)): ?>
    <a href="<?= make_url('project_team', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())); ?>" class="list-item <?php if ($pachno_response->getPage() == 'project_team') echo 'selected'; ?>">
        <?= fa_image_tag('users', ['class' => 'icon']); ?>
        <span class="name"><?= __('Team overview'); ?></span>
    </a>
    <?php \pachno\core\framework\Event::createNew('core', 'project_sidebar_links_team')->trigger(array('submenu' => false)); ?>
<?php endif; ?>
<?php if ($pachno_user->hasProjectPageAccess('project_timeline', $selected_project)): ?>
    <a href="<?= make_url('project_timeline_important', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())); ?>" class="list-item <?php if ($pachno_response->getPage() == 'project_timeline') echo 'selected'; ?>">
        <?= fa_image_tag('stream', ['class' => 'icon']); ?>
        <span class="name"><?= __('Timeline'); ?></span>
    </a>
    <?php \pachno\core\framework\Event::createNew('core', 'project_sidebar_links_timeline')->trigger(array('submenu' => false)); ?>
<?php endif; ?>
<?php $event = \pachno\core\framework\Event::createNew('core', 'project_sidebar_links')->trigger(array('submenu' => false)); ?>
<?php foreach ($event->getReturnList() as $menuitem): ?>
    <a href="<?= $menuitem['url']; ?>" class="list-item">
        <span class="name"><?= $menuitem['title']; ?></span>
    </a>
<?php endforeach; ?>
