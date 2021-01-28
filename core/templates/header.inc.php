<?php

    use pachno\core\entities\Project;
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
    if ($pachno_response->getPage() === 'home' || in_array($pachno_routing->getCurrentRoute()->getModuleName(), ['project', 'search'])) {
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
        <div class="menu-toggler-container">
            <button class="button icon secondary menu-toggler">
                <?= fa_image_tag('bars', ['class' => 'icon']); ?>
            </button>
        </div>
        <a class="logo" href="<?= $link; ?>">
            <?php echo image_tag(Settings::getHeaderIconUrl(), ['class' => 'logo-icon'], true); ?>
            <span id="logo_name" class="logo_name"><?php echo Settings::getSiteHeaderName() ?? 'Pachno'; ?></span>
        </a>
        <?php if (!Settings::isSingleProjectTracker()): ?>
            <a class="<?php if ($selected_tab == 'projects') echo ' selected'; ?>" href="<?= make_url('home'); ?>">
                <?= fa_image_tag('list-alt', ['class' => 'icon'], 'far'); ?>
                <span class="name"><?= __('Projects'); ?></span>
            </a>
        <?php endif; ?>
        <?php Event::createNew('core', 'header_menu_entries')->trigger(); ?>
        <a class="<?php if ($selected_tab == 'teams') echo 'selected'; ?>" href="<?= make_url('home'); ?>">
            <?= fa_image_tag('users', ['class' => 'icon']); ?>
            <span class="name"><?= __('Teams and clients'); ?></span>
        </a>
        <div id="quicksearch-container">
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo (\pachno\core\framework\Context::isProjectContext()) ? make_url('search', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())) : make_url('search'); ?>" method="get" name="quicksearchform" id="quicksearchform">
                <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                <input type="search" name="search_value" accesskey="f" placeholder="<?php echo __('Quick actions'); ?>"><div id="searchfor_autocomplete_choices" class="autocomplete rounded_box"></div>
            </form>
        </div>
        <a id="header_config_link" class="<?php if (in_array(\pachno\core\framework\Context::getRouting()->getCurrentRoute()->getModuleName(), ['configuration', 'import'])) echo ' selected'; ?>" href="<?= make_url('configure'); ?>">
            <?= fa_image_tag('cog', ['class' => 'icon']); ?>
        </a>
        <?php if (!$pachno_user->isGuest()): ?>
            <div class="notifications-container dropper-container" id="user_notifications_container">
                <a href="javascript:void(0);" class="dropper">
                    <?= fa_image_tag('bell', ['class' => 'icon']); ?>
                    <span id="user_notifications_count" class="notifications-indicator"><?= image_tag('spinning_16_white.gif'); ?></span>
                </a>
                <div class="notifications dropdown-container list-mode" id="user_notifications" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'notifications']); ?>">
                    <div class="header">
                        <span><?= __('Your notifications'); ?></span>
                        <button class="button icon secondary" href="javascript:void(0);" onclick="Pachno.Main.Notifications.markAllRead();"><?= fa_image_tag('check'); ?></button>
                    </div>
                    <div id="user_notifications_list" class="nano"></div>
                    <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="dropper-container">
            <?php if ($pachno_user->isGuest()): ?>
                <a href="javascript:void(0);" <?php if (\pachno\core\framework\Context::getRouting()->getCurrentRoute()->getName() != 'auth_login_page'): ?>data-login-section="#regular_login_container" class="trigger-show-login"<?php endif; ?>><?= fa_image_tag('user'); ?><span><?= __('Log in'); ?></span></a>
            <?php else: ?>
                <button href="javascript:void(0);" class="button secondary dropper header-user-info avatar-container">
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
                    <a href="<?= make_url('dashboard'); ?>" class="list-item">
                        <?= fa_image_tag('columns', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Your dashboard'); ?></span>
                    </a>
                    <?php if ($pachno_response->getPage() == 'dashboard'): ?>
                        <a href="javascript:void(0);" onclick="$$('.dashboard').each(function (elm) { elm.toggleClass('editable');});" class="list-item">
                            <?= fa_image_tag('edit', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Customize your dashboard'); ?></span>
                        </a>
                    <?php endif; ?>
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
                    <a href="https://pachno.com/help/<?= \pachno\core\framework\Context::getRouting()->getCurrentRoute()->getName(); ?>" id="global_help_link" class="list-item">
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
            <?php endif; ?>
        </div>
    </div>
    <nav class="menu-strip">
        <?= Event::createNew('core', 'header_menu_strip', $pachno_routing->getCurrentRoute())->triggerUntilProcessed()->getReturnValue(); ?>
    </nav>
    <?php if (!Settings::isMaintenanceModeEnabled()): ?>
        <?php Event::createNew('core', 'header_menu_end')->trigger(); ?>
    <?php endif; ?>
</header>
