<?php

    /**
     * @var \pachno\core\entities\SavedSearch[][] $saved_searches
     * @var framework\Response $pachno_response
     * @var \pachno\core\entities\User $pachno_user
     */

    use pachno\core\entities\tables\Issues;
    use pachno\core\entities\tables\SavedSearches;
    use pachno\core\framework\Context;

    $saved_searches = SavedSearches::getTable()->getAllSavedSearchesByUserIDAndPossiblyProjectID(Context::getUser()->getID(), Context::getCurrentProject()->getID());
    $recent_issues = Issues::getSessionIssues();


?>
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
