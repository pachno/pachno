<?php
    $pachno_response->setTitle(__('Configure data types'));
    //$pachno_response->addStylesheet(make_url('asset_css_unthemed', array('css' => 'spectrum.css')));
?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_ISSUEFIELDS]); ?>
    <div class="configuration-container" id="config_issuefields">
        <div class="configuration-content centered">
            <h1><?php echo __('Configure issue fields'); ?></h1>
            <div class="helper-text centered">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_configuration_issue_fields_icon.png', [], true); ?></div>
                <span class="description">
                    <?php echo __('Add, remove and edit available issue fields and their valid values. Field visibility (such as in the issue view or during reporting) is decided by the %issuetype_scheme in use by the project.', array('%issuetype_scheme' => link_tag(make_url('configure_issuetypes_schemes'), __('Issuetype scheme')))); ?>
                </span>
            </div>
            <div class="configurable-components-container" id="issue-fields-configuration-container">
                <div class="configurable-components-list-container">
                    <h3><?php echo __('Issue fields'); ?></h3>
                    <div id="custom-types-list" class="configurable-components-list">
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
                        <a class="configurable-component" href="javascript:void(0);" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_issuefield']); ?>');">
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
