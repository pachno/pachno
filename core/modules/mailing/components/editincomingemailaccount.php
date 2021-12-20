<script type="text/javascript">
    require(['domReady'], function (domReady) {
        domReady(function () {
            Pachno.Modules.mailing.saveIncomingEmailAccount = function(url) {
                Pachno.UI.fetch(url, {
                    form: 'incoming_email_account_form',
                    loading: {indicator: '#add_account_indicator'},
                    success: {
                        update: {element: <?php echo ($account->getID()) ? "'mailing_account_{$account->getID()}_name', from: 'name'" : "'mailing_incoming_accounts', insertion: true"; ?>},
                        callback: function() {
                            Pachno.UI.Backdrop.reset();
                        }
                    }
                });
            };
        });
    });
</script>
<div class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?php echo ($account->getId()) ? __('Edit incoming email account') : __('Add new incoming email account'); ?></span>
        <a href="javascript:void(0);" class="closer"><?= fa_image_tag('times'); ?></a>
    </div>
    <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" id="incoming_email_account_form" action="<?php echo make_url('mailing_save_incoming_account', array('project_key' => $project->getKey())); ?>" method="post" id="build_form" onsubmit="Pachno.Modules.mailing.saveIncomingEmailAccount('<?php echo make_url('mailing_save_incoming_account', array('project_key' => $project->getKey())); ?>');return false;">
        <div id="backdrop_detail_content" class="backdrop_detail_content">
            <?php echo __('Pachno can check email accounts and create issues from incoming emails. Set up a new account here, and check the %online_documentation for more information.', array('%online_documentation' => link_tag('https://projects.pach.no/pachno/docs/IncomingEmail', '<b>'.__('online documentation').'</b>'))); ?>
            <input type="hidden" name="account_id" value="<?php echo $account->getID(); ?>">
            <?php if (!\pachno\core\framework\Settings::isUploadsEnabled()): ?>
                <div class="permissions_warning">
                    File uploads are disabled, attachments will be silently discarded.
                </div>
            <?php endif; ?>
            <table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
                <tr>
                    <td style="width: 200px;"><label for="account_name"><?php echo __('Account name:'); ?></label></td>
                    <td style="width: 580px;"><input type="text" name="name" id="account_name" style="width: 570px;" value="<?php echo $account->getName(); ?>"></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class="faded_out"><?php echo __('Enter a short, descriptive name for this incoming email account'); ?></td>
                </tr>
                <tr>
                    <td><label for="account_type_imap"><?php echo __('Account type'); ?></label></td>
                    <td>
                        <div><input type="radio" class="fancy-checkbox" name="account_type" id="account_type_imap" value="<?php echo \pachno\core\entities\IncomingEmailAccount::SERVER_IMAP; ?>"<?php if ($account->isImap()) echo ' checked'; ?>><label for="account_type_imap" style="font-weight: normal;"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Microsoft Exchange / Outlook / Gmail / IMAP'); ?></label></div>
                        <div><input type="radio" class="fancy-checkbox" name="account_type" id="account_type_pop3" value="<?php echo \pachno\core\entities\IncomingEmailAccount::SERVER_POP3; ?>"<?php if ($account->isPop3()) echo ' checked'; ?>><label for="account_type_pop3" style="font-weight: normal;"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('POP3'); ?></label></div>
                    </td>
                </tr>
                <tr>
                    <td><label for="account_servername"><?php echo __('Server name:'); ?></label></td>
                    <td><input type="text" name="servername" id="account_servername" style="width: 300px;" value="<?php echo $account->getServer(); ?>"></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class="faded_out"><?php echo __('Enter the name of the incoming email server'); ?></td>
                </tr>
                <tr>
                    <td><label for="account_port"><?php echo __('Port number:'); ?></label></td>
                    <td><input type="text" name="port" id="account_port" style="width: 50px;" value="<?php echo $account->getPort(); ?>"></td>
                </tr>
                <tr>
                    <td><label for="account_username"><?php echo __('Email username:'); ?></label></td>
                    <td><input type="text" name="username" id="account_username" style="width: 200px;" value="<?php echo $account->getUsername(); ?>"></td>
                </tr>
                <tr>
                    <td><label for="account_password"><?php echo __('Email password:'); ?></label></td>
                    <td><input type="password" name="password" id="account_password" style="width: 200px;" value="<?php echo $account->getPassword(); ?>"></td>
                </tr>
                <tr>
                    <td><label for="account_plaintext_authentication_yes"><?php echo __('Use plaintext authentication'); ?></label></td>
                    <td>
                        <input type="radio" class="fancy-checkbox" name="plaintext_authentication" class="fancy-checkbox" id="account_plaintext_authentication_yes" value="1"<?php if ($account->usesPlaintextAuthentication()) echo ' checked'; ?>><label for="account_plaintext_authentication_yes" style="font-weight: normal;"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
                        <input type="radio" class="fancy-checkbox" name="plaintext_authentication" class="fancy-checkbox" id="account_plaintext_authentication_no" value="0"<?php if (!$account->usesPlaintextAuthentication()) echo ' checked'; ?>><label for="account_plaintext_authentication_no" style="font-weight: normal;"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
                    </td>
                </tr>
                <tr>
                    <td><label for="account_ssl"><?php echo __('Use secure connection (SSL)'); ?></label></td>
                    <td>
                        <input type="radio" class="fancy-checkbox" name="ssl" id="ssl_yes" value="1"<?php if ($account->usesSSL()) echo ' checked'; ?>><label for="ssl_yes" style="font-weight: normal;"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
                        <input type="radio" class="fancy-checkbox" name="ssl" id="ssl_no" value="0"<?php if (!$account->usesSSL()) echo ' checked'; ?>><label for="ssl_no" style="font-weight: normal;"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
                    </td>
                </tr>
                <tr>
                    <td><label for="account_ignore_certificate_validation_yes"><?php echo __('Ignore certificate errors'); ?></label></td>
                    <td>
                        <input type="radio" class="fancy-checkbox" name="ignore_certificate_validation" id="account_ignore_certificate_validation_yes" value="1"<?php if ($account->doesIgnoreCertificateValidation()) echo ' checked'; ?>><label for="account_ignore_certificate_validation_yes" style="font-weight: normal;"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
                        <input type="radio" class="fancy-checkbox" name="ignore_certificate_validation" id="account_ignore_certificate_validation_no" value="0"<?php if (!$account->doesIgnoreCertificateValidation()) echo ' checked'; ?>><label for="account_ignore_certificate_validation_no" style="font-weight: normal;"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class="faded_out"><?php echo __('Select this to ignore certificate validation error messages'); ?></td>
                </tr>
                <tr>
                    <td><label for="account_foldername"><?php echo __('Email folder name:'); ?></label></td>
                    <td><input type="text" name="folder" id="account_foldername" style="width: 200px;" value="<?php echo $account->getFoldername(); ?>"></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class="faded_out"><?php echo __('Enter folder name to read from. Leave blank for default (INBOX)'); ?></td>
                </tr>
                <tr>
                    <td><label for="account_keepemail_yes"><?php echo __('Keep email:'); ?></label></td>
                    <td>
                        <input type="radio" name="keepemail" class="fancy-checkbox" id="account_keepemail_yes" value="1"<?php if ($account->doesKeepEmails()) echo ' checked'; ?>><label for="account_keepemail_yes" style="font-weight: normal;"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
                        <input type="radio" name="keepemail" class="fancy-checkbox" id="account_keepemail_no" value="0"<?php if (!$account->doesKeepEmails()) echo ' checked'; ?>><label for="account_keepemail_no" style="font-weight: normal;"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class="faded_out"><?php echo __('Select whether emails should be kept or removed from the account after being downloaded'); ?></td>
                </tr>
                <tr>
                    <td><label for="account_ssl"><?php echo __('Prefer HTML content'); ?></label></td>
                    <td>
                        <input type="radio" class="fancy-checkbox" name="prefer_html" id="prefer_html_yes" value="1"<?php if ($account->prefersHtml()) echo ' checked'; ?>><label for="prefer_html_yes" style="font-weight: normal;"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
                        <input type="radio" class="fancy-checkbox" name="prefer_html" id="prefer_html_no" value="0"<?php if (!$account->prefersHtml()) echo ' checked'; ?>><label for="prefer_html_no" style="font-weight: normal;"><?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
                    </td>
                </tr>
                <tr>
                    <td><label for="account_issuetype"><?php echo __('Issuetype'); ?></label></td>
                    <td>
                        <select id="account_issuetype" name="issuetype">
                            <?php foreach ($project->getIssuetypeScheme()->getReportableIssuetypes() as $issuetype): ?>
                            <option value="<?php echo $issuetype->getID(); ?>"<?php if ($account->getIssuetypeID() == $issuetype->getID()) echo " selected"; ?>><?php echo $issuetype->getName(); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td class="faded_out"><?php echo __('Any issues created will be set to this issuetype, and its first workflow step will be applied'); ?></td>
                </tr>
            </table>
        </div>
        <div class="backdrop_details_submit">
            <span class="explanation">
                <?php if ($account->getId()): ?>
                    <?php echo __('When you are done, click "%save_changes" to update the details for this account', array('%save_changes' => __('Save changes'))); ?>
                <?php else: ?>
                    <?php echo __('When you are done, click "%add_account" to add this account', array('%add_account' => __('Add account'))); ?>
                <?php endif; ?>
            </span>
            <div class="submit_container">
                <?php if ($account->getID()): ?>
                    <input type="hidden" name="account_id" value="<?php echo $account->getID(); ?>">
                    <input type="hidden" name="project_id" value="<?php echo $project->getID(); ?>">
                <?php endif; ?>
                <button type="submit" class="button"><?php echo image_tag('spinning_16.gif', ['id' => 'add_account_indicator', 'style' => 'display: none;']) . (($account->getId()) ? __('Save changes') : __('Add account')); ?></button>
            </div>
        </div>
    </form>
</div>
