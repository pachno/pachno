<div class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?php echo __('Update header icon and favicon'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.UI.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div class="backdrop_detail_content">
        <div class="form-container">
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_site_icons'); ?>" method="post" id="build_form" onsubmit="$('update_icons_indicator').show();return true;" enctype="multipart/form-data">
                <div class="column">
                    <div class="form-row">
                        <h4><?php echo __('Favicon'); ?></h4>
                        <div style="text-align: center; padding: 30px; height: 60px;">
                            <?php echo image_tag(\pachno\core\framework\Settings::getFaviconUrl(), array('style' => 'width: 16px; height: 16px;'), \pachno\core\framework\Settings::isUsingCustomFavicon()); ?>
                        </div>
                        <div class="rounded_box lightgrey borderless" style="margin: 5px 0;">
                            <ul class="simple-list" style="margin-top: 0;">
                                <li><input type="radio" id="small_no_change" name="small_icon_action" value="0" checked><label for="small_no_change"><?php echo __('Leave as is'); ?></label></li>
                                <?php if (\pachno\core\framework\Settings::isUsingCustomFavicon()): ?>
                                    <li><input type="radio" id="small_clear_icon" name="small_icon_action" value="clear_file"><label for="small_clear_icon"><?php echo __('Remove icon and return to default'); ?></label></li>
                                <?php endif; ?>
                                <?php if (\pachno\core\framework\Settings::isUploadsEnabled()): ?>
                                    <li><input type="radio" id="small_upload" name="small_icon_action" value="upload_file"><label for="small_upload"><?php echo __('Upload new icon'); ?>:</label><br><input type="file" name="small_icon"></li>
                                <?php else: ?>
                                    <li class="faded_out" style="padding: 2px; font-style: italic;"><?php echo __('Enable file uploads to upload site icons'); ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="form-row">
                        <h4><?php echo __('Header icon'); ?></h4>
                        <div style="text-align: center; padding: 30px; height: 60px;">
                            <?php echo image_tag(\pachno\core\framework\Settings::getHeaderIconUrl(), array('style' => 'width: 24px; height: 24px;'), \pachno\core\framework\Settings::isUsingCustomHeaderIcon()); ?>
                        </div>
                        <div class="rounded_box lightgrey borderless" style="margin: 5px 0;">
                            <ul class="simple-list" style="margin-top: 0;">
                                <li><input type="radio" id="large_no_change" name="large_icon_action" value="0" checked><label for="large_no_change"><?php echo __('Leave as is').'</span>'; ?></label></li>
                                <?php if (\pachno\core\framework\Settings::isUsingCustomHeaderIcon()): ?>
                                    <li><input type="radio" id="large_clear_icon" name="large_icon_action" value="clear_file"><label for="large_clear_icon"><?php echo __('Remove icon and return to default'); ?></label></li>
                                <?php endif; ?>
                                <?php if (\pachno\core\framework\Settings::isUploadsEnabled()): ?>
                                    <li><input type="radio" id="large_upload" name="large_icon_action" value="upload_file"><label for="large_upload"><?php echo __('Upload new icon'); ?>:</label><br><input type="file" name="large_icon"></li>
                                <?php else: ?>
                                    <li class="faded_out" style="padding: 2px; font-style: italic;"><?php echo __('Enable file uploads to upload site icons'); ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="form-row submit-container">
                    <button class="button primary" type="submit">
                        <?php echo fa_image_tag('spinner', ['class' => 'fa-spin', 'id' => 'update_icons_indicator', 'style' => 'display: none']); ?>
                        <span><?= __('Update icons'); ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
