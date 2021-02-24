<?php

    /** @var \pachno\core\entities\User $pachno_user */

?>
<?php if ($project_count > 0): ?>
    <div class="project-list">
        <?php foreach ($projects as $project): ?>
            <?php include_component('project/project', ['project' => $project, 'include_subprojects' => true]); ?>
        <?php endforeach; ?>
    </div>
    <?php if ($pagination->getTotalPages() > 1): ?>
        <?php include_component('main/pagination', compact('pagination')); ?>
    <?php endif; ?>
<?php else: ?>
    <div class="onboarding large">
        <div class="image-container">
            <?php if ($project_state == 'archived'): ?>
                <?= image_tag('/unthemed/no-archived-projects.png', [], true); ?>
            <?php else: ?>
                <?= image_tag('/unthemed/no-projects.png', [], true); ?>
            <?php endif; ?>
        </div>
        <div class="helper-text">
            <?php if ($list_mode == 'all'): ?>
                <?php if ($show_project_config_link): ?>
                    <?php if ($project_state == 'archived'): ?>
                        <span class="title"><?= __('There are no archived projects'); ?></span>
                        <span><?= __('Archived projects can be found in this list'); ?></span>
                    <?php else: ?>
                        <span class="title"><?= __('Every journey starts with the first step'); ?></span>
                        <span><?= __('Create your first project to get started'); ?></span>
                    <?php endif; ?>
                <?php elseif ($project_state == 'archived'): ?>
                    <?= __("There are no archived projects."); ?>
                <?php elseif (!$pachno_user->isGuest()): ?>
                    <?= __("You don't have access to any projects yet."); ?>
                <?php else: ?>
                    <?= __("Log in to see projects in this space"); ?>
                <?php endif; ?>
            <?php elseif ($list_mode == 'team'): ?>
                <?php if ($show_project_config_link): ?>
                    <?= __('There are no projects linked to this team. Get started by clicking the "%create_a_project" button', ['%create_a_project' => __('Create a project')]); ?>
                <?php elseif ($project_state == 'archived'): ?>
                    <?= __("There are no archived projects for this team."); ?>
                <?php else: ?>
                    <?= __("There are no projects linked to this team."); ?>
                <?php endif; ?>
            <?php elseif ($list_mode == 'client'): ?>
                <?php if ($show_project_config_link): ?>
                    <?= __('There are no projects linked to this client. Get started by clicking the "%create_a_project" button', ['%create_a_project' => __('Create a project')]); ?>
                <?php elseif ($project_state == 'archived'): ?>
                    <?= __("There are no archived projects for this team."); ?>
                <?php else: ?>
                    <?= __("There are no projects linked to this client."); ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
