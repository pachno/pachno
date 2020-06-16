<?php

    use pachno\core\framework\Settings;
    use pachno\core\framework\Context;

    /**
     * @var \pachno\core\framework\Response $pachno_response
     * @var int $access_level
     */

    $pachno_response->setTitle(__('Configure uploads & attachments'));

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_UPLOADS]); ?>
    <div class="configuration-container">
        <div class="configuration-content centered">
            <h1><?php echo __('Configure uploads & attachments'); ?></h1>
            <div class="helper-text">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_configure_uploads_icon.png', [], true); ?></div>
                <span class="description"><?= __('Configure file uploads so users can attach files to issues and pages. File uploads also lets you provide self-hosted project downloads. Read more about configuring uploads in %ConfigureUploads.', ['%ConfigureUploads' => link_tag(\pachno\core\modules\publish\Publish::getArticleLink('ConfigureUploads'), 'ConfigureUploads')]); ?></span>
            </div>
            <?php if (\pachno\core\framework\Context::getScope()->getMaxUploadLimit()): ?>
                <div class="message-box type-warning">
                    <?php include_component('main/percentbar', array('height' => '20', 'percent' => \pachno\core\framework\Context::getScope()->getCurrentUploadUsagePercent())); ?>
                    <?php echo __('%mb MB of %max MB', array('%mb' => \pachno\core\framework\Context::getScope()->getCurrentUploadUsageMB(), '%max' => \pachno\core\framework\Context::getScope()->getMaxUploadLimit())); ?>
                </div>
            <?php endif; ?>
            <div class="form-container">
                <?php if ($access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                    <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_files'); ?>" method="post" data-simple-submit id="config_uploads">
                <?php endif; ?>
                <?php if (!function_exists('mime_content_type') && !extension_loaded('fileinfo')): ?>
                    <div class="message-box type-warning">
                        <?php echo __('The file upload functionality can be enhanced with file type detection. To enable this, please install and enable the fileinfo extension.'); ?>
                    </div>
                <?php endif; ?>
                <?php if ($uploads_enabled): ?>
                        <div class="form-row">
                            <label for="enable_uploads_yes"><?php echo __('Enable uploads'); ?></label>
                            <div class="fancy-label-select">
                                <input type="radio" class="fancy-checkbox" name="enable_uploads" value="1" id="enable_uploads_yes"<?php if (\pachno\core\framework\Settings::isUploadsEnabled()) echo ' checked'; ?> onclick="toggleSettings();">
                                <label for="enable_uploads_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Yes'); ?></label>
                                <input type="radio" class="fancy-checkbox" name="enable_uploads" value="0" id="enable_uploads_no"<?php if (!\pachno\core\framework\Settings::isUploadsEnabled()) echo ' checked'; ?> onclick="toggleSettings();">
                                <label for="enable_uploads_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('No'); ?></label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="helper-text">
                                <?php echo __('When uploads are disabled, users will not be able to attach files to issues or upload documents, images or PDFs in project planning. More fine-grained permissions are available from the permissions configuration.'); ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="helper-text">
                                <p><?php echo __('Current php values: %max_upload_size=%ini_max_upload_size and %post_max_size=%ini_post_max_size', ['%max_upload_size' => '<span class="command_box">max_upload_size', '%ini_max_upload_size' => (int) ini_get('upload_max_filesize') . __('MB') . '</span>', '%post_max_size' => '<span class="command_box">post_max_size', '%ini_post_max_size' => (int) ini_get('post_max_size') . __('MB') . '</span>']); ?></p>
                                <?php if (\pachno\core\framework\Context::getScope()->getMaxUploadLimit()): ?>
                                    <div class="message-box type-warning">
                                        <?php echo __('Also note that there is a total upload limit on this instance, which is %limit MB.', array('%limit' => '<u>' . \pachno\core\framework\Context::getScope()->getMaxUploadLimit() . '</u>')); ?><br></b>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <label for="upload-restriction-whitelist"><?php echo __('Upload restrictions'); ?></label>
                            <div class="fancy-label-select">
                                <input name="upload_restriction_mode" class="fancy-checkbox" id="upload-restriction-whitelist" type="radio" value="whitelist"<?php if (\pachno\core\framework\Settings::getUploadsRestrictionMode() == 'whitelist') echo ' checked'; ?> onchange="$('#label_upload_extensions_list').update('<?php echo __('Allowed extensions'); ?>');">
                                <label for="upload-restriction-whitelist"><?= fa_image_tag('check', ['class' => 'checked']) . __('Whitelist'); ?></label>
                                <input name="upload_restriction_mode" class="fancy-checkbox" id="upload-restriction-blacklist" type="radio" value="blacklist"<?php if (\pachno\core\framework\Settings::getUploadsRestrictionMode() == 'blacklist') echo ' checked'; ?> onchange="$('#label_upload_extensions_list').update('<?php echo __('Denied extensions'); ?>');">
                                <label for="upload-restriction-blacklist"><?= fa_image_tag('check', ['class' => 'checked']) . __('Blacklist'); ?></label>
                            </div>
                        </div>
                        <div class="form-row">
                            <input type="text" name="upload_extensions_list" id="upload_extensions_list" class="medium" value="<?php echo implode(',', \pachno\core\framework\Settings::getUploadsExtensionsList()); ?>"<?php if (!\pachno\core\framework\Settings::isUploadsEnabled()): ?> disabled<?php endif; ?>>
                            <label id="label_upload_extensions_list" for="upload_extensions_list"><?php if (\pachno\core\framework\Settings::getUploadsRestrictionMode() == 'whitelist') echo __('Allowed extensions'); else echo __('Denied extensions'); ?></label>
                        </div>
                        <div class="form-row">
                            <div class="helper-text">
                                <p><?php echo __('A space-, comma- or semicolon-separated list of extensions (without the dot) used to filter uploads, based on the "%upload_restrictions" setting above. Ex: "%example_1" or "%example_2" or "%example_3"', ['%upload_restrictions' => __('Upload restrictions'), '%example_1' => '<i>txt doc jpg png</i>', '%example_2' => '<i>txt,doc,jpg,png</i>', '%example_3' => '<i>txt;doc;jpg;png</i>']); ?></p>
                            </div>
                        </div>
                        <?php if (\pachno\core\framework\Context::getScope()->isDefault()): ?>
                            <div class="form-row">
                                <label for="upload-storage-database"><?php echo __('File storage'); ?></label>
                                <div class="fancy-label-select">
                                    <input name="upload_storage" class="fancy-checkbox" id="upload-storage-filesystem" type="radio" value="files"<?php if (\pachno\core\framework\Settings::getUploadStorage() == 'files') echo ' checked'; ?> onchange="$('#upload_localpath').enable();">
                                    <label for="upload-storage-filesystem"><?= fa_image_tag('check', ['class' => 'checked']) . __('File system'); ?></label>
                                    <input name="upload_storage" class="fancy-checkbox" id="upload-storage-database" type="radio" value="database"<?php if (\pachno\core\framework\Settings::getUploadStorage() == 'database') echo ' checked'; ?> onchange="$('#upload_localpath').disable();">
                                    <label for="upload-storage-database"><?= fa_image_tag('check', ['class' => 'checked']) . __('Database'); ?></label>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="helper-text">
                                    <?php echo __('Specify whether you want to use the filesystem or database to store uploaded files. Using the database will make it easier to move your installation to another server.'); ?>
                                </div>
                            </div>
                            <div class="form-row">
                                <label><?php echo __('Storage usage'); ?></label>
                                <?php echo __('%mb MB used', ['%mb' => \pachno\core\framework\Context::getScope()->getCurrentUploadUsageMB()]); ?>
                            </div>
                            <div class="form-row">
                                <input type="text" name="upload_localpath" id="upload_localpath" value="<?php echo (\pachno\core\framework\Settings::getUploadsLocalpath() != "") ? \pachno\core\framework\Settings::getUploadsLocalpath() : PACHNO_PATH . 'files/'; ?>"<?php if (!\pachno\core\framework\Settings::isUploadsEnabled() || \pachno\core\framework\Settings::getUploadStorage() == 'database'): ?> disabled<?php endif; ?>>
                                <label for="upload_localpath"><?php echo __('Upload location'); ?></label>
                            </div>
                        <?php endif; ?>
                        <div class="form-row">
                            <label for="upload_delivery_use_xsend_yes"><?php echo __('Php X-Sendfile extension'); ?></label>
                            <div class="fancy-label-select">
                                <input type="radio" class="fancy-checkbox" name="upload_delivery_use_xsend" value="1" class="fancy-checkbox" id="upload_delivery_use_xsend_yes"<?php if (\pachno\core\framework\Settings::isUploadsDeliveryUseXsend()): ?> checked<?php endif; ?> onclick="toggleSettings();">
                                <label for="upload_delivery_use_xsend_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Use X-Sendfile'); ?></label>
                                <input type="radio" class="fancy-checkbox" name="upload_delivery_use_xsend" value="0" class="fancy-checkbox" id="upload_delivery_use_xsend_no"<?php if (!\pachno\core\framework\Settings::isUploadsDeliveryUseXsend()): ?> checked<?php endif; ?> onclick="toggleSettings();">
                                <label for="upload_delivery_use_xsend_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __("Don't use X-Sendfile"); ?></label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="helper-text">
                                <p><?php echo __("Choose whether files shall be delivered through PHP or the X-Sendfile server extension. X-Sendfile allows delivering big files without impacting PHP's memory limit."); ?></p>
                            </div>
                            <div class="message-box type-warning">
                                <?= __('Warning: When enabling this option, make sure the X-Sendfile extension is installed on your server and configured properly to serve files from the above upload location, or file delivery will be severely broken.'); ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <label for="upload_allow_image_caching_yes"><?php echo __('Browser caching (images)'); ?></label>
                            <div class="fancy-label-select">
                                <input type="radio" class="fancy-checkbox" name="upload_allow_image_caching" value="1" class="fancy-checkbox" id="upload_allow_image_caching_yes"<?php if (\pachno\core\framework\Settings::isUploadsImageCachingEnabled()): ?> checked<?php endif; ?> onclick="toggleSettings();">
                                <label for="upload_allow_image_caching_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Enable'); ?></label>
                                <input type="radio" class="fancy-checkbox" name="upload_allow_image_caching" value="0" class="fancy-checkbox" id="upload_allow_image_caching_no"<?php if (!\pachno\core\framework\Settings::isUploadsImageCachingEnabled()): ?> checked<?php endif; ?> onclick="toggleSettings();">
                                <label for="upload_allow_image_caching_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Disable'); ?></label>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="helper-text"><?php echo __('By default, browser caching is disabled for uploads and attachments. When enabling this option, image files will be delivered to the browser with a valid caching header.'); ?></div>
                        </div>
                <?php else: ?>
                    <div class="form-row">
                        <div class="message-box type-warning">
                            <?php echo __('File uploads are not available in this instance of Pachno.'); ?>
                            <?php echo __('When uploads are disabled, users will not be able to attach files to issues or upload documents, images or PDFs in project planning. More fine-grained permissions are available from the permissions configuration.'); ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($uploads_enabled && $access_level == \pachno\core\framework\Settings::ACCESS_FULL): ?>
                        <div class="form-row submit-container">
                            <button type="submit" class="button primary">
                                <span><?php echo __('Save'); ?></span>
                                <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    function toggleSettings()
    {
        if ($('#enable_uploads_yes').checked)
        {
            $('#upload_restriction_mode').enable();
            $('#upload_extensions_list').enable();
            if ($('#upload_storage')) $('#upload_storage').enable();
            $('#upload_max_file_size').enable();
            if ($('#upload_storage').val() == 'files')
            {
                $('#upload_localpath').enable();
            }
            $('#upload_allow_image_caching').enable();
            $('#upload_delivery_use_xsend').enable();
        }
        else
        {
            $('#upload_restriction_mode').disable();
            $('#upload_extensions_list').disable();
            if ($('#upload_storage')) $('#upload_storage').disable();
            $('#upload_max_file_size').disable();
            $('#upload_localpath').disable();
            $('#upload_allow_image_caching').disable();
            $('#upload_delivery_use_xsend').disable();
        }
    }

</script>
