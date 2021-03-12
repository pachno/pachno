<?php

    $pachno_response->setTitle(__('Configure users, teams and clients'));
    $users_text = (\pachno\core\framework\Context::getScope()->getMaxUsers()) ? __('Users (%num/%max)', array('%num' => '<span id="current_user_num_count">'.\pachno\core\entities\User::getUsersCount().'</span>', '%max' => \pachno\core\framework\Context::getScope()->getMaxUsers())) : __('Users');
    $teams_text = (\pachno\core\framework\Context::getScope()->getMaxTeams()) ? __('Teams (%num/%max)', array('%num' => '<span id="current_team_num_count">'.\pachno\core\entities\Team::countAll().'</span>', '%max' => \pachno\core\framework\Context::getScope()->getMaxTeams())) : __('Teams');

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_USERS]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
            <h1>
                <span class="name"><?= __('Manage users and groups'); ?></span>
            </h1>
            <div class="fancy-tabs tab-switcher">
                <a class="tab tab-switcher-trigger selected" data-tab-target="users" href="javascript:void(0);">
                    <?= fa_image_tag('user', ['class' => 'icon']); ?>
                    <span class="name"><?= $users_text; ?><span class="count-badge"><?= $number_of_users; ?></span></span>
                </a>
                <a class="tab tab-switcher-trigger" data-tab-target="groups" href="javascript:void(0);">
                    <?= fa_image_tag('users', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Groups'); ?></span>
                </a>
            </div>
            <div id="usersteamsgroups_menu_panes">
                <div id="tab_users_pane" class="top-search-filters-container" data-tab-id="users">
                    <form action="<?= make_url('configure_users_find_user'); ?>" method="post" data-simple-submit data-update-container="#users_results" id="find_users_form">
                        <div class="search-and-filters-strip">
                            <div class="search-strip">
                                <div class="dropper-container">
                                    <button type="button" class="button secondary icon dropper"><?= fa_image_tag('ellipsis-v'); ?></button>
                                    <div class="dropdown-container from-left">
                                        <div class="list-mode">
                                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Config.User.show('<?= make_url('configure_users_find_user'); ?>', 'all');">
                                                <span class="name"><?= __('Show all users'); ?></span>
                                            </a>
                                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Config.User.show('<?= make_url('configure_users_find_user'); ?>', 'unactivated');">
                                                <span class="name"><?= __('Show unactivated users'); ?></span>
                                            </a>
                                            <a href="javascript:void(0);" class="list-item" onclick="Pachno.Config.User.show('<?= make_url('configure_users_find_user'); ?>', 'newusers');">
                                                <span class="name"><?= __('Show newly created users'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <input type="search" name="findstring" id="findusers" value="<?= $finduser; ?>" placeholder="<?= __('Type user details to find users'); ?>" class="filter_searchfield">
                                <button type="submit" class="button secondary">
                                    <?= fa_image_tag('search', ['class' => 'icon']); ?>
                                    <span class="name"><?= __('Find'); ?></span>
                                    <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                                </button>
                                <button style="<?php if (!\pachno\core\framework\Context::getScope()->hasUsersAvailable()): ?>display: none;<?php endif; ?>" type="button" class="button secondary icon" onclick="$('#adduser_div').toggle();">
                                    <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                                </button>
                            </div>
                        </div>
                    </form>
                    <div id="users_results" class="search-results"></div>
                </div>
                <div id="tab_groups_pane" data-tab-id="groups" style="display: none;">
                    <div class="lightyellowbox" style="margin-top: 5px; padding: 7px;">
                        <form id="create_group_form" action="<?= make_url('configure_users_add_group'); ?>" method="post" accept-charset="<?= \pachno\core\framework\Settings::getCharset(); ?>" onsubmit="Pachno.Config.Group.add('<?= make_url('configure_users_add_group'); ?>');return false;">
                            <div id="add_group">
                                <label for="group_name"><?= __('Create a new group'); ?></label>
                                <input type="text" id="group_name" name="group_name" placeholder="<?= __('Enter group name here'); ?>">
                                <input type="submit" value="<?= __('Create'); ?>">
                            </div>
                        </form>
                    </div>
                    <table cellpadding=0 cellspacing=0 style="display: none; margin-left: 5px; width: 300px;" id="create_group_indicator">
                        <tr>
                            <td style="width: 20px; padding: 2px;"><?= image_tag('spinning_20.gif'); ?></td>
                            <td style="padding: 0px; text-align: left;"><?= __('Adding group, please wait'); ?>...</td>
                        </tr>
                    </table>
                    <div id="groupconfig_list">
                        <?php foreach ($groups as $group): ?>
                            <?php include_component('configuration/groupbox', array('group' => $group)); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="fullpage_backdrop" id="adduser_div" style="display: none;">
    <script>
        var import_cb = function () {
            Pachno.UI.Dialog.show('<?= __('Would you like to add this user to the current scope?'); ?>',
                '<?= __('The username you tried to create already exists. You can give this user access to the current scope by pressing "%yes" below. If you want to create a different user, press "%no" and enter a different username.', array('%yes' => __('yes'), '%no' => __('no'))); ?>',
                {
                    yes: {
                        click: function() {Pachno.Config.User.addToScope('<?= make_url('configure_users_import_user'); ?>');}
                    },
                    no: {click: Pachno.UI.Dialog.dismiss}
                });
        };
    </script>
    <div class="fullpage_backdrop_content">
        <div class="backdrop_box medium">
            <div class="backdrop_detail_header">
                <span><?= __('Add a user'); ?></span>
                <?= javascript_link_tag(fa_image_tag('times'), ['onclick' => "$('#adduser_div').toggle();", 'class' => 'closer']); ?>
            </div>
            <div class="backdrop_detail_content">
                <div class="form-container">
                    <form action="<?= make_url('configure_users_add_user'); ?>" method="post" onsubmit="Pachno.Config.User.add('<?= make_url('configure_users_add_user'); ?>', import_cb);return false;" id="createuser_form">
                        <div class="form-row">
                            <input type="text" name="username" id="adduser_username" class="name-input-enhance" placeholder="<?= __('Enter the username here'); ?>">
                            <label for="adduser_username" class="required"><?= __('Username'); ?></label>
                        </div>
                        <div class="form-row">
                            <input type="text" name="realname" id="adduser_realname" style="width: 300px;">
                            <label for="adduser_realname"><?= __('Full name'); ?></label>
                        </div>
                        <div class="form-row">
                            <input type="text" name="email" id="adduser_email" style="width: 300px;">
                            <label for="adduser_email"><?= __('Email address'); ?></label>
                        </div>
                        <?php \pachno\core\framework\Event::createNew('core', 'config.createuser.email')->trigger(); ?>
                        <div class="form-row">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown">
                                    <label><?= __('Add user to group'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <?php foreach ($groups as $group): ?>
                                            <input type="radio" name="group_id" class="fancy-checkbox" id="add-user-group-<?= $group->getId(); ?>" value="<?= $group->getID(); ?>" <?php if ($group->getID() == \pachno\core\framework\Settings::getDefaultGroup()->getID()) echo ' checked'; ?>>
                                            <label for="add-user-group-<?= $group->getId(); ?>" class="list-item">
                                                <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                                <span class="name value"><?= $group->getName(); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="fancy-dropdown-container">
                                <div class="fancy-dropdown" data-default-label="<?= __('No teams selected'); ?>">
                                    <label><?= __('Add user to team'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <?php foreach ($teams as $team): ?>
                                            <input type="checkbox" name="team_id[<?= $team->getId(); ?>]" class="fancy-checkbox" id="add-user-team-<?= $team->getId(); ?>" value="<?= $team->getID(); ?>">
                                            <label for="add-user-team-<?= $team->getId(); ?>" class="list-item">
                                                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                <span class="name value"><?= $team->getName(); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row submit-container">
                            <button type="submit" class="button primary">
                                <span class="name"><?= __('Create user'); ?></span>
                                <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    <?php if ($finduser): ?>
        Pachno.on(Pachno.EVENTS.ready, function () {
            pachno_index_js.Config.User.show('<?= make_url('configure_users_find_user'); ?>', '<?= $finduser; ?>');
        });
    <?php endif; ?>
</script>
