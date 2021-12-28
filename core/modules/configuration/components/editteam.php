<?php

    use pachno\core\entities\Team;
    use pachno\core\entities\User;
    use pachno\core\framework\Context;
    use pachno\core\framework\Event;

    /**
     * @var Team $team
     * @var string $members_url
     * @var string $form_url
     */

?>
<div class="backdrop_box huge edit_agileboard">
    <div class="backdrop_detail_header">
        <span><?php echo ($team->getId()) ? __('Edit team') : __('Create new team'); ?></span>
        <a href="javascript:void(0);" class="closer"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content with-sidebar">
        <div class="sidebar">
            <div class="list-mode tab-switcher" id="team_form_tabs">
                <a href="javascript:void(0);" data-tab-target="team-info" class="tab-switcher-trigger list-item selected">
                    <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Information'); ?></span>
                </a>
                <a href="javascript:void(0);" data-tab-target="members" class="<?= ($team->getID()) ? 'tab-switcher-trigger' : 'disabled'; ?> list-item">
                    <?= fa_image_tag('users', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Members'); ?></span>
                </a>
                <a href="javascript:void(0);" data-tab-target="permissions" class="<?= ($team->getID()) ? 'tab-switcher-trigger' : 'disabled'; ?> list-item">
                    <?= fa_image_tag('lock', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Team permissions'); ?></span>
                </a>
            </div>
        </div>
        <div class="content" id="team_form_tabs_panes">
            <div data-tab-id="team-info" class="form-container">
                <form action="<?php echo $form_url; ?>" id="edit_team_form" method="post" data-simple-submit>
                    <div class="form-row">
                        <input type="text" id="team_<?php echo $team->getID(); ?>_name" name="name" value="<?php echo __e($team->getName()); ?>" class="name-input-enhance">
                        <label style for="team_<?php echo $team->getID(); ?>_name"><?php echo __('Team name'); ?></label>
                        <?php if (!$team->getID()): ?>
                            <div class="message-box type-info">
                                <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                                <span><?= __('You can add team members and define permissions after the team has been created.'); ?></span>
                            </div>
                        <?php else: ?>
                            <div class="helper-text">
                                <?php echo __("Team names can be anything - it's an arbitrary collection of users."); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="form-row submit-container">
                        <button type="submit" class="button primary">
                            <?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin icon']); ?>
                            <span><?php echo ($team->getID()) ? __('Save team') : __('Create team'); ?></span>
                        </button>
                    </div>
                </form>
            </div>
            <div data-tab-id="members" class="form-container" style="display: none;">
                <div id="team_members_list" class="flexible-table assignee-results-list">
                    <div class="row header">
                        <div class="column header name-container"><?= __('Name'); ?></div>
                        <div class="column header role"><?= __('Role'); ?></div>
                        <div class="column header actions"></div>
                    </div>
                    <div class="row">
                        <div class="column name-container" id="team-lead-container" data-team-id="<?= $team->getID(); ?>" data-url="<?= $members_url; ?>">
                            <?php if ($team->getTeamLead() instanceof User): ?>
                                <?php include_component('main/userdropdown', ['user' => $team->getTeamLead(), 'size' => 'small']); ?>
                            <?php else: ?>
                                <?= __('No team lead assigned'); ?>
                            <?php endif; ?>
                        </div>
                        <div class="column">
                            <span class="count-badge"><?= __('Team lead'); ?></span>
                        </div>
                        <div class="column actions">
                            <div class="dropper-container">
                                <button class="button dropper secondary icon"><?= fa_image_tag('ellipsis-v'); ?></button>
                                <?php include_component('main/identifiableselector', [
                                    'base_id'         => 'internal_contact',
                                    'header'          => __('Change / set team lead'),
                                    'clear_link_text' => __('Clear team lead'),
                                    'trigger_class'   => 'trigger-set-team-lead',
                                    'allow_clear'     => true,
                                    'include_teams'   => false
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    <?php foreach ($team->getMembers() as $member): ?>
                        <?php include_component('configuration/team_member', ['user' => $member, 'team' => $team]); ?>
                    <?php endforeach; ?>
                </div>
                <div class="form-container">
                    <form accept-charset="<?= Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_team_members', ['team_id' => $team->getID()]); ?>" method="post" data-simple-submit data-update-container="#find_team_members_results" id="find_team_members_form">
                        <div class="form-row search-container">
                            <label for="add_team_search_input"></label>
                            <input type="search" name="find_by" id="add_team_search_input" value="" placeholder="<?= __('Enter user details or email address to find or invite users'); ?>">
                            <button type="submit" class="button primary">
                                <?= fa_image_tag('search', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Find'); ?></span>
                                <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                            </button>
                        </div>
                    </form>
                </div>
                <div id="find_team_members_results">
                    <div class="onboarding medium">
                        <div class="image-container">
                            <?= image_tag('/unthemed/onboarding_invite.png', [], true); ?>
                        </div>
                        <div class="helper-text">
                            <?= __('Add existing users or invite new users by adding them to the team'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div data-tab-id="permissions" class="form-container" style="display: none;">
                <form action="<?php echo make_url('configure_team', ['team_id' => $team->getID()]); ?>" id="edit_team_permissions_form" method="post" data-simple-submit>
                    <div class="form-row">
                        <div class="list-mode">
                            <div class="interactive_menu_values filter_existing_values">
                                <?php include_component('configuration/grouppermissionseditlist', ['target' => $team, 'permissions_list' => Context::getAvailablePermissions('user'), 'module' => 'core', 'target_id' => null]); ?>
                                <?php include_component('configuration/grouppermissionseditlist', ['target' => $team, 'permissions_list' => Context::getAvailablePermissions('pages'), 'module' => 'core', 'target_id' => null]); ?>
                                <?php include_component('configuration/grouppermissionseditlist', ['target' => $team, 'permissions_list' => Context::getAvailablePermissions('configuration'), 'module' => 'core', 'target_id' => null, 'is_configuration' => true]); ?>
                                <?php Event::createNew('core', 'teampermissionsedit', $team)->trigger(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-row submit-container">
                        <button type="submit" class="button primary">
                            <?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin icon']); ?>
                            <span><?php echo __('Save team'); ?></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
