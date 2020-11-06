<?php

    $pachno_response->setTitle(__('"%project_name" project planning', array('%project_name' => $selected_project->getName())));

?>
<div class="content-with-sidebar">
    <?php include_component('project/sidebar'); ?>
    <div class="project-boards-list-container boards-container">
        <div id="roadmap-header" class="top-search-filters-container">
            <div class="header">
                <div class="name-container">
                    <span class="board-name"><?= __('Project boards'); ?></span>
                </div>
                <div class="stripe-container">
                    <div class="stripe"></div>
                </div>
            <?php if (!$pachno_user->isGuest()): ?>
                <button class="button primary" onclick="Pachno.UI.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'agileboard', 'project_id' => $selected_project->getID(), 'is_private' => 0)); ?>');"><?= fa_image_tag('plus-square'); ?><span><?= __('Create board'); ?></span></button>
            <?php endif; ?>
            </div>
        </div>
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