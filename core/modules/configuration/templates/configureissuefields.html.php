<?php
    $pachno_response->setTitle(__('Configure data types'));
    $pachno_response->addStylesheet(make_url('asset_css_unthemed', array('css' => 'spectrum.css')));
?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_ISSUEFIELDS]); ?>
    <div class="configuration-container" id="config_issuefields">
        <div class="configuration-content centered">
            <h3><?php echo __('Configure issue fields'); ?></h3>
            <div class="content faded_out">
                <p><?php echo __('Edit built-in and custom issue fields and values here. Remember that the issue fields visibility (in the issue view or during reporting) is decided by the %issuetype_scheme in use by the project.', array('%issuetype_scheme' => link_tag(make_url('configure_issuetypes_schemes'), __('Issuetype scheme')))); ?></p>
            </div>
            <div style="display: none;">
                <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('configure_issuefields_add_customtype'); ?>" onsubmit="Pachno.Config.Issuefields.Custom.add('<?php echo make_url('configure_issuefields_add_customtype'); ?>');return false;" id="add_custom_type_form">
                    <div style="position: absolute; right: 15px; top: 15px;">
                        <input type="submit" value="<?php echo __('Add issue field'); ?>" style="font-weight: normal; font-size: 14px;" id="add_custom_type_button">
                        <?php echo image_tag('spinning_16.gif', array('style' => 'margin-right: 5px; display: none;', 'id' => 'add_custom_type_indicator')); ?>
                    </div>
                    <label for="new_custom_field_name" style="width: 150px; display: inline-block;"><?php echo __('Add new issue field'); ?></label>
                    <input type="text" name="name" id="new_custom_field_name" style="width: 250px;">
                    <br style="clear: both;">
                    <label for="new_custom_field_name" style="width: 150px; display: inline-block;"><?php echo __('Field type'); ?></label>
                    <select id="new_custom_field_type" name="field_type" style="width: 400px;">
                        <?php foreach (\pachno\core\entities\CustomDatatype::getFieldTypes() as $type => $description): ?>
                            <option value="<?php echo $type; ?>"><?php echo $description; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <br style="clear: both;">
                </form>
            </div>
            <div class="configurable-components-container" id="issue-fields-configuration-container">
                <div class="configurable-components-list-container">
                    <h5><?php echo __('Issue fields'); ?></h5>
                    <div id="custom_types_list" class="configurable-components-list">
                        <?php foreach ($builtin_types as $type_key => $type): ?>
                            <?php if ($type_key == 'activitytype') continue; ?>
                            <?php include_component('configuration/issuefield', compact('type_key', 'type')); ?>
                            <?php /*
                            <div class="greybox">
                                <button class="button" onclick="Pachno.Config.Issuefields.Options.show('<?php echo make_url('configure_issuefields_getoptions', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>');"" style="float: right; margin-left: 5px;"><?php echo __('Edit'); ?></button>
                                <?php echo image_tag('spinning_32.gif', array('style' => 'float: right; margin-left: 5px; display: none;', 'id' => $type_key . '_indicator')); ?>
                                <div class="header"><a href="javascript:void(0);" onclick="Pachno.Config.Issuefields.Options.show('<?php echo make_url('configure_issuefields_getoptions', array('type' => $type_key)); ?>', '<?php echo $type_key; ?>');"><?php echo $type['description']; ?></a>&nbsp;<span class="faded_out dark" style="font-weight: normal; font-size: 12px;"><?php echo $type['key']; ?></span></div>
                                <div class="content" id="<?php echo $type_key; ?>_content" style="display: none;"> </div>
                            </div>
                            */ ?>
                        <?php endforeach; ?>
                        <?php foreach ($custom_types as $type_key => $type): ?>
                            <?php include_component('configuration/issuefield', compact('type_key', 'type')); ?>
                            <?php //include_component('issuefields_customtype', array('type_key' => $type_key, 'type' => $type)); ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="configurable-components-list">
                        <a class="configurable-component" href="javascript:void(0);" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_issuefield']); ?>');">
                            <span class="row">
                                <span class="icon"><?= fa_image_tag('plus'); ?></span>
                                <span class="name">
                                    <span class="title"><?= __('Create a new field'); ?></span>
                                </span>
                            </span>
                        </a>
                    </div>
                </div>
                <div class="configurable-component-options" id="selected-issue-field-options"></div>
            </div>
        </div>
    </div>
</div>
<script>
    require(['domReady', 'pachno/index', 'jquery'], function (domReady, pachno_index_js, jQuery) {
        domReady(function () {
            jQuery('body').on('click', '.issue-field .open', function(event) {
                event.preventDefault();
                event.stopPropagation();

                const $item = jQuery(this).parents('.issue-field');
                pachno_index_js.Config.Issuefields.showOptions($item);
            });

        });
    });
</script>
