<?php

    /**
     * @var \pachno\core\framework\Response $pachno_response
     * @var \pachno\core\helpers\Pagination $pagination
     * @var \pachno\core\entities\Scope[] $scopes
     * @var boolean $exclude_empty_projects
     * @var boolean $exclude_empty_issues
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
                <button class="button" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'scope_config']); ?>');"><?= __('Create scope'); ?></button>
            </h2>
            <div class="top-search-filters-container">
                <form method="post" action="<?= make_url('configure_scopes'); ?>" data-interactive-form data-update-container="#scopes_list" id="scopes_list_form">
                    <div class="search-and-filters-strip">
                        <div class="filters-strip">
                            <div class="filters">
                                <div class="fancy-dropdown-container">
                                    <div class="fancy-dropdown">
                                        <label><?= __('Filter'); ?></label>
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode from-left">
                                            <input type="checkbox" name="exclude_empty_projects" value="1" class="fancy-checkbox" id="scopes_exclude_empty_projects" <?php if ($exclude_empty_projects) echo ' checked'; ?>>
                                            <label for="scopes_exclude_empty_projects" class="list-item">
                                                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                <span class="name value"><?= __('Only show scopes with projects'); ?></span>
                                            </label>
                                            <input type="checkbox" name="exclude_empty_issues" value="1" class="fancy-checkbox" id="scopes_exclude_empty_issues" <?php if ($exclude_empty_issues) echo ' checked'; ?>>
                                            <label for="scopes_exclude_empty_issues" class="list-item">
                                                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                <span class="name value"><?= __('Only show scopes with issues'); ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div id="scopes_list">
                <?php include_component('configuration/scopelist', ['scopes' => $scopes, 'pagination' => $pagination]); ?>
            </div>
            <h2></h2>
        </div>
    </div>
</div>
