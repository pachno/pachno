<?php

/**
 * @var integer $access_level
 * @var \pachno\core\entities\Project $project
 */

    $selected_tab = (!$project->isComponentsEnabled() && $project->isEditionsEnabled()) ? 'editions' : 'components';

?>
<div class="fancy-tabs tab-switcher" id="project-hierarchy-menu">
    <a id="tab_project_components" class="tab <?php if ($selected_tab == 'components') echo 'selected'; ?> tab-switcher-trigger" data-tab-target="components"><?= fa_image_tag('boxes', ['class' => 'icon']); ?><span><?= __('Components'); ?></span></a>
    <a id="tab_project_editions" class="tab <?php if ($selected_tab == 'editions') echo 'selected'; ?> tab-switcher-trigger" data-tab-target="editions"><?= fa_image_tag('layer-group', ['class' => 'icon']); ?><span><?= __('Editions'); ?></span></a>
</div>
<div id="project-hierarchy-menu_panes">
    <div id="tab_project_components_pane" style="<?php if ($selected_tab != 'components') echo 'display: none;'; ?>" data-tab-id="components">
        <div class="helper-text">
            <div class="image-container"><?= image_tag('/unthemed/onboarding_project_components_icon.png', [], true); ?></div>
            <span class="description"><?= __('Components are a great way to split a project into parts, to separate and keep track of different parts of the project individually.') ;?></span>
        </div>
        <div class="configurable-components-container">
            <div class="configurable-components-list-container">
                <h3>
                    <span><?php echo __('Project components'); ?></span>
                    <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                        <input type="checkbox" class="fancy-checkbox" data-interactive-toggle value="1" id="toggle_enable_components_input" data-url="<?= make_url('configure_project_setting', ['project_id' => $project->getID(), 'setting_key' => 'enable_components']); ?>" <?php if ($project->isComponentsEnabled()) echo ' checked'; ?>><label class="button secondary" for="toggle_enable_components_input"><?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?><span><?= __('Components enabled'); ?></span></label>
                    <?php endif; ?>
                </h3>
                <div class="configurable-components-list" id="project-components-list">
                    <?php foreach ($project->getComponents() as $component): ?>
                        <?php include_component('project/component', array('component' => $component, 'access_level' => $access_level)); ?>
                    <?php endforeach; ?>
                </div>
                <div class="form-container">
                    <form id="project-add-component-form" accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_project_component_post', ['project_id' => $project->getID(), 'component_id' => 0]); ?>" data-interactive-form>
                        <div class="form-row add-placeholder">
                            <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                            <input type="text" name="name" class="invisible" placeholder="<?= __('Add a component'); ?>">
                        </div>
                        <div class="form-row error-container">
                            <div class="error"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="tab_project_editions_pane" style="<?php if ($selected_tab != 'editions') echo 'display: none;'; ?>" data-tab-id="editions">
        <div class="helper-text">
            <div class="image-container"><?= image_tag('/unthemed/onboarding_project_editions_icon.png', [], true); ?></div>
            <span class="description"><?= __('Use editions when you have variations of a project or product which include different components. This lets you keep track of the different editions without setting up multiple projects.') ;?></span>
        </div>
        <div class="configurable-components-container" id="project-editions-list-container">
            <div class="configurable-components-list-container">
                <h3>
                    <span><?php echo __('Project editions'); ?></span>
                    <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                        <input type="checkbox" class="fancy-checkbox" data-interactive-toggle value="1" id="toggle_enable_editions_input" data-url="<?= make_url('configure_project_setting', ['project_id' => $project->getID(), 'setting_key' => 'enable_editions']); ?>" <?php if ($project->isEditionsEnabled()) echo ' checked'; ?>><label class="button secondary" for="toggle_enable_editions_input"><?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?><span><?= __('Editions enabled'); ?></span></label>
                    <?php endif; ?>
                </h3>
                <div class="configurable-components-list" id="project-editions-list">
                    <?php foreach ($project->getEditions() as $edition): ?>
                        <?php include_component('project/edition', array('edition' => $edition, 'access_level' => $access_level)); ?>
                    <?php endforeach; ?>
                </div>
                <div class="form-container">
                    <form id="project-add-edition-form" accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_project_edition_post', ['project_id' => $project->getID(), 'edition_id' => 0]); ?>" data-interactive-form>
                        <div class="form-row add-placeholder">
                            <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                            <input type="text" name="name" class="invisible" placeholder="<?= __('Add an edition'); ?>">
                        </div>
                        <div class="form-row error-container">
                            <div class="error"></div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="configurable-component-options" id="selected-edition-options"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
        const json = data.json;
        const $form = $(`#${data.form}`);
        switch (data.form) {
            case 'project-add-component-form':
                $form.trigger('reset');
                $('#project-components-list').append(json.component);
                break;
            case 'project-add-edition-form':
                $form.trigger('reset');
                $('#project-editions-list').append(json.edition);
                break;
        }
    });

</script>
