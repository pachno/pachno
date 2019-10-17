<?php

/**
 * @var \pachno\core\entities\Project $project
 * @var \pachno\core\entities\Project[] $valid_subproject_targets
 */

?>
<h1><?= __('Project details'); ?></h1>
<div class="form-container">
    <?php use pachno\core\modules\publish\Publish;if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
        <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_settings', array('project_id' => $project->getID())); ?>" method="post" id="project_info" onsubmit="Pachno.Project.submitInfo('<?php echo make_url('configure_project_settings', array('project_id' => $project->getID())); ?>'); return false;">
    <?php endif; ?>
        <div class="form-row">
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <input type="text" class="name-input-enhance" name="project_name" id="project_name_input" onblur="Pachno.Project.updatePrefix('<?= make_url('configure_project_get_updated_key', ['project_id' => $project->getID()]); ?>', <?= $project->getID(); ?>);" value="<?php print $project->getName(); ?>" placeholder="<?= __('A great project name'); ?>">
            <?php else: ?>
                <span class="value"><?= $project->getName(); ?></span>
            <?php endif; ?>
            <label for="project_name_input"><?= __('Project name'); ?></label>
        </div>
        <div class="form-row">
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <div id="project_key_indicator" class="semi_transparent" style="position: absolute; height: 23px; background-color: #FFF; width: 210px; text-align: center; display: none;"><?= image_tag('spinning_16.gif'); ?></div>
                <input type="text" name="project_key" id="project_key_input" value="<?php print $project->getKey(); ?>" style="width: 150px;">
            <?php else: ?>
                <?= $project->getKey(); ?>
            <?php endif; ?>
            <label for="project_key_input"><?= __('Project key'); ?></label>
            <div class="helper-text"><?= __('This is a part of all urls referring to this project'); ?></div>
        </div>
        <div class="form-row">
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown">
                        <label><?= __('Subproject of'); ?></label>
                        <span class="value"><?= __('Not a subproject'); ?></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <input type="radio" class="fancy-checkbox" id="subproject_id_checkbox_0" name="subproject_id" value="0" <?php if (!$project->hasParent()) echo 'checked'; ?>>
                            <label for="subproject_id_checkbox_0" class="list-item">
                                <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                <span class="name value"><?= __('Not a subproject'); ?></span>
                            </label>
                            <?php foreach ($valid_subproject_targets as $aproject): ?>
                                <input type="radio" class="fancy-checkbox" id="subproject_id_checkbox_<?= $aproject->getID(); ?>" name="subproject_id" value="<?= $aproject->getID(); ?>" <?php if ($project->hasParent() && $project->getParent()->getID() == $aproject->getID()) echo 'checked'; ?>>
                                <label for="subproject_id_checkbox_<?= $aproject->getID(); ?>" class="list-item">
                                    <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span class="name value"><?= $aproject->getName(); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php if (!($project->hasParent())): echo __('Not a subproject'); else: echo $project->getParent()->getName(); endif; ?>
                <label for="subproject_id"><?php echo __('Subproject of'); ?></label>
            <?php endif; ?>
        </div>
        <div class="form-row">
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown">
                        <label><?= __('Client'); ?></label>
                        <span class="value"><?= __('No client assigned'); ?></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <?php if (count(\pachno\core\entities\Client::getAll())): ?>
                                <input type="radio" class="fancy-checkbox" id="client_id_checkbox_0" name="client_id" value="0" <?php if (!$project->hasClient()) echo 'checked'; ?>>
                                <label for="client_id_checkbox_0" class="list-item">
                                    <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span class="name value"><?= __('No client assigned'); ?></span>
                                </label>
                                <?php foreach (\pachno\core\entities\Client::getAll() as $client): ?>
                                    <input type="radio" class="fancy-checkbox" id="client_id_checkbox_<?= $client->getID(); ?>" name="client_id" value="<?= $client->getID(); ?>" <?php if ($project->hasClient() && $project->getClient()->getID() == $client->getID()) echo 'checked'; ?>>
                                    <label for="client_id_checkbox_<?= $client->getID(); ?>" class="list-item">
                                        <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                        <span class="name value"><?= $client->getName(); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <input type="radio" class="fancy-checkbox" id="client_id_checkbox_0" name="client_id" value="0">
                                <label for="client_id_checkbox_0" class="list-item disabled">
                                    <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span class="name value"><?= __('No clients exist'); ?></span>
                                </label>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php if ($project->getClient() == null): echo __('No client'); else: echo $project->getClient()->getName(); endif; ?>
                <label for="client"><?php echo __('Client'); ?></label>
            <?php endif; ?>
        </div>
        <div class="form-row">
            <label for="use_prefix_yes"><?php echo __('Use prefix'); ?></label>

                <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                    <input type="radio" name="use_prefix" value="1" class="fancy-checkbox" id="use_prefix_yes"<?php if ($project->usePrefix()): ?> checked<?php endif; ?> onchange="if ($(this).checked) { $('prefix').enable(); }"><label for="use_prefix_yes"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
                    <input type="radio" name="use_prefix" value="0" class="fancy-checkbox" id="use_prefix_no"<?php if (!$project->usePrefix()): ?> checked<?php endif; ?> onchange="if ($(this).checked) { $('prefix').disable(); }"><label for="use_prefix_no"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
                <?php else: ?>
                    <?php echo ($project->usePrefix()) ? __('Yes') : __('No'); ?>
                <?php endif; ?>

        </div>
        <div class="form-row">
            <label for="prefix"><?php echo __('Issue prefix'); ?></label>

                <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                    <input type="text" name="prefix" id="prefix" maxlength="10" value="<?php print $project->getPrefix(); ?>" style="width: 70px;"<?php if (!$project->usePrefix()): ?> disabled<?php endif; ?>>
                <?php elseif ($project->hasPrefix()): ?>
                    <?php echo $project->getPrefix(); ?>
                <?php else: ?>
                    <span class="faded_out"><?php echo __('No prefix set'); ?></span>
                <?php endif; ?>
                <div style="float: right; margin-right: 5px;" class="faded_out"><?php echo __('See %about_issue_prefix for an explanation about issue prefixes', array('%about_issue_prefix' => link_tag(Publish::getArticleLink('AboutIssuePrefixes'), __('about issue prefixes'), array('target' => '_new')))); ?></div>

        </div>
        <div class="form-row">
            <div class="form-row">
                <label for="project_description_input"><?= __('Project description'); ?></label>
                <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                    <?php include_component('main/textarea', ['area_name' => 'description', 'target_type' => 'project', 'target_id' => $project->getID(), 'area_id' => 'project_description_input', 'height' => '200px', 'width' => '100%', 'value' => $project->getDescription(), 'hide_hint' => true]); ?>
                <?php else: ?>
                    <span class="value"><?= ($project->hasDescription()) ? $project->getDescription() : __('No description set'); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php \pachno\core\framework\Event::createNew('core', 'project/projectinfo', $project)->trigger(); ?>
    <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
        <div class="form-row submit-container">
            <button type="submit" class="button primary">
                <span><?php echo __('Save'); ?></span>
                <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
            </button>
        </div>
    </form>
    <?php endif; ?>
</div>
