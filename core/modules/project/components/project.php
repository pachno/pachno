<?php

    use pachno\core\entities\Project;
    use pachno\core\entities\tables\Permissions;
    use pachno\core\entities\User;
    use pachno\core\framework\Event;

    /**
     * @var User $pachno_user
     * @var Project $project
     */

?>
<div class="project-strip">
    <div class="details">
        <div class="icon-container">
            <div class="icon-large">
                <?= image_tag($project->getIconName(), array('alt' => '[i]'), true); ?>
            </div>
        </div>
        <div class="information">
            <span class="name">
                <a href="<?= make_url('project_dashboard', ['project_key' => $project->getKey()]); ?>">
                    <span><?= $project->getName(); ?></span>
                    <?php if ($project->usePrefix()): ?>
                        <span class="count-badge"><?= mb_strtoupper($project->getPrefix()); ?></span>
                    <?php endif; ?>
                </a>
            </span>
            <?php if ($project->hasDescription()): ?>
                <div class="description">
                    <?= \pachno\core\helpers\TextParser::parseText($project->getDescription()); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <nav class="button-group">
        <?php if ($project->hasHomepage()): ?>
            <a href="<?= $project->getHomepage(); ?>" target="_blank" class="button secondary"><?= fa_image_tag('globe') . '<span>'.__('Website').'</span>'; ?></a>
        <?php endif; ?>
        <?php if ($project->hasDocumentationURL()): ?>
            <a href="<?= $project->getDocumentationURL(); ?>" target="_blank" class="button secondary"><?= fa_image_tag('book') . '<span>'.__('Documentation').'</span>'; ?></a>
        <?php endif; ?>
        <?php Event::createNew('core', 'project_overview_item_links', $project)->trigger(); ?>
        <?php if ($pachno_user->hasProjectPermission(Permissions::PERMISSION_PROJECT_ACCESS_ISSUES, $project)): ?>
            <?= link_tag(make_url('project_open_issues', array('project_key' => $project->getKey())), fa_image_tag('file-alt') . '<span>'.__('Issues').'</span>', ['class' => 'button secondary']); ?>
        <?php endif; ?>
        <?php if ($pachno_user->canManageProject($project)): ?>
            <div class="dropper-container">
                <button class="dropper button secondary icon"><?php echo fa_image_tag('ellipsis-v', ['class' => 'icon']); ?></button>
                <div class="dropdown-container">
                    <div class="list-mode">
                        <a href="javascript:void(0);" class="list-item trigger-backdrop" data-url="<?php echo make_url('get_partial_for_backdrop', ['key' => 'project_add_people', 'invite' => true, 'project_id' => $project->getID()]); ?>">
                            <?= fa_image_tag('user-plus', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Invite someone to this project'); ?></span>
                        </a>
                        <div class="list-item separator"></div>
                        <a href="<?= make_url('project_settings', ['project_key' => $project->getKey()]); ?>" class="list-item">
                            <?= fa_image_tag('cog', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Configure project'); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!$project->isLocked() && $pachno_user->canReportIssues($project)): ?>
            <button class="button secondary highlight report-issue-button trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'reportissue', 'project_id' => $project->getId()]); ?>">
                <?= fa_image_tag('plus-square', ['class' => 'icon']); ?>
                <span><?= __('New issue'); ?></span>
            </button>
        <?php endif; ?>
    </nav>
</div>
<?php if ($include_subprojects && $project->hasChildren()): ?>
    <div class="subprojects-list">
        <h5><?= __('Subprojects'); ?></h5>
        <div class="configurable-components-list">
            <?php foreach ($project->getChildren() as $child): ?>
                <?php include_component('project/subproject', ['project' => $child]); ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
