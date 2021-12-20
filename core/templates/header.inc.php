<?php

    use pachno\core\entities\Permission;
    use pachno\core\entities\User;
    use pachno\core\framework;
    use pachno\core\framework\Event;
    use pachno\core\framework\Settings;

    /**
     * @var User $pachno_user
     * @var framework\Routing $pachno_routing
     * @var framework\Response $pachno_response
     */

    $saved_searches = \pachno\core\entities\tables\SavedSearches::getTable()->getAllSavedSearchesByUserIDAndPossiblyProjectID(framework\Context::getUser()->getID());
    $recent_issues = \pachno\core\entities\tables\Issues::getSessionIssues();
    $link = (Settings::getHeaderLink() == '') ? \pachno\core\framework\Context::getWebroot() : Settings::getHeaderLink();

    $selected_tab = '';
    if ($pachno_response->getPage() === 'home') {
        $selected_tab = 'home';
    } elseif ($pachno_response->getPage() === 'projects_list' || in_array($pachno_routing->getCurrentRoute()->getModuleName(), ['project', 'search'])) {
        $selected_tab = 'projects';
    } elseif ($pachno_response->getPage() === 'teams_dashboard') {
        $selected_tab = 'teams';
    }
    $selected_tab_event = Event::createNew('core', 'header_menu::selectedTab');
    $selected_tab_event->setReturnValue($selected_tab);
    $selected_tab_event->triggerUntilProcessed();
    $selected_tab = $selected_tab_event->getReturnValue();

