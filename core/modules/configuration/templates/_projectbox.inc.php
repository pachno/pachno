<?php \pachno\core\framework\Context::loadLibrary('ui'); ?>
<div id="project_box_<?= $project->getID();?>" class="row">
    <div class="column info-icons">
        <?= image_tag($project->getSmallIconName(), ['class' => 'icon-large', 'alt' => '[i]'], $project->hasSmallIcon()); ?>
    </div>
    <div class="column smaller">
        <?php if ($project->usePrefix()): ?>
            <?= $project->getPrefix(); ?>
        <?php endif; ?>
    </div>
    <div class="column name-container">
        <?php if ($project->isArchived()): ?>
            <span class="status-badge"><span class="name"><?= __('ARCHIVED'); ?> </span></span>
        <?php endif; ?>
        <?= link_tag(make_url('project_dashboard', ['project_key' => $project->getKey()]), $project->getName()); ?>&nbsp;<span class="project_key" style="position: relative;">(<div class="tooltip leftie"><?= __('This is the project key, used in most places when accessing the project'); ?></div><?= $project->getKey(); ?>)</span>
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
    <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
        <div class="column actions">
            <div class="dropper-container">
                <button class="dropper button secondary">
                    <span><?= __('Actions'); ?></span>
                    <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
                </button>
                <div class="dropdown-container">
                    <div class="list-mode">
                        <?php if (!$project->isArchived()): ?>
                            <a class="list-item" href="javascript:void(0);" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', array('key' => 'project_config', 'project_id' => $project->getID())); ?>'">
                                <?= ($access_level == \pachno\core\framework\Settings::ACCESS_FULL) ? fa_image_tag('edit', ['class' => 'icon']) : fa_image_tag('info-circle', ['class' => 'icon']); ?>
                                <span class="name"><?= ($access_level == \pachno\core\framework\Settings::ACCESS_FULL) ? __('Edit project') : __('Show project details'); ?></span>
                            </a>
                        <?php endif; ?>
                        <a class="list-item" href="javascript:void(0);" onclick="$('project_<?= $project->getID(); ?>_permissions').toggle();">
                            <?= fa_image_tag('lock', ['class' => 'icon']); ?>
                            <span class="name"><?= ($access_level == \pachno\core\framework\Settings::ACCESS_FULL) ? __('Edit project permissions') : __('Show project permissions'); ?></span>
                        </a>
                        <a class="list-item" href="javascript:void(0);" id="project_<?= $project->getID(); ?>_unarchive" style="<?php if (!$project->isArchived()) echo 'display: none;'; ?>" onclick="Pachno.Project.unarchive('<?= make_url('configure_project_unarchive', array('project_id' => $project->getID())); ?>', <?php print $project->getID(); ?>)">
                            <span class="name"><?= __('Unarchive project');?></span>
                        </a>
                        <a class="list-item" href="javascript:void(0);" id="project_<?= $project->getID(); ?>_archive" style="<?php if ($project->isArchived()) echo 'display: none;'; ?>" onclick="Pachno.Main.Helpers.Dialog.show('<?= __('Archive this project?'); ?>', '<?= __('If you archive a project, it is placed into a read only mode, where the project and its issues can no longer be edited. This will also prevent you from creating new issues, and will hide it from project lists (it can be viewed from an Archived Projects list). This will not, however, affect any subprojects this one has.').'<br>'.__('If you need to reactivate this subproject, you can do this from projects configuration.'); ?>', {yes: {click: function() {Pachno.Project.archive('<?= make_url('configure_project_archive', array('project_id' => $project->getID())); ?>', <?php print $project->getID(); ?>);}}, no: {click: Pachno.Main.Helpers.Dialog.dismiss}});">
                            <span class="name"><?= __('Archive project');?></span>
                        </a>
                        <a class="list-item" href="javascript:void(0)" onclick="Pachno.Main.Helpers.Dialog.show('<?= __('Really delete project?'); ?>', '<?= __('Deleting this project will prevent users from accessing it or any associated data, such as issues.'); ?>', {yes: {click: function() {Pachno.Project.remove('<?= make_url('configure_project_delete', array('project_id' => $project->getID())); ?>', <?= $project->getID(); ?>); }}, no: { click: Pachno.Main.Helpers.Dialog.dismiss }});">
                            <?= fa_image_tag('times', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Delete');?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="fullpage_backdrop" style="display: none;" id="project_<?= $project->getID(); ?>_permissions">
        <div class="fullpage_backdrop_content backdrop_box large">
            <div class="backdrop_detail_header">
                <span><?= __('Edit project permissions'); ?></span>
                <a href="javascript:void(0);" class="closer" onclick="$('project_<?= $project->getID(); ?>_permissions').hide();"><?= fa_image_tag('times'); ?></a>
            </div>
            <div class="backdrop_detail_content">
                <?php include_component('project/projectpermissions', array('access_level' => $access_level, 'project' => $project)); ?>
            </div>
        </div>
    </div>
</div>
<?php if ($project->hasChildren()): ?>
    <div class="body" id="project_<?= $project->getID(); ?>_children">
        <?php foreach ($project->getChildren() as $child_project): ?>
            <?php include_component('projectbox', array('project' => $child_project, 'access_level' => $access_level)); ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
