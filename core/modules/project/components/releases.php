<?php

    use pachno\core\entities\Build;
    use pachno\core\entities\Project;

    /**
     * @var Project $project
     * @var Build[][] $builds
     * @var string $mode
     */

?>
<div class="flexible-table">
    <div class="row header">
        <div class="column header info-icons">&nbsp;</div>
        <div class="column header name-container"><?= __('Release name'); ?></div>
        <div class="column header"><?= __('Version'); ?></div>
        <div class="column header"><?= __('Release date'); ?></div>
        <div class="column header actions"></div>
    </div>
    <div class="body" id="<?= $mode; ?>_releases_list">
    <?php foreach ($builds[0] as $build): ?>
        <?php include_component('project/release', ['build' => $build]); ?>
    <?php endforeach; ?>
    </div>
    <?php if ($project->isEditionsEnabled()): ?>
        <?php foreach ($project->getEditions() as $edition_id => $edition): ?>
            <div class="body" id="<?= $mode; ?>_releases_list_<?= $edition_id; ?>">
                <?php foreach ($builds[$edition_id] as $build): ?>
                    <?php include_component('project/release', ['build' => $build]); ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
