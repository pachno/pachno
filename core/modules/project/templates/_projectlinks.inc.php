<?php

/**
 * @var \pachno\core\entities\Project $project
 * @var \pachno\core\entities\Project[] $valid_subproject_targets
 */

?>
<h1><?= __('Project links'); ?></h1>
<div class="form-container">
    <?php use pachno\core\framework\Settings;
    use pachno\core\modules\publish\Publish;if ($access_level == Settings::ACCESS_FULL): ?>
        <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_settings', array('project_id' => $project->getID())); ?>" method="post" id="project_info" onsubmit="Pachno.Project.submitLinks('<?php echo make_url('configure_project_settings', array('project_id' => $project->getID())); ?>'); return false;">
    <?php endif; ?>
        <div class="form-row">
            <?php if ($access_level == Settings::ACCESS_FULL): ?>
                <input type="text" name="homepage" id="homepage" value="<?php echo $project->getHomepage(); ?>" style="width: 100%;">
            <?php elseif ($project->hasHomepage()): ?>
                <a href="<?php echo $project->getHomepage(); ?>"><?php echo $project->getHomepage(); ?></a>
            <?php else: ?>
                <span class="faded_out"><?php echo __('No homepage set'); ?></span>
            <?php endif; ?>
            <label for="homepage"><?php echo __('Homepage'); ?></label>
        </div>
        <div class="form-row">
            <?php if ($access_level == Settings::ACCESS_FULL): ?>
                <input type="text" name="doc_url" id="doc_url" value="<?php echo $project->getDocumentationURL(); ?>" style="width: 100%;">
            <?php elseif ($project->hasDocumentationURL()): ?>
                <a href="<?php echo $project->getDocumentationURL(); ?>"><?php echo $project->getDocumentationURL(); ?></a>
            <?php else: ?>
                <span class="faded_out"><?php echo __('No documentation URL provided'); ?></span>
            <?php endif; ?>
            <label for="doc_url"><?php echo __('Documentation URL'); ?></label>
        </div>
        <div class="form-row">
            <?php if ($access_level == Settings::ACCESS_FULL): ?>
                <input type="text" name="wiki_url" id="wiki_url" value="<?php echo $project->getWikiURL(); ?>" style="width: 100%;">
            <?php elseif ($project->hasWikiURL()): ?>
                <a href="<?php echo $project->getWikiURL(); ?>"><?php echo $project->getWikiURL(); ?></a>
            <?php else: ?>
                <span class="faded_out"><?php echo __('No wiki URL provided'); ?></span>
            <?php endif; ?>
            <label for="wiki_url"><?php echo __('Wiki URL'); ?></label>
        </div>
    <?php \pachno\core\framework\Event::createNew('core', 'project/projectinfo', $project)->trigger(); ?>
        <?php if ($access_level == Settings::ACCESS_FULL): ?>
        <div class="form-row submit-container">
            <button type="submit" class="button primary">
                <span><?php echo __('Save'); ?></span>
                <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
            </button>
        </div>
    </form>
    <?php endif; ?>
</div>
