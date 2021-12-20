<?php

    use pachno\core\entities\Permission;
    use pachno\core\entities\Project;
    use pachno\core\entities\tables\Permissions;
    use pachno\core\entities\User;
    use pachno\core\helpers\TextParser;
    use pachno\core\framework\Event;

    /**
     * @var Project $project
     * @var User $pachno_user
     */

?>
<div class="configurable-component">
    <div class="row">
        <div class="icon">
            <?= image_tag($project->getIconName(), ['alt' => '[i]'], true); ?>
        </div>
        <div class="name">
            <a href="<?= make_url('project_dashboard', array('project_key' => $project->getKey())); ?>" class="title">
                <span><?= $project->getName(); ?></span>
                <?php if ($project->usePrefix()): ?>
                    <span class="count-badge"><?= mb_strtoupper($project->getPrefix()); ?></span>
                <?php endif; ?>
            </a>
            <?php if ($project->hasDescription()): ?>
                <span class="description">
                    <?= TextParser::parseText($project->getDescription()); ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="button-group">
            <?php if ($project->hasHomepage()): ?>
                <a href="<?= $project->getHomepage(); ?>" target="_blank" class="button secondary"><?= fa_image_tag('globe'); ?><span><?= __('Website'); ?></span></a>
            <?php endif; ?>
            <?php if ($project->hasDocumentationURL()): ?>
                <a href="<?= $project->getDocumentationURL(); ?>" target="_blank" class="button secondary"><?= fa_image_tag('book'); ?><span><?= __('Documentation'); ?></span></a>
            <?php endif; ?>
            <?php Event::createNew('core', 'project_overview_item_links', $project)->trigger(); ?>
            <?php if ($pachno_user->hasProjectPermission(Permission::PERMISSION_PROJECT_ACCESS_ISSUES, $project)): ?>
                <a href="<?= make_url('project_open_issues', ['project_key' => $project->getKey()]); ?>" class="button secondary"><?= fa_image_tag('file-alt'); ?><span><?= __('Issues'); ?></span></a>
            <?php endif; ?><?php if (!$project->isLocked() && $pachno_user->canReportIssues($project)): ?>
                <button class="button secondary highlight trigger-backdrop" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'reportissue', 'project_id' => $project->getId()]); ?>">
                    <?= fa_image_tag('plus-square', ['class' => 'icon']); ?>
                    <span><?= __('New issue'); ?></span>
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>
