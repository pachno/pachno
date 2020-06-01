<?php

    /** @var \pachno\core\entities\IssuetypeScheme[] $issue_type_schemes */

    $pachno_response->setTitle(__('Configure issue types'));

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_ISSUETYPE_SCHEMES]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
            <h1><?php echo __('Configure issue type schemes'); ?></h1>
            <div class="helper-text centered">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_configuration_issue_type_schemes_icon.png', [], true); ?></div>
                <span class="description">
                    <?php echo __('Issue fields and available issue types for a project is determined by its issue type scheme. Read more about how issue type schemes works and are configured in the %online_documentation', array('%online_documentation' => link_tag('https://projects.pachno.com/pachno/docs/IssuetypeScheme', __('online documentation')))); ?>
                </span>
            </div>
            <h3>
                <span><?php echo __('Existing issue type schemes'); ?></span>
                <button class="button" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_issuetype_scheme']); ?>');"><?= __('Create scheme'); ?></button>
            </h3>
            <div class="flexible-table" id="issuetype_schemes_list">
                <div class="row header">
                    <div class="column header name-container"><?= __('Issue type scheme name'); ?></div>
                    <div class="column header"></div>
                    <div class="column header actions"></div>
                </div>
                <?php foreach ($schemes as $scheme): ?>
                    <?php include_component('configuration/issuetypescheme', ['scheme' => $scheme]); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
