<?php

    use pachno\core\framework;
    use pachno\core\framework\Context;

/**
     * @var \pachno\core\entities\SavedSearch[][] $saved_searches
     * @var framework\Response $pachno_response
     * @var \pachno\core\entities\User $pachno_user
     */

    $saved_searches = \pachno\core\entities\tables\SavedSearches::getTable()->getAllSavedSearchesByUserIDAndPossiblyProjectID(Context::getUser()->getID(), Context::getCurrentProject()->getID());
    $recent_issues = \pachno\core\entities\tables\Issues::getSessionIssues();
    if (!isset($dashboard)) $dashboard = '';

?>
<nav class="project-context sidebar <?= (isset($collapsed) && $collapsed) ? 'collapsed' : ''; ?>" id="project-menu" data-project-id="<?= (\pachno\core\framework\Context::isProjectContext()) ? \pachno\core\framework\Context::getCurrentProject()->getId() : ''; ?>">
    <div class="list-mode">
        <?php include_component('project/projectheader', ['subpage' => (isset($dashboard) && $dashboard instanceof \pachno\core\entities\Dashboard) ? $dashboard->getName() : $dashboard, 'show_back' => ($show_back) ?? $pachno_response->getPage() == 'project_settings']); ?>
        <?php if ($pachno_response->getPage() == 'project_settings'): ?>
            <div id="project_config_menu">
                <a id="tab_information" href="javascript:void(0);" onclick="Pachno.UI.tabSwitcher('tab_information', 'project_config_menu');return false;" class="list-item <?php if ($selected_tab == 'info') echo 'selected'; ?>">
                    <?= fa_image_tag('edit', ['class' => 'icon'], 'far'); ?>
                    <span class="name"><?= __('Details'); ?></span>
                </a>
                <a id="tab_settings" href="javascript:void(0);" onclick="Pachno.UI.tabSwitcher('tab_settings', 'project_config_menu');return false;" class="list-item <?php if ($selected_tab == 'settings') echo 'selected'; ?>">
                    <?= fa_image_tag('list-alt', ['class' => 'icon'], 'far'); ?>
                    <span class="name"><?= __('Settings'); ?></span>
                </a>
                <a id="tab_developers" href="javascript:void(0);" onclick="Pachno.UI.tabSwitcher('tab_developers', 'project_config_menu');return false;" class="list-item <?php if ($selected_tab == 'developers') echo 'selected'; ?>">
                    <?= fa_image_tag('users', ['class' => 'icon']); ?>
                    <span class="name"><?= __('People'); ?></span>
                </a>
                <a id="tab_permissions" href="javascript:void(0);" onclick="Pachno.UI.tabSwitcher('tab_permissions', 'project_config_menu');return false;" class="list-item <?php if ($selected_tab == 'permissions') echo 'selected'; ?>">
                    <?= fa_image_tag('user-shield', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Roles and access'); ?></span>
                </a>
                <div class="list-item separator"></div>
                <a id="tab_client" href="javascript:void(0);" onclick="Pachno.UI.tabSwitcher('tab_client', 'project_config_menu');return false;" class="list-item <?php if ($selected_tab == 'client') echo 'selected'; ?>">
                    <?= fa_image_tag('user-tie', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Client'); ?></span>
                </a>
                <div class="list-item separator"></div>
                <a id="tab_hierarchy" href="javascript:void(0);" onclick="Pachno.UI.tabSwitcher('tab_hierarchy', 'project_config_menu');return false;" class="list-item <?php if ($selected_tab == 'hierarchy') echo 'selected'; ?>">
                    <?= fa_image_tag('boxes', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Editions and components'); ?></span>
                </a>
                <a id="tab_issues_and_workflow" href="javascript:void(0);" onclick="Pachno.UI.tabSwitcher('tab_issues_and_workflow', 'project_config_menu');return false;" class="list-item <?php if ($selected_tab == 'issues_and_workflow') echo 'selected'; ?>">
                    <?= fa_image_tag('edit', ['class' => 'icon'], 'far'); ?>
                    <span class="name"><?= __('Issues and workflow'); ?></span>
                </a>
                <a id="tab_links" href="javascript:void(0);" onclick="Pachno.UI.tabSwitcher('tab_links', 'project_config_menu');return false;" class="list-item <?php if ($selected_tab == 'links') echo 'selected'; ?>">
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
                <div class="list-item with-pop-out <?php if (in_array($pachno_response->getPage(), ['project_issues', 'viewissue'])) echo 'selected'; ?>">
                    <a href="<?= make_url('project_issues', ['project_key' => Context::getCurrentProject()->getKey()]); ?>">
                        <?= fa_image_tag('file-alt', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Issues'); ?></span>
                    </a>
                    <div class="dropper-container pop-out-expander">
                        <?= fa_image_tag('angle-right', ['class' => 'dropper']); ?>
                        <div class="dropdown-container interactive_filters_list list-mode columns two-columns from-left pop-out">
                            <div class="column">
                                <div class="header"><?= __('Predefined searches'); ?></div>
                                <?= link_tag(make_url('project_open_issues', array('project_key' => Context::getCurrentProject()->getKey())), fa_image_tag('search', ['class' => 'icon']) . '<span class="name">' . __('Open issues for this project') . '</span>', ['class' => 'list-item']); ?>
                                <?= link_tag(make_url('project_closed_issues', array('project_key' => Context::getCurrentProject()->getKey())), fa_image_tag('search', ['class' => 'icon']) . '<span class="name">' . __('Closed issues for this project') . '</span>', ['class' => 'list-item']); ?>
                                <?= link_tag(make_url('project_wishlist_issues', array('project_key' => Context::getCurrentProject()->getKey())), fa_image_tag('search', ['class' => 'icon']) . '<span class="name">' . __('Wishlist for this project') . '</span>', ['class' => 'list-item']); ?>
                                <?= link_tag(make_url('project_milestone_todo_list', array('project_key' => Context::getCurrentProject()->getKey())), fa_image_tag('search', ['class' => 'icon']) . '<span class="name">' . __('Milestone todo-list for this project') . '</span>', ['class' => 'list-item']); ?>
                                <?= link_tag(make_url('project_most_voted_issues', array('project_key' => Context::getCurrentProject()->getKey())), fa_image_tag('search', ['class' => 'icon']) . '<span class="name">' . __('Most voted for issues') . '</span>', ['class' => 'list-item']); ?>
                                <?= link_tag(make_url('project_month_issues', array('project_key' => Context::getCurrentProject()->getKey())), fa_image_tag('search', ['class' => 'icon']) . '<span class="name">' . __('Issues reported this month') . '</span>', ['class' => 'list-item']); ?>
                                <?= link_tag(make_url('project_last_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'units' => 30, 'time_unit' => 'days')), fa_image_tag('search', ['class' => 'icon']) . '<span class="name">' . __('Issues reported last 30 days') . '</span>', ['class' => 'list-item']); ?>
                                <div class="header"><?= __('Saved searches'); ?></div>
                                <?php if (count($saved_searches['user']) + count($saved_searches['public'])): ?>
                                    <?php if (!$pachno_user->isGuest()): ?>
                                        <?php foreach ($saved_searches['user'] as $savedsearch): ?>
                                            <?= link_tag(make_url('project_issues', ['project_key' => Context::getCurrentProject()->getKey(), 'saved_search' => $savedsearch->getID(), 'search' => true]), fa_image_tag('user', ['title' => __('This is a saved search only visible to you'), 'class' => 'icon'], 'far') . '<span class="name">' . __($savedsearch->getName()) . '</span>', ['class' => 'list-item']); ?>
                                        <?php endforeach; ?>
                                        <?php if (count($saved_searches['user']) && count($saved_searches['public'])): ?>
                                            <div class="separator"></div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php foreach ($saved_searches['public'] as $savedsearch): ?>
                                        <?= link_tag(make_url('project_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'saved_search' => $savedsearch->getID(), 'search' => true)), fa_image_tag('search', ['class' => 'icon']) . '<span class="name">' . __($savedsearch->getName()) . '</span>', ['class' => 'list-item']); ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <a href="javascript:void(0);" class="list-item disabled">
                                        <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                                        <span class="name"><?= __('Saved searches for this project will show here'); ?></span>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="column">
                                <div class="header"><?= __('Recently visited issues'); ?></div>
                                <?php foreach ($recent_issues as $issue): ?>
                                    <?php include_component('search/sessionissue', ['issue' => $issue]); ?>
                                <?php endforeach; ?>
                                <?php if (!count($recent_issues)): ?>
                                    <a href="javascript:void(0);" class="list-item disabled">
                                        <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                                        <span class="name"><?= __("Recently visited issues will appear here"); ?></span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
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
