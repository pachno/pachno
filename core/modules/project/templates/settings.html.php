<?php

    /**
     * @var \pachno\core\framework\Response $pachno_response
     * @var \pachno\core\entities\Project $selected_project
     */

    $pachno_response->setTitle(__('"%project_name" settings', array('%project_name' => $selected_project->getName())));
    $selected_tab = ($selected_tab) ?? 'info';

?>
<div id="project_settings" class="content-with-sidebar">
    <?php include_component('project/sidebar', ['selected_tab' => $selected_tab]); ?>
    <div id="project_config_menu_panes" class="configuration-container">
        <div data-tab-id="information" class="configuration-content centered" <?php if ($selected_tab != 'info'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/projectinfo', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div data-tab-id="settings" class="configuration-content centered" <?php if ($selected_tab != 'settings'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/projectsettings', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div data-tab-id="links" class="configuration-content centered" <?php if ($selected_tab != 'links'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/projectlinks', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div data-tab-id="issues_and_workflow" class="configuration-content centered" <?php if ($selected_tab != 'issues_and_workflow'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/settings_project_issues_and_workflow', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div data-tab-id="hierarchy" class="configuration-content centered" <?php if ($selected_tab != 'hierarchy'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/projecthierarchy', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div data-tab-id="developers" class="configuration-content centered" <?php if ($selected_tab != 'developers'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/projectdevelopers', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div data-tab-id="client" class="configuration-content centered" <?php if ($selected_tab != 'client'): ?> style="display: none;"<?php endif; ?>>
            <?php include_component('project/settings_project_client', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <div data-tab-id="permissions" class="configuration-content centered" <?php if ($selected_tab != 'permissions'): ?>style=" display: none;"<?php endif; ?>>
            <?php include_component('project/projectpermissions', array('access_level' => $access_level, 'project' => $selected_project)); ?>
        </div>
        <?php \pachno\core\framework\Event::createNew('core', 'config_project_panes')->trigger(['selected_tab' => $selected_tab, 'access_level' => $access_level, 'project' => $selected_project]); ?>
    </div>
</div>
<script type="text/javascript">
    Pachno.on(Pachno.EVENTS.ready, function () {
        Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
            const json = data.json;
            const project = json.project;
            switch (data.form) {
                case 'project_config_icon_form':
                    const $project_icons = $(`.project-icon[data-project-id=${project.id}]`);
                    $project_icons.attr('src', project.icon);
                    $project_icons.prop('data-src', project.icon);
                    $project_icons.data('src', project.icon);
                    break;
            }
        });
    });

    // $(document).ready(() => {
    //     $('body').on('click', '.project-edition .open', function(event) {
    //         event.preventDefault();
    //         event.stopPropagation();
    //
    //         const $item = $(this).parents('.project-edition');
    //         Pachno.Config.loadComponentOptions(
    //             {
    //                 container: '#project-editions-list-container',
    //                 options: '#selected-edition-options',
    //                 component: '.project-edition'
    //             },
    //             $item
    //         );
    //     });
    // });
</script>
