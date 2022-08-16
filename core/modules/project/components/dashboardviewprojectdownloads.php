<?php
    
    use pachno\core\entities\Build;
    use pachno\core\entities\Edition;
    
    /**
     * @var array<int, array<Build>> $editions
     * @var int $num_releases
     */
    
?>
<?php if (!$num_releases): ?>
    <div class="onboarding medium">
        <div class="image-container">
            <?= image_tag('/unthemed/project-no-releases.png', [], true); ?>
        </div>
        <div class="helper-text">
            <?= __("There are no downloadable releases"); ?><br>
            <?= __('But check back later.'); ?>
        </div>
    </div>
<?php else: ?>
    <div class="flexible-table">
        <?php foreach ($editions as $releases): ?>
            <?php foreach ($releases as $build): ?>
                <?php include_component('project/release', ['build' => $build]); ?>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
