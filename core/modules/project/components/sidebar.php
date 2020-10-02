<?php

    use pachno\core\framework;
    use pachno\core\framework\Context;

    /**
     * @var framework\Response $pachno_response
     * @var \pachno\core\entities\User $pachno_user
     */

    if (!isset($dashboard)) $dashboard = '';

?>
<nav class="project-context sidebar <?= (isset($collapsed) && $collapsed) ? 'collapsed' : ''; ?>" id="project-menu" data-project-id="<?= (\pachno\core\framework\Context::isProjectContext()) ? \pachno\core\framework\Context::getCurrentProject()->getId() : ''; ?>">
    <div class="list-mode">
        <?php include_component('project/projectheader', ['subpage' => (isset($dashboard) && $dashboard instanceof \pachno\core\entities\Dashboard) ? $dashboard->getName() : $dashboard, 'show_back' => ($show_back) ?? $pachno_response->getPage() == 'project_settings']); ?>
        <?php if ($pachno_response->getPage() == 'project_settings'): ?>
            <div id="project_config_menu" class="tab-switcher">
                <a href="javascript:void(0);" data-tab-target="information" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'info') echo 'selected'; ?>">
                    <?= fa_image_tag('edit', ['class' => 'icon'], 'far'); ?>
                    <span class="name"><?= __('Details'); ?></span>
                </a>
                <a href="javascript:void(0);" data-tab-target="settings" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'settings') echo 'selected'; ?>">
                    <?= fa_image_tag('list-alt', ['class' => 'icon'], 'far'); ?>
                    <span class="name"><?= __('Settings'); ?></span>
                </a>
                <a href="javascript:void(0);" data-tab-target="developers" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'developers') echo 'selected'; ?>">
                    <?= fa_image_tag('users', ['class' => 'icon']); ?>
                    <span class="name"><?= __('People'); ?></span>
                </a>
                <a href="javascript:void(0);" data-tab-target="permissions" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'permissions') echo 'selected'; ?>">
                    <?= fa_image_tag('user-shield', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Roles and access'); ?></span>
                </a>
                <div class="list-item separator"></div>
                <a href="javascript:void(0);" data-tab-target="client" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'client') echo 'selected'; ?>">
                    <?= fa_image_tag('user-tie', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Client'); ?></span>
                </a>
                <div class="list-item separator"></div>
                <a href="javascript:void(0);" data-tab-target="hierarchy" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'hierarchy') echo 'selected'; ?>">
                    <?= fa_image_tag('boxes', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Editions and components'); ?></span>
                </a>
                <a href="javascript:void(0);" data-tab-target="issues_and_workflow" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'issues_and_workflow') echo 'selected'; ?>">
                    <?= fa_image_tag('edit', ['class' => 'icon'], 'far'); ?>
                    <span class="name"><?= __('Issues and workflow'); ?></span>
                </a>
                <a href="javascript:void(0);" data-tab-target="links" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'links') echo 'selected'; ?>">
                    <?= fa_image_tag('link', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Links'); ?></span>
                </a>
                <div class="list-item separator"></div>
                <?php \pachno\core\framework\Event::createNew('core', 'config_project_tabs_other')->trigger(array('selected_tab' => $selected_tab)); ?>
            </div>
        <?php else: ?>
            <a href="<?= make_url('project_dashboard', ['project_key' => Context::getCurrentProject()->getKey()]); ?>" class="list-item expandable <?php if (in_array($pachno_response->getPage(), array('project_dashboard', 'project_scrum_sprint_details', 'project_timeline', 'project_team', 'project_roadmap', 'project_releases', 'project_statistics', 'vcs_commitspage'))): ?>expanded<?php endif; ?>">
                <?= fa_image_tag('columns', ['class' => 'icon']); ?>
                <span class="name"><?= __('Project details'); ?></span>
                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
            </a>
            <div id="project_information_menu" class="submenu">
                <?php include_component('project/projectinfolinks', array('submenu' => true)); ?>
            </div>
            <?php if ($pachno_user->canSearchForIssues()): ?>
                <?php if (in_array($pachno_response->getPage(), ['project_issues', 'viewissue'])): ?>
                    <a href="<?= make_url('project_issues', ['project_key' => Context::getCurrentProject()->getKey()]); ?>" class="list-item expandable expanded">
                        <?= fa_image_tag('file-alt', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Issues'); ?></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                    </a>
                    <div class="submenu">
                        <?php include_component('project/searchmenu'); ?>
                    </div>
                <?php else: ?>
                    <div class="list-item">
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
            <?php endif; ?>
            <?php framework\Event::createNew('core', 'templates/headermainmenu::projectmenulinks', Context::getCurrentProject())->trigger(); ?>
            <?php if ($pachno_user->canEditProjectDetails(Context::getCurrentProject())): ?>
                <?php if (Context::getCurrentProject()->isBuildsEnabled()): ?>
                    <a href="<?= make_url('project_release_center', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())); ?>" class="list-item <?php if ($pachno_response->getPage() == 'project_release_center') echo 'selected'; ?>">
                        <?= fa_image_tag('file-archive', ['class' => 'icon']); ?>
                        <span class="name"><?=  __('Release center'); ?></span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php if (!$pachno_user->isGuest()): ?>
        <?php include_component('main/onboarding_invite'); ?>
    <?php endif; ?>
    <div class="collapser list-mode">
        <a class="list-item" href="javascript:void(0);">
            <span class="icon"><?= fa_image_tag('angle-double-left'); ?></span>
            <span class="name"><?= __('Toggle sidebar'); ?></span>
        </a>
    </div>
</nav>
