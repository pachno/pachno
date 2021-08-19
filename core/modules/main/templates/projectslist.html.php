<?php 

    $pachno_response->setTitle(__('Frontpage'));

/**
 * @var \pachno\core\entities\User $pachno_user
 * @var \pachno\core\helpers\Pagination $active_pagination
 * @var \pachno\core\helpers\Pagination $archived_pagination
 * @var \pachno\core\entities\Project[] $active_projects
 * @var \pachno\core\entities\Project[] $archived_projects
 * @var int $active_project_count
 * @var int $archived_project_count
 * @var bool $show_project_config_link
 * @var bool $show_project_list
 */

?>
<div class="content-with-sidebar">
    <nav class="sidebar">
        <div id="projects_list_tabs" class="list-mode">
            <div class="list-item filter-container">
                <label for="project-search-input" class="icon"><?= fa_image_tag('search'); ?></label>
                <input id="project-search-input" type="search" name="value" placeholder="<?= __('Find projects') ;?>">
            </div>
            <div class="list-item separator"></div>
            <a class="list-item tab selected" data-project-category="active" id="tab_active" href="javascript:void(0);">
                <?= fa_image_tag('boxes', ['class' => 'icon']); ?>
                <span class="name"><?= ($pachno_user->isGuest()) ? __('Projects') : __('Active projects'); ?></span>
                <?= fa_image_tag('spinner', ['style' => 'display: none;', 'id' => 'project_list_tab_active_indicator', 'class' => 'icon fa-spin']); ?>
            </a>
            <?php if (!$pachno_user->isGuest()): ?>
                <a class="list-item tab" id="tab_archived" data-project-category="archived" href="javascript:void(0);">
                    <?= fa_image_tag('archive', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Archived projects'); ?></span>
                    <?= fa_image_tag('spinner', ['style' => 'display: none;', 'id' => 'project_list_tab_archived_indicator', 'class' => 'icon fa-spin']); ?>
                </a>
                <?php /* if ($pachno_user->isAuthenticated()): ?>
                    <div class="list-item separator"></div>
                    <div class="button-container">
                        <?= link_tag(make_url('configure_projects'), fa_image_tag('cog'), ['class' => 'button icon secondary']); ?>
                        <?php if ($list_mode !== 'client' && $pachno_user->canAccessConfigurationPage(framework\Settings::CONFIGURATION_SECTION_PROJECTS) && framework\Context::getScope()->hasProjectsAvailable()): ?>
                            <button class="button primary" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', $partial_options); ?>');"><?= fa_image_tag('plus-square'); ?><span><?= __('Create a project'); ?></span></button>
                        <?php endif; ?>
                    </div>
                <?php endif; */ ?>
            <?php endif; ?>
        </div>
        <?php \pachno\core\framework\Event::createNew('core', 'index_left')->trigger(); ?>
        <?php if (!$pachno_user->isGuest()): ?>
            <?php include_component('main/onboarding_invite'); ?>
        <?php endif; ?>
    </nav>
    <div class="main_area frontpage">
        <?php \pachno\core\framework\Event::createNew('core', 'index_right_top')->trigger(); ?>
        <?php include_component('main/projectlist', ['list_mode' => 'all', 'admin' => false]); ?>
        <?php \pachno\core\framework\Event::createNew('core', 'index_right_bottom')->trigger(); ?>
    </div>
</div>
