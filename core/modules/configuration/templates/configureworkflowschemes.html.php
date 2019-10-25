<?php

    /**
     * @var \pachno\core\entities\WorkflowScheme[] $schemes
     * @var \pachno\core\framework\Response $pachno_response
     */

    $pachno_response->setTitle(__('Configure workflow schemes'));

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_WORKFLOW_SCHEMES]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
            <h1><?php echo __('Configure workflow schemes'); ?></h1>
            <div class="helper-text centered">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_configuration_workflow_schemes_icon.png', [], true); ?></div>
                <span class="description">
                    <?php echo __('Workflow schemes links issue types to workflows. You can read more about how workflow schemes work and are set up in %link_to_wiki_workflow.', array('%link_to_wiki_workflow' => link_tag(\pachno\core\modules\publish\Publish::getArticleLink('Workflow'), 'Workflow'))); ?>
                </span>
            </div>
            <h3>
                <span><?php echo __('Existing workflow schemes'); ?></span>
                <button class="button" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_workflow_scheme']); ?>');"><?= __('Create scheme'); ?></button>
            </h3>
            <div class="flexible-table" id="workflow-schemes-list">
                <div class="row header">
                    <div class="column header name-container"><?= __('Workflow scheme name'); ?></div>
                    <div class="column header"></div>
                    <div class="column header actions"></div>
                </div>
                <?php foreach ($schemes as $scheme): ?>
                    <?php include_component('configuration/workflowscheme', array('scheme' => $scheme)); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
