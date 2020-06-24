<?php

    use pachno\core\framework;

?>
<?php if (count($pachno_user->getAssociatedProjects()) > 0): ?>
    <div id="associated-projects" class="project-list">
        <?php foreach ($pachno_user->getAssociatedProjects() as $project): ?>
            <?php if ($project->isDeleted()) continue; ?>
            <?php include_component('project/project', compact('project')); ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="no-items no-projects">
        <?= fa_image_tag('star-half-alt'); ?>
        <span><?php echo __('You are not associated with any projects'); ?></span>
        <?php if ($pachno_user->canAccessConfigurationPage(framework\Settings::CONFIGURATION_SECTION_PROJECTS) && framework\Context::getScope()->hasProjectsAvailable()): ?>
            <button class="button" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'project_config']); ?>');"><?= __('Create project'); ?></button>
        <?php endif; ?>
    </div>
<?php endif; ?>
