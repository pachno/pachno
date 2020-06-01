<div class="fancy-tabs" id="permissions_<?php echo $mode; ?>_<?php echo $base_id; ?>_tabs">
    <a class="tab selected" id="<?php echo $mode.'_'.$base_id; ?>_tab_general" onclick="Pachno.UI.tabSwitcher('<?php echo $mode.'_'.$base_id; ?>_tab_general', 'permissions_<?php echo $mode.'_'.$base_id; ?>_tabs');" href="javascript:void(0);">
        <span class="name"><?php echo __('General permissions'); ?></span>
    </a>
    <a class="tab" id="<?php echo $mode.'_'.$base_id; ?>_tab_pages" onclick="Pachno.UI.tabSwitcher('<?php echo $mode.'_'.$base_id; ?>_tab_pages', 'permissions_<?php echo $mode.'_'.$base_id; ?>_tabs');" href="javascript:void(0);">
        <span class="name"><?php echo __('Page access permissions'); ?></span>
    </a>
    <a class="tab" id="<?php echo $mode.'_'.$base_id; ?>_tab_projects" onclick="Pachno.UI.tabSwitcher('<?php echo $mode.'_'.$base_id; ?>_tab_projects', 'permissions_<?php echo $mode.'_'.$base_id; ?>_tabs');" href="javascript:void(0);">
        <span class="name"><?php echo __('Project-specific permissions'); ?></span>
    </a>
    <a class="tab" id="<?php echo $mode.'_'.$base_id; ?>_tab_modules" onclick="Pachno.UI.tabSwitcher('<?php echo $mode.'_'.$base_id; ?>_tab_modules', 'permissions_<?php echo $mode.'_'.$base_id; ?>_tabs');" href="javascript:void(0);">
        <span class="name"><?php echo __('Module-specific permissions'); ?></span>
    </a>
