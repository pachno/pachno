<?php

    /** @var \pachno\core\entities\User $pachno_user */

?>
<?php if ($project_count > 0): ?>
    <ul class="project_list simple-list">
        <?php foreach ($projects as $project): ?>
            <li><?php include_component('project/project', compact('project')); ?></li>
        <?php endforeach; ?>
    </ul>
    <?php if ($pagination->getTotalPages() > 1): ?>
        <?php include_component('main/pagination', compact('pagination')); ?>
    <?php endif; ?>
<?php else: ?>
    <div class="onboarding large">
        <div class="image-container">
            <?php if ($pachno_user->isGuest()): ?>
                <?= image_tag('/unthemed/onboarding_login_more_projects.png', [], true); ?>
            <?php else: ?>
                <?= image_tag('/unthemed/no-projects.png', [], true); ?>
            <?php endif; ?>
        </div>
        <div class="helper-text">
            <?php if ($list_mode == 'all'): ?>
                <?php if ($show_project_config_link): ?>
                    <?php if ($project_state == 'archived'): ?>
                        <?= __('There are no archived projects. Whenever you archive a project, you can find it here.'); ?>
                    <?php else: ?>
                        <?= __('Every journey starts with the first step.'); ?><br>
                        <?= __('Create your first project to get started.'); ?>
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
                    <?= __('There are no projects linked to this team. Get started by clicking the "%create_project" button', ['%create_project' => __('Create project')]); ?>
                <?php elseif ($project_state == 'archived'): ?>
                    <?= __("There are no archived projects for this team."); ?>
                <?php else: ?>
                    <?= __("There are no projects linked to this team."); ?>
                <?php endif; ?>
            <?php elseif ($list_mode == 'client'): ?>
                <?php if ($show_project_config_link): ?>
                    <?= __('There are no projects linked to this client. Get started by clicking the "%create_project" button', ['%create_project' => __('Create project')]); ?>
                <?php elseif ($project_state == 'archived'): ?>
                    <?= __("There are no archived projects for this team."); ?>
                <?php else: ?>
                    <?= __("There are no projects linked to this client."); ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
