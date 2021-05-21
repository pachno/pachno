<?php

    use pachno\core\entities\Project;
    use pachno\core\entities\Role;
    use pachno\core\entities\Team;
    use pachno\core\entities\User;

    /**
     * @var Role[] $global_roles
     * @var Role[] $project_roles
     * @var User $email_user
     * @var User[] $users
     * @var Team[] $teams
     * @var Project $selected_project
     */

?>
<?php if (!count($users) && isset($email)): ?>
    <div class="helper-text">
        <div class="image-container"><?= image_tag('/unthemed/onboarding_invite_email.png', [], true); ?></div>
        <span class="description">
            <?= __('There are no users registered with that email address. Send them an invite below!'); ?>
        </span>
    </div>
    <div class="flexible-table" data-url="<?php echo make_url('configure_project_add_assignee', ['project_id' => $selected_project->getID()]); ?>">
        <div class="row">
            <div class="column header name-container"><?= __('Invite user'); ?></div>
        </div>
        <div class="row" data-email="<?= $email; ?>">
            <div class="column name-container">
                <?php echo image_tag($email_user->getAvatarURL(16), ['class' => 'avatar small'], true); ?>
                <span><?php echo $email_user->getEmail(); ?></span>
                <span class="count-badge tooltip-container">
                    <?= fa_image_tag('envelope-open-text', ['class' => 'icon']); ?><span><?= __('Not registered'); ?></span>
                    <span class="tooltip">
                        <span><?= __('An email will be sent to this user with information on how to log in'); ?></span>
                    </span>
                </span>
            </div>
            <div class="column">
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown" data-default-label="<?= __('No role selected'); ?>">
                        <label><?php echo __('Role: %role_name', ['%role_name' => '']); ?></label>
                        <span class="value"><?= __('Do nothing'); ?></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <?php foreach ($global_roles as $role): ?>
                                <input type="radio" class="fancy-checkbox" name="role_id" value="<?= $role->getID(); ?>" id="add_team_email_role_<?= $role->getID(); ?>">
                                <label for="add_team_email_role_<?= $role->getID(); ?>" class="list-item">
                                    <span class="name value"><?= $role->getName(); ?></span>
                                </label>
                            <?php endforeach; ?>
                            <?php foreach ($project_roles as $role): ?>
                                <input type="radio" class="fancy-checkbox" name="role_id" value="<?= $role->getID(); ?>" id="add_team_email_role_<?= $role->getID(); ?>">
                                <label for="add_team_email_role_<?= $role->getID(); ?>" class="list-item">
                                    <span class="name value"><?= $role->getName(); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column actions">
                <button class="button secondary trigger-assign-to-project">
                    <span class="name"><?php echo __('Invite'); ?></span>
                    <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                </button>
            </div>
        </div>
    </div>
<?php elseif (!count($teams) && !count($users)): ?>
    <div class="onboarding medium">
        <div class="image-container">
            <?= image_tag('/unthemed/onboarding_no_users_found.png', [], true); ?>
        </div>
        <div class="helper-text">
            <?= __("Oh noes, we didn't find any users or teams."); ?>
        </div>
    </div>
