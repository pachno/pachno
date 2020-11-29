<?php

/**
 * @var \pachno\core\entities\User $pachno_user
 */

?>
<nav class="header_menu" id="header_userinfo">
    <ul>
        <?php if ($pachno_user->canAccessConfigurationPage()): ?>
            <li id="header_config_link" class="<?php if (in_array(\pachno\core\framework\Context::getRouting()->getCurrentRoute()->getModuleName(), ['configuration', 'import'])) echo ' selected'; ?>">
                <?= link_tag(make_url('configure'), fa_image_tag('cog')); ?>
            </li>
        <?php endif; ?>
        <?php if (!$pachno_user->isGuest()): ?>
            <li class="user_notifications_container" id="user_notifications_container">
                <div id="user_notifications_count" class="notifications-indicator"><?= image_tag('spinning_16_white.gif'); ?></div>
                <a href="javascript:void(0);" class="dropper"><?= fa_image_tag('bell'); ?></a>
                <div class="popup_box tab_menu_dropdown notifications dynamic_menu_link" id="user_notifications" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'notifications']); ?>">
                    <div class="header with-link">
                        <span><?= __('Your notifications'); ?></span>
                        <a class="icon-link" href="javascript:void(0);" onclick="Pachno.Main.Notifications.markAllRead();"><?= fa_image_tag('check'); ?></a>
                    </div>
                    <div id="user_notifications_list_wrapper_nano" class="nano">
                        <div id="user_notifications_list_wrapper" class="nano-content">
                            <ul id="user_notifications_list" data-notifications-url="<?= make_url('get_partial_for_backdrop', ['key' => 'notifications']); ?>" data-offset="25"></ul>
                        </div>
                    </div>
                    <?= image_tag('spinning_32.gif', array('id' => 'user_notifications_loading_indicator')); ?>
                </div>
            </li>
        <?php endif; ?>
        <li class="with-dropdown dropper-container <?php if ($pachno_request->hasCookie('original_username')): ?>temporarily_switched<?php endif; ?> <?php if ($pachno_routing->getCurrentRoute()->getName() == 'account') echo 'selected'; ?>" id="header_usermenu_link">
            <?php if ($pachno_user->isGuest()): ?>
                <a href="javascript:void(0);" <?php if (\pachno\core\framework\Context::getRouting()->getCurrentRoute()->getName() != 'auth_login_page'): ?>data-login-section="#regular_login_container" class="trigger-show-login"<?php endif; ?>><?= fa_image_tag('user'); ?><span><?= __('Log in'); ?></span></a>
            <?php else: ?>
                <a href="javascript:void(0);" class="dropper header-user-info">
                    <span class="header_avatar">
                        <?php if (\pachno\core\framework\Context::getRouting()->getCurrentRoute()->getName() != 'auth_login_page'): ?>
                            <?= image_tag($pachno_user->getAvatarURL(true), array('alt' => '[avatar]', 'id' => 'header_avatar'), true); ?>
                        <?php else: ?>
                            <?= image_tag($pachno_user->getAvatarURL(true), array('alt' => '[avatar]', 'id' => 'header_avatar'), true); ?>
                        <?php endif; ?>
                    </span>
                    <span class="header-user-name">
                        <span class="header-user-name-name"><?= $pachno_user->getName(); ?></span>
                        <span class="header-user-name-username">@<?= $pachno_user->getUsername(); ?></span>
                    </span>
                    <?php if (\pachno\core\framework\Context::getRouting()->getCurrentRoute()->getName() != 'auth_login_page') echo fa_image_tag('angle-down', ['class' => 'dropdown-indicator']); ?>
                </a>
            <?php endif; ?>
            <?php if (\pachno\core\framework\Context::getRouting()->getCurrentRoute()->getName() != 'auth_login_page'): ?>
                <?php if (\pachno\core\framework\Event::createNew('core', 'header_usermenu_decider')->trigger()->getReturnValue() !== false): ?>
                    <?php if (!$pachno_user->isGuest()): ?>
                        <div class="dropdown-container popup_box" id="user_menu">
                            <div class="list-mode">
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
                                <?php \pachno\core\framework\Event::createNew('core', 'user_dropdown_reg')->trigger(); ?>
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
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </li>
        <?php \pachno\core\framework\Event::createNew('core', 'after_header_userinfo')->trigger(); ?>
    </ul>
</nav>
