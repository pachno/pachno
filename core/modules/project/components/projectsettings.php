<?php

    use pachno\core\framework\Context;
    use pachno\core\framework\Settings;

    /**
     * @var \pachno\core\entities\Project $project
     * @var int $access_level
     */

?>
<div class="form-container">
    <?php if ($access_level == Settings::ACCESS_FULL): ?>
    <form
        accept-charset="<?= Context::getI18n()->getCharset(); ?>"
        data-submit-project-settings
        data-project-id="<?= $project->getID(); ?>"
        action="<?= make_url('configure_project_settings', ['project_id' => $project->getID()]); ?>"
        method="post"
        id="project_settings_form"
        data-interactive-form
    >
    <?php endif; ?>
        <div class="form-row">
            <h3><?= __('Project settings'); ?></h3>
        </div>
        <div class="form-row">
            <label for="enable_builds_yes"><?php echo __('Enable releases'); ?></label>
            <div class="fancy-label-select">
                <?php if ($access_level == Settings::ACCESS_FULL): ?>
                    <input type="radio" name="enable_builds" value="1" class="fancy-checkbox" id="enable_builds_yes"<?php if ($project->isBuildsEnabled()): ?> checked<?php endif; ?>>
                    <label for="enable_builds_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Yes'); ?></label>
                    <input type="radio" name="enable_builds" value="0" class="fancy-checkbox" id="enable_builds_no"<?php if (!$project->isBuildsEnabled()): ?> checked<?php endif; ?>>
                    <label for="enable_builds_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('No'); ?></label>
                <?php else: ?>
                    <?php echo ($project->isBuildsEnabled()) ? __('Yes') : __('No'); ?>
                <?php endif; ?>
            </div>
            <div class="helper-text"><?php echo __('If this project has regular new main- or test-releases, you can use this feature to track issue across different releases'); ?></div>
        </div>
        <div class="form-row">
            <label for="project_downloads_enabled"><?php echo __('Enable downloads'); ?></label>
            <div class="fancy-label-select">
                <?php if ($access_level == Settings::ACCESS_FULL): ?>
                    <input type="radio" name="enable_downloads" value="1" class="fancy-checkbox" id="enable_downloads_yes"<?php if ($project->hasDownloads()): ?> checked<?php endif; ?>>
                    <label for="enable_downloads_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Yes'); ?></label>
                    <input type="radio" name="enable_downloads" value="0" class="fancy-checkbox" id="enable_downloads_no"<?php if (!$project->hasDownloads()): ?> checked<?php endif; ?>>
                    <label for="enable_downloads_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('No'); ?></label>
                <?php else: ?>
                    <?php echo ($project->hasDownloads()) ? __('Yes') : __('No'); ?>
                <?php endif; ?>
            </div>
            <div class="helper-text"><?php echo __('If project releases can be downloaded, use this feature to either upload the files or point to download links'); ?></div>
        </div>
    <?php if ($access_level == Settings::ACCESS_FULL): ?>
        <div class="form-row submit-container">
            <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator submit-indicator']); ?>
        </div>
    </form>
    <?php endif; ?>
</div>
