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
    <p class="no-items">
        <?= fa_image_tag('users'); ?>
        <span><?php echo __('No users or teams assigned to this project'); ?></span>
        <?php if ($pachno_user->canEditProjectDetails($project)): ?>
            <a href="<?= make_url('project_settings', ['project_key' => $project->getKey()]); ?>" class="button project-quick-edit"><?= __('Set up project team'); ?></a>
        <?php endif; ?>
    </p>
<?php endif; ?>
