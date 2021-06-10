<?php

    use pachno\core\framework\Settings;

    /**
     * @var \pachno\core\framework\Response $pachno_response
     * @var \pachno\core\entities\User $pachno_user
     */

    $pachno_response->setTitle(__('Your account details'));

?>
<?php if (!$pachno_user->isGuest()): ?>
    <div class="fullpage_backdrop" id="change_password_div" style="<?php if (!$has_autopassword) echo 'display: none;'; ?>">
        <div class="fullpage_backdrop_content">
            <div class="backdrop_box medium">
                <div class="backdrop_detail_header">
                    <span><?= ($has_autopassword) ? __('Pick a password') : __('Changing your password'); ?></span>
                    <?php if (!$has_autopassword): ?>
                        <button class="closer" onclick="$('#change_password_div').toggle();"><?= fa_image_tag('times'); ?></button>
                    <?php endif; ?>
                </div>
                <div class="backdrop_detail_content">
                    <div class="form-container">
                        <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('account_change_password'); ?>" data-simple-submit data-auto-close-container method="post" id="change_password_form">
                            <?php if (Settings::isUsingExternalAuthenticationBackend()): ?>
                                <div class="form-row">
                                    <div class="helper-text">
                                        <span><?= \pachno\core\helpers\TextParser::parseText(Settings::get('changepw_message'), false, null, array('embedded' => true)); ?></span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="form-row">
                                    <div class="helper-text">
                                        <?php if ($has_autopassword): ?>
                                            <span><?= __('To be able to log in later, you have to set a password. Enter your desired password twice (to prevent you from typing mistakes). Press the "%set_password" button to set your password.', array('%set_password' => __('Set password'))); ?></span>
                                        <?php else: ?>
                                            <span><?= __('Enter your new password twice (to prevent you from typing mistakes), then press the "%change_password" button to change your password.', array('%change_password' => __('Change password'))); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($has_autopassword): ?>
                                        <div class="message-box type-warning">
                                            <?= fa_image_tag('grin-beam-sweat', ['class' => 'icon large'], 'far'); ?>
                                            <span class="message"><?= __('Remember to set a password before you continue, or you may be locked out of your account'); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-row" data-field="new_password_1">
                                    <label for="new_password_1"><?= ($has_autopassword) ? __('Password') : __('New password'); ?></label>
                                    <input type="password" name="new_password_1" id="new_password_1" value="">
                                    <div class="error"></div>
                                </div>
                                <div class="form-row" data-field="new_password_2">
                                    <label for="new_password_2"><?= ($has_autopassword) ? __('Password (repeated, to confirm)') : __('New password (repeated, to confirm)'); ?></label>
                                    <input type="password" name="new_password_2" id="new_password_2" value="">
                                    <div class="error"></div>
                                </div>
                            <?php endif; ?>
                        <?php if (!Settings::isUsingExternalAuthenticationBackend()): ?>
                            <div class="form-row submit-container">
                                <button type="submit" class="button primary">
                                    <span><?= ($has_autopassword) ? __('Set password') : __('Change password'); ?></span>
                                    <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                                </button>
                            </div>
                        <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php if ($pachno_user->isOpenIdLocked()): ?>
    <div class="fullpage_backdrop" id="pick_username_div" style="display: none;">
        <div class="fullpage_backdrop_content">
            <div class="backdrop_box medium">
                <div class="backdrop_detail_header">
                    <span><?= __('Picking a username'); ?></span>
                    <a href="javascript:void(0);" class="closer" onclick="$('#pick_username_div').toggle();"><?= fa_image_tag('times'); ?></a>
                </div>
                <div class="backdrop_detail_content">
                    <div class="form-container">
                        <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('account_check_username'); ?>" method="post" id="check_username_form" data-simple-submit data-update-container="#add_application_password_container">
                            <div class="form-row">
                                <div class="helper-text">
                                    <span><?= __('Since this account was created via an OpenID login, you will have to pick a username to be able to log in with a username or password. You can continue to use your account with your OpenID login, so this is only if you want to pick a username for your account.'); ?><p>
                                </div>
                            </div>
                            <div class="form-row" data-field="desired_username">
                                <label for="username_pick"><?= __('Type desired username'); ?></label>
                                <input type="text" name="desired_username" id="username_pick">
                                <div class="error"></div>
                            </div>
                        </form>
                        <div class="form-row">
                            <div class="helper-text"><?= __('Click "%check_availability" to see if your desired username is available.', array('%check_availability' => __('Check availability'))); ?></div>
                        </div>
                        <div class="form-row submit-container">
                            <button type="submit" class="button primary">
                                <span><?= ('Check availability'); ?></span>
                                <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="fullpage_backdrop" id="add_application_password_div" style="display: none;">
    <div class="fullpage_backdrop_content">
        <div class="backdrop_box medium">
            <div class="form-container">
                <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('account_add_application_password'); ?>" onsubmit="Pachno.Main.Profile.addApplicationPassword('<?= make_url('account_add_application_password'); ?>'); return false;" method="post" id="add_application_password_form">
                    <div id="add_application_password_container">
                        <div class="backdrop_detail_header">
                            <span><?= __('Add application-specific password'); ?></span>
                            <a href="javascript:void(0);" class="closer" onclick="$('#add_application_password_div').toggle();"><?= fa_image_tag('times'); ?></a>
                        </div>
                        <div class="backdrop_detail_content login_content">
                            <div class="logindiv regular active">
                                <div class="article"><?= __('Please enter the name of the application or computer which will be using this password. Examples include "Toms computer", "Work laptop", "My iPhone" and similar.'); ?></div>
                                <ul class="account_popupform">
                                    <li>
                                        <label for="add_application_password_name"><?= __('Application name'); ?></label>
                                        <input type="text" name="name" id="add_application_password_name" value="">
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="backdrop_details_submit">
                            <span class="explanation"></span>
                            <div class="submit_container">
                                <button type="submit" class="button"><?= image_tag('spinning_20.gif', array('id' => 'add_application_password_indicator', 'style' => 'display: none;')) . __('Add application password'); ?></button>
                            </div>
                        </div>
                    </div>
                    <div id="add_application_password_response" style="display: none;">
                        <div class="backdrop_detail_header">
                            <span><?= __('Application password generated'); ?></span>
                        </div>
                        <div class="backdrop_detail_content login_content">
                            <div class="article"><?= __("Use this one-time password when authenticating with the application. Spaces don't matter, and you don't have to write it down."); ?></div>
                            <div class="application_password_preview" id="application_password_preview"></div>
                        </div>
                        <div class="backdrop_details_submit">
                            <span class="explanation"></span>
                            <div class="submit_container">
                                <a href="<?= make_url('profile_account'); ?>" class="button"><?= __('Done'); ?></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="account_info_container">
    <?php if ($error): ?>
        <div class="message-box type-error">
            <?= fa_image_tag('exclamation-circle'); ?>
            <span class="message">
                <span class="title"><?= __('An error occurred'); ?></span>
                <span class="description"><?= $error; ?></span>
            </span>
        </div>
    <?php endif; ?>
    <?php if ($rsskey_generated): ?>
        <div class="message-box type-success">
            <?= fa_image_tag('exclamation-circle'); ?>
            <span class="message">
                <span class="title"><?= __('Your RSS key has been regenerated'); ?></span>
                <span class="description"><?= __('All previous RSS links have been invalidated.'); ?></span>
            </span>
        </div>
    <?php endif; ?>
    <?php if ($username_chosen): ?>
        <div class="message-box type-success">
            <?= fa_image_tag('exclamation-circle'); ?>
            <span class="message">
                <span class="title"><?= __("You\'ve chosen the username \'%username\'", array('%username' => $pachno_user->getUsername())); ?></span>
                <span class="description"><?= __('Before you can use the new username to log in, you must pick a password via the "%change_password" button.', array('%change_password' => __('Change password'))); ?></span>
            </span>
        </div>
    <?php endif; ?>
    <?php if ($openid_used): ?>
        <div class="message-box type-error">
            <?= fa_image_tag('exclamation-circle'); ?>
            <span class="message">
                <span class="title"><?= __('This OpenID identity is already in use'); ?></span>
                <span class="description"><?= __('Someone is already using this identity. Check to see if you have already added this account.'); ?></span>
            </span>
        </div>
    <?php endif; ?>
    <div id="account_user_info">
        <?= image_tag($pachno_user->getAvatarURL(false), array('style' => 'float: left; margin-right: 5px;', 'alt' => '[avatar]'), true); ?>
        <span id="user_name_span">
            <?= $pachno_user->getRealname(); ?><br>
            <?php if (!$pachno_user->isOpenIdLocked()): ?>
                @<?= $pachno_user->getUsername(); ?>
            <?php endif; ?>
        </span>
    </div>
    <div id="account_details_container">
        <div id="account_tabs" class="fancy-tabs tab-switcher">
            <a class="tab tab-switcher-trigger <?php if ($selected_tab == 'profile'): ?> selected<?php endif; ?>" id="tab_profile" href="javascript:void(0);" data-tab-target="profile">
                <?= fa_image_tag('edit', ['class' => 'icon']); ?>
                <span class="name"><?= __('Profile'); ?></span>
            </a>
            <a class="tab tab-switcher-trigger <?php if ($selected_tab == 'security'): ?> selected<?php endif; ?>" id="tab_security" href="javascript:void(0);" data-tab-target="security">
                <?= fa_image_tag('lock', ['class' => 'icon']); ?>
                <span class="name"><?= __('Security'); ?></span>
            </a>
            <a class="tab tab-switcher-trigger" id="tab_notificationsettings" href="javascript:void(0);" data-tab-target="notificationsettings">
                <?= fa_image_tag('bell', ['class' => 'icon']); ?>
                <span class="name"><?= __('Notification settings'); ?></span>
            </a>
            <?php \pachno\core\framework\Event::createNew('core', 'account_tabs')->trigger(); ?>
            <?php foreach (\pachno\core\framework\Context::getAllModules() as $modules): ?>
                <?php foreach ($modules as $module_name => $module): ?>
                    <?php if ($module->hasAccountSettings()): ?>
                        <a class="tab tab-switcher-trigger" id="tab_settings_<?= $module_name; ?>" href="javascript:void(0);" data-tab-target="<?= $module_name; ?>">
                            <?= fa_image_tag($module->getAccountSettingsLogo(), ['class' => 'icon']); ?>
                            <span class="name"><?= __($module->getAccountSettingsName()); ?></span>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?php if (count($pachno_user->getScopes()) > 1): ?>
                <a class="tab tab-switcher-trigger" id="tab_scopes" href="javascript:void(0);" data-tab-target="scopes">
                    <?= fa_image_tag('clone', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Scope memberships'); ?></span>
                </a>
            <?php endif; ?>
        </div>
        <div id="account_tabs_panes">
            <div id="tab_profile_pane" style="<?php if ($selected_tab != 'profile'): ?> display: none;<?php endif; ?>" data-tab-id="profile" class="form-container">
                <?php if (Settings::isUsingExternalAuthenticationBackend()): ?>
                    <?= \pachno\core\helpers\TextParser::parseText(Settings::get('changedetails_message'), false, null, array('embedded' => true)); ?>
                <?php else: ?>
                    <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('profile_account', ['mode' => 'information']); ?>" data-simple-submit method="post" id="profile_information_form">
                        <div class="row">
                            <div class="column large">
                                <div class="form-row header"><h3><?= __('About yourself'); ?></h3></div>
                                <div class="form-row">
                                    <label for="profile_buddyname">* <?= __('Display name'); ?></label>
                                    <input type="text" class="name-input-enhance" name="buddyname" id="profile_buddyname" value="<?= $pachno_user->getBuddyname(); ?>">
                                    <div class="helper-text">
                                        <?= __('This name is what other people will see you as.'); ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label for="profile_realname"><span><?= __('Full name'); ?></span><span class="status-badge"><?= __('Optional'); ?></span></label>
                                    <input type="text" name="realname" id="profile_realname" value="<?= $pachno_user->getRealname(); ?>" style="width: 300px;">
                                </div>
                                <div class="form-row">
                                    <label for="profile_homepage"><span><?= __('Homepage'); ?></span><span class="status-badge"><?= __('Optional'); ?></span></label>
                                    <input type="url" name="homepage" id="profile_homepage" value="<?= $pachno_user->getHomepage(); ?>" style="width: 300px;">
                                </div>
                                <div class="form-row">
                                    <label for="profile_email">* <?= __('Email address'); ?></label>
                                    <input type="email" name="email" id="profile_email" value="<?= $pachno_user->getEmail(); ?>">
                                    <input type="checkbox" class="fancy-checkbox" name="email_private" value="1" id="profile_email_private_yes"<?php if (!$pachno_user->isEmailPublic()): ?> checked<?php endif; ?>>
                                    <label for="profile_email_private_yes"><?php echo fa_image_tag('check-square', ['class' => 'checked']) . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?><span><?= __('Hide my email address from other users'); ?></span></label>
                                </div>
                                <div class="form-row">
                                    <input type="checkbox" name="use_gravatar" value="1" class="fancy-checkbox" id="profile_use_gravatar_yes"<?php if ($pachno_user->usesGravatar()): ?> checked<?php endif; ?>>
                                    <label for="profile_use_gravatar_yes"><?= fa_image_tag('check-square', ['class' => 'checked']) . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?><span><?= __('Use my email avatar from %link_to_gravatar', ['%link_to_gravatar' => link_tag('https://gravatar.com', 'gravatar.com', ['target' => '_blank'])]); ?></span></label>&nbsp;&nbsp;
                                    <div class="helper-text">
                                        <span><?= __("Pachno can use your %link_to_gravatar profile picture, if you have one.", ['%link_to_gravatar' => link_tag('http://www.gravatar.com', 'Gravatar', ['target' => '_blank'])]); ?></span>
                                        <a id="gravatar_change" href="http://en.gravatar.com/emails/" class="button secondary" target="_blank" style="margin-left: auto">
                                            <?= image_tag('gravatar.png', ['class' => 'icon']); ?>
                                            <span><?= __('Change my profile picture / avatar'); ?></span>
                                            <?= fa_image_tag('external-link-alt', ['class' => 'icon external'], 'fas'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="column">
                                <div class="form-row header">
                                    <h3><?= __('Language and location'); ?></h3>
                                    <div class="helper-text"><?= __('This information is used to provide a more localized experience based on your location and language preferences. Items such as timestamps will be displayed in your local timezone, and you can choose to use Pachno in your own language.'); ?></div>
                                </div>
                                <div class="form-row">
                                    <label><?= __('Preferred timezone'); ?></label>
                                    <div class="fancy-dropdown-container">
                                        <div class="fancy-dropdown" data-default-label="<?= __('Use server timezone'); ?>">
                                            <span class="value"><?= __('No client assigned'); ?></span>
                                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                            <div class="dropdown-container list-mode">
                                                <div class="list-item filter-container">
                                                    <input type="search" placeholder="<?= __('Filter available timezones'); ?>">
                                                </div>
                                                <div class="filter-values-container">
                                                    <input type="radio" name="timezone" class="fancy-checkbox" id="user-timezone-sys" value="sys"<?php if (in_array($pachno_user->getTimezoneIdentifier(), ['sys', null])): ?> checked<?php endif; ?>>
                                                    <label class="list-item filtervalue" for="user-timezone-sys">
                                                        <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                                        <span class="name value"><?= __('Use server timezone'); ?></span>
                                                    </label>
                                                    <?php foreach ($timezones as $timezone => $description): ?>
                                                        <input type="radio" name="timezone" class="fancy-checkbox" id="user-timezone-<?= $timezone; ?>" value="<?= $timezone; ?>"<?php if ($pachno_user->getTimezoneIdentifier() == $timezone): ?> checked<?php endif; ?>>
                                                        <label class="list-item filtervalue" for="user-timezone-<?= $timezone; ?>">
                                                            <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                                            <span class="name value"><?= $description; ?></span>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="helper-text">
                                        <?= __('Based on this information, the time at your location should be: %time', array('%time' => \pachno\core\framework\Context::getI18n()->formatTime(time(), 1))); ?>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label><?= __('Language'); ?></label>
                                    <div class="fancy-dropdown-container">
                                        <div class="fancy-dropdown" data-default-label="<?= __('Default'); ?>">
                                            <span class="value"><?= __('Default'); ?></span>
                                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                            <div class="dropdown-container list-mode">
                                                <div class="filter-values-container">
                                                    <input type="radio" name="profile_language" class="fancy-checkbox" id="profile_language_sys" value="sys"<?php if ($pachno_user->getLanguage() == 'sys'): ?> checked<?php endif; ?>>
                                                    <label class="list-item filtervalue" for="profile_language_sys">
                                                        <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                                        <span class="name value"><?= __('Use global setting - %lang', array('%lang' => Settings::getLanguage())); ?></span>
                                                    </label>
                                                    <?php foreach ($languages as $lang_code => $lang_desc): ?>
                                                        <input type="radio" name="profile_language" class="fancy-checkbox" id="profile_language_<?= $lang_code; ?>" value="<?= $lang_code; ?>"<?php if ($pachno_user->getLanguage() == $lang_code): ?> checked<?php endif; ?>>
                                                        <label class="list-item" for="profile_language_<?= $lang_code; ?>">
                                                            <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                                            <span class="name value"><?= $lang_desc; ?> <?php if (Settings::getLanguage() == $lang_code): ?> <?= __('(site default)'); endif;?></span>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row submit-container">
                            <div class="message"><?= __('Click "%save" to save your account information', array('%save' => __('Save'))); ?></div>
                            <button type="submit" id="submit_information_button">
                                <span class="name-add
                                åpenbar anledning utlyses internt
"><?= __('Save'); ?></span>
                                <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            <div id="tab_security_pane" data-tab-id="security" class="form-container" style="<?php if ($selected_tab != 'security'): ?> display: none;<?php endif; ?>">
                <div class="form">
                    <div class="form-row header" id="account_2fa_disabled" style="<?php if ($pachno_user->is2FaEnabled()) echo 'display: none;'; ?>">
                        <h5>
                            <?= fa_image_tag('exclamation-triangle', ['class' => 'icon']); ?>
                            <span><?= __('Two-factor authentication is not enabled'); ?></span>
                            <button class="button primary" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'enable_2fa']); ?>');"><?= __('Enable'); ?></button>
                        </h5>
                        <div class="helper-text"><?= __("Enabling two-factor authentication increases account security by requiring that you provide a one-time code every time you log in on a new device."); ?></div>
                    </div>
                    <div class="form-row" id="account_2fa_enabled" style="<?php if (!$pachno_user->is2FaEnabled()) echo 'display: none;'; ?>">
                        <span class="message">
                            <h3><?= fa_image_tag('check', ['class' => 'icon']); ?><span><?= __('Two-factor authentication is enabled'); ?></span></h3>
                            <span><?= __('A one-time code is required to log in on a new device'); ?></span>
                        </span>
                        <button class="button secondary" onclick="Pachno.UI.Dialog.show('<?= __('Really disable two-factor authentication?'); ?>', '<?= __('Do you really want to two-factor authentication? By doing this, only your username and password is required when logging in.'); ?>', {yes: {click: function () { Pachno.trigger('template_trigger_disable_2fa', { url: '<?= make_url('account_disable_2fa', array('csrf_token' => \pachno\core\framework\Context::getCsrfToken())); ?>'}) }}, no: {click: Pachno.UI.Dialog.dismiss}});"><?= __('Disable 2FA'); ?></button>
                    </div>
                    <div class="form-row separator"></div>
                    <div class="form-row header">
                        <h5>
                            <?= fa_image_tag('key', ['class' => 'icon']); ?>
                            <span><?= __('Passwords and keys'); ?></span>
                            <span class="actions">
                                <?php if (!$pachno_user->isGuest() && !$pachno_user->isOpenIdLocked()): ?>
                                    <button class="button secondary" onclick="$('#change_password_div').toggle();"><?= fa_image_tag('key', ['class' => 'icon']); ?><span><?= __('Change my password'); ?></span></button>
                                <?php elseif ($pachno_user->isOpenIdLocked()): ?>
                                    <button class="button secondary" onclick="$('#pick_username_div').toggle();" id="pick_username_button"><?= fa_image_tag('user-tag', ['class' => 'icon']); ?><span><?= __('Pick a username'); ?></span></button>
                                <?php else: ?>
                                    <button class="button secondary" onclick="Pachno.UI.Message.error('<?= __('Changing password disabled'); ?>', '<?= __('Changing your password can not be done via this interface. Please contact your administrator to change your password.'); ?>');" class="disabled"><?= fa_image_tag('key', ['class' => 'icon']); ?><span><?= __('Change my password'); ?></span></button>
                                <?php endif; ?>
                                <button class="button secondary" onclick="$('#add_application_password_div').toggle();"><?= fa_image_tag('handshake', ['class' => 'icon'], 'far'); ?><span><?= __('Add application-specific password'); ?></span></button>
                            </span>
                        </h5>
                        <div class="helper-text"><?= __("When authenticating with Pachno you only use your main password on the website - other applications and RSS feeds needs specific access tokens that you can enable / disable on an individual basis. You can control all your passwords and keys from here."); ?></div>
                    </div>
                    <div class="form-row">
                        <h4>
                            <?= fa_image_tag('rss', ['class' => 'icon']); ?>
                            <span><?= __('RSS feeds access key'); ?></span>
                            <button class="button secondary" onclick="Pachno.UI.Dialog.show('<?= __('Regenerate your RSS key?'); ?>', '<?= __('Do you really want to regenerate your RSS access key? By doing this all your previously bookmarked or linked RSS feeds will stop working and you will have to get the link from inside Pachno again.'); ?>', {yes: {href: '<?= make_url('account_regenerate_rss_key', array('csrf_token' => \pachno\core\framework\Context::getCsrfToken())); ?>'}, no: {click: Pachno.UI.Dialog.dismiss}});"><?= __('Reset'); ?></button>
                        </h4>
                        <div class="helper-text"><?= __('Automatically used as part of RSS feed URLs. Regenerating this key prevents your previous RSS feed links from working.'); ?></div>
                    </div>
                    <?php foreach ($pachno_user->getApplicationPasswords() as $password): ?>
                        <div class="form-row" id="application_password_<?= $password->getID(); ?>">
                            <h4>
                                <span><?= __('Application password: %password_name', array('%password_name' => $password->getName())); ?></span>
                                <button class="button" onclick="Pachno.UI.Dialog.show('<?= __('Remove this application-specific password?'); ?>', '<?= __('Do you really want to remove this application-specific password? By doing this, that application will no longer have access, and you will have to generate a new application password for the application to regain access.'); ?>', {yes: {click: function() {Pachno.Main.Profile.removeApplicationPassword('<?= make_url('account_remove_application_password', array('id' => $password->getID(), 'csrf_token' => \pachno\core\framework\Context::getCsrfToken())); ?>', <?= $password->getID(); ?>);}}, no: {click: Pachno.UI.Dialog.dismiss}});"><?= __('Delete'); ?></button>
                            </h4>
                            <div class="helper-text"><?= __('Last used: %last_used_time, created at: %created_at_time', array('%last_used_time' => ($password->getLastUsedAt()) ? \pachno\core\framework\Context::getI18n()->formatTime($password->getLastUsedAt(), 20) : __('never used'), '%created_at_time' => \pachno\core\framework\Context::getI18n()->formatTime($password->getCreatedAt(), 20))); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                </div>
            <div id="tab_notificationsettings_pane" data-tab-id="notificationsettings" style="display: none;" class="form-container">
                <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= make_url('profile_account', ['mode' => 'settings']); ?>" method="post" id="profile_notificationsettings_form" data-simple-submit>
                    <div class="form-row">
                        <h3><?= __('Subscriptions'); ?></h3>
                        <div class="helper-text"><?= __('Pachno can subscribe you to issues, articles and other items in the system, so you can receive notifications when they are updated. Please select when you would like Pachno to subscribe you.'); ?></div>
                    </div>
                    <div class="row">
                        <div class="column">
                        <?php foreach ($subscriptionssettings as $key => $description): ?>
                            <?php if (in_array($key, [Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS_CATEGORY, Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS_CATEGORY, Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS])) continue; ?>
                            <div class="form-row">
                                <input type="checkbox" class="fancy-checkbox" name="core_<?= $key; ?>" value="1" id="<?= $key; ?>_yes"<?php if (!$pachno_user->getNotificationSetting($key, true)->isOff()): ?> checked<?php endif; ?>><label for="<?= $key; ?>_yes"><?= fa_image_tag('check-square', ['class' => 'icon checked']) . fa_image_tag('square', ['class' => 'icon unchecked'], 'far'); ?><span><?= $description ?></span></label>
                            </div>
                        <?php endforeach; ?>
                        </div>
                        <div class="column">
                            <?php $category_key = Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS_CATEGORY; ?>
                            <?php foreach ($subscriptionssettings as $key => $description): ?>
                                <?php if (!in_array($key, [Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS_CATEGORY])) continue; ?>
                                <div class="form-row">
                                    <input type="checkbox" class="fancy-checkbox" name="core_<?= $key; ?>" value="1" id="<?= $key; ?>_yes"<?php if (!$pachno_user->getNotificationSetting($key, true)->isOff()): ?> checked<?php endif; ?>><label for="<?= $key; ?>_yes"><?= fa_image_tag('check-square', ['class' => 'icon checked']) . fa_image_tag('square', ['class' => 'icon unchecked'], 'far'); ?><span><?= $description ?></span></label>
                                    <div class="fancy-dropdown-container">
                                        <div class="fancy-dropdown" data-default-label="<?= __('All categories'); ?>">
                                            <span class="value"><?= __('All categories'); ?></span>
                                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                            <div class="dropdown-container list-mode">
                                                <div class="filter-values-container">
                                                    <?php foreach ($categories as $category_id => $category): ?>
                                                        <input type="checkbox" class="fancy-checkbox" value="<?= $category_id; ?>" name="core_<?= $category_key; ?>_<?= $category_id; ?>" data-text="<?= __($category->getName()); ?>" id="core_<?= $key; ?>_value_<?= $category_id; ?>" <?php if (in_array($category_id, $selected_category_subscriptions)) echo 'checked'; ?>>
                                                        <label class="list-item" for="core_<?= $key; ?>_value_<?= $category_id; ?>">
                                                            <span class="icon"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?></span>
                                                            <span class="name value"><?= __($category->getName()); ?></span>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php $project_issues_key = Settings::SETTINGS_USER_SUBSCRIBE_NEW_ISSUES_MY_PROJECTS; ?>
                            <div class="form-row">
                                <input type="checkbox" class="fancy-checkbox" name="core_<?= $project_issues_key; ?>" value="1" id="<?= $project_issues_key; ?>_yes"<?php if (!$pachno_user->getNotificationSetting($project_issues_key, true)->isOff()): ?> checked<?php endif; ?>><label for="<?= $project_issues_key; ?>_yes"><?= fa_image_tag('check-square', ['class' => 'icon checked']) . fa_image_tag('square', ['class' => 'icon unchecked'], 'far'); ?><span><?= __('New issues in my project(s)') ?></span></label>
                                <div class="fancy-dropdown-container">
                                    <div class="fancy-dropdown" data-default-label="<?= __('All my projects'); ?>">
                                        <span class="value"><?= __('All my projects'); ?></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <div class="filter-values-container">
                                                <input type="checkbox" class="fancy-checkbox" value="all" name="core_<?= $project_issues_key; ?>_all" id="core_<?= $project_issues_key; ?>_value_all" <?php if ($all_projects_subscription) echo 'checked'; ?> class="fancy-checkbox" data-exclusive data-selection-group="1" data-exclude-group="2">
                                                <label class="list-item" for="core_<?= $project_issues_key; ?>_value_all">
                                                    <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                                    <span class="name value"><?= __('All my projects'); ?></span>
                                                </label>
                                                <div class="list-item separator"></div>
                                                <?php foreach ($projects as $project_id => $project): ?>
                                                    <input type="checkbox" class="fancy-checkbox" value="<?= $category_id; ?>" name="core_<?= $category_key; ?>_<?= $category_id; ?>" id="core_<?= $key; ?>_value_<?= $category_id; ?>" <?php if (in_array($category_id, $selected_category_subscriptions)) echo 'checked'; ?> data-selection-group="2" data-exclude-group="1">
                                                    <label class="list-item" for="core_<?= $project_issues_key; ?>_value_<?= $project_id; ?>">
                                                        <span class="icon"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?></span>
                                                        <span class="icon"><?= image_tag($project->getIconName(), ['class' => 'icon', 'alt' => '[i]'], true); ?></span>
                                                        <span class="name value"><?= $project->getName(); ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <h3><?= __('Notifications'); ?></h3>
                        <div class="helper-text"><?= __('Pachno will send you notifications based on system actions and/or your subscriptions. Notifications can be received in the notifications box so you only have to deal with them after logging in, and/or via email for those important notifications you want to be alerted of right away.'); ?></div>
                    </div>
                    <div class="flexible-table" cellpadding=0 cellspacing=0>
                        <div class="row header">
                            <div class="column header name-container"></div>
                            <div class="column header info-icons large centered"><?= fa_image_tag('bell', ['class' => 'icon'], 'far'); ?></div>
                            <?php \pachno\core\framework\Event::createNew('core', 'account_pane_notificationsettings_table_header')->trigger(); ?>
                        </div>
                        <?php foreach ($notificationsettings as $key => $description): ?>
                            <?php if (in_array($key, [Settings::SETTINGS_USER_NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY, Settings::SETTINGS_USER_NOTIFY_GROUPED_NOTIFICATIONS])) continue; ?>
                            <div class="row">
                                <div class="column name-container"><label for="<?= $key; ?>_yes"><?= $description ?></label></div>
                                <div class="column info-icons centered">
                                    <input type="checkbox" class="fancy-checkbox" name="core_<?= $key; ?>" value="1" id="<?= $key; ?>_yes"<?php if ($pachno_user->getNotificationSetting($key, $key == Settings::SETTINGS_USER_NOTIFY_MENTIONED, 'core')->isOn()) echo ' checked'; ?>><label for="<?= $key; ?>_yes"><?= fa_image_tag('check-square', ['class' => 'icon checked']) . fa_image_tag('square', ['class' => 'icon unchecked'], 'far'); ?></label>
                                </div>
                                <?php \pachno\core\framework\Event::createNew('core', 'account_pane_notificationsettings_cell')->trigger(compact('key')); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php /* $category_key = Settings::SETTINGS_USER_NOTIFY_NEW_ISSUES_MY_PROJECTS_CATEGORY; ?>
                    <table class="padded_table" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: auto; border-bottom: 1px solid #DDD; vertical-align: middle;">
                                <label for="<?= $category_key; ?>_yes"><?= __('Notify to notifications box when issues are created in selected categories') ?></label><br>
                                <?= __('If you want to be notified when an issue is created in a specific category, but do not want to automatically subscribe for updates to these issues, make sure auto-subscriptions are turned off in the "%subscriptions"-section, then use this dropdown to configure notifications.', ['%subscriptions' => __('Subscriptions')]); ?>
                            </td>
                            <td style="width: 350px; text-align: right; border-bottom: 1px solid #DDD; vertical-align: middle;">
                                <label><?= __('Notifications box'); ?></label><br>
                                <div class="filter interactive_dropdown rightie" data-filterkey="<?= $category_key; ?>" data-value="" data-all-value="<?= __('None selected'); ?>">
                                    <input type="hidden" name="core_<?= $category_key; ?>" value="<?= join(',', $selected_category_notifications); ?>" id="filter_<?= $category_key; ?>_value_input">
                                    <label><?= __('Categories'); ?></label>
                                    <span class="value"><?php if (empty($selected_category_notifications)) echo __('None selected'); ?></span>
                                    <div class="interactive_menu">
                                        <h1><?= __('Select which categories to subscribe to'); ?></h1>
                                        <input type="search" placeholder="<?= __('Filter categories'); ?>">
                                        <div class="interactive_values_container">
                                            <ul class="interactive_menu_values">
                                                <?php foreach ($categories as $category_id => $category): ?>
                                                    <li data-value="<?= $category_id; ?>" class="filtervalue<?php if (in_array($category_id, $selected_category_notifications)) echo ' selected'; ?>">
                                                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                        <input type="checkbox" value="<?= $category_id; ?>" name="core_<?= $category_key; ?>_<?= $category_id; ?>" data-text="<?= __($category->getName()); ?>" id="core_<?= $category_key; ?>_value_<?= $category_id; ?>" <?php if (in_array($category_id, $selected_category_notifications)) echo 'checked'; ?>>
                                                        <label for="core_<?= $category_key; ?>_value_<?= $category_id; ?>"><?= __($category->getName()); ?></label>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <?php \pachno\core\framework\Event::createNew('core', 'account_pane_notificationsettings_notification_categories')->trigger(compact('categories')); ?>
                            </td>
                        </tr>
                    </table>
                    <?php \pachno\core\framework\Event::createNew('core', 'account_pane_notificationsettings_subscriptions')->trigger(compact('categories')); */ ?>
                    <div class="form-row header">
                        <h3>
                            <span class="name"><?= __('Desktop notifications'); ?></span>
                            <span class="actions">
                                <input type="checkbox" class="fancy-checkbox" data-interactive-toggle value="1" id="toggle_enable_desktop_notifications" data-event-key="profile-toggle-desktop-notifications"><label class="button secondary" for="toggle_enable_desktop_notifications"><?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']) . fa_image_tag('toggle-on', ['class' => 'icon checked']) . fa_image_tag('toggle-off', ['class' => 'icon unchecked']); ?><span><?= __('Desktop notifications enabled'); ?></span></label>
                            </span>
                        </h3>
                        <div class="helper-text"><?= __('You can receive desktop notifications based on system actions or your subscriptions. Choose your desktop notification preferences from this section.'); ?></div>
                    </div>
                    <div class="form-row submit-container">
                        <div class="message"><?= __('Click "%save" to save your notification settings', array('%save' => __('Save'))); ?></div>
                        <button type="submit" class="primary">
                            <span class="name"><?= __('Save'); ?></span>
                            <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                        </button>
                    </div>
                </form>
            </div>
            <?php \pachno\core\framework\Event::createNew('core', 'account_tab_panes')->trigger(); ?>
            <?php foreach (\pachno\core\framework\Context::getAllModules() as $modules): ?>
                <?php foreach ($modules as $module_name => $module): ?>
                    <?php if ($module->hasAccountSettings()): ?>
                        <div id="tab_settings_<?= $module_name; ?>_pane" data-tab-id="<?= $module_name; ?>" style="display: none;">
                            <?php include_component("{$module_name}/accountsettings", array('module' => $module)); ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?php if (count($pachno_user->getScopes()) > 1): ?>
                <div id="tab_scopes_pane" data-tab-id="scopes" style="display: none;">
                    <h3><?= __('Pending memberships'); ?></h3>
                    <ul class="simple-list" id="pending_scope_memberships">
                        <?php foreach ($pachno_user->getUnconfirmedScopes() as $scope): ?>
                            <?php include_component('main/userscope', array('scope' => $scope)); ?>
                        <?php endforeach; ?>
                    </ul>
                    <span id="no_pending_scope_memberships" class="faded_out" style="<?php if (count($pachno_user->getUnconfirmedScopes())): ?>display: none;<?php endif; ?>"><?= __('You have no pending scope memberships'); ?></span>
                    <h3 style="margin-top: 20px;"><?= __('Confirmed memberships'); ?></h3>
                    <ul class="simple-list" id="confirmed_scope_memberships">
                        <?php foreach ($pachno_user->getConfirmedScopes() as $scope_id => $scope): ?>
                            <?php include_component('main/userscope', array('scope' => $scope)); ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    Pachno.on('template_trigger_disable_2fa', function (PachnoApplication, data) {
        const url = data.url;
        Pachno.fetch(url, {method: 'POST'})
            .then((json) => {
                if (json.disabled === 'ok') {
                    $('#account_2fa_enabled').hide();
                    $('#account_2fa_disabled').show();
                }
                Pachno.UI.Dialog.dismiss();
            })
            .catch((error) => {
                console.error(error);
            });
    });
    Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
        switch (data.form) {
            case 'check_username_form':
                const json = data.json;
                if (json.available) {
                    Pachno.UI.Backdrop.show(json.url);
                }
                break;
        }
    });

</script>