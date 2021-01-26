<?php

    $pachno_response->setTitle(__('"%project_name" project planning', array('%project_name' => $selected_project->getName())));

?>
<div class="content-with-sidebar">
    <?php include_component('project/sidebar', ['fixed' => true]); ?>
    <div class="project-boards-list-container boards-container">
        <div id="agileboards" class="project-boards-list">
            <?php foreach ($project_boards as $board): ?>
                <?php include_component('agile/boardbox', compact('board')); ?>
            <?php endforeach; ?>
            <?php foreach ($user_boards as $board): ?>
                <?php include_component('agile/boardbox', compact('board')); ?>
            <?php endforeach; ?>
        </div>
        <div id="onboarding-no-boards" class="onboarding" style="<?php if (count($project_boards) + count($user_boards)) echo 'display: none;'; ?>">
            <div class="image-container">
                <?= image_tag('/unthemed/no-boards.png', [], true); ?>
            </div>
            <div class="helper-text">
                <?= __('Understand the full picture'); ?><br>
                <?= __('Boards let you organize issues and tasks the way you want'); ?>
            </div>
        </div>
    </div>
</div>