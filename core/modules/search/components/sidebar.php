<?php

    use pachno\core\entities\SavedSearch;
    use pachno\core\entities\User;
    use pachno\core\framework\Context;

    /**
     * @var User $pachno_user
     * @var SavedSearch[][] $savedsearches
     */

?>
<nav class="sidebar<?php if ($hide): ?> collapsed<?php endif; ?>" id="search-sidebar" data-project-id="<?= (Context::isProjectContext()) ? Context::getCurrentProject()->getId() : ''; ?>">
    <div class="list-mode">
        <div class="header"><?= __('Predefined searches'); ?></div>
        <?php if (Context::isProjectContext()): ?>
            <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES; ?>">
                <?= link_tag(make_url('project_open_issues', array('project_key' => Context::getCurrentProject()->getKey())), __('Open issues for this project'), ['class' => 'name']); ?>
                <span class="count-badge">-</span>
                <?= link_tag(make_url('project_open_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
            </div>
            <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_PROJECT_OPEN_ISSUES_INCLUDING_SUBPROJECTS; ?>">
                <?= link_tag(make_url('project_allopen_issues', array('project_key' => Context::getCurrentProject()->getKey())), __('Open issues (including subprojects)'), ['class' => 'name']); ?>
                <span class="count-badge">-</span>
                <?= link_tag(make_url('project_allopen_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
            </div>
            <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES; ?>">
                <?= link_tag(make_url('project_closed_issues', array('project_key' => Context::getCurrentProject()->getKey())), __('Closed issues for this project'), ['class' => 'name']); ?>
                <?= link_tag(make_url('project_closed_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
            </div>
            <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_PROJECT_CLOSED_ISSUES_INCLUDING_SUBPROJECTS; ?>">
                <?= link_tag(make_url('project_allclosed_issues', array('project_key' => Context::getCurrentProject()->getKey())), __('Closed issues (including subprojects)'), ['class' => 'name']); ?>
                <?= link_tag(make_url('project_allclosed_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
            </div>
            <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_PROJECT_WISHLIST; ?>">
                <?= link_tag(make_url('project_wishlist_issues', array('project_key' => Context::getCurrentProject()->getKey())), __('Project wishlist'), ['class' => 'name']); ?>
                <span class="count-badge">-</span>
                <?= link_tag(make_url('project_wishlist_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
            </div>
            <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_PROJECT_MILESTONE_TODO; ?>">
                <?= link_tag(make_url('project_milestone_todo_list', array('project_key' => Context::getCurrentProject()->getKey())), __('Milestone todo-list for this project'), ['class' => 'name']); ?>
                <?= link_tag(make_url('project_milestone_todo_list', array('project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
            </div>
            <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_PROJECT_MOST_VOTED; ?>">
                <?= link_tag(make_url('project_most_voted_issues', array('project_key' => Context::getCurrentProject()->getKey())), __('Most voted for issues'), ['class' => 'name']); ?>
                <?= link_tag(make_url('project_most_voted_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
            </div>
            <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_PROJECT_REPORTED_THIS_MONTH; ?>">
                <?= link_tag(make_url('project_month_issues', array('project_key' => Context::getCurrentProject()->getKey())), __('Issues reported this month'), ['class' => 'name']); ?>
                <span class="count-badge">-</span>
                <?= link_tag(make_url('project_month_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
            </div>
            <?php if (!$pachno_user->isGuest()): ?>
                <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_MY_REPORTED_ISSUES; ?>">
                    <?= link_tag(make_url('project_my_reported_issues', array('project_key' => Context::getCurrentProject()->getKey())), __('Issues reported by me'), ['class' => 'name']); ?>
                    <span class="count-badge">-</span>
                    <?= link_tag(make_url('project_my_reported_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
                </div>
                <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES; ?>">
                    <?= link_tag(make_url('project_my_assigned_issues', array('project_key' => Context::getCurrentProject()->getKey())), __('Open issues assigned to me'), ['class' => 'name']); ?>
                    <span class="count-badge">-</span>
                    <?= link_tag(make_url('project_my_assigned_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
                </div>
                <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES; ?>">
                    <?= link_tag(make_url('project_my_teams_assigned_issues', array('project_key' => Context::getCurrentProject()->getKey())), __('Open issues assigned to my teams'), ['class' => 'name']); ?>
                    <span class="count-badge">-</span>
                    <?= link_tag(make_url('project_my_teams_assigned_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
                </div>
            <?php endif; ?>
        <?php elseif (!$pachno_user->isGuest()): ?>
            <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_MY_REPORTED_ISSUES; ?>">
                <?= link_tag(make_url('my_reported_issues'), __('Issues reported by me'), ['class' => 'name']); ?>
                <span class="count-badge">-</span>
                <?= link_tag(make_url('my_reported_issues', array('format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
            </div>
            <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_MY_ASSIGNED_OPEN_ISSUES; ?>">
                <?= link_tag(make_url('my_assigned_issues'), __('Open issues assigned to me'), ['class' => 'name']); ?>
                <span class="count-badge">-</span>
                <?= link_tag(make_url('my_assigned_issues', array('format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
            </div>
            <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_MY_OWNED_OPEN_ISSUES; ?>">
                <?= link_tag(make_url('my_owned_issues'), __('Open issues owned by me'), ['class' => 'name']); ?>
                <span class="count-badge">-</span>
                <?= link_tag(make_url('my_owned_issues', array('format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
            </div>
            <?php if ($pachno_user->hasTeams()): ?>
                <div class="list-item" data-search-id="predefined_<?= SavedSearch::PREDEFINED_SEARCH_TEAM_ASSIGNED_OPEN_ISSUES; ?>">
                    <?= link_tag(make_url('my_teams_assigned_issues'), __('Open issues assigned to my teams'), ['class' => 'name']); ?>
                    <span class="count-badge">-</span>
                    <?= link_tag(make_url('my_teams_assigned_issues', array('format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'), 'class' => 'icon')); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="header"><?= (Context::isProjectContext()) ? __('Your saved searches for this project') : __('Your saved searches'); ?></div>
        <?php if (count($savedsearches['user']) > 0): ?>
            <?php foreach ($savedsearches['user'] as $saved_search): ?>
                <div id="saved_search_<?= $saved_search->getID(); ?>_container" class="list-item" data-search-id="<?= $saved_search->getID(); ?>">
                    <div style="clear: both;">
                        <?= link_tag(make_url('search', array('saved_search' => $saved_search->getID(), 'search' => true, 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'))); ?>
                        <div class="action_icons">
                            <?= link_tag(make_url('search', array('saved_search' => $saved_search->getID(), 'search' => 0)) . '#edit_modal', fa_image_tag('edit'), array('title' => __('Edit saved search'))); ?>
                            <?= javascript_link_tag(image_tag('icon_delete.png', array('title' => __('Delete saved search'))), array('onclick' => "$('#delete_search_" . $saved_search->getID() . "').toggle();")); ?>
                        </div>
                        <span class="count-badge">-</span>
                        <?= link_tag(make_url('search', array('saved_search' => $saved_search->getID(), 'search' => true)), __($saved_search->getName())); ?>
                    </div>
                    <div class="rounded_box white shadowed" style="position: absolute; width: 300px; display: none;" id="delete_search_<?= $saved_search->getID(); ?>">
                        <div class="header"><?= __('Do you really want to delete this saved search?'); ?></div>
                        <div class="content">
                            <?= __('This action cannot be reverted. Note: this will not modify any issues affected by this search'); ?>
                            <div style="text-align: right; margin-top: 10px;">
                                <?= image_tag('spinning_16.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'delete_search_' . $saved_search->getID() . '_indicator')); ?>
                                <input type="submit" onclick="Pachno.Search.deleteSavedSearch('<?= make_url('search', array('saved_search_id' => $saved_search->getID(), 'search' => 0, 'delete_saved_search' => true)); ?>', <?= $saved_search->getID(); ?>);" value="<?= __('Yes, delete'); ?>" style="font-weight: bold;">
                                <?= __('%yes_delete or %cancel', array('%yes_delete' => '', '%cancel' => javascript_link_tag(__('cancel'), array('onclick' => "$('#delete_search_" . $saved_search->getID() . "').toggle();")))); ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($saved_search->getDescription() != ''): ?>
                        <div style="clear: both; padding: 0 0 10px 3px;"><?= $saved_search->getDescription(); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="list-item disabled" id="no_public_saved_searches">
                <span class="icon"><?= fa_image_tag('info-circle'); ?></span>
                <span class="name"><?= __('Your saved searches will show up here'); ?></span>
            </div>
        <?php endif; ?>
        <div class="header"><?= (Context::isProjectContext()) ? __('Public saved searches for this project') : __('Public saved searches'); ?></div>
        <?php if (count($savedsearches['public']) > 0): ?>
            <?php foreach ($savedsearches['public'] as $saved_search): ?>
                <div id="saved_search_<?= $saved_search->getID(); ?>_container" class="list-item" data-search-id="<?= $saved_search->getID(); ?>">
                    <div style="clear: both;">
                        <?php if (Context::isProjectContext()): ?>
                            <?= link_tag(make_url('project_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'saved_search' => $saved_search->getID(), 'search' => true, 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'))); ?>
                            <?php if ($pachno_user->canCreatePublicSearches(Context::getCurrentProject())): ?>
                                <div class="action_icons">
                                    <?= link_tag(make_url('project_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'saved_search' => $saved_search->getID(), 'search' => 0)) . '#edit_modal', fa_image_tag('edit'), array('title' => __('Edit saved search'))); ?>
                                    <?= javascript_link_tag(image_tag('icon_delete.png', array('title' => __('Delete saved search'))), array('onclick' => "$('#delete_search_" . $saved_search->getID() . "').toggle();")); ?>
                                </div>
                            <?php endif; ?>
                            <span class="count-badge">-</span>
                            <?= link_tag(make_url('project_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'saved_search' => $saved_search->getID(), 'search' => true)), __($saved_search->getName())); ?>
                        <?php else: ?>
                            <?= link_tag(make_url('search', array('saved_search' => $saved_search->getID(), 'search' => true, 'format' => 'rss')), fa_image_tag('rss-square', ['class' => 'rss-icon']), array('title' => __('Download feed'))); ?>
                            <?php if ($pachno_user->canCreatePublicSearches(Context::getCurrentProject())): ?>
                                <div class="action_icons">
                                    <?= javascript_link_tag(image_tag('icon_delete.png', array('title' => __('Delete saved search'))), array('onclick' => "$('#delete_search_" . $saved_search->getID() . "').toggle();")); ?>
                                    <?= link_tag(make_url('search', array('saved_search' => $saved_search->getID(), 'search' => 0)) . '#edit_modal', fa_image_tag('edit'), array('title' => __('Edit saved search'))); ?>
                                </div>
                            <?php endif; ?>
                            <?= link_tag(make_url('search', array('saved_search' => $saved_search->getID(), 'search' => true)), __($saved_search->getName())); ?>
                        <?php endif; ?>
                    </div>
                    <div class="rounded_box white shadowed" style="position: absolute; width: 300px; display: none;" id="delete_search_<?= $saved_search->getID(); ?>">
                        <div class="header"><?= __('Do you really want to delete this saved search?'); ?></div>
                        <div class="content">
                            <?= __('This action cannot be reverted. Note: this will not modify any issues affected by this search'); ?>
                            <div style="text-align: right; margin-top: 10px;">
                                <?= image_tag('spinning_16.gif', array('style' => 'margin-left: 5px; display: none;', 'id' => 'delete_search_' . $saved_search->getID() . '_indicator')); ?>
                                <?php if (Context::isProjectContext()): ?>
                                    <input type="submit" onclick="Pachno.Search.deleteSavedSearch('<?= make_url('project_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'saved_search_id' => $saved_search->getID(), 'search' => 0, 'delete_saved_search' => true)); ?>', <?= $saved_search->getID(); ?>);" value="<?= __('Yes, delete'); ?>" style="font-weight: bold;">
                                <?php else: ?>
                                    <input type="submit" onclick="Pachno.Search.deleteSavedSearch('<?= make_url('search', array('saved_search_id' => $saved_search->getID(), 'search' => 0, 'delete_saved_search' => true)); ?>', <?= $saved_search->getID(); ?>);" value="<?= __('Yes, delete'); ?>" style="font-weight: bold;">
                                <?php endif; ?>
                                <?= __('%yes_delete or %cancel', array('%yes_delete' => '', '%cancel' => javascript_link_tag(__('cancel'), array('onclick' => "$('#delete_search_" . $saved_search->getID() . "').toggle();")))); ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($saved_search->getDescription() != ''): ?>
                        <div style="clear: both; padding: 0 0 10px 3px;"><?= $saved_search->getDescription(); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="list-item disabled" id="no_public_saved_searches">
                <span class="icon"><?= fa_image_tag('info-circle'); ?></span>
                <span class="name"><?= __('Public saved searches will show up here'); ?></span>
            </div>
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
