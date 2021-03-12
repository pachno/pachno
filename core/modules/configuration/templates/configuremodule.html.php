<?php

    /**
     * @var \pachno\core\framework\Response $pachno_response
     * @var \pachno\core\entities\Module $module
     * @var string $module_message
     * @var string $module_error
     * @var string[] $module_error_details
     * @var int $access_level
     */

    $pachno_response->setTitle(__('Configure modules'));

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_MODULES]); ?>
    <div class="configuration-container" id="config_modules">
        <?php if ($module_error !== null): ?>
            <div class="redbox" style="margin: 5px 0px 5px 0px;" id="module_error">
                <div class="header"><?php echo $module_error; ?></div>
                <div class="content"><b><?php echo __('Error details:'); ?></b><br>
                    <?php if ($module_error_details !== null): ?>
                        <?php if (is_array($module_error_details)): ?>
                            <?php foreach ($module_error_details as $detail): ?>
                                <?php echo $detail; ?><br>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php echo $module_error_details; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($module_message !== null): ?>
            <div class="greenbox" style="margin: 5px 0px 5px 0px;" id="module_message">
                <?php echo $module_message; ?>
            </div>
        <?php endif; ?>
        <?php include_component($module->getName() . '/settings', array('access_level' => $access_level, 'module' => $module)); ?>
    </div>
</div>
