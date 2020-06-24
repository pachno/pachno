<?php

/** @var \pachno\core\entities\User $pachno_user */

use pachno\core\framework;

?>
<div class="project_overview">
    <div id="projects_list_tabs" class="fancy-tabs">
        <a class="tab selected" data-project-category="active" id="tab_active" href="javascript:void(0);">
            <?= fa_image_tag('boxes', ['class' => 'icon']); ?>
            <?= fa_image_tag('spinner', ['style' => 'display: none;', 'id' => 'project_list_tab_active_indicator', 'class' => 'icon fa-spin']); ?>
            <span class="name">
                <?= ($pachno_user->isGuest()) ? __('Projects') : __('Active projects'); ?>
            </span>
        </a>
        <?php if (!$pachno_user->isGuest()): ?>
            <a class="tab" id="tab_archived" data-project-category="archived" href="javascript:void(0);">
                <?= fa_image_tag('archive', ['class' => 'icon']); ?>
                <?= fa_image_tag('spinner', ['style' => 'display: none;', 'id' => 'project_list_tab_archived_indicator', 'class' => 'icon fa-spin']); ?>
                <span class="name">
                    <?= __('Archived projects'); ?>
                </span>
            </a>
            <?php if ($pachno_user->isAuthenticated()): ?>
                <div class="spacer"></div>
                <div class="button-container">
                    <?= link_tag(make_url('configure_projects'), fa_image_tag('cog'), ['class' => 'button icon secondary']); ?>
                    <?php if ($list_mode !== 'client' && $pachno_user->canAccessConfigurationPage(framework\Settings::CONFIGURATION_SECTION_PROJECTS) && framework\Context::getScope()->hasProjectsAvailable()): ?>
                        <button class="button primary project-quick-edit" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', $partial_options); ?>');"><?= fa_image_tag('plus-square'); ?><span><?= __('Create a project'); ?></span></button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="spacer"></div>
        <?php endif; ?>
    </div>
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
