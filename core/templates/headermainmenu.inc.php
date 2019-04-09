<?php

    use pachno\core\framework;

    /**
     * @var \pachno\core\entities\User $pachno_user
     * @var framework\Response $pachno_response
     */

    $saved_searches = \pachno\core\entities\tables\SavedSearches::getTable()->getAllSavedSearchesByUserIDAndPossiblyProjectID(framework\Context::getUser()->getID());
    $recent_issues = \pachno\core\entities\tables\Issues::getSessionIssues();

?>
<nav class="header_menu" id="main_menu">
    <ul>
        <?php if (!framework\Settings::isSingleProjectTracker()): ?>
            <li<?php if ($pachno_response->getPage() == 'home'): ?> class="selected"<?php endif; ?>>
                <?= link_tag(make_url('home'), fa_image_tag('home') . '<span>'.__('Projects').'</span>'); ?>
            </li>
        <?php endif; ?>
        <?php if (!$pachno_user->isGuest() && !framework\Settings::isSingleProjectTracker()): ?>
            <li class="<?php if ($pachno_response->getPage() == 'dashboard'): ?>selected<?php endif; ?>">
                <?= link_tag(make_url('dashboard'), fa_image_tag('columns') . '<span>'.__('Dashboard').'</span>'); ?>
            </li>
        <?php endif; ?>
        <?php if (!$pachno_user->isGuest() && $pachno_user->canSearchForIssues()): ?>
            <li class="with-dropdown <?php if (in_array($pachno_response->getPage(), array('project_issues', 'viewissue'))): ?>selected<?php endif; ?>">
                <?= link_tag(make_url('search'), fa_image_tag('file-alt') . __('Issues') . fa_image_tag('angle-down', ['class' => 'dropdown-indicator']), ['class' => 'dropper']); ?>
                <div id="issues_menu" class="tab_menu_dropdown popup_box two-columns">
                    <ul>
                        <li class="header"><?= __('Predefined searches'); ?></li>
                        <li><?= link_tag(make_url('my_reported_issues'), fa_image_tag('search') . __('Issues reported by me')); ?></li>
                        <li><?= link_tag(make_url('my_assigned_issues'), fa_image_tag('search') . __('Open issues assigned to me')); ?></li>
                        <li><?= link_tag(make_url('my_owned_issues'), fa_image_tag('search') . __('Open issues owned by me')); ?></li>
                        <?php if ($pachno_user->hasTeams()): ?>
                            <li><?= link_tag(make_url('my_teams_assigned_issues'), fa_image_tag('search') . __('Open issues assigned to my teams')); ?></li>
                        <?php endif; ?>
                        <li class="header"><?= __('Saved searches'); ?></li>
                        <?php if (count($saved_searches['user']) + count($saved_searches['public'])): ?>
                            <?php if (!$pachno_user->isGuest()): ?>
                                <?php foreach ($saved_searches['user'] as $saved_search): ?>
                                    <li><?= link_tag(make_url('search', array('saved_search' => $saved_search->getID(), 'search' => true)), fa_image_tag('user', ['title' => __('This is a saved search only visible to you')], 'far') . __($saved_search->getName())); ?></li>
                                <?php endforeach; ?>
                                <?php if (count($saved_searches['user']) && count($saved_searches['public'])): ?>
                                    <li class="separator"></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php foreach ($saved_searches['public'] as $saved_search): ?>
                                <li><?= link_tag(make_url('project_issues', array('saved_search' => $saved_search->getID(), 'search' => true)), fa_image_tag('search') . __($saved_search->getName())); ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="disabled"><?= __('You have no saved searches'); ?></li>
                        <?php endif; ?>
                    </ul>
                    <ul>
                        <li class="header"><?= __('Recently watched issues'); ?></li>
                        <?php foreach ($recent_issues as $issue): ?>
                            <?php include_component('search/sessionissue', ['issue' => $issue]); ?>
                        <?php endforeach; ?>
                        <?php if (!count($recent_issues)): ?>
                            <li class="disabled" href="javascript:void(0);"><?= __('No recent issues'); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>
        <?php endif; ?>
        <?php if (($pachno_user->hasPageAccess('teamlist') || count($pachno_user->getTeams())) && !is_null(\pachno\core\entities\Team::getAll())): ?>
            <li class="with-dropdown <?php if ($pachno_response->getPage() == 'team'): ?>selected<?php endif; ?>">
                <?= link_tag('javascript:void(0)', fa_image_tag('users') . '<span>'.__('Teams').'</span>' . fa_image_tag('angle-down', ['class' => 'dropdown-indicator']), ['class' => 'dropper']); ?>
                <ul id="team_menu" class="tab_menu_dropdown popup_box">
                    <?php foreach (\pachno\core\entities\Team::getAll() as $team): ?>
                        <?php if (!$team->hasAccess()) continue; ?>
                        <li><?= link_tag(make_url('team_dashboard', array('team_id' => $team->getID())), fa_image_tag('users') . $team->getName()); ?></li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endif; ?>
        <?php if ($pachno_user->hasPageAccess('clientlist') && count($pachno_user->getClients()) && !is_null(\pachno\core\entities\Client::getAll())): ?>
            <li class="with-dropdown <?php if ($pachno_response->getPage() == 'client'): ?>selected<?php endif; ?>">
                <?= link_tag('javascript:void(0)', fa_image_tag('users') . '<span>'.__('Clients').'</span>' . fa_image_tag('angle-down', ['class' => 'dropdown-indicator']), ['class' => 'dropper']); ?>
                <ul id="client_menu" class="tab_menu_dropdown popup_box">
                    <?php foreach (\pachno\core\entities\Client::getAll() as $client): ?>
                        <?php if (!$client->hasAccess()) continue; ?>
                        <li><?= link_tag(make_url('client_dashboard', array('client_id' => $client->getID())), fa_image_tag('users') . $client->getName()); ?></li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endif; ?>
    </ul>
</nav>
