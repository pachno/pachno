<?php

    $pachno_response->addBreadcrumb(__('Project settings'), make_url('project_settings', array('project_key' => $selected_project->getKey())));
    $pachno_response->setTitle(__('"%project_name" settings', array('%project_name' => $selected_project->getName())));

?>
<div id="project_settings" class="content-with-sidebar">
    <?php include_component('project/sidebar'); ?>
    <?php if ($settings_saved): ?>
        <script type="text/javascript">
            require(['domReady', 'pachno/index'], function (domReady, Pachno) {
                domReady(function () {
                    Pachno.Main.Helpers.Message.success('<?php echo __('Settings saved'); ?>', '<?php echo __('Project settings have been saved successfully'); ?>');
                });
            });
        </script>
    <?php endif; ?>
    <div id="project_settings_container" class="project_info_container">
        <?php if (!isset($selected_tab)) $selected_tab = 'info'; ?>
        <div class="fancy-tabs" id="project_config_menu">
            <a id="tab_information" href="javascript:void(0);" onclick="Pachno.Main.Helpers.tabSwitcher('tab_information', 'project_config_menu');return false;" class="tab <?php if ($selected_tab == 'info') echo 'selected'; ?>">
                <?= fa_image_tag('edit', ['class' => 'icon'], 'far'); ?>
                <span class="name"><?= __('Project details'); ?></span>
            </a>
            <a id="tab_other" href="javiscript:void(0);" onclick="Pachno.Main.Helpers.tabSwitcher('tab_other', 'project_config_menu');return false;" class="tab <?php if ($selected_tab == 'other') echo 'selected'; ?>">
                <?= fa_image_tag('list-alt', ['class' => 'icon'], 'far'); ?>
                <span class="name"><?= __('Display settings'); ?></span>
            </a>
            <a id="tab_settings" href="javascript:void(0);" onclick="Pachno.Main.Helpers.tabSwitcher('tab_settings', 'project_config_menu');return false;" class="tab <?php if ($selected_tab == 'settings') echo 'selected'; ?>">
                <?= fa_image_tag('cogs', ['class' => 'icon']); ?>
                <span class="name"><?= __('Advanced settings'); ?></span>
            </a>
            <a id="tab_hierarchy" href="javascript:void(0);" onclick="Pachno.Main.Helpers.tabSwitcher('tab_hierarchy', 'project_config_menu');return false;" class="tab <?php if ($selected_tab == 'hierarchy') echo 'selected'; ?>">
                <?= fa_image_tag('boxes', ['class' => 'icon']); ?>
                <span class="name"><?= __('Editions and components'); ?></span>
            </a>
            <a id="tab_developers" href="javascript:void(0);" onclick="Pachno.Main.Helpers.tabSwitcher('tab_developers', 'project_config_menu');return false;" class="tab <?php if ($selected_tab == 'developers') echo 'selected'; ?>">
                <?= fa_image_tag('users', ['class' => 'icon']); ?>
                <span class="name"><?= __('Team'); ?></span>
            </a>
            <a id="tab_permissions" href="javascript:void(0);" onclick="Pachno.Main.Helpers.tabSwitcher('tab_permissions', 'project_config_menu');return false;" class="tab <?php if ($selected_tab == 'permissions') echo 'selected'; ?>">
                <?= fa_image_tag('user-shield', ['class' => 'icon']); ?>
                <span class="name"><?= __('Roles and permissions'); ?></span>
            </a>
            <?php \pachno\core\framework\Event::createNew('core', 'config_project_tabs')->trigger(array('selected_tab' => $selected_tab)); ?>
        </div>
        <?php include_component('project/projectconfig', array('project' => $selected_project)); ?>
    </div>
</div>
