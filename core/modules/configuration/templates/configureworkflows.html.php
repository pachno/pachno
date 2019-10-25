<?php

    /** @var \pachno\core\entities\Workflow[] $workflows */

    $pachno_response->setTitle(__('Configure workflows'));

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_WORKFLOW]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
            <h1><?php echo __('Configure workflows'); ?></h1>
            <div class="helper-text centered">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_configuration_workflows_icon.png', [], true); ?></div>
                <span class="description">
                    <?php echo __('Workflows lets you define the lifecycle of an issue: steps, transitions, lifecycle events and more. Read more about how workflows are set up in %link_to_wiki_workflow.', array('%link_to_wiki_workflow' => link_tag(\pachno\core\modules\publish\Publish::getArticleLink('Workflow'), 'Workflow'))); ?>
                </span>
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
