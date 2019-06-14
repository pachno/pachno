<?php

    /** @var \pachno\core\entities\Workflow[] $workflows */

    $pachno_response->setTitle(__('Configure workflows'));

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_WORKFLOW]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
            <h1><?php echo __('Configure workflows'); ?></h1>
            <div class="helper-text">
                <p>
                    <?php echo __('Workflow lets you define the lifecycle of an issue. You can define steps, transitions and more, that makes an issue move through its defined lifecycle.'); ?>
                    <?php echo __('You can read more about how the workflow in Pachno works and is set up in %link_to_wiki_workflow.', array('%link_to_wiki_workflow' => link_tag(make_url('publish_article', array('article_name' => 'Workflow')), 'Workflow'))); ?>
                </p>
            </div>
            <h3>
                <span><?php echo __('Existing workflows'); ?></span>
                <button class="button" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_workflow']); ?>');"><?= __('Create workflow'); ?></button>
            </h3>
            <div class="flexible-table" id="workflow-schemes-list">
                <div class="row header">
                    <div class="column header name-container"><?= __('Workflow'); ?></div>
                    <div class="column header grid"><?= __('Included statuses'); ?></div>
                    <div class="column header actions"></div>
                </div>
                <?php foreach ($workflows as $workflow): ?>
                    <?php include_component('configuration/workflow', array('workflow' => $workflow)); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
