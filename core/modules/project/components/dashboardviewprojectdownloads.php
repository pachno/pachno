<?php

    $found = false;
    
    foreach ($editions as $releases)
    {
        if (array_key_exists(0, $releases))
        {
            $found = true;
            
            if ($releases[0]->getEdition() instanceof \pachno\core\entities\Edition)
                echo '<div class="tab_header">'.$releases[0]->getEdition()->getName().'</div>';

            echo '<ul class="simple-list">'.get_component_html('project/release', array('build' => $releases[0])).'</ul>';
        }
    }

?>
<?php if (!$found): ?>
    <div class="onboarding medium">
        <div class="image-container">
            <?= image_tag('/unthemed/project-no-releases.png', [], true); ?>
        </div>
        <div class="helper-text">
            <?= __("There are no downloadable releases"); ?><br>
            <?= __('But check back later.'); ?>
        </div>
    </div>
    <?php if ($pachno_user->canEditProjectDetails($project) && $project->isBuildsEnabled()): ?>
        <div class="button-container">
            <a href="<?= make_url('project_release_center', ['project_key' => $project->getKey()]); ?>" class="button secondary project-quick-edit">
                <?= fa_image_tag('cloud-download-alt', ['class' => 'icon']); ?>
                <span><?= __('Manage project releases'); ?></span>
            </a>
        </div>
    <?php endif; ?>
<?php endif; ?>
