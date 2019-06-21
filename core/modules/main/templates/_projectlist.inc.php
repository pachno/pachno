<?php

/** @var \pachno\core\entities\User $pachno_user */

use pachno\core\framework;

?>
<div class="project_overview">
    <?php if (!$pachno_user->isGuest()): ?>
        <div id="projects_list_tabs" class="fancy-tabs">
            <a class="tab selected" id="tab_active" onclick="Pachno.Project.loadList('active', '<?= $active_url; ?>');return false;" href="javascript:void(0);">
                <?= fa_image_tag('boxes', ['class' => 'icon']); ?>
                <?= fa_image_tag('spinner', ['style' => 'display: none;', 'id' => 'project_list_tab_active_indicator', 'class' => 'icon fa-spin']); ?>
                <span class="name">
                    <?= __('Active projects'); ?>
                </span>
            </a>
            <a class="tab" id="tab_archived" onclick="Pachno.Project.loadList('archived', '<?= $archived_url; ?>');return false;" href="javascript:void(0);">
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
                        <button class="button primary project-quick-edit" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', $partial_options); ?>');"><?= fa_image_tag('plus-square'); ?><span><?= __('Create a project'); ?></span></button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div id="projects_list_tabs_panes">
        <div id="tab_active_pane" style=""></div>
        <div id="tab_archived_pane" style="display: none;"></div>
    </div>
</div>
<script type="text/javascript">
 require(['domReady', 'pachno/index', 'prototype'], function (domReady, Pachno, prototype) {
     domReady(function () {
         // Default to active tab, unless archived tab was specified
         // in URL.
         if (window.location.hash === '#tab_archived') {
             Pachno.Project.loadList('archived', '<?= $archived_url; ?>');
         }
         else {
             Pachno.Project.loadList('active', '<?= $active_url; ?>');
         }
     });
 });
</script>
