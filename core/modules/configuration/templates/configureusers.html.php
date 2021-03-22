<?php

    $pachno_response->setTitle(__('Configure users, teams and clients'));
    $users_text = (\pachno\core\framework\Context::getScope()->getMaxUsers()) ? __('Users (%num/%max)', array('%num' => '<span id="current_user_num_count">'.\pachno\core\entities\User::getUsersCount().'</span>', '%max' => \pachno\core\framework\Context::getScope()->getMaxUsers())) : __('Users');
    $teams_text = (\pachno\core\framework\Context::getScope()->getMaxTeams()) ? __('Teams (%num/%max)', array('%num' => '<span id="current_team_num_count">'.\pachno\core\entities\Team::countAll().'</span>', '%max' => \pachno\core\framework\Context::getScope()->getMaxTeams())) : __('Teams');

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_USERS]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
            <h1><?= __('Manage users and groups'); ?></h1>
            <div class="helper-text centered">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_configure_users_groups.png', [], true); ?></div>
                <span class="description">
                    <?php echo __('Add, remove and manage users and user groups in this installation. For more information about user management, see the %online_documentation.', array('%online_documentation' => link_tag(\pachno\core\modules\publish\Publish::getArticleLink('ManageUsers'), __('Online documentation')))); ?>
                </span>
            </div>
            <div id="usersteamsgroups_menu_panes">
                <div id="tab_users_pane" data-tab-id="users">
                    <form action="<?= make_url('configure_users_find_user'); ?>" class="top-search-filters-container" method="post" data-simple-submit data-update-container="#users-results" id="find_users_form">
                        <div class="search-and-filters-strip">
                            <div class="search-strip">
                                <div class="dropper-container">
                                    <button type="button" class="button secondary icon dropper"><?= fa_image_tag('ellipsis-v'); ?></button>
                                    <div class="dropdown-container from-left">
                                        <div class="list-mode">
                                            <a href="javascript:void(0);" class="list-item trigger-find-users" data-url="<?= make_url('configure_users_find_user'); ?>?findstring=all">
                                                <span class="name"><?= __('Show all users'); ?></span>
                                            </a>
                                            <a href="javascript:void(0);" class="list-item trigger-find-users" data-url="<?= make_url('configure_users_find_user'); ?>?findstring=unactivated">
                                                <span class="name"><?= __('Show unactivated users'); ?></span>
                                            </a>
                                            <a href="javascript:void(0);" class="list-item trigger-find-users" data-url="<?= make_url('configure_users_find_user'); ?>?findstring=newusers">
                                                <span class="name"><?= __('Show newly created users'); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <input type="search" name="findstring" id="findusers" value="<?= $finduser; ?>" placeholder="<?= __('Enter user details here to find users'); ?>" class="filter_searchfield">
                                <button type="submit" class="button secondary">
                                    <?= fa_image_tag('search', ['class' => 'icon']); ?>
                                    <span class="name"><?= __('Find'); ?></span>
                                    <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                                </button>
                            </div>
                        </div>
                    </form>
                    <div id="users-results" class="search-results"></div>
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
    Pachno.on(Pachno.EVENTS.ready, () => {
        const $body = $('body');
        $body.on('click', '.trigger-find-users', function (event) {
            event.preventDefault();
            event.stopPropagation();
            const $link = $(this);
            const url = $link.data('url');
            const $form = $('#find_users_form');
            $form.addClass('submitting');

            Pachno.fetch(url, {
                method: 'POST'
            })
            .then((json) => {
                $('#users-results').html(json.content);
                $form.removeClass('submitting');
            })
        });

        $body.on('click', '.trigger-generate-password', function (event) {
            const $link = $(this);
            const url = $link.data('url');
            Pachno.UI.Dialog.show('<?php echo __('Generate new password for this user?'); ?>', '<?= __('Please confirm that you want to generate a new password for this user.'); ?>', {yes: {click: function() { Pachno.trigger(Pachno.EVENTS.configuration.generatePassword, { url });}}, no: {click: Pachno.UI.Dialog.dismiss}});
        });

        Pachno.on(Pachno.EVENTS.configuration.generatePassword, (PachnoApplication, data) => {
            const url = data.url;
            Pachno.UI.Dialog.setSubmitting();

            Pachno.fetch(url, {
                method: 'POST'
            })
            .then((json) => {
                Pachno.UI.Dialog.dismiss();
                Pachno.UI.Dialog.showModal('<?= __('Password reset'); ?>', '<?= __('The password has been reset. The new password is: %password'); ?>'.replace('%password', '<span class="command_box">' + json.password + '</span>'));
            })
        });
    })
</script>
