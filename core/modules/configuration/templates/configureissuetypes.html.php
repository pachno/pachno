<?php $pachno_response->setTitle(__('Configure issue types')); ?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_ISSUETYPES]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
            <h1><?php echo __('Configure issue types'); ?></h1>
            <div class="helper-text centered">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_configuration_issue_types_icon.png', [], true); ?></div>
                <span class="description">
                    <?php echo __('Issue types let you categorize requests into different groups, and specify additional fields per issue type. Read more about how issue types works and is set up in the %online_documentation', array('%online_documentation' => link_tag('https://projects.pachno.com/pachno/docs/IssuetypeScheme', __('online documentation')))); ?>
                </span>
            </div>
            <h3>
                <span><?php echo __('Existing issue types'); ?></span>
                <button class="button" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_issuetype']); ?>');"><?= __('Create issue type'); ?></button>
            </h3>
            <div id="issuetypes_list" class="flexible-table">
                <div class="row header">
                    <div class="column header name-container"><?= __('Issue type'); ?></div>
                    <div class="column header actions"></div>
                </div>
                <?php foreach ($issue_types as $type): ?>
                    <?php include_component('issuetype', ['type' => $type]); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
