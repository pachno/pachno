<?php

    /**
     * @var \pachno\core\framework\Response $pachno_response
     * @var \pachno\core\entities\Project $selected_project
     */

    $pachno_response->addBreadcrumb(__('Project settings'), make_url('project_settings', array('project_key' => $selected_project->getKey())));
    $pachno_response->setTitle(__('"%project_name" settings', array('%project_name' => $selected_project->getName())));
    $selected_tab = ($selected_tab) ?? 'info';

?>
<div id="project_settings" class="content-with-sidebar">
    <?php include_component('project/sidebar', ['selected_tab' => $selected_tab]); ?>
    <div id="project_config_menu_panes" class="configuration-container">
        <div id="tab_information_pane" class="configuration-content centered" <?php if ($selected_tab != 'info'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/projectinfo', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div id="tab_settings_pane" class="configuration-content centered" <?php if ($selected_tab != 'settings'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/projectsettings', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div id="tab_links_pane" class="configuration-content centered" <?php if ($selected_tab != 'links'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/projectlinks', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div id="tab_icons_pane" class="configuration-content centered" <?php if ($selected_tab != 'icons'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/settings_project_icons', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div id="tab_issues_and_workflow_pane" class="configuration-content centered" <?php if ($selected_tab != 'issues_and_workflow'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/settings_project_issues_and_workflow', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div id="tab_hierarchy_pane" class="configuration-content centered" <?php if ($selected_tab != 'hierarchy'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/projecthierarchy', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div id="tab_developers_pane" class="configuration-content centered" <?php if ($selected_tab != 'developers'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/projectdevelopers', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div id="tab_client_pane" class="configuration-content centered" <?php if ($selected_tab != 'client'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/settings_project_client', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div id="tab_other_pane" class="configuration-content centered" <?php if ($selected_tab != 'other'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/projectother', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div id="tab_permissions_pane" class="configuration-content centered" <?php if ($selected_tab != 'permissions'): ?>style=" display: none;"<?php endif; ?>>
            <?php include_component('project/projectpermissions', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <?php \pachno\core\framework\Event::createNew('core', 'config_project_panes')->trigger(['selected_tab' => $selected_tab, 'access_level' => $access_level, 'project' => $selected_project]); ?>
    </div>
</div>
<script>
    require(['domReady', 'pachno/index', 'jquery'], function (domReady, pachno_index_js, jQuery) {
        domReady(function () {
            jQuery('body').on('click', '.project-edition .open', function(event) {
                event.preventDefault();
                event.stopPropagation();

                const $item = jQuery(this).parents('.project-edition');
                pachno_index_js.Project.Edition.showOptions($item);
            });

        });
    });
</script>