<?php else: ?>
    <div class="helper-text">
        <div class="image-container"><?= image_tag('/unthemed/onboarding_project_add_to_team.png', [], true); ?></div>
        <span class="description">
            <?= __('We found someone that matched what you searched for'); ?>
        </span>
    </div>
    <div class="flexible-table" data-url="<?php echo make_url('configure_project_add_assignee', ['project_id' => $selected_project->getID()]); ?>">
        <div class="row">
            <div class="column header name-container"><?= __('Name'); ?></div>
            <div class="column header"></div>
            <div class="column header actions"></div>
        </div>
        <?php foreach ($teams as $team): ?>
            <div class="row" data-id="<?= $team->getId(); ?>" data-identifiable-type="team">
                <div class="column name-container">
                    <?php echo fa_image_tag('users', ['class' => 'icon']); ?>
                    <span><?php echo $team->getName(); ?></span>
                </div>
                <div class="column">
                    <?php if (!$selected_project->isAssigned($team)): ?>
                        <div class="fancy-dropdown-container">
                            <div class="fancy-dropdown" data-default-label="<?= __('No role selected'); ?>">
                                <label><?php echo __('Role: %role_name', ['%role_name' => '']); ?></label>
                                <span class="value"><?= __('Do nothing'); ?></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach ($global_roles as $role): ?>
                                        <input type="radio" class="fancy-checkbox" name="role_id" value="<?= $role->getID(); ?>" id="add_team_<?= $team->getID(); ?>_role_<?= $role->getID(); ?>">
                                        <label for="add_team_<?= $team->getID(); ?>_role_<?= $role->getID(); ?>" class="list-item">
                                            <span class="name value"><?= $role->getName(); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                    <?php foreach ($project_roles as $role): ?>
                                        <input type="radio" class="fancy-checkbox" name="role_id" value="<?= $role->getID(); ?>" id="add_team_<?= $team->getID(); ?>_role_<?= $role->getID(); ?>">
                                        <label for="add_team_<?= $team->getID(); ?>_role_<?= $role->getID(); ?>" class="list-item">
                                            <span class="name value"><?= $role->getName(); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="column actions tooltip-container">
                    <?php if ($selected_project->isAssigned($team)): ?>
                        <button class="button secondary" disabled>
                            <span class="name"><?= fa_image_tag('check'); ?></span>
                        </button>
                        <div class="tooltip from-bottom from-right">
                            <span><?= __('This team is already assigned to the project'); ?></span>
                        </div>
                    <?php else: ?>
                        <button class="button secondary trigger-assign-to-project">
                            <span class="name"><?php echo __('Add'); ?></span>
                            <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <?php foreach ($users as $user): ?>
            <div class="row" data-id="<?= $user->getId(); ?>" data-identifiable-type="user">
                <div class="column name-container">
                    <?php echo image_tag($user->getAvatarURL(16), ['class' => 'avatar small'], true); ?>
                    <?php if ($user->isActivated()): ?>
                        <span><?php echo $user->getNameWithUsername(); ?></span>
                    <?php else: ?>
                        <span><?php echo $user->getUsername(); ?></span>
                        <span class="count-badge"><?= fa_image_tag('envelope-open-text', ['class' => 'icon']); ?><span><?= __('Not activated yet'); ?></span></span>
                    <?php endif; ?>
                </div>
                <div class="column">
                    <?php if (!$selected_project->isAssigned($user)): ?>
                        <div class="fancy-dropdown-container">
                            <div class="fancy-dropdown" data-default-label="<?= __('No role selected'); ?>">
                                <label><?php echo __('Role: %role_name', ['%role_name' => '']); ?></label>
                                <span class="value"><?= __('Do nothing'); ?></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach ($global_roles as $role): ?>
                                        <input type="radio" class="fancy-checkbox" name="role_id" value="<?= $role->getID(); ?>" id="add_user_<?= $user->getID(); ?>_role_<?= $role->getID(); ?>">
                                        <label for="add_user_<?= $user->getID(); ?>_role_<?= $role->getID(); ?>" class="list-item">
                                            <span class="name value"><?= $role->getName(); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                    <?php foreach ($project_roles as $role): ?>
                                        <input type="radio" class="fancy-checkbox" name="role_id" value="<?= $role->getID(); ?>" id="add_user_<?= $user->getID(); ?>_role_<?= $role->getID(); ?>">
                                        <label for="add_user_<?= $user->getID(); ?>_role_<?= $role->getID(); ?>" class="list-item">
                                            <span class="name value"><?= $role->getName(); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="column actions tooltip-container">
                    <?php if ($selected_project->isAssigned($user)): ?>
                        <button class="button secondary" disabled>
                            <span class="name"><?= fa_image_tag('check'); ?></span>
                        </button>
                        <div class="tooltip from-bottom from-right">
                            <span><?= __('This user is already assigned to the project'); ?></span>
                        </div>
                    <?php else: ?>
                        <button class="button secondary trigger-assign-to-project">
                            <span class="name"><?php echo __('Add'); ?></span>
                            <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
