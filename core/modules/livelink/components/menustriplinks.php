<?php

    use pachno\core\framework\Context;

    /**
     * @var \pachno\core\framework\Routing $pachno_routing
     * @var \pachno\core\entities\Branch[] $branches
     */

    $selected = $pachno_routing->getCurrentRoute()->getModuleName() == 'livelink';
    $current_route = $pachno_routing->getCurrentRoute()->getName();

?>
<a href="<?= make_url('livelink_project_commits', ['project_key' => Context::getCurrentProject()->getKey()]); ?>" class="list-item <?php if ($selected) echo ' selected'; ?>">
    <?= fa_image_tag('code', ['class' => 'icon']); ?>
    <span class="name"><?= __('Code'); ?></span>
</a>
<?php /* if ($selected): ?>
    <div class="list-item expandable expanded">
        <?= fa_image_tag('code', ['class' => 'icon']); ?>
        <span class="name"><?= __('Code'); ?></span>
        <?php echo fa_image_tag('angle-down', ['class' => 'expander']); ?>
    </div>
    <div class="submenu list-mode">
        <a href="<?= make_url('livelink_project_commits', ['project_key' => Context::getCurrentProject()->getKey()]); ?>" class="list-item <?php if ($current_route === 'livelink_project_commits') echo 'selected'; ?>">
            <?= fa_image_tag('code-branch', ['class' => 'icon']); ?>
            <span class="name"><?= __('Commits'); ?></span>
        </a>
    </div>
<?php else: ?>
    <div class="list-item">
        <a href="<?= make_url('livelink_project_commits', ['project_key' => Context::getCurrentProject()->getKey()]); ?>">
            <?= fa_image_tag('code', ['class' => 'icon']); ?>
            <span class="name"><?= __('Code'); ?></span>
        </a>
        <div class="dropper-container pop-out-expander">
            <?= fa_image_tag('angle-right', ['class' => 'dropper']); ?>
            <div class="dropdown-container interactive_filters_list list-mode from-left slide-out">
                <a href="<?= make_url('livelink_project_commits', ['project_key' => Context::getCurrentProject()->getKey()]); ?>" class="list-item">
                    <?= fa_image_tag('code-branch', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Commits'); ?></span>
                </a>
            </div>
        </div>
    </div>
<?php endif; */ ?>
