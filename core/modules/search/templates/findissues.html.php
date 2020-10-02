<?php

    use pachno\core\framework;
    use pachno\core\framework\Context;

    /**
     * @var \pachno\core\entities\User $pachno_user
     * @var framework\Response $pachno_response
     * @var framework\Request $pachno_request
     * @var boolean $show_results
     * @var string $searchtitle
     * @var string $search_message
     * @var string $search_error
     */

    if ($show_results)
    {
        $pachno_response->setTitle($searchtitle);
    }
    else
    {
        $pachno_response->setTitle((Context::isProjectContext()) ? __('Find issues for %project_name', ['%project_name' => Context::getCurrentProject()->getName()]) : __('Find issues'));
    }
    if (Context::isProjectContext())
    {
        $pachno_response->addBreadcrumb(__('Issues'), make_url('project_issues', ['project_key' => Context::getCurrentProject()->getKey()]));
        $pachno_response->addFeed(make_url('project_open_issues', ['project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss']), __('Open issues for %project_name', ['%project_name' => Context::getCurrentProject()->getName()]));
        $pachno_response->addFeed(make_url('project_allopen_issues', ['project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss']), __('Open issues for %project_name (including subprojects)', array('%project_name' => Context::getCurrentProject()->getName())));
        $pachno_response->addFeed(make_url('project_closed_issues', ['project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss']), __('Closed issues for %project_name', array('%project_name' => Context::getCurrentProject()->getName())));
        $pachno_response->addFeed(make_url('project_allclosed_issues', ['project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss']), __('Closed issues for %project_name (including subprojects)', array('%project_name' => Context::getCurrentProject()->getName())));
        $pachno_response->addFeed(make_url('project_wishlist_issues', ['project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss']), __('Wishlist for %project_name', array('%project_name' => Context::getCurrentProject()->getName())));
        $pachno_response->addFeed(make_url('project_milestone_todo_list', ['project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss']), __('Milestone todo-list for %project_name', array('%project_name' => Context::getCurrentProject()->getName())));
        $pachno_response->addFeed(make_url('project_month_issues', ['project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss']), __('Issues reported for %project_name this month', array('%project_name' => Context::getCurrentProject()->getName())));
        $pachno_response->addFeed(make_url('project_last_issues', ['project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss', 'units' => 30, 'time_unit' => 'days']), __('Issues reported for %project_name last 30 days', array('%project_name' => Context::getCurrentProject()->getName())));
        if (!$pachno_user->isGuest())
        {
            $pachno_response->addFeed(make_url('project_my_reported_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss')), __('Issues reported by me') . ' ('. Context::getCurrentProject()->getName().')');
            $pachno_response->addFeed(make_url('project_my_assigned_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss')), __('Open issues assigned to me') . ' ('. Context::getCurrentProject()->getName().')');
            $pachno_response->addFeed(make_url('project_my_teams_assigned_issues', array('project_key' => Context::getCurrentProject()->getKey(), 'format' => 'rss')), __('Open issues assigned to my teams') . ' ('. Context::getCurrentProject()->getName().')');
        }
    }
    else
    {
        $pachno_response->addBreadcrumb(__('Issues'), make_url('search'));
        if (!$pachno_user->isGuest())
        {
            $pachno_response->addFeed(make_url('my_reported_issues', array('format' => 'rss')), __('Issues reported by me'));
            $pachno_response->addFeed(make_url('my_assigned_issues', array('format' => 'rss')), __('Open issues assigned to you'));
            $pachno_response->addFeed(make_url('my_teams_assigned_issues', array('format' => 'rss')), __('Open issues assigned to your teams'));
        }
    }

?>
<div class="content-with-sidebar">
    <?php if (Context::isProjectContext()): ?>
        <?php include_component('project/sidebar', ['dashboard' => __('Find issues')]); ?>
    <?php else: ?>
        <?php include_component('search/sidebar', ['hide' => $show_results]); ?>
    <?php endif; ?>
    <div id="find_issues">
        <?php if ($search_error !== null): ?>
            <div class="redbox" style="margin: 0; vertical-align: middle;" id="search_error">
                <div class="header"><?php echo $search_error; ?></div>
            </div>
        <?php endif; ?>
        <?php if ($search_message == 'saved_search'): ?>
            <?php include_component('main/hideableInfoBoxModal', array('key' => 'save_search_saved', 'template' => 'search/infobox_saved_search_saved', 'title' => __('Search details have been saved'), 'button_label' => __('Got it!'))); ?>
        <?php elseif ($search_message !== null): ?>
            <div class="greenbox" style="margin: 0; vertical-align: middle;" id="search_message">
                <div class="header"><?php echo $search_message; ?></div>
            </div>
        <?php endif; ?>
        <div class="results_header">
            <span id="findissues_search_title" style="<?php if (!$searchtitle) echo 'display: none'; ?>"><?php echo $searchtitle; ?></span>
            <span id="findissues_search_generictitle" style="<?php if ($searchtitle) echo 'display: none'; ?>"><?php echo __("Find issues"); ?></span>
            <span id="findissues_num_results" class="count-badge" style="<?php if (!$show_results) echo 'display: none;'; ?>"><?php echo __('%number_of issue(s)', array('%number_of' => '<span id="findissues_num_results_span">-</span>')); ?></span>
            <?php if (!$pachno_user->isGuest()) include_component('search/bulkactions', array('mode' => 'bottom')); ?>
        </div>
        <?php include_component('search/searchbuilder', compact('search_object', 'show_results')); ?>
        <div id="search_results_container">
            <div id="search_results_loading_indicator" style="display: none;"><?php echo image_tag('spinning_30.gif'); ?></div>
            <div id="search-results" class="search-results">
                <div class="onboarding large">
                    <div class="image-container">
                        <?= image_tag('/unthemed/no-issues.png', [], true); ?>
                    </div>
                    <div class="helper-text">
                        <?= __("Maybe you have issues, maybe you don't"); ?><br>
                        <?= __('Start typing or selecting filters above to find out'); ?>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            Pachno.on(Pachno.EVENTS.ready, function () {
                const search = new Search({
                    save_columns_url: "<?= make_url('search_save_column_settings'); ?>",
                    history_url: "<?= (Context::isProjectContext()) ? make_url('project_issues', array('project_key' => Context::getCurrentProject()->getKey())) : make_url('search'); ?>",
                    dynamic_callback_url: "<?= make_url('search_filter_getdynamicchoices'); ?>",
                    project_id: <?= (Context::isProjectContext()) ? Context::getCurrentProject()->getID() : 0; ?>,
                    show_results: <?= ($show_results) ? 'true' : 'false'; ?>
                });
                window.currentSearch = search;
            });

            //var Pachno;
            //require(['domReady', 'pachno/index'], function (domReady, pachno_index_js) {
            //    domReady(function () {
            //        Pachno = pachno_index_js;
            //        Pachno.Search.initializeFilters();
            //        <?php //if ($pachno_user->isKeyboardNavigationEnabled()): ?>
            //            Pachno.Search.initializeKeyboardNavigation();
            //        <?php //endif; ?>
            <!--        --><?php //if ($show_results): ?>
            //            setTimeout(function() { Pachno.Search.liveUpdate(true); }, 250);
            //        <?php //else: ?>
            //            Pachno.Search.updateSavedSearchCounts();
            //        <?php //endif; ?>
            //
            //        var hash = window.location.hash;
            //
            //        if (hash != undefined && hash.indexOf('edit_modal') == 1) {
            //            $('#saved_search_details').toggle('block');
            //        }
            //    });
            //});
        </script>
    </div>
</div>