<?php

    $pachno_response->addBreadcrumb(__('Planning'), make_url('agile_index', array('project_key' => $selected_project->getKey())));
    $pachno_response->setTitle(__('"%project_name" project planning', array('%project_name' => $selected_project->getName())));

?>
<div class="content-with-sidebar">
    <?php include_component('project/sidebar'); ?>
    <div class="project-boards-list-container boards-container">
        <h3>
            <span class="name"><?php echo __('Project boards'); ?></span>
            <button class="button primary" onclick="Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'agileboard', 'project_id' => $selected_project->getID(), 'is_private' => 0)); ?>');"><?= fa_image_tag('plus-square'); ?><span><?= __('Create board'); ?></span></button>
        </h3>
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
                <?= __('Boards lets you organize issues and tasks the way you want'); ?>
            </div>
        </div>
    </div>
</div>