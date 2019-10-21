<?php

/** @var \pachno\core\entities\Project $project */

?>
<h1><?= __('Project settings'); ?></h1>
<div class="form-container">
    <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
    <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_settings', array('project_id' => $project->getID())); ?>" method="post" onsubmit="Pachno.Project.submitAdvancedSettings('<?php echo make_url('configure_project_settings', array('project_id' => $project->getID())); ?>'); return false;" id="project_settings">
    <?php endif; ?>
        <div class="form-row">
            <label for="enable_builds_yes"><?php echo __('Enable releases'); ?></label>
            <div class="fancy-label-select">
                <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                    <input type="radio" name="enable_builds" value="1" class="fancy-checkbox" id="enable_builds_yes"<?php if ($project->isBuildsEnabled()): ?> checked<?php endif; ?>>
                    <label for="enable_builds_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Yes'); ?></label>
                    <input type="radio" name="enable_builds" value="0" class="fancy-checkbox" id="enable_builds_no"<?php if (!$project->isBuildsEnabled()): ?> checked<?php endif; ?>>
                    <label for="enable_builds_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('No'); ?></label>
                <?php else: ?>
                    <?php echo ($project->isBuildsEnabled()) ? __('Yes') : __('No'); ?>
                <?php endif; ?>
            </div>
            <div class="helper-text"><?php echo __('If this project has regular new main- or test-releases, you should enable releases'); ?></div>
        </div>
        <div class="form-row">
            <label for="enable_editions_yes"><?php echo __('Use editions'); ?></label>
            <div class="fancy-label-select">
                <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                    <input type="radio" name="enable_editions" value="1" class="fancy-checkbox" id="enable_editions_yes"<?php if ($project->isEditionsEnabled()): ?> checked<?php endif; ?>>
                    <label for="enable_editions_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Yes'); ?></label>
                    <input type="radio" name="enable_editions" value="0" class="fancy-checkbox" id="enable_editions_no"<?php if (!$project->isEditionsEnabled()): ?> checked<?php endif; ?>>
                    <label for="enable_editions_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('No'); ?></label>
                <?php else: ?>
                    <?php echo ($project->isEditionsEnabled()) ? __('Yes') : __('No'); ?>
                <?php endif; ?>
            </div>
            <div class="helper-text"><?php echo __('If the project has more than one edition which differ in features or capabilities, you should enable editions'); ?></div>
        </div>
        <div class="form-row">
            <label for="enable_components_yes"><?php echo __('Use components'); ?></label>
            <div class="fancy-label-select">
                <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                    <input type="radio" name="enable_components" value="1" class="fancy-checkbox" id="enable_components_yes"<?php if ($project->isComponentsEnabled()): ?> checked<?php endif; ?>>
                    <label for="enable_components_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Yes'); ?></label>
                    <input type="radio" name="enable_components" value="0" class="fancy-checkbox" id="enable_components_no"<?php if (!$project->isComponentsEnabled()): ?> checked<?php endif; ?>>
                    <label for="enable_components_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('No'); ?></label>
                <?php else: ?>
                    <?php echo ($project->isComponentsEnabled()) ? __('Yes') : __('No'); ?>
                <?php endif; ?>
            </div>
            <div class="helper-text"><?php echo __('If the project consists of several easily identifiable sub-parts, you should enable components'); ?></div>
        </div>
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