?>
<header>
    <div class="header-strip">
        <a class="logo" href="<?= $link; ?>">
            <?php echo image_tag(Settings::getHeaderIconUrl(), ['class' => 'logo-icon'], true); ?>
            <span id="logo_name" class="logo_name"><?php echo Settings::getSiteHeaderName() ?? 'Pachno'; ?></span>
        </a>
        <?php if ($pachno_user->hasPermission(Permission::PERMISSION_PAGE_ACCESS_DASHBOARD)): ?>
            <a class="<?php if ($selected_tab == 'home') echo ' selected'; ?>" href="<?= make_url('home'); ?>">
                <?= fa_image_tag('window-restore', ['class' => 'icon']); ?>
                <span class="name"><?= __('Home'); ?></span>
            </a>
        <?php endif; ?>
        <?php if ($pachno_user->hasPermission(Permission::PERMISSION_PAGE_ACCESS_PROJECT_LIST)): ?>
            <a class="<?php if ($selected_tab == 'projects') echo ' selected'; ?>" href="<?= make_url('projects_list'); ?>">
                <?= fa_image_tag('boxes', ['class' => 'icon']); ?>
                <span class="name"><?= __('Projects'); ?></span>
            </a>
        <?php endif; ?>
        <?php Event::createNew('core', 'header_menu_entries')->trigger(); ?>
        <a class="<?php if ($selected_tab == 'teams') echo 'selected'; ?> disabled" href="<?= make_url('home'); ?>" style="display: none;">
            <?= fa_image_tag('users', ['class' => 'icon']); ?>
            <span class="name"><?= __('Teams and clients'); ?><i class="count-badge"><?= __('Disabled in this release'); ?></i></span>
        </a>
        <a href="javascript:void(0);" class="trigger-quicksearch">
            <?= fa_image_tag('search', ['class' => 'icon']); ?>
            <span class="name"><?= __('Press %slash or click here to open the quicksearch', ['%slash' => '<code>/</code>']); ?></span>
        </a>
        <?php if (!$pachno_user->isGuest()): ?>
            <?php if ($pachno_user->canAccessConfigurationPage()): ?>
                <a id="header_config_link" class="only-icon <?php if (in_array(\pachno\core\framework\Context::getRouting()->getCurrentRoute()->getModuleName(), ['configuration', 'import'])) echo ' selected'; ?>" href="<?= make_url('configure'); ?>">
                    <?= fa_image_tag('cog', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Configure Pachno'); ?></span>
                </a>
            <?php endif; ?>
            <div class="notifications-container dropper-container" id="user_notifications_container">
                <a href="javascript:void(0);" class="dropper <?php if (!$pachno_user->getNumberOfUnreadNotifications()) echo 'disabled'; ?> dynamic_menu_link">
                    <?= fa_image_tag('bell', ['class' => 'icon']); ?>
                    <span id="user_notifications_count" class="notifications-indicator"><?= $pachno_user->getNumberOfUnreadNotifications(); ?></span>
                </a>
                <div class="notifications dropdown-container list-mode dynamic_menu populate-once" id="user_notifications" data-menu-url="<?= make_url('get_partial_for_backdrop', ['key' => 'notifications']); ?>">
                    <div class="list-item">
                        <span class="icon">
                            <?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="notifications-container dropper-container" id="user_timers_container">
                <a href="javascript:void(0);" class="dropper <?php if (!$pachno_user->getNumberOfOngoingTimers()) echo 'disabled'; ?> dynamic_menu_link">
                    <?= fa_image_tag('stopwatch', ['class' => 'icon']); ?>
                    <span id="user_timers_count" class="notifications-indicator"><?= $pachno_user->getNumberOfOngoingTimers(); ?></span>
                </a>
                <div class="timers dropdown-container list-mode dynamic_menu" id="user_timers" data-menu-url="<?= make_url('get_partial_for_backdrop', ['key' => 'timers']); ?>">
                    <div class="header">
                        <span><?= __('Ongoing timers'); ?></span>
                    </div>
                    <div class="list-item">
                        <span class="icon">
                            <?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($pachno_user->isGuest()): ?>
            <a class="only-icon" href="<?= make_url('auth_login'); ?>">
                <?= fa_image_tag('user', ['class' => 'icon']); ?>
                <span class="name"><?= __('Log in'); ?></span>
            </a>
        <?php else: ?>
            <div class="dropper-container">
                <button class="button secondary dropper header-user-info avatar-container header-button">
                    <span class="avatar medium">
                        <?= image_tag($pachno_user->getAvatarURL(true), array('alt' => '[avatar]', 'id' => 'header_avatar'), true); ?>
                    </span>
                    <span class="name-container">
                        <span class="header-user-name-name"><?= $pachno_user->getName(); ?></span>
                        <span class="header-user-name-username">@<?= $pachno_user->getUsername(); ?></span>
                    </span>
                    <?php if (\pachno\core\framework\Context::getRouting()->getCurrentRoute()->getName() != 'auth_login_page') echo fa_image_tag('angle-down', ['class' => 'dropdown-indicator']); ?>
                </button>
                <div class="dropdown-container list-mode" id="user_menu">
                    <div class="list-item header multiline user-info">
                        <span class="name">
                            <span class="title"><?= $pachno_user->getRealname(); ?></span>
                            <span class="description">@<?= $pachno_user->getUsername(); ?></span>
                        </span>
                    </div>
                    <a href="<?= make_url('profile_account'); ?>" class="list-item">
                        <?= fa_image_tag('user-md', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Your account'); ?></span>
                    </a>
                    <?php if ($pachno_request->hasCookie('original_username')): ?>
                        <div class="header"><?= __('You are temporarily this user'); ?></div>
                        <a href="<?= make_url('switch_back_user'); ?>" class="list-item">
                            <?= fa_image_tag('switchuser.png'); ?>
                            <span class="name"><?= __('Switch back to original user'); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php Event::createNew('core', 'user_dropdown_reg')->trigger(); ?>
                    <a href="https://pach.no/help/<?= \pachno\core\framework\Context::getRouting()->getCurrentRoute()->getName(); ?>" id="global_help_link" class="list-item">
                        <?= fa_image_tag('question-circle', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Help for this page'); ?></span>
                    </a>
                    <div class="list-item header"><?= __('Your issues'); ?></div>
                    <a href="<?= make_url('my_reported_issues'); ?>" class="list-item">
                        <?= fa_image_tag('search', ['class' => 'icon']); ?>
                        <span class="name"><?=  __('Issues reported by me'); ?></span>
                    </a>
                    <a href="<?= make_url('my_assigned_issues'); ?>" class="list-item">
                        <?= fa_image_tag('search', ['class' => 'icon']); ?>
                        <span class="name"><?=  __('Open issues assigned to me') ; ?></span>
                    </a>
                    <a href="<?= make_url('my_teams_assigned_issues'); ?>" class="list-item">
                        <?= fa_image_tag('search', ['class' => 'icon']); ?>
                        <span class="name"><?=  __('Open issues assigned to my teams'); ?></span>
                    </a>
                    <div class="list-item separator"></div>
                    <a href="<?= make_url('auth_logout'); ?>" class="list-item">
                        <?= fa_image_tag('sign-out-alt', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Logout'); ?></span>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</header>
