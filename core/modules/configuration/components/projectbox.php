<?php
    
    use pachno\core\entities\Project;
    use pachno\core\framework\Settings;
    
    /**
     * @var int $access_level
     * @var Project $project
     */

?>
<div id="project_box_<?= $project->getID();?>" class="row">
    <div class="column info-icons">
        <?= image_tag($project->getIconName(), ['class' => 'icon-large', 'alt' => '[i]'], true); ?>
    </div>
    <div class="column name-container">
        <?= link_tag(make_url('project_dashboard', ['project_key' => $project->getKey()]), $project->getName()); ?>
    </div>
    <div class="column">
        <span class="count-badge"><?= $project->getKey(); ?></span>
    </div>
    <div class="column smaller">
        <?php if ($project->usePrefix()): ?>
            <?= $project->getPrefix(); ?>
        <?php endif; ?>
    </div>
    <div class="column">
        <?php if ($project->getOwner() != null): ?>
            <?php if ($project->getOwner() instanceof \pachno\core\entities\User): ?>
                <?= include_component('main/userdropdown', ['user' => $project->getOwner(), 'size' => 'small']); ?>
            <?php elseif ($project->getOwner() instanceof \pachno\core\entities\Team): ?>
                <?= include_component('main/teamdropdown', ['team' => $project->getOwner()]); ?>
            <?php endif; ?>
        <?php else: ?>
            <div style="color: #AAA; padding: 2px; width: auto;"><?= __('None'); ?></div>
        <?php endif; ?>
    </div>
    <?php if ($access_level == Settings::ACCESS_FULL): ?>
        <div class="column actions">
            <div class="dropper-container">
                <button class="dropper button secondary">
                    <span><?= __('Actions'); ?></span>
                    <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
                </button>
                <div class="dropdown-container">
                    <div class="list-mode">
                        <?php if (!$project->isArchived()): ?>
                            <a class="list-item" href="<?= make_url('project_settings', ['project_key' => $project->getKey()]); ?>">
                                <?= fa_image_tag('edit', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Edit project'); ?></span>
                            </a>
                            <a class="list-item" href="javascript:void(0);" onclick="Pachno.UI.Dialog.show('<?= __('Archive this project?'); ?>', '<?= __('If you archive a project, it is placed into a read only mode, where the project and its issues can no longer be edited. This will also prevent you from creating new issues, and will hide it from project lists (it can be viewed from an Archived Projects list). This will not affect any subprojects.'); ?>', {yes: {click: function() {Pachno.trigger(Pachno.EVENTS.configuration.archiveProject, { url: '<?= make_url('configure_project_archive', ['project_id' => $project->getID()]); ?>', project_id: <?= $project->getID(); ?>});}}, no: {click: Pachno.UI.Dialog.dismiss}});">
                                <?= fa_image_tag('archive', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Archive project');?></span>
                            </a>
                        <?php else: ?>
                            <a class="list-item" href="javascript:void(0);" onclick="Pachno.UI.Dialog.show('<?= __('Unarchive this project?'); ?>', '<?= __('Restoring this project disables read only mode and makes the project and its issues editable again. Depending on project settings this will also allow new issues to be created and makes it visible in project lists again. Restoring this project will not affect any subprojects.'); ?>', {yes: {click: function() {Pachno.trigger(Pachno.EVENTS.configuration.unarchiveProject, { url: '<?= make_url('configure_project_unarchive', ['project_id' => $project->getID()]); ?>', project_id: <?= $project->getID(); ?>});}}, no: {click: Pachno.UI.Dialog.dismiss}});">
                                <?= fa_image_tag('undo', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Unarchive project');?></span>
                            </a>
                        <?php endif; ?>
                        <div class="list-item separator"></div>
                        <a class="list-item danger" href="javascript:void(0)" onclick="Pachno.UI.Dialog.show('<?= __('Really delete project?'); ?>', '<?= __('Deleting this project will prevent users from accessing it or any associated data such as issues, boards, milestones or releases. If you just wish to hide this project or make it read-only, consider archiving it instead.'); ?>', {yes: {click: function() {Pachno.Project.remove('<?= make_url('configure_project_delete', array('project_id' => $project->getID())); ?>', <?= $project->getID(); ?>); }}, no: { click: Pachno.UI.Dialog.dismiss }});">
                            <?= fa_image_tag('times', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Delete');?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php if ($project->hasChildren()): ?>
    <div class="body" id="project_<?= $project->getID(); ?>_children">
        <?php foreach ($project->getChildren() as $child_project): ?>
            <?php include_component('projectbox', array('project' => $child_project, 'access_level' => $access_level)); ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
