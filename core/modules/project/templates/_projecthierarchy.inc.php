<?php

/**
 * @var integer $access_level
 * @var \pachno\core\entities\Project $project
 */

    $selected_tab = (!$project->isComponentsEnabled() && $project->isEditionsEnabled()) ? 'editions' : 'components';

?>
<div class="fancy-tabs" id="project-hierarchy-menu">
    <a id="tab_project_components" class="tab <?php if ($selected_tab == 'components') echo 'selected'; ?>" onclick="Pachno.Main.Helpers.tabSwitcher('tab_project_components', 'project-hierarchy-menu');"><?= fa_image_tag('boxes', ['class' => 'icon']); ?><span><?= __('Components'); ?></span></a>
    <a id="tab_project_editions" class="tab <?php if ($selected_tab == 'editions') echo 'selected'; ?>" onclick="Pachno.Main.Helpers.tabSwitcher('tab_project_editions', 'project-hierarchy-menu');"><?= fa_image_tag('layer-group', ['class' => 'icon']); ?><span><?= __('Editions'); ?></span></a>
</div>
<div id="project-hierarchy-menu_panes">
    <div id="tab_project_editions_pane" style="<?php if ($selected_tab != 'editions') echo 'display: none;'; ?>">
        <h3>
            <span><?php echo __('Project editions'); ?></span>
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <button class="button secondary" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_workflow_scheme']); ?>');"><?= fa_image_tag('toggle-on', ['class' => 'checked']); ?><span><?= __('Editions enabled'); ?></span></button>
                <button class="button" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_workflow_scheme']); ?>');"><?= __('New edition'); ?></button>
            <?php endif; ?>
        </h3>
        <div class="flexible-table" id="workflow-schemes-list">
            <div class="row header">
                <div class="column header name-container"><?= __('Edition'); ?></div>
                <div class="column header actions"></div>
            </div>
            <?php foreach ($project->getEditions() as $edition): ?>
                <?php include_component('project/edition', array('theProject' => $project, 'edition' => $edition, 'access_level' => $access_level)); ?>
            <?php endforeach; ?>
        </div>
    </div>
    <div id="tab_project_components_pane" style="<?php if ($selected_tab != 'components') echo 'display: none;'; ?>">
        <h3>
            <span><?php echo __('Project components'); ?></span>
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <button class="button secondary" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_workflow_scheme']); ?>');"><?= fa_image_tag('toggle-on', ['class' => 'checked']); ?><span><?= __('Components enabled'); ?></span></button>
                <button class="button" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_component']); ?>');"><?= __('New component'); ?></button>
            <?php endif; ?>
        </h3>
        <div class="flexible-table" id="workflow-schemes-list">
            <div class="row header">
                <div class="column header name-container"><?= __('Component'); ?></div>
                <div class="column header actions"></div>
            </div>
            <?php foreach ($project->getComponents() as $component): ?>
                <?php include_component('project/component', array('component' => $component, 'access_level' => $access_level)); ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
