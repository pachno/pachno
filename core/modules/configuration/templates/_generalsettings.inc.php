<div class="form-row">
    <label for="setting_<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME; ?>"><?php echo __('Pachno custom name'); ?></label>
    <input type="text" name="<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME; ?>" id="setting_<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME; ?>"
           value="<?php echo str_replace('"', '&quot;', \pachno\core\framework\Settings::getSiteHeaderName()); ?>"
           <?php if ($access_level != \pachno\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>
    >
    <?php echo config_explanation(
        __('This is the name appearing in the headers and several other places, usually displaying "Pachno"')
    ); ?>
</div>
<div class="form-row">
        <label><?php echo __('Custom header and favicons'); ?></label>
        <div class="button button-blue" onclick="Pachno.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', ['key' => 'site_icons']); ?>');"><span><?php echo __('Configure icons'); ?></span></div>
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
    <input type="radio" name="<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME_HTML; ?>" class="fancy-checkbox" <?php if ($access_level != \pachno\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>id="<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME_HTML; ?>_yes" value=1<?php if (\pachno\core\framework\Settings::isHeaderHtmlFormattingAllowed()): ?> checked<?php endif; ?>><label for="<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME_HTML; ?>_yes"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
    <input type="radio" name="<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME_HTML; ?>" class="fancy-checkbox" <?php if ($access_level != \pachno\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?>id="<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME_HTML; ?>_no" value=0<?php if (!\pachno\core\framework\Settings::isHeaderHtmlFormattingAllowed()): ?> checked<?php endif; ?>><label for="<?php echo \pachno\core\framework\Settings::SETTING_SITE_NAME_HTML; ?>_no"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
</div>
<div class="form-row">
    <label for="singleprojecttracker">
        <span><?php echo __('Single project tracker mode'); ?></span>
        <?php echo config_explanation(
            __('In single project tracker mode, Pachno will display the homepage for the first project as the main page instead of the regular index page') .
            "<br>" .
            ((count(\pachno\core\entities\Project::getAll()) > 1) ?
                '<br><b class="more_than_one_project_warning">'.
                __('More than one project exists. When in "single project" mode, accessing other projects than the first will become harder.') .
                '</b>'
                : ''
            )
        ); ?>
    </label>
    <input type="radio" name="<?php echo \pachno\core\framework\Settings::SETTING_IS_SINGLE_PROJECT_TRACKER; ?>" class="fancy-checkbox" <?php if ($access_level != \pachno\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?php echo \pachno\core\framework\Settings::SETTING_IS_SINGLE_PROJECT_TRACKER; ?>_yes" value=1<?php if (\pachno\core\framework\Settings::isSingleProjectTracker()): ?> checked<?php endif; ?>><label for="<?php echo \pachno\core\framework\Settings::SETTING_IS_SINGLE_PROJECT_TRACKER; ?>_yes"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
    <input type="radio" name="<?php echo \pachno\core\framework\Settings::SETTING_IS_SINGLE_PROJECT_TRACKER; ?>" class="fancy-checkbox" <?php if ($access_level != \pachno\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?php echo \pachno\core\framework\Settings::SETTING_IS_SINGLE_PROJECT_TRACKER; ?>_no" value=0<?php if (!\pachno\core\framework\Settings::isSingleProjectTracker()): ?> checked<?php endif; ?>><label for="<?php echo \pachno\core\framework\Settings::SETTING_IS_SINGLE_PROJECT_TRACKER; ?>_no"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
</div>
<div class="form-row">
    <label for="showprojectsoverview">
        <span><?php echo __('Show project list on frontpage'); ?></span>
        <?php echo config_explanation(
            __('Whether the project overview list should appear on the frontpage or not')
        ); ?>
    </label>
    <input type="radio" name="<?php echo \pachno\core\framework\Settings::SETTING_SHOW_PROJECTS_OVERVIEW; ?>" class="fancy-checkbox" <?php if ($access_level != \pachno\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?php echo \pachno\core\framework\Settings::SETTING_SHOW_PROJECTS_OVERVIEW; ?>_yes" value=1<?php if (\pachno\core\framework\Settings::isFrontpageProjectListVisible()): ?> checked<?php endif; ?>><label for="<?php echo \pachno\core\framework\Settings::SETTING_SHOW_PROJECTS_OVERVIEW; ?>_yes"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('Yes'); ?></label>
    <input type="radio" name="<?php echo \pachno\core\framework\Settings::SETTING_SHOW_PROJECTS_OVERVIEW; ?>" class="fancy-checkbox" <?php if ($access_level != \pachno\core\framework\Settings::ACCESS_FULL): ?> disabled<?php endif; ?> id="<?php echo \pachno\core\framework\Settings::SETTING_SHOW_PROJECTS_OVERVIEW; ?>_no" value=0<?php if (!\pachno\core\framework\Settings::isFrontpageProjectListVisible()): ?> checked<?php endif; ?>><label for="<?php echo \pachno\core\framework\Settings::SETTING_SHOW_PROJECTS_OVERVIEW; ?>_no"><?php echo fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far') . __('No'); ?></label>
</div>
