<?php

    /**
     * @var \pachno\core\entities\Team $team
     */

?>
<div class="backdrop_box large edit_agileboard">
    <div class="backdrop_detail_header">
        <span><?php echo ($team->getId()) ? __('Edit team') : __('Create new team'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
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
                <form action="<?php echo make_url('configure_team', ['team_id' => $team->getID()]); ?>" id="edit_team_form" method="post" data-simple-submit>
                    <div class="form-row">
                        <input type="text" id="team_<?php echo $team->getID(); ?>_name" name="name" value="<?php echo htmlentities($team->getName(), ENT_COMPAT, \pachno\core\framework\Context::getI18n()->getCharset()); ?>" class="name-input-enhance">
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
            <div data-tab-id="permissions" class="form-container" style="display: none;">
                <form action="<?php echo make_url('configure_team', ['team_id' => $team->getID(), 'save_permissions' => '1']); ?>" id="edit_team_permissions_form" method="post" data-simple-submit>
                    <div class="form-row">
                        <div class="list-mode">
                            <div class="interactive_menu_values filter_existing_values">
                                <?php include_component('configuration/grouppermissionseditlist', ['target' => $team, 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('user'), 'module' => 'core', 'target_id' => null]); ?>
                                <?php include_component('configuration/grouppermissionseditlist', ['target' => $team, 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('pages'), 'module' => 'core', 'target_id' => null]); ?>
                                <?php include_component('configuration/grouppermissionseditlist', ['target' => $team, 'permissions_list' => \pachno\core\framework\Context::getAvailablePermissions('configuration'), 'module' => 'core', 'target_id' => null, 'is_configuration' => true]); ?>
                                <?php \pachno\core\framework\Event::createNew('core', 'teampermissionsedit', $team)->trigger(); ?>
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
