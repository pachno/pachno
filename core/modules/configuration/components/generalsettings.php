<div class="form-row">
    <label for="setting_<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME; ?>">
        <span><?php echo __('Pachno custom name'); ?></span>
        <?php echo config_explanation(
            __('This is the name appearing in the headers and several other places, usually displaying "Pachno"')
        ); ?>
    </label>
    <input type="text" name="<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME; ?>" id="setting_<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME; ?>"
           value="<?php echo str_replace('"', '&quot;', \pachno\core\framework\Settings::getSiteHeaderName()); ?>"
           <?php if ($access_level != \pachno\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>
    >
</div>
<div class="form-row">
        <label><?php echo __('Custom header and favicons'); ?></label>
        <div class="button button-blue" onclick="Pachno.UI.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', ['key' => 'site_icons']); ?>');"><span><?php echo __('Configure icons'); ?></span></div>
</div>
<div class="form-row">
    <label for="header_link">
        <span><?php echo __('Custom header link'); ?></span>
        <?php echo config_explanation(
            __('You can alter the webpage that clicking on the header icon navigates to. If left blank it will link to the main page of this installation.')
        ); ?>
    </label>
    <input type="text" name="<?php echo \pachno\core\framework\Settings::SETTING_HEADER_LINK; ?>"
           id="header_link" value="<?php echo \pachno\core\framework\Settings::getHeaderLink(); ?>"
           style="width: 90%;"<?php if ($access_level != \pachno\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>
        >
</div>
<div class="form-row">
    <label for="pachno_header_name_html">
        <span><?php echo __('Allow HTML in site title'); ?></span>
        <?php echo config_explanation(
            __('Enabling this setting allows a malicious admin user to potentially insert harmful code'), 'exclamation-triangle', 'fas'
        ); ?>
    </label>
    <div class="fancy-label-select">
        <input type="radio" name="<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME_HTML; ?>" class="fancy-checkbox" <?php if ($access_level != \pachno\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>id="<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME_HTML; ?>_yes" value=1<?php if (\pachno\core\framework\Settings::isHeaderHtmlFormattingAllowed()): ?> checked<?php endif; ?>>
        <label for="<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME_HTML; ?>_yes"><?php echo fa_image_tag('check', ['class' => 'checked']) . __('Yes, allow HTML'); ?></label>
        <input type="radio" name="<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME_HTML; ?>" class="fancy-checkbox" <?php if ($access_level != \pachno\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>id="<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME_HTML; ?>_no" value=0<?php if (!\pachno\core\framework\Settings::isHeaderHtmlFormattingAllowed()): ?> checked<?php endif; ?>>
        <label for="<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME_HTML; ?>_no"><?php echo fa_image_tag('check', ['class' => 'checked']) . __("No, don't allow HTML"); ?></label>
    </div>
</div>
