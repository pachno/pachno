<?php $pachno_response->setTitle(__('Configure issue types')); ?>
<div class="content-with-sidebar">
    <?php include_component('leftmenu', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_ISSUETYPE_SCHEMES]); ?>
    <div class="configuration-container">
        <div class="configuration-content">
            <h1><?php echo __('Configure issue type schemes'); ?></h1>
            <div class="helper-text">
                <p><?php echo __('Issue fields and available issue types is determined by a projects issue type scheme. You can read more about how issue types and schemes in Pachno works and is set up in the %online_documentation', array('%online_documentation' => link_tag('https://projects.pachno.com/pachno/docs/IssuetypeScheme', __('online documentation')))); ?></p>
            </div>
            <h3>
                <span><?php echo __('Existing issue type schemes'); ?></span>
                <button class="button" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'edit_issuetype_scheme']); ?>');"><?= __('Create scheme'); ?></button>
            </h3>
            <div class="flexible-table" id="issuetype_schemes_list">
                <div class="row header">
                    <div class="column header name-container"><?= __('Issue type scheme name'); ?></div>
                    <div class="column header"></div>
                    <div class="column header actions"></div>
                </div>
                <?php foreach ($issue_type_schemes as $scheme): ?>
                    <?php include_component('issuetypescheme', ['scheme' => $scheme]); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
