<?php $pachno_response->setTitle(__('Configure workflow schemes')); ?>
<div class="content-with-sidebar">
    <?php include_component('leftmenu', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_WORKFLOW]); ?>
    <div class="configuration-container">
        <?php include_component('configuration/workflowmenu', array('selected_tab' => 'schemes')); ?>
        <ul class="scheme_list workflow_list simple-list" id="workflow_schemes_list">
            <?php foreach ($schemes as $workflow_scheme): ?>
                <?php include_component('configuration/workflowscheme', array('scheme' => $workflow_scheme)); ?>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
