<div class="backdrop_box large edit_agileboard">
    <div class="backdrop_detail_header">
        <span><?php echo ($scope->getId()) ? __('Edit scope') : __('Create new scope'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form method="post" accept-charset="<?php echo \pachno\core\framework\Settings::getCharset(); ?>">
                <div class="form-row">
                    <input type="text" id="scope_name_input" name="name" value="<?php echo $scope->getName(); ?>" class="name-input-enhance">
                    <label for="scope_name_input"><?php echo __('Scope name'); ?></label>
                </div>
                <?php if (!$scope->getID()): ?>
                    <div class="form-row">
                        <input id="new_scope_hostname_input" name="hostname" placeholder="internal.<?= \pachno\core\framework\Context::getScope()->getCurrentHostname(); ?>">
                        <label for="new_scope_hostname_input"><?php echo __('Scope hostname'); ?></label>
                        <div class="helper-text">
                            <?php echo __('The hostname should be provided without protocol or the trailing slash (.com, not .com/) and port specified if desired. Valid examples are: %examples', array('%examples' => '<i>bugs.mycompany.com , internal.company.org , pachno.company.com , dev.company.com:8080</i>')); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="form-row">
                        <label><?php echo __('Scope hostname'); ?></label>
                        <?php foreach ($scope->getHostnames() as $hostname): ?>
                            <?php echo $hostname; ?>
                            <br style="clear: both;">
                        <?php endforeach; ?>
                        <div class="helper-text"><?php echo __('This is the list of hostnames for which this scope will be active.'); ?></div>
                    </div>
                    <div class="form-row">
                        <input id="scope_description_input" name="description" value="<?php echo $scope->getDescription(); ?>" style="width: 500px;">
                        <label for="scope_description_input"><?php echo __('Scope description'); ?></label>
                    </div>
                    <div class="form-row">
                        <label for="scope_workflows_yes"><?php echo __('Allow custom workflows'); ?></label>
                        <input type="radio"<?php if ($scope->isCustomWorkflowsEnabled()): ?> checked<?php endif; ?> id="scope_workflows_yes" name="custom_workflows_enabled" value="1">
                        <label for="scope_workflows_yes" style="font-weight: normal;"><?php echo __('Yes'); ?></label>&nbsp;
                        <input type="radio"<?php if (!$scope->isCustomWorkflowsEnabled()): ?> checked<?php endif; ?> id="scope_workflows_no" name="custom_workflows_enabled" value="0">
                        <label for="scope_workflows_no" style="font-weight: normal;"><?php echo __('No'); ?></label>&nbsp;
                    </div>
                    <div class="form-row">
                        <label for="scope_workflow_limit"><?php echo __('Custom workflows'); ?></label>
                        <input id="scope_workflow_limit" name="workflow_limit" value="<?php echo $scope->getMaxWorkflowsLimit(); ?>" style="width: 30px; text-align: right;">
                        <div class="helper-text"><?php echo __('Setting the workflow limit to "0" disables limitations on number of custom workflows completely.'); ?></div>
                    </div>
                    <div class="form-row">
                        <label for="scope_uploads_yes"><?php echo __('Allow file uploads'); ?></label>
                        <input type="radio"<?php if ($scope->isUploadsEnabled()): ?> checked<?php endif; ?> id="scope_uploads_yes" name="file_uploads_enabled" value="1">
                        <label for="scope_uploads_yes" style="font-weight: normal;"><?php echo __('Yes'); ?></label>&nbsp;
                        <input type="radio"<?php if (!$scope->isUploadsEnabled()): ?> checked<?php endif; ?> id="scope_uploads_no" name="file_uploads_enabled" value="0">
                        <label for="scope_uploads_no" style="font-weight: normal;"><?php echo __('No'); ?></label>&nbsp;
                    </div>
                    <div class="form-row">
                        <label for="scope_upload_limit"><?php echo __('Total upload quota'); ?></label>
                        <input id="scope_upload_limit" name="upload_limit" value="<?php echo $scope->getMaxUploadLimit(); ?>" style="width: 30px; text-align: right;"> MB
                        <div class="helper-text"><?php echo __('Setting the upload quota to "0" MB disables the qouta completely'); ?></div>
                    </div>
                    <div class="form-row">
                        <label for="scope_project_limit"><?php echo __('Max projects'); ?></label>
                        <input id="scope_project_limit" name="project_limit" value="<?php echo $scope->getMaxProjects(); ?>" style="width: 30px; text-align: right;">
                        <div class="helper-text"><?php echo __('Total number of allowed projects. Setting the value to "0" disables limitations on number of projects.'); ?></div>
                    </div>
                    <div class="form-row">
                        <label for="scope_user_limit"><?php echo __('Max users'); ?></label>
                        <input id="scope_user_limit" name="user_limit" value="<?php echo $scope->getMaxUsers(); ?>" style="width: 30px; text-align: right;">
                        <div class="helper-text"><?php echo __('Total number of allowed users. Setting the value to "0" disables limitations on number of users.'); ?></div>
                    </div>
                    <div class="form-row">
                        <label for="scope_team_limit"><?php echo __('Max teams'); ?></label>
                        <input id="scope_team_limit" name="team_limit" value="<?php echo $scope->getMaxTeams(); ?>" style="width: 30px; text-align: right;">
                        <div class="helper-text"><?php echo __('Total number of allowed teams. Setting the value to "0" disables limitations on number of teams.'); ?></div>
                    </div>
                    <h3><?php echo __('Available modules'); ?></h3>
                    <div class="form-row">
                        <div class="list-mode">
                            <?php foreach (\pachno\core\framework\Context::getModules() as $module): ?>
                                <?php if (array_key_exists($module->getName(), $modules) && !$modules[$module->getName()]): ?>
                                    <div class="list-item disabled multiline">
                                        <span class="name">
                                            <span class="title"><?php echo $module->getLongname(); ?></span>
                                            <span class="additional_information"><?php echo __('This module has been disabled in the selected scope by its admin'); ?></span>
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <div class="list-item">
                                        <input type="radio"<?php if (array_key_exists($module->getName(), $modules)): ?> checked<?php endif; ?> name="module_enabled[<?php echo $module->getName(); ?>]" id="module_<?php echo $module->getName(); ?>_available_yes" value="1">
                                        <label for="module_<?php echo $module->getName(); ?>_available_yes" style="font-weight: normal;"><?php echo __('Available'); ?></label>&nbsp;
                                        <input type="radio"<?php if (!array_key_exists($module->getName(), $modules)): ?> checked<?php endif; ?> name="module_enabled[<?php echo $module->getName(); ?>]" id="module_<?php echo $module->getName(); ?>_available_no" value="0">
                                        <label for="module_<?php echo $module->getName(); ?>_available_no" style="font-weight: normal;"><?php echo __('Not available'); ?></label>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary">
                        <?php if ($scope->getId()): ?>
                            <?= fa_image_tag('save'); ?><span><?= __('Save scope'); ?></span>
                        <?php else: ?>
                            <?= fa_image_tag('plus-square'); ?><span><?= __('Create scope'); ?></span>
                        <?php endif; ?>
                        <span class="indicator"><?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
