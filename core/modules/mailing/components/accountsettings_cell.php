<?php

    use pachno\core\framework\Settings;

?>
<?php if (array_key_exists($key, $notificationsettings)): ?>
    <div class="column info-icons centered">
        <input type="checkbox" class="fancy-checkbox" name="mailing_<?php echo $key; ?>" id="mailing_<?= $key; ?>" value="1"<?php if ($pachno_user->getNotificationSetting($key, $key == Settings::SETTINGS_USER_NOTIFY_MENTIONED, 'mailing')->isOn()): ?> checked<?php endif; ?>><label for="mailing_<?= $key; ?>"><?= fa_image_tag('check-square', ['class' => 'icon checked']) . fa_image_tag('square', ['class' => 'icon unchecked'], 'far'); ?></label>
    </div>
<?php else: ?>
    <div class="column info-icons"></div>
<?php endif; ?>
