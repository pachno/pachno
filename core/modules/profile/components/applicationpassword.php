<?php

    /**
     * @var string $password
     */

?>
<div id="add_application_password_response">
    <div class="form-row">
        <div class="helper-text">
            <div class="image-container"><?= image_tag('/unthemed/onboarding_application_password_created_icon.png', [], true); ?></div>
            <span class="description"><?= __("Use this one-time password when authenticating with the application. Spaces don't matter, and you don't have to write it down."); ?></span>
        </div>
    </div>
    <div class="form-row">
        <div class="application_password_preview">
            <?php for ($cc = 0; $cc < 4; $cc++): ?><span><?= substr($password, $cc * 4, 4); ?></span><?php endfor; ?>
        </div>
    </div>
    <div class="form-row submit-container">
        <a href="<?= make_url('profile_account'); ?>" class="button primary">
            <?= __('Done'); ?>
        </a>
    </div>
</div>
