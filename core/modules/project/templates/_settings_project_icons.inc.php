<?php

/**
 * @var \pachno\core\entities\Project $project
 * @var \pachno\core\entities\Project[] $valid_subproject_targets
 */

?>
<h1><?= __('Project links'); ?></h1>
<div class="form-container">
    <?php use pachno\core\modules\publish\Publish;if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
    <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_project_settings', array('project_id' => $project->getID())); ?>" method="post" id="project_info" onsubmit="Pachno.Project.submitLinks('<?php echo make_url('configure_project_settings', array('project_id' => $project->getID())); ?>'); return false;">
    <?php endif; ?>
        <div class="form-row">
            <label><?php echo __('Project icons'); ?></label>
            <div class="button-group project_icons_buttons">
                <div class="button" onclick="Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_icons', 'project_id' => $project->getId())); ?>');"><?php echo ($project->hasSmallIcon() || $project->hasLargeIcon()) ? __('Change project icons') : __('Set project icons'); ?></div>
                <?php if ($project->hasSmallIcon() || $project->hasLargeIcon()): ?>
                    <div class="button" onclick="Pachno.Main.Helpers.Dialog.show('<?php echo __('Reset project icons?'); ?>', '<?php echo __('Do you really want to reset the project icons? Please confirm.'); ?>', {yes: {click: function() {Pachno.Project.resetIcons('<?php echo make_url('configure_projects_icons', array('project_id' => $project->getID())); ?>');}}, no: {click: Pachno.Main.Helpers.Dialog.dismiss}});"><?php echo __('Reset icons'); ?></div>
                <?php endif; ?>
            </div>
            <?php echo image_tag($project->getSmallIconName(), array('style' => 'float: left; margin: 8px 10px 0 0; width: 16px; height: 16px;'), $project->hasSmallIcon()); ?>
            <?php echo image_tag($project->getLargeIconName(), array('style' => 'width: 32px; height: 32px;'), $project->hasLargeIcon()); ?> &nbsp;
        </div>
        <?php \pachno\core\framework\Event::createNew('core', 'project/projectinfo', $project)->trigger(); ?>
    <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
    </form>
    <?php endif; ?>
</div>
