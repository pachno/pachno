<?php

    use pachno\core\framework\Event;

    /** @var \pachno\core\entities\Project $project */

?>
<div class="project-strip">
    <div class="icon-container">
        <div class="icon-large">
            <?= image_tag($project->getIconName(), array('alt' => '[i]'), true); ?>
        </div>
    </div>
    <div class="details">
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
    <nav class="button-group">
        <?php if ($project->hasHomepage()): ?>
            <a href="<?= $project->getHomepage(); ?>" target="_blank" class="button secondary"><?= fa_image_tag('globe') . '<span>'.__('Website').'</span>'; ?></a>
        <?php endif; ?>
        <?php if ($project->hasDocumentationURL()): ?>
            <a href="<?= $project->getDocumentationURL(); ?>" target="_blank" class="button secondary"><?= fa_image_tag('book') . '<span>'.__('Documentation').'</span>'; ?></a>
        <?php endif; ?>
        <?php Event::createNew('core', 'project_overview_item_links', $project)->trigger(); ?>
        <?php if ($pachno_user->canSearchForIssues() && $pachno_user->hasPageAccess('project_issues', $project->getID())): ?>
            <?= link_tag(make_url('project_open_issues', array('project_key' => $project->getKey())), fa_image_tag('file-alt') . '<span>'.__('Issues').'</span>', ['class' => 'button secondary']); ?>
        <?php endif; ?><?php if (!$project->isLocked() && $pachno_user->canReportIssues($project)): ?>
            <?= javascript_link_tag(fa_image_tag('plus-square') . '<span>'.__('New issue').'</span>', ['onclick' => "Pachno.Issues.Add('" . make_url('get_partial_for_backdrop', ['key' => 'reportissue', 'project_id' => $project->getId()]) . "', this);", 'class' => 'button secondary highlight']); ?>
        <?php endif; ?>
    </nav>
</div>
<?php if ($project->hasChildren()): ?>
    <div class="subprojects-list">
        <h5><?= __('Subprojects'); ?></h5>
        <div class="configurable-components-list">
            <?php foreach ($project->getChildren() as $child): ?>
                <?php include_component('project/subproject', ['project' => $child]); ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
