<?php

use pachno\core\framework;

/**
 * @var \pachno\core\entities\User $pachno_user
 */
?>
<div class="name-container shaded">
    <div class="form-container">
        <form id="documentation-search">
            <div class="form-row">
                <div class="search-container">
                    <label for="documentation-search-input" class="icon"><?= fa_image_tag('search'); ?></label>
                    <input id="documentation-search-input" type="search" name="value" placeholder="<?= __('Find project') ;?>">
                </div>
            </div>
        </form>
    </div>
</div>
<div class="spacer"></div>
<?php if ($pachno_user->isAuthenticated()): ?>
    <div class="spacer"></div>
    <div class="action-container">
        <?php if ($pachno_user->canAccessConfigurationPage(framework\Settings::CONFIGURATION_SECTION_PROJECTS)): ?>
            <?= link_tag(make_url('configure_projects'), fa_image_tag('cog'), ['class' => 'button icon secondary']); ?>
        <?php endif; ?>
        <?php if (framework\Context::getScope()->hasProjectsAvailable()): ?>
            <button class="button secondary highlight project-quick-edit" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', ['key' => 'project_config']); ?>');"><?= fa_image_tag('plus-square'); ?><span><?= __('Create a project'); ?></span></button>
        <?php endif; ?>
    </div>
<?php endif; ?>
