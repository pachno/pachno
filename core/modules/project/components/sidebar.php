<?php

    use pachno\core\entities\Project;
    use pachno\core\entities\User;
    use pachno\core\framework\Context;
    use pachno\core\framework\Event;
    use pachno\core\framework\Response;

    /**
     * @var Response $pachno_response
     * @var User $pachno_user
     * @var string $selected_tab
     */

    $selected_project = Context::getCurrentProject();

?>
<nav class="project-context sidebar <?= (isset($collapsed) && $collapsed) ? 'collapsed' : ''; ?> <?= (isset($fixed) && $fixed) ? 'fixed' : ''; ?>" id="project-menu" data-project-id="<?= (Context::isProjectContext()) ? Context::getCurrentProject()->getId() : ''; ?>">
    <div class="list-mode">
        <?php if ($pachno_response->getPage() == 'project_settings'): ?>
            <div id="project_config_menu" class="tab-switcher">
                <a href="<?= make_url('project_dashboard', ['project_key' => $selected_project->getKey()]); ?>" class="list-item">
                    <?= fa_image_tag('arrow-left', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Back'); ?></span>
                </a>
                <span class="list-item separator"></span>
                <a href="javascript:void(0);" data-tab-target="information" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'info') echo 'selected'; ?>">
                    <?= fa_image_tag('edit', ['class' => 'icon'], 'far'); ?>
                    <span class="name"><?= __('Details'); ?></span>
                </a>
                <a href="javascript:void(0);" data-tab-target="settings" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'settings') echo 'selected'; ?>">
                    <?= fa_image_tag('list-alt', ['class' => 'icon'], 'far'); ?>
                    <span class="name"><?= __('Settings'); ?></span>
                </a>
                <a href="javascript:void(0);" data-tab-target="developers" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'developers') echo 'selected'; ?>">
                    <?= fa_image_tag('users', ['class' => 'icon']); ?>
                    <span class="name"><?= __('People and access'); ?></span>
                </a>
                <a href="javascript:void(0);" data-tab-target="permissions" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'permissions') echo 'selected'; ?>">
                    <?= fa_image_tag('user-shield', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Roles and permissions'); ?></span>
                </a>
                <div class="list-item separator"></div>
                <a href="javascript:void(0);" data-tab-target="client" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'client') echo 'selected'; ?>">
                    <?= fa_image_tag('user-tie', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Client'); ?></span>
                </a>
                <div class="list-item separator"></div>
                <a href="javascript:void(0);" data-tab-target="hierarchy" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'hierarchy') echo 'selected'; ?>">
                    <?= fa_image_tag('boxes', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Editions and components'); ?></span>
                </a>
                <a href="javascript:void(0);" data-tab-target="issues_and_workflow" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'issues_and_workflow') echo 'selected'; ?>">
                    <?= fa_image_tag('edit', ['class' => 'icon'], 'far'); ?>
                    <span class="name"><?= __('Issues and workflow'); ?></span>
                </a>
                <a href="javascript:void(0);" data-tab-target="links" class="tab-switcher-trigger list-item <?php if ($selected_tab == 'links') echo 'selected'; ?>">
                    <?= fa_image_tag('link', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Links'); ?></span>
                </a>
                <div class="list-item separator"></div>
                <?php Event::createNew('core', 'config_project_tabs_other')->trigger(array('selected_tab' => $selected_tab)); ?>
                <div class="list-item separator"></div>
                <a href="javascript:void(0);" data-tab-target="faq" class="tab-switcher-trigger list-item help <?php if ($selected_tab == 'faq') echo 'selected'; ?>">
                    <?= fa_image_tag('question-circle', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Help / FAQ'); ?></span>
                </a>
            </div>
        <?php else: ?>
            <?php include_component('project/sidebarlinks', ['project' => Context::getCurrentProject()]); ?>
        <?php endif; ?>
    </div>
    <?php if (!$pachno_user->isGuest()): ?>
        <?php include_component('main/onboarding_invite'); ?>
    <?php endif; ?>
    <div class="collapser list-mode">
        <a class="list-item" href="javascript:void(0);">
            <span class="icon"><?= fa_image_tag('angle-double-left'); ?></span>
            <span class="name"><?= __('Toggle sidebar'); ?></span>
        </a>
    </div>
</nav>
