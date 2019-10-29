<?php

/**
 * @var \pachno\core\entities\Project $project
 * @var \pachno\core\entities\Project[] $valid_subproject_targets
 */

?>
<div class="form-container">
    <?php use pachno\core\modules\publish\Publish;if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
        <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_settings', array('project_id' => $project->getID())); ?>" method="post" id="project_info" onsubmit="Pachno.Project.submitInfo('<?php echo make_url('configure_project_settings', array('project_id' => $project->getID())); ?>'); return false;" data-interactive-form>
    <?php endif; ?>
        <div class="form-row">
            <h3><?= __('Project details'); ?></h3>
        </div>
        <div class="form-row">
            <label for="project_name_input"><?= __('Project name'); ?></label>
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <input type="text" class="name-input-enhance" name="project_name" id="project_name_input" onblur="Pachno.Project.updatePrefix('<?= make_url('configure_project_get_updated_key', ['project_id' => $project->getID()]); ?>', <?= $project->getID(); ?>);" value="<?php print $project->getName(); ?>" placeholder="<?= __('A great project name'); ?>">
            <?php else: ?>
                <span class="value"><?= $project->getName(); ?></span>
            <?php endif; ?>
        </div>
        <div class="form-row">
            <label for="project_key_input"><?= __('Project key'); ?></label>
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <div id="project_key_indicator" style="display: none;"><?= image_tag('spinning_16.gif'); ?></div>
                <input type="text" name="project_key" id="project_key_input" value="<?php print $project->getKey(); ?>" class="prefix-and-key">
            <?php else: ?>
                <?= $project->getKey(); ?>
            <?php endif; ?>
            <div class="helper-text"><?= __('This is a part of all urls referring to this project'); ?></div>
        </div>
        <div class="form-row">
            <label><?php echo __('Project icon'); ?></label>
            <div class="image-container">
                <?php echo image_tag($project->getIconName(), [], true); ?>
            </div>
            <button class="button secondary" onclick="Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_icons', 'project_id' => $project->getId())); ?>');return false;">
                <?= fa_image_tag('image', ['class' => 'icon']); ?>
                <span class="name"><?php echo __('Change project icon'); ?></span>
            </button>
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
