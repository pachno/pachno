<?php

    use pachno\core\framework;

    /**
     * @var \pachno\core\entities\User $pachno_user
     * @var \pachno\core\framework\Response $pachno_response
     */

?>
<?php if (count($pachno_user->getAssociatedProjects()) > 0): ?>
    <div id="associated-projects" class="project-list">
        <?php foreach ($pachno_user->getAssociatedProjects() as $project): ?>
            <?php if ($project->isDeleted()) continue; ?>
            <?php include_component('project/project', ['project' => $project, 'include_subprojects' => false]); ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="no-items no-projects">
        <?= fa_image_tag('star-half-alt'); ?>
        <span><?php echo __('You are not associated with any projects'); ?></span>
        <?php if ($pachno_user->canSaveConfiguration() && framework\Context::getScope()->hasProjectsAvailable()): ?>
            <button class="button" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'project_config']); ?>');"><?= __('Create project'); ?></button>
        <?php endif; ?>
    </div>
<?php endif; ?>
