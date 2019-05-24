<?php

    use pachno\core\framework\Settings;
    $themes = \pachno\core\framework\Context::getThemes();
    $languages = \pachno\core\framework\I18n::getLanguages();
    
?>
<div class="form-row">
    <div class="fancydropdown-container">
        <div class="fancydropdown">
            <label for="<?= Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>_yes"><?= __('Enable elevated login'); ?></label>
            <span class="value"></span>
            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
            <div class="dropdown-container list-mode">
                <input type="radio" name="<?= Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>" class="fancycheckbox" <?php if ($access_level != Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>_yes" value=0<?php if (Settings::isElevatedLoginRequired()): ?> checked<?php endif; ?>>
                <label class="list-item multiline" for="<?= Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>_yes">
                    <?= fa_image_tag('check-square', ['class' => 'checked icon'], 'far') . fa_image_tag('square', ['class' => 'unchecked icon'], 'far'); ?>
                    <span class="name">
                        <span class="title value"><?= __('Yes'); ?></span>
                        <span class="additional_information"><?= __('Require users to re-enter their password to access the configuration section'); ?></span>
                    </span>
                </label>
                <input type="radio" name="<?= Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>" class="fancycheckbox" <?php if ($access_level != Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>_no" value=1<?php if (!Settings::isElevatedLoginRequired()): ?> checked<?php endif; ?>>
                <label class="list-item multiline" for="<?= Settings::SETTING_ELEVATED_LOGIN_DISABLED; ?>_no">
                    <?= fa_image_tag('check-square', ['class' => 'checked icon'], 'far') . fa_image_tag('square', ['class' => 'unchecked icon'], 'far'); ?>
                    <span class="name">
                        <span class="title value"><?= __('No'); ?></span>
                        <span class="additional_information"><?= __('Do not require users to re-enter their password to access the configuration section'); ?></span>
                    </span>
                </label>
            </div>
        </div>
    </div>
    <div class="helper-text"><?= __('If this is turned on, users will have to re-enter their password to go to the configuration section'); ?></div>
</div>
<div class="form-row">
    <div class="fancydropdown-container">
        <div class="fancydropdown">
            <label for="requirelogin"><?= __('Allow anonymous access'); ?></label>
            <span class="value"></span>
            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
            <div class="dropdown-container list-mode">
                <input type="radio" name="<?= Settings::SETTING_REQUIRE_LOGIN; ?>" class="fancycheckbox" <?php if ($access_level != Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= Settings::SETTING_REQUIRE_LOGIN; ?>_no" value=0<?php if (!Settings::isLoginRequired()): ?> checked<?php endif; ?>>
                <label class="list-item multiline" for="<?= Settings::SETTING_REQUIRE_LOGIN; ?>_no">
                    <?= fa_image_tag('check-square', ['class' => 'checked icon'], 'far') . fa_image_tag('square', ['class' => 'unchecked icon'], 'far'); ?>
                    <span class="name">
                        <span class="title value"><?= __('Yes'); ?></span>
                        <span class="additional_information"><?= __('Allow anonymous users to access content based on project visibility settings'); ?></span>
                    </span>
                </label>
                <input type="radio" name="<?= Settings::SETTING_REQUIRE_LOGIN; ?>" class="fancycheckbox" <?php if ($access_level != Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= Settings::SETTING_REQUIRE_LOGIN; ?>_yes" value=1<?php if (Settings::isLoginRequired()): ?> checked<?php endif; ?>>
                <label class="list-item multiline" for="<?= Settings::SETTING_REQUIRE_LOGIN; ?>_yes">
                    <?= fa_image_tag('check-square', ['class' => 'checked icon'], 'far') . fa_image_tag('square', ['class' => 'unchecked icon'], 'far'); ?>
                    <span class="name">
                        <span class="title value"><?= __('No'); ?></span>
                        <span class="additional_information"><?= __('A valid user account is required to access any content'); ?></span>
                    </span>
                </label>
            </div>
        </div>
    </div>
    <div class="helper-text"><?= __('If anonymous access is turned off, a valid user account is required to access any content in this installation'); ?></div>
</div>
<div class="form-row">
    <label for="<?= Settings::SETTING_ENABLE_GRAVATARS; ?>_yes"><?= __('Enable %gravatar user icons', ['%gravatar' => link_tag('https://gravatar.com', 'gravatar.com')]); ?></label>
    <input type="radio" name="<?= Settings::SETTING_ENABLE_GRAVATARS; ?>" class="fancycheckbox" <?php if ($access_level != Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= Settings::SETTING_ENABLE_GRAVATARS; ?>_yes" value=1<?php if (Settings::isGravatarsEnabled()): ?> checked<?php endif; ?>><label for="<?= Settings::SETTING_ENABLE_GRAVATARS; ?>_yes"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
    <input type="radio" name="<?= Settings::SETTING_ENABLE_GRAVATARS; ?>" class="fancycheckbox" <?php if ($access_level != Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= Settings::SETTING_ENABLE_GRAVATARS; ?>_no" value=0<?php if (!Settings::isGravatarsEnabled()): ?> checked<?php endif; ?>><label for="<?= Settings::SETTING_ENABLE_GRAVATARS; ?>_no"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
</div>
<div class="form-row">
    <div class="helper-text"><?= __('Select whether to use the %gravatar.com user icon service for user avatars, or just use the default ones', array('%gravatar.com' => link_tag('http://www.gravatar.com', 'gravatar.com'))); ?></div>
</div>
<div class="form-row">
    <div class="fancydropdown-container">
        <div class="fancydropdown">
            <label for="<?= Settings::SETTING_ALLOW_REGISTRATION; ?>>"><?= __('Allow self-registration'); ?></label>
            <span class="value"></span>
            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
            <div class="dropdown-container list-mode">
                <input type="radio" name="<?= Settings::SETTING_ALLOW_REGISTRATION; ?>" class="fancycheckbox" <?php if ($access_level != Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= Settings::SETTING_ALLOW_REGISTRATION; ?>_yes" value=1<?php if (Settings::isRegistrationEnabled()): ?> checked<?php endif; ?>>
                <label class="list-item multiline" for="<?= Settings::SETTING_ALLOW_REGISTRATION; ?>_yes">
                    <?= fa_image_tag('check-square', ['class' => 'checked icon'], 'far') . fa_image_tag('square', ['class' => 'unchecked icon'], 'far'); ?>
                    <span class="name">
                        <span class="title value"><?= __('Yes'); ?></span>
                        <span class="additional_information"><?= __('Let users create new user accounts by signing up'); ?></span>
                    </span>
                </label>
                <input type="radio" name="<?= Settings::SETTING_ALLOW_REGISTRATION; ?>" class="fancycheckbox" <?php if ($access_level != Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= Settings::SETTING_ALLOW_REGISTRATION; ?>_no" value=0<?php if (!Settings::isRegistrationEnabled()): ?> checked<?php endif; ?>>
                <label class="list-item multiline" for="<?= Settings::SETTING_ALLOW_REGISTRATION; ?>_no">
                    <?= fa_image_tag('check-square', ['class' => 'checked icon'], 'far') . fa_image_tag('square', ['class' => 'unchecked icon'], 'far'); ?>
                    <span class="name">
                        <span class="title value"><?= __('No'); ?></span>
                        <span class="additional_information"><?= __('All user accounts will be created by an admin user'); ?></span>
                    </span>
                </label>
            </div>
        </div>
    </div>
</div>
<div class="form-row">
    <label for="limit_registration"><?= __('Registration domain whitelist'); ?></label>
    <input type="text" name="<?= Settings::SETTING_REGISTRATION_DOMAIN_WHITELIST; ?>" id="limit_registration" <?php if ($access_level != Settings::ACCESS_FULL): ?> disabled<?php endif; ?> value="<?= Settings::getRegistrationDomainWhitelist(); ?>">
    <div class="helper-text"><?= __('Comma-separated list of allowed domains (ex: %example). Leave empty to allow all domains.', ['%example' => 'pachno.com, zegeniestudios.net']); ?></div>
</div>
<div class="form-row">
    <div class="fancydropdown-container">
        <div class="fancydropdown">
            <label for="<?= Settings::SETTING_USER_GROUP; ?>"><?= __('Default user group'); ?></label>
            <span class="value"></span>
            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
            <div class="dropdown-container list-mode">
                <?php foreach (\pachno\core\entities\Group::getAll() as $user_group): ?>
                    <input type="radio" class="fancycheckbox" name="<?= Settings::SETTING_USER_GROUP; ?>" id="<?= Settings::SETTING_USER_GROUP; ?>_<?= $user_group->getId(); ?>" value="<?php print $user_group->getID(); ?>" <?php if (($default_group = Settings::getDefaultGroup()) instanceof \pachno\core\entities\Group && $default_group->getID() == $user_group->getID()): ?> checked<?php endif; ?>>
                    <label for="<?= Settings::SETTING_USER_GROUP; ?>_<?= $user_group->getId(); ?>" class="list-item">
                        <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                        <span class="name value"><?php print $user_group->getName(); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<div class="form-row">
    <label for="displayname_format"><?= __('User\'s display name format'); ?></label>
    <input type="radio" name="<?= Settings::SETTING_USER_DISPLAYNAME_FORMAT; ?>" class="fancycheckbox" <?php if ($access_level != Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= Settings::SETTING_USER_DISPLAYNAME_FORMAT; ?>_no" value=<?= Settings::USER_DISPLAYNAME_FORMAT_BUDDY; ?> <?php if (Settings::getUserDisplaynameFormat() == Settings::USER_DISPLAYNAME_FORMAT_BUDDY): ?> checked<?php endif; ?>><label for="<?= Settings::SETTING_USER_DISPLAYNAME_FORMAT; ?>_no"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Prefer nickname'); ?></label>
    <input type="radio" name="<?= Settings::SETTING_USER_DISPLAYNAME_FORMAT; ?>" class="fancycheckbox" <?php if ($access_level != Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?= Settings::SETTING_USER_DISPLAYNAME_FORMAT; ?>_yes" value=<?= Settings::USER_DISPLAYNAME_FORMAT_REALNAME; ?> <?php if (Settings::getUserDisplaynameFormat() == Settings::USER_DISPLAYNAME_FORMAT_REALNAME): ?> checked<?php endif; ?>><label for="<?= Settings::SETTING_USER_DISPLAYNAME_FORMAT; ?>_yes"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Prefer full name'); ?></label>
</div>
