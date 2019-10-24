<?php

    /**
     * @var \pachno\core\entities\Role[] $global_roles
     * @var \pachno\core\entities\Role[] $project_roles
     * @var \pachno\core\entities\User[] $users
     * @var \pachno\core\entities\Team[] $teams
     */

?>
<?php if ($message): ?>
    <div class="onboarding medium">
        <div class="image-container">
            <?= image_tag('/unthemed/onboarding_search_missing_input.png', [], true); ?>
        </div>
        <div class="helper-text">
            <?= __('Woops, you probably forgot to type something before you tried searching.'); ?>
        </div>
    </div>
<?php elseif (!count($users) && isset($email)): ?>
    <div class="helper-text">
        <div class="image-container"><?= image_tag('/unthemed/onboarding_invite_email.png', [], true); ?></div>
        <span class="description">
            <?= __('There are no users registered with that email address. Send them an invite below!'); ?>
        </span>
    </div>
    <div class="flexible-table">
        <div class="row">
            <div class="column header name-container"><?= __('Invite user'); ?></div>
        </div>
        <div class="row">
            <div class="column name-container">
                <?= $email; ?>
            </div>
            <div class="column">
                <div class="fancy-dropdown-container">
                    <div class="fancy-dropdown" data-default-label="<?= __('No role selected'); ?>">
                        <label><?php echo __('Role: %role_name', ['%role_name' => '']); ?></label>
                        <span class="value"><?= __('Do nothing'); ?></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <?php foreach ($global_roles as $role): ?>
                                <input type="radio" class="fancy-checkbox" name="role" value="<?= $role->getID(); ?>" id="add_team_email_role_<?= $role->getID(); ?>">
                                <label for="add_team_email_role_<?= $role->getID(); ?>" class="list-item">
                                    <span class="name value"><?= $role->getName(); ?></span>
                                </label>
                            <?php endforeach; ?>
                            <?php foreach ($project_roles as $role): ?>
                                <input type="radio" class="fancy-checkbox" name="role" value="<?= $role->getID(); ?>" id="add_team_email_role_<?= $role->getID(); ?>">
                                <label for="add_team_email_role_<?= $role->getID(); ?>" class="list-item">
                                    <span class="name value"><?= $role->getName(); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="target" value="project_<?php echo $selected_project->getID(); ?>">
                <input type="hidden" name="user_id" value="0">
                <input type="hidden" name="email" value="<?= $email; ?>">
            </div>
            <div class="column actions">
                <button class="button secondary" onclick="Pachno.Project.assign('<?php echo make_url('configure_project_add_assignee', array('project_id' => $selected_project->getID(), 'assignee_type' => 'invite', 'assignee_id' => $email)); ?>', 'assign_email_container');return false;">
                    <span class="name"><?php echo __('Invite'); ?></span>
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
    <div class="flexible-table">
        <div class="row">
            <div class="column header name-container"><?= __('Name'); ?></div>
            <div class="column header"></div>
            <div class="column header actions"></div>
        </div>
        <?php foreach ($teams as $team): ?>
            <div class="row">
                <div class="column name-container">
                    <label for="role_team_<?php echo $team->getID(); ?>">
                        <?php echo fa_image_tag('users', ['class' => 'icon']); ?>
                        <span><?php echo $team->getName(); ?></span>
                    </label>&nbsp;
                </div>
                <div class="column">
                    <div class="fancy-dropdown-container">
                        <div class="fancy-dropdown" data-default-label="<?= __('No role selected'); ?>">
                            <label><?php echo __('Role: %role_name', ['%role_name' => '']); ?></label>
                            <span class="value"><?= __('Do nothing'); ?></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <?php foreach ($global_roles as $role): ?>
                                    <input type="radio" class="fancy-checkbox" name="role" value="<?= $role->getID(); ?>" id="add_team_<?= $team->getID(); ?>_role_<?= $role->getID(); ?>">
                                    <label for="add_team_<?= $team->getID(); ?>_role_<?= $role->getID(); ?>" class="list-item">
                                        <span class="name value"><?= $role->getName(); ?></span>
                                    </label>
                                <?php endforeach; ?>
                                <?php foreach ($project_roles as $role): ?>
                                    <input type="radio" class="fancy-checkbox" name="role" value="<?= $role->getID(); ?>" id="add_team_<?= $team->getID(); ?>_role_<?= $role->getID(); ?>">
                                    <label for="add_team_<?= $team->getID(); ?>_role_<?= $role->getID(); ?>" class="list-item">
                                        <span class="name value"><?= $role->getName(); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="target" value="project_<?php echo $selected_project->getID(); ?>">
                </div>
                <div class="column actions">
                    <button class="button secondary" onclick="Pachno.Project.assign('<?php echo make_url('configure_project_add_assignee', array('project_id' => $selected_project->getID(), 'assignee_type' => 'team', 'assignee_id' => $team->getID())); ?>', 'assign_team_<?php echo $team->getID(); ?>');return false;">
                        <span class="name"><?php echo __('Add'); ?></span>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
        <?php foreach ($users as $user): ?>
            <div class="row">
                <div class="column name-container">
                    <label for="role_user_<?php echo $user->getID(); ?>">
                        <?php echo image_tag($user->getAvatarURL(16), ['class' => 'avatar small'], true); ?>
                        <span><?php echo $user->getNameWithUsername(); ?></span>
                    </label>&nbsp;
                </div>
                <div class="column">
                    <div class="fancy-dropdown-container">
                        <div class="fancy-dropdown" data-default-label="<?= __('No role selected'); ?>">
                            <label><?php echo __('Role: %role_name', ['%role_name' => '']); ?></label>
                            <span class="value"><?= __('Do nothing'); ?></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <?php foreach ($global_roles as $role): ?>
                                    <input type="radio" class="fancy-checkbox" name="role" value="<?= $role->getID(); ?>" id="add_user_<?= $user->getID(); ?>_role_<?= $role->getID(); ?>">
                                    <label for="add_user_<?= $user->getID(); ?>_role_<?= $role->getID(); ?>" class="list-item">
                                        <span class="name value"><?= $role->getName(); ?></span>
                                    </label>
                                <?php endforeach; ?>
                                <?php foreach ($project_roles as $role): ?>
                                    <input type="radio" class="fancy-checkbox" name="role" value="<?= $role->getID(); ?>" id="add_user_<?= $user->getID(); ?>_role_<?= $role->getID(); ?>">
                                    <label for="add_user_<?= $user->getID(); ?>_role_<?= $role->getID(); ?>" class="list-item">
                                        <span class="name value"><?= $role->getName(); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="target" value="project_<?php echo $selected_project->getID(); ?>">
                </div>
                <div class="column actions">
                    <button class="button secondary" onclick="Pachno.Project.assign('<?php echo make_url('configure_project_add_assignee', array('project_id' => $selected_project->getID(), 'assignee_type' => 'user', 'assignee_id' => $user->getID())); ?>', 'assign_user_<?php echo $user->getID(); ?>');return false;">
                        <span class="name"><?php echo __('Add'); ?></span>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<div style="padding: 10px 0 10px 0; display: none;" id="assign_dev_indicator"><span style="float: left;"><?php echo image_tag('spinning_16.gif'); ?></span>&nbsp;<?php echo __('Please wait'); ?></div>
