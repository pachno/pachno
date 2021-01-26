<?php

    use pachno\core\framework\Context;

    /**
     * @var \pachno\core\framework\Routing $pachno_routing
     * @var \pachno\core\entities\Branch[] $branches
     */

    $selected = $pachno_routing->getCurrentRoute()->getModuleName() == 'livelink';

?>
<a href="<?= make_url('livelink_project_commits', ['project_key' => Context::getCurrentProject()->getKey()]); ?>" class="list-item expandable <?php if ($selected) echo 'expanded'; ?>">
    <?= fa_image_tag('code', ['class' => 'icon']); ?>
    <span class="name"><?= __('Code'); ?></span>
    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
</a>
<div class="submenu list-mode">
    <a href="<?= make_url('livelink_project_commits', ['project_key' => $selected_project->getKey()]); ?>" class="list-item">
        <?= fa_image_tag('code-branch', ['class' => 'icon']); ?>
        <span class="name"><?= __('Commits'); ?></span>
    </a>
</div>
