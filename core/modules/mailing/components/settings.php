<?php

    use pachno\core\modules\mailing\Mailing;

    /**
     * @var int $access_level
     * @var Mailing $module
     * @var \pachno\core\entities\User $pachno_user
     */

?>
<div class="configuration-content centered">
    <h1><?php echo __('Configure outgoing emails'); ?></h1>
    <div class="helper-text">
        <div class="image-container"><?= image_tag('/unthemed/onboarding_configure_email_icon.png', [], true); ?></div>
        <span class="description"><?= __('Configure settings for outgoing emails such as registration information, notifications and more. Read more about configuring outgoing emails in the %online_documentation.', ['%online_documentation' => link_tag(\pachno\core\modules\publish\Publish::getArticleLink('ConfigureOutgoingEmail'), __('online documentation'))]); ?></span>
    </div>
    <div id="mailnotification_settings_container" class="form-container mailer-type-<?php echo $module->getMailerType(); ?>">
        <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_module', array('config_module' => $module->getName())); ?>" id="mailing_settings_form" method="post" data-simple-submit <?php if (!\pachno\core\framework\Context::getScope()->isDefault()) echo 'data-interactive-form'; ?>>
            <div class="row aligned">
                <div class="column small">
                    <div class="form-row">
                        <label for="enable_outgoing_notifications_yes"><?php echo __('Enable outgoing email notifications'); ?></label>
                        <div class="fancy-label-select">
                            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                                <input type="radio" name="enable_outgoing_notifications" value="1" class="fancy-checkbox toggle-enable-controls" id="enable_outgoing_notifications_yes"<?php if ($module->isOutgoingNotificationsEnabled()): ?> checked<?php endif; ?>>
                                <label for="enable_outgoing_notifications_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Yes'); ?></label>
                                <input type="radio" name="enable_outgoing_notifications" value="0" class="fancy-checkbox toggle-enable-controls" id="enable_outgoing_notifications_no"<?php if (!$module->isOutgoingNotificationsEnabled()): ?> checked<?php endif; ?>>
                                <label for="enable_outgoing_notifications_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('No'); ?></label>
                            <?php else: ?>
                                <?= ($module->isOutgoingNotificationsEnabled()) ? __('Yes') : __('No'); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="column small">
                    <div class="form-row">
                        <label for="activation_needed_yes"><?php echo __('Require email activation'); ?></label>
                        <div class="fancy-label-select">
                            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                                <input type="radio" name="activation_needed" value="1" class="fancy-checkbox" id="activation_needed_yes"<?php if ($module->isActivationNeeded()): ?> checked<?php endif; ?>>
                                <label for="activation_needed_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Yes'); ?></label>
                                <input type="radio" name="activation_needed" value="0" class="fancy-checkbox" id="activation_needed_no"<?php if (!$module->isActivationNeeded()): ?> checked<?php endif; ?>>
                                <label for="activation_needed_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('No'); ?></label>
                            <?php else: ?>
                                <?= ($module->isActivationNeeded()) ? __('Yes') : __('No'); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="column small">
                    <div class="form-row">
                        <label for="use_queue_yes"><?php echo __('Use email queueing'); ?></label>
                        <div class="fancy-label-select">
                            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                                <input type="radio" name="use_queue" value="1" class="fancy-checkbox" id="use_queue_yes"<?php if ($module->usesEmailQueue()): ?> checked<?php endif; ?>>
                                <label for="use_queue_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Yes'); ?></label>
                                <input type="radio" name="use_queue" value="0" class="fancy-checkbox" id="use_queue_no"<?php if (!$module->usesEmailQueue()): ?> checked<?php endif; ?>>
                                <label for="use_queue_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('No'); ?></label>
                            <?php else: ?>
                                <?= ($module->usesEmailQueue()) ? __('Yes') : __('No'); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (\pachno\core\framework\Context::getScope()->isDefault()): ?>
                <div class="row">
                    <div class="column">
                        <div class="form-row">
                            <label for="from_name" class="required">
                                <span><?php echo __('Email "from"-name'); ?></span>
                                <span class="required-indicator">* </span>
                            </label>
                            <input type="text" name="from_name" id="from_name" value="<?php echo $module->getSetting('from_name'); ?>"<?php echo ($access_level != \pachno\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>
                        </div>
                    </div>
                    <div class="column">
                        <div class="form-row">
                            <label for="from_address" class="required">
                                <span><?php echo __('Email "from"-address'); ?></span>
                                <span class="required-indicator">* </span>
                            </label>
                            <input type="text" name="from_addr" id="from_address" value="<?php echo $module->getSetting('from_addr'); ?>"<?php echo ($access_level != \pachno\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="helper-text">
                        <?php echo __('Outgoing emails from Pachno will use this name and address, unless configured otherwise (such as specific project notifications with a custom reply-address)'); ?>
                    </div>
                </div>
                <div class="form-row">
                    <label for="cli_mailing_url" class="required">
                        <span><?php echo __('Issue tracker URL'); ?></span>
                        <span class="required-indicator">* </span>
                    </label>
                    <input type="text" name="cli_mailing_url" id="cli_mailing_url" value="<?php echo $module->getMailingUrl(); ?>" placeholder="<?php echo __('e.g.: %example', ['%example' => \pachno\core\framework\Context::getScope()->getCurrentHostname()]); ?>" style="width: 100%;"<?php echo ($access_level != \pachno\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>
                </div>
                <div class="form-row">
                    <div class="helper-text">
                        <?php echo __("This is the full URL to the issue tracker, used when sending outgoing emails. If this isn't configured, you will not be able to use the outgoing email feature."); ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="fancy-dropdown-container">
                        <div class="fancy-dropdown">
                            <label><?= __('Email backend'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <input type="radio" class="fancy-checkbox trigger-backend-config-type" name="mail_type" id="mail_type_<?php echo Mailing::MAIL_TYPE_SMTP; ?>" value="<?php echo Mailing::MAIL_TYPE_SMTP; ?>" <?php if ($module->getMailerType() == Mailing::MAIL_TYPE_SMTP): ?> checked<?php endif; ?>>
                                <label class="list-item" for="mail_type_<?php echo Mailing::MAIL_TYPE_SMTP; ?>">
                                    <span class="name value"><?php echo __('SMTP Transport'); ?></span>
                                </label>
                                <input type="radio" class="fancy-checkbox trigger-backend-config-type" name="mail_type" id="mail_type_<?php echo Mailing::MAIL_TYPE_SENDMAIL; ?>" value="<?php echo Mailing::MAIL_TYPE_SENDMAIL; ?>" <?php if ($module->getMailerType() == Mailing::MAIL_TYPE_SENDMAIL): ?> checked<?php endif; ?>>
                                <label class="list-item" for="mail_type_<?php echo Mailing::MAIL_TYPE_SENDMAIL; ?>">
                                    <span class="name value"><?php echo __('Sendmail Transport'); ?></span>
                                </label>
                                <input type="radio" class="fancy-checkbox trigger-backend-config-type" name="mail_type" id="mail_type_<?php echo Mailing::MAIL_TYPE_PHP; ?>" value="<?php echo Mailing::MAIL_TYPE_PHP; ?>" <?php if ($module->getMailerType() == Mailing::MAIL_TYPE_PHP): ?> checked<?php endif; ?>>
                                <label class="list-item" for="mail_type_<?php echo Mailing::MAIL_TYPE_PHP; ?>">
                                    <span class="name value"><?php echo __('Fallback PHP mail() transport (not recommended)'); ?></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="helper-text">
                        <span><?php echo __('For outgoing emails Pachno uses the %swiftmailer library, which supports several different mail transports. Please see the %swiftmailer_configuration for more information.', array('%swiftmailer_configuration' => '<a href="http://swiftmailer.org/docs/sending.html#transport-types" target="_blank">'.__('Swiftmailer configuration').'</a>', '%swiftmailer' => '<a href="http://swiftmailer.org/docs/sending.html#transport-types" target="_blank">Swiftmailer</a>')); ?></span>
                    </div>
                </div>
                <div class="form <?php if ($module->getMailerType() != Mailing::MAIL_TYPE_SENDMAIL) echo 'hidden'; ?>" id="mailer_config_<?= Mailing::MAIL_TYPE_SENDMAIL; ?>">
                    <div class="form-row">
                        <label for="mailing_sendmail_command"><?php echo __('Sendmail command'); ?></label>
                        <input type="text" placeholder="<?php echo __("Default ('/usr/sbin/sendmail -bs')"); ?>" name="sendmail_command" id="mailing_sendmail_command" value="<?php echo $module->getSendmailCommand(); ?>" style="width: 100%;"<?php echo ($access_level != \pachno\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>
                    </div>
                    <div class="form-row">
                        <div class="helper-text">
                            <span><?php echo __('Please see the %swiftmailer_configuration for more information about the sendmail transport.', array('%swiftmailer_configuration' => '<a href="http://swiftmailer.org/docs/sending.html#the-sendmail-transport" target="_blank">'.__('Swiftmailer configuration').'</a>', '%swiftmailer' => '<a href="http://swiftmailer.org/docs/sending.html#transport-types" target="_blank">Swiftmailer</a>')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="form <?php if ($module->getMailerType() != Mailing::MAIL_TYPE_SMTP) echo 'hidden'; ?>" id="mailer_config_<?= Mailing::MAIL_TYPE_SMTP; ?>">
                    <div class="form-row">
                        <div class="header"><?= __('SMTP configuration'); ?></div>
                    </div>
                    <div class="row">
                        <div class="column large">
                            <div class="form-row">
                                <label for="smtp_host"><?php echo __('SMTP server address'); ?></label>
                                <input type="text" name="smtp_host" id="smtp_host" value="<?php echo $module->getSetting('smtp_host'); ?>" style="width: 100%;"<?php echo ($access_level != \pachno\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>
                            </div>
                            <div class="form-row">
                                <label for="smtp_user"><?php echo __('SMTP username'); ?></label>
                                <input type="text" name="smtp_user" id="smtp_user" value="<?php echo $module->getSmtpUsername(); ?>" style="width: 300px;"<?php echo ($access_level != \pachno\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>
                            </div>
                            <div class="form-row">
                                <label for="mailing_smtp_password"><?php echo __('SMTP password'); ?></label>
                                <input type="password" name="smtp_pwd" id="mailing_smtp_password" value="<?php echo $module->getSmtpPassword(); ?>" style="width: 150px;"<?php echo ($access_level != \pachno\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>
                            </div>
                        </div>
                        <div class="column small">
                            <div class="form-row">
                                <label for="smtp_port"><?php echo __('SMTP address port'); ?></label>
                                <input type="text" name="smtp_port" id="smtp_port" value="<?php echo $module->getSetting('smtp_port'); ?>" style="width: 40px;"<?php echo ($access_level != \pachno\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>
                            </div>
                            <div class="form-row">
                                <label for="timeout"><?php echo __('SMTP server timeout'); ?></label>
                                <input type="text" name="timeout" id="timeout" value="<?php echo $module->getSmtpTimeout(); ?>" style="width: 40px;"<?php echo ($access_level != \pachno\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>><?php echo __('%number_of seconds', array('%number_of' => '')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="helper-text"><?php echo __('Connection information for the outgoing email server'); ?></div>
                    </div>
                    <div class="form-row">
                        <label for="mailing_encryption"><?php echo __('Connection encryption'); ?></label>
                        <div class="fancy-label-select" name="smtp_encryption" id="mailing_encryption" <?php echo ($access_level != \pachno\core\framework\Settings::ACCESS_FULL || !$module->isOutgoingNotificationsEnabled()) ? ' disabled' : ''; ?>>
                            <input type="radio" class="fancy-checkbox" id="smtp_encryption_choice_nothing" value="" <?php if (!$module->getSmtpEncryption()): ?> checked<?php endif; ?>>
                            <label for="smtp_encryption_choice_nothing"><?= fa_image_tag('check', ['class' => 'checked']) . __('No encryption'); ?></label>
                            <input type="radio" class="fancy-checkbox" id="smtp_encryption_choice_ssl" value="ssl" <?php if (!$module->isSSLEncryptionAvailable()): ?> disabled<?php elseif ($module->getSmtpEncryption() == 'ssl'): ?> checked<?php endif; ?>>
                            <label for="smtp_encryption_choice_ssl"><?= fa_image_tag('check', ['class' => 'checked']) . __('Use SSL encryption'); ?></label>
                            <input type="radio" class="fancy-checkbox" id="smtp_encryption_choice_tls" value="tls" <?php if (!$module->isTLSEncryptionAvailable()): ?> disabled<?php elseif ($module->getSmtpEncryption() == 'tls'): ?> checked<?php endif; ?>>
                            <label for="smtp_encryption_choice_tls"><?= fa_image_tag('check', ['class' => 'checked']) . __('Use TLS encryption'); ?></label>
                        </div>
                    </div>
                </div>
            <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                <script>
                    Pachno.on(Pachno.EVENTS.ready, () => {
                        $('.trigger-backend-config-type').change(function () {
                            const value = $(this).val();
                            const mailertypes = ['<?php echo Mailing::MAIL_TYPE_PHP; ?>', '<?php echo Mailing::MAIL_TYPE_SMTP; ?>', '<?php echo Mailing::MAIL_TYPE_SENDMAIL; ?>'];
                            for (const mailertype of mailertypes) {
                                const $configContainer = $(`#mailer_config_${mailertype}`);
                                (mailertype == value) ? $configContainer.removeClass('hidden') : $configContainer.addClass('hidden');
                            }
                        })

                        const onEnableChange = function () {
                            const $inputs = $('#mailnotification_settings_container').find('input:not(.toggle-enable-controls)');
                            if ($('#enable_outgoing_notifications_yes').is(':checked')) {
                                $inputs.prop('disabled', false);
                            } else {
                                $inputs.prop('disabled', true);
                            }
                        }

                        $('#enable_outgoing_notifications_yes').change(onEnableChange);
                        $('#enable_outgoing_notifications_no').change(onEnableChange);
                    });
                </script>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary">
                        <span><?php echo __('Save'); ?></span>
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                    </button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        </form>
        <?php if ($module->isEnabled()): ?>
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('mailing_testemail'); ?>" method="post" data-simple-submit id="mailing_send_test_email_form">
                <div class="form-row header">
                    <h3><?= __('Send test email'); ?></h3>
                </div>
                <div class="form-row">
                    <div class="helper-text">
                        <?php if ($pachno_user->getEmail()): ?>
                            <?= __('Click the "%send_test_email"-button to send a test email to your current email-address (%email)', ['%send_test_email' => __('Send test email'), '%email' => $pachno_user->getEmail()]); ?>
                        <?php else: ?>
                            <?= __('You can send a test email to your registered email address by adding an email address in your account profile settings'); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary" <?php if (!$pachno_user->getEmail()) echo 'disabled'; ?>>
                        <?= fa_image_tag('paper-plane', ['class' => 'icon'], 'far'); ?>
                        <span><?= __('Send test email'); ?></span>
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
