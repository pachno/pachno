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
        <div class="column">
            <div class="form-row">
                <label for="use_prefix_yes"><?php echo __('Prefix issue number'); ?></label>
                <div class="fancy-label-select">
                    <input name="use_prefix" class="fancy-checkbox" id="use_prefix_yes" type="radio" value="1"<?php if ($project->usePrefix()) echo ' checked'; ?> onchange="$('project_prefix_input').enable();">
                    <label for="use_prefix_yes"><?= fa_image_tag('check', ['class' => 'checked']) . __('Yes'); ?></label>
                    <input name="use_prefix" class="fancy-checkbox" id="use_prefix_no" type="radio" value="0"<?php if (!$project->usePrefix()) echo ' checked'; ?> onchange="$('project_prefix_input').disable();">
                    <label for="use_prefix_no"><?= fa_image_tag('check', ['class' => 'checked']) . __('No'); ?></label>
                </div>
            </div>
        </div>
        <div class="column">
            <div class="form-row">
                <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                    <input type="text" name="prefix" id="project_prefix_input" maxlength="10" value="<?php print $project->getPrefix(); ?>" style="width: 70px;"<?php if (!$project->usePrefix()): ?> disabled<?php endif; ?>>
                <?php elseif ($project->hasPrefix()): ?>
                    <?php echo $project->getPrefix(); ?>
                <?php else: ?>
                    <span class="faded_out"><?php echo __('No prefix set'); ?></span>
                <?php endif; ?>
                <label for="project_prefix_input"><?php echo __('Issue number prefix'); ?></label>
            </div>
            <div class="helper-text"><?php echo __('See %about_issue_prefix for an explanation about issue prefixes', array('%about_issue_prefix' => link_tag(Publish::getArticleLink('AboutIssuePrefixes'), __('about issue prefixes'), array('target' => '_new')))); ?></div>
        </div>
        <div class="form-row">
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
