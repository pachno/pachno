<?php

    $pachno_response->setTitle(__('Configuration center'));
    
?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => '']); ?>
    <div class="dashboard layout_standard">
        <ul class="dashboard_column">
            <li class="dashboard_view_container">
                <div class="container_div">
                    <div class="dashboard_view_content">
                        <span><?php echo __('You currently have version %pachno_version of Pachno.', array('%pachno_version' => \pachno\core\framework\Settings::getVersion())); ?></span>
                        <div class="button-container">
                            <a class="button primary" id="update_button" href="javascript:void(0);" onclick="Pachno.Config.updateCheck('<?php echo make_url('configure_update_check'); ?>');"><?php echo __('Check for updates now'); ?></a>
                        </div>
                    </div>
                </div>
            </li>
            <?php if (count($outdated_modules) > 0): ?>
                <li class="dashboard_view_container">
                    <div class="container_div transparent">
                        <div class="dashboard_view_content">
                            <div class="header"><?php echo __('You have %count outdated modules. They have been disabled until you upgrade them, you can upgrade them from Module settings.', array('%count' => count($outdated_modules))); ?></div>
                        </div>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
