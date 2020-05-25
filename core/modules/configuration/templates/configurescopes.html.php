<?php

    /**
     * @var \pachno\core\framework\Response $pachno_response
     * @var \pachno\core\helpers\Pagination $pagination
     */

    $pachno_response->setTitle(__('Configure scopes'));

?>
<div class="content-with-sidebar">
    <?php include_component('configuration/sidebar', ['selected_section' => \pachno\core\framework\Settings::CONFIGURATION_SECTION_SCOPES]); ?>
    <div class="configuration-container" id="configure-scopes-container">
        <div class="configuration-content">
            <h1><?php echo __('Configure scopes'); ?></h1>
            <div class="helper-text centered">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_configuration_scopes_icon.png', [], true); ?></div>
                <span class="description">
                    <?php echo __('Pachno scopes are self-contained environments within the same Pachno installation, set up to be initialized when Pachno is accessed via different hostnames. Read more about scopes in %ConfigureScopes.', ['%ConfigureScopes' => link_tag(\pachno\core\modules\publish\Publish::getArticleLink('ConfigureScopes'), 'ConfigureScopes')]); ?>
                </span>
            </div>
            <?php if (isset($scope_deleted)): ?>
                <div class="greenbox" style="margin: 0 0 5px 0; font-size: 14px;">
                    <?php echo __('The scope was deleted'); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($scope_hostname_error)): ?>
                <div class="redbox" style="margin: 0 0 5px 0; font-size: 14px;">
                    <?php echo __('The hostname must be unique and cannot be blank'); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($scope_name_error)): ?>
                <div class="redbox" style="margin: 0 0 5px 0; font-size: 14px;">
                    <?php echo __('The scope name must be unique and cannot be blank'); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($scope_saved)): ?>
                <div class="greenbox" style="margin: 0 0 5px 0; font-size: 14px;">
                    <?php echo __('The settings were saved successfully'); ?>
                </div>
            <?php endif; ?>
            <h2>
                <span class="name"><?php echo __('Scopes available on this installation'); ?></span>
                <button class="button" onclick="Pachno.Main.Helpers.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'scope_config']); ?>');"><?= __('Create scope'); ?></button>
            </h2>
            <?php include_component('main/pagination', ['pagination' => $pagination]); ?>
            <div id="scopes_list" class="flexible-table">
                <div class="row header">
                    <div class="column header name-container"><?= __('Name'); ?></div>
                    <div class="column header"><?= __('Hostname(s)'); ?></div>
                    <div class="column header numeric"><?= __('Project(s)'); ?></div>
                    <div class="column header numeric"><?= __('Issue(s)'); ?></div>
                    <div class="column header actions"></div>
                </div>
                <div class="body">
                    <?php foreach ($scopes as $scope): ?>
                        <?php include_component('configuration/scopebox', array('scope' => $scope)); ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php include_component('main/pagination', ['pagination' => $pagination]); ?>
        </div>
    </div>
</div>
