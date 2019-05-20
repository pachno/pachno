<?php if (count($assignees) > 0): ?>
    <?php foreach ($assignees as $assignee): ?>
        <div class="project_team_assignee">
            <?php if ($assignee instanceof \pachno\core\entities\User): ?>
                <?php echo include_component('main/userdropdown', array('user' => $assignee)); ?>
            <?php else: ?>
                <?php echo include_component('main/teamdropdown', array('team' => $assignee)); ?>
            <?php endif; ?>
            <span class="faded_out"> -
                <?php $roles = ($assignee instanceof \pachno\core\entities\User) ? $project->getRolesForUser($assignee) : $project->getRolesForTeam($assignee); ?>
                <?php $role_names = array(); ?>
                <?php foreach ($roles as $role) $role_names[] = $role->getName(); ?>
                <?php echo join(', ', $role_names); ?>
            </span>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="onboarding medium">
        <div class="image-container">
            <?= image_tag('/unthemed/project-no-users-or-teams.png', [], true); ?>
        </div>
        <div class="helper-text">
            <?= __("This project has no users or teams"); ?><br>
            <?= __('Like a boat with no captain and no crew'); ?>
        </div>
    </div>
<?php endif; ?>
<?php if ($pachno_user->canEditProjectDetails($project)): ?>
    <div class="button-container">
        <a href="<?= make_url('project_settings', ['project_key' => $project->getKey()]); ?>" class="button secondary project-quick-edit">
            <?= fa_image_tag('users', ['class' => 'icon']); ?>
            <span><?= __('Set up project team'); ?></span>
        </a>
    </div>
<?php endif; ?>