</div>
<div id="permissions_<?php echo $mode; ?>_<?php echo $base_id; ?>_tabs_panes" class="permission_list">
    <div id="<?php echo $mode.'_'.$base_id; ?>_tab_general_pane" class="tab_pane">
        <p><?php echo __('These permissions control what you can do in Pachno. Some of these permissions are also available as project-specific permissions, from the "%project_specific_permissions" tab.', array('%project_specific_permissions' => '<i>'.__('Project-specific permissions').'</i>')); ?></p>
        <ul>
            <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'general_permissions', 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('general'), 'mode' => 'general', 'target_id' => 0, 'module' => 'core', 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
            <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'user_permissions', 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('user'), 'mode' => 'user', 'target_id' => 0, 'module' => 'core', 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
            <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'issues_permissions', 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('issues'), 'mode' => 'general', 'target_id' => 0, 'module' => 'core', 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
            <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'project_permissions', 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('project'), 'mode' => 'general', 'target_id' => 0, 'module' => 'core', 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
        </ul>
    </div>
    <div id="<?php echo $mode; ?>_<?php echo $base_id; ?>_tab_pages_pane" class="tab_pane" style="display: none;">
        <p><?php echo __('These permissions control which pages you can access in Pachno. Some of these permissions are also available as project-specific permissions, from the "%project_specific_permissions" tab.', array('%project_specific_permissions' => '<i>'.__('Project-specific permissions').'</i>')); ?></p>
        <ul>
            <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'page_permissions', 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('pages'), 'mode' => 'pages', 'target_id' => 0, 'module' => 'core', 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
            <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'configuration_permissions', 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('configuration'), 'mode' => 'configuration', 'target_id' => 0, 'module' => 'core', 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
            <?php //include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'project_page_permissions', 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('project_pages'), 'mode' => 'project_pages', 'target_id' => 0, 'module' => 'core', 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
        </ul>
    </div>
    <div id="<?php echo $mode; ?>_<?php echo $base_id; ?>_tab_modules_pane" class="tab_pane" style="display: none;">
        <p><?php echo __('Module-specific permissions are also available from the "%configure_modules" configuration page', array('%configure_modules' => link_tag(make_url('configure_modules'), __('Configure modules')))); ?></p>
        <ul>
        <?php foreach (\pachno\core\framework\Context::getModules() as $module_key => $module): ?>
            <?php if (!count($module->getAvailablePermissions())) continue; ?>
            <li>
                <a href="javascript:void(0);" onclick="$('<?php echo $mode.'_'.$base_id; ?>_module_permission_details_<?php echo $module_key; ?>').toggle();"><?php echo image_tag('icon_project_permissions.png', array('style' => 'float: right;')); ?><?php echo $module->getLongName(); ?> <span class="faded_out smaller"><?php echo $module_key; ?></span></a>
                <ul style="display: none;" id="<?php echo $mode.'_'.$base_id; ?>_module_permission_details_<?php echo $module_key; ?>">
                    <?php include_component('configuration/permissionsblock', array('base_id' => $mode.'_'.$base_id . 'module_' . $module_key . '_permissions', 'permissions_list' => $module->getAvailablePermissions(), 'mode' => 'module_permissions', 'target_id' => 0, 'module' => $module_key, 'user_id' => $user_id, 'team_id' => $team_id, 'access_level' => $access_level)); ?>
                </ul>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
    <div id="<?php echo $mode.'_'.$base_id; ?>_tab_projects_pane" class="tab_pane" style="display: none;">
        <div class="permissions_warning">
            <strong><?php echo __('Warning'); ?></strong>
            <p><?php echo __('The recommended way to set project-specific permissions is to use roles. Assigning teams and users to projects with a specific role keeps all permissions synchronized with roles, whereas editing it from here should only be done in very specific scenarios.'); ?></p>
        </div>
        <p><?php echo __('These permissions control what you can do, and which pages you can access in Pachno - on a project-specific basis. Some of these permissions are also available as site-wide permissions, from the "%general_permissions" tab.', array('%general_permissions' => '<i>'.__('General permissions').'</i>')); ?></p>
        <?php if (count(\pachno\core\entities\Project::getAll()) > 0): ?>
            <ul>
                <?php foreach (\pachno\core\entities\Project::getAll() as $project): ?>
                    <li>
                        <a href="javascript:void(0);" onclick="$('<?php echo $base_id; ?>_project_permission_details_<?php echo $project->getID(); ?>').toggle();"><?php echo image_tag('expand_small.png', array('style' => 'float: left; margin-right: 5px; margin-top: 2px;')); ?><?php echo $project->getName(); ?> <span class="faded_out smaller"><?php echo $project->getKey(); ?></span></a>
                        <ul style="display: none;" id="<?php echo $base_id; ?>_project_permission_details_<?php echo $project->getID(); ?>">
                            <?php include_component('configuration/permissionsblock', array('base_id' => $base_id . 'project_' . $project->getID() . '_project_permissions', 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('project'), 'mode' => 'general', 'target_id' => $project->getID(), 'module' => 'core', 'user_id' => $user_id, 'access_level' => $access_level)); ?>
                            <?php //include_component('configuration/permissionsblock', array('base_id' => $base_id . 'project_' . $project->getID() . '_page_permissions', 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('project_pages'), 'mode' => 'project_pages', 'target_id' => $project->getID(), 'module' => 'core', 'user_id' => $user_id, 'access_level' => $access_level)); ?>
                            <?php include_component('configuration/permissionsblock', array('base_id' => $base_id . 'project_' . $project->getID() . '_issue_permissions', 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('issues'), 'mode' => 'general', 'target_id' => $project->getID(), 'module' => 'core', 'user_id' => $user_id, 'access_level' => $access_level)); ?>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="faded_out" style="padding: 2px;"><?php echo __('There are no projects'); ?></div>
        <?php endif; ?>
    </div>
</div>
