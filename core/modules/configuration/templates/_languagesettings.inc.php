<div class="form-row">
    <div class="fancydropdown-container">
        <div class="fancydropdown">
            <label><?php echo __('Interface language'); ?></label>
            <span class="value"></span>
            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
            <div class="dropdown-container list-mode">
                <?php foreach ($languages as $language_code => $language): ?>
                    <input type="radio" name="<?php echo \pachno\core\framework\Settings::SETTING_DEFAULT_LANGUAGE; ?>" id="setting_<?php echo \pachno\core\framework\Settings::SETTING_DEFAULT_LANGUAGE; ?>" value="<?php echo $language_code; ?>" <?php if (\pachno\core\framework\Settings::getLanguage() == $language_code): ?> checked<?php endif; ?> class="fancycheckbox">
                    <label for="setting_<?php echo \pachno\core\framework\Settings::SETTING_DEFAULT_LANGUAGE; ?>" class="list-item">
                        <?= fa_image_tag('check-circle', ['class' => 'checked icon'], 'far') . fa_image_tag('circle', ['class' => 'unchecked icon'], 'far'); ?>
                        <span class="name"><?php echo $language; ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="helper-text"><?php echo __('This is the language that will be used in Pachno. Depending on other settings, users may change the language displayed to them.'); ?></div>
</div>
<div class="form-row">
    <label for="charset"><?php echo __('Override language character set'); ?></label>
    <input type="text" name="<?php echo \pachno\core\framework\Settings::SETTING_DEFAULT_CHARSET; ?>" id="charset" value="<?php echo \pachno\core\framework\Settings::getCharset(); ?>" style="width: 150px;" <?php if ($access_level != \pachno\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> placeholder="<?= \pachno\core\framework\Context::getI18n()->getLangCharset(); ?>">
    <div class="helper-text">
        <?php echo __('Current character set defined in the language is %charset', array('%charset' => '<b>' . \pachno\core\framework\Context::getI18n()->getLangCharset() . '</b>')); ?>
    </div>
</div>
<div class="form-row">
    <label for="server_timezone"><?php echo __('Server timezone'); ?></label>
    <div class="fancydropdown-container">
        <div class="fancydropdown">
            <label><?php echo __('Interface language'); ?></label>
            <span class="value"></span>
            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
            <div class="dropdown-container list-mode">
                <input type="radio" name="<?php echo \pachno\core\framework\Settings::SETTING_SERVER_TIMEZONE; ?>" id="setting_<?php echo \pachno\core\framework\Settings::SETTING_SERVER_TIMEZONE; ?>" value="0" <?php if (!\pachno\core\framework\Settings::getServerTimezoneIdentifier()): ?> checked<?php endif; ?> class="fancycheckbox">
                <label for="setting_<?php echo \pachno\core\framework\Settings::SETTING_SERVER_TIMEZONE; ?>" class="list-item">
                    <?= fa_image_tag('check-circle', ['class' => 'checked icon'], 'far') . fa_image_tag('circle', ['class' => 'unchecked icon'], 'far'); ?>
                    <span class="name"><?php echo __('Not set'); ?></span>
                </label>
                <div class="list-item filter-container">
                    <input type="search">
                </div>
                <div class="filter_existing_values">
                    <?php foreach ($timezones as $timezone => $description): ?>
                        <input type="radio" name="<?php echo \pachno\core\framework\Settings::SETTING_SERVER_TIMEZONE; ?>" id="setting_<?php echo \pachno\core\framework\Settings::SETTING_SERVER_TIMEZONE; ?>" value="<?php echo $timezone; ?>" <?php if (\pachno\core\framework\Settings::getServerTimezoneIdentifier() == $timezone): ?> checked<?php endif; ?> class="fancycheckbox">
                        <label for="setting_<?php echo \pachno\core\framework\Settings::SETTING_SERVER_TIMEZONE; ?>" class="list-item">
                            <?= fa_image_tag('check-circle', ['class' => 'checked icon'], 'far') . fa_image_tag('circle', ['class' => 'unchecked icon'], 'far'); ?>
                            <span class="name"><?php echo $description; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="helper-text">
        <?php echo __('The timezone for the server hosting Pachno. Make sure this is the same as the timezone the server is running in - this is not necessarily the same as your own timezone!'); ?>
    </div>
    <div class="helper-text">
        <?= __('The time is now: %time', array('%time' => \pachno\core\framework\Context::getI18n()->formatTime(time(), 1, true))); ?>
    </div>
</div>
