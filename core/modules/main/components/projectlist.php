<?php

/** @var \pachno\core\entities\User $pachno_user */

use pachno\core\framework;

?>
<div class="project_overview">
    <div id="projects_list_tabs_panes">
        <div id="tab_active_pane" data-tab-id="active" style=""></div>
        <?php if (!$pachno_user->isGuest()): ?>
            <div id="tab_archived_pane" data-tab-id="archived" style="display: none;"></div>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">
    Pachno.on(Pachno.EVENTS.ready, (PachnoApplication) => {
        const loadList = function (key) {
            let urls = {
                archived: '<?= $archived_url; ?>',
                active: '<?= $active_url; ?>'
            };
            PachnoApplication.UI.tabSwitcher($('#tab_' + key), key, $('#projects_list_tabs'), true);

            if ($('#tab_' + key + '_pane').html() == '') {
                PachnoApplication.fetch(urls[key], {
                    loading: {indicator: '#project_list_tab_' + key + '_indicator'},
                    success: {
                        update: {element: '#tab_' + key + '_pane'},
                    }
                });
            }
        }

        // Default to active tab, unless archived tab was specified
        // in URL.
        if (window.location.hash === '#tab_archived') {
            loadList('archived');
        } else {
            loadList('active');
        }

        $('body').on('click', '#projects_list_tabs .tab', function (event) {
            event.preventDefault();
            loadList($(this).data('project-category'));
        });

    });
</script>
