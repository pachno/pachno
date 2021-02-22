<?php

    use pachno\core\framework\Context;

    /**
     * @var \pachno\core\framework\Routing $pachno_routing
     * @var \pachno\core\entities\Branch[] $branches
     */

    $selected = $pachno_routing->getCurrentRoute()->getModuleName() == 'livelink';

?>
<a href="javascript:void(0);<?php // echo make_url('livelink_project_commits', ['project_key' => Context::getCurrentProject()->getKey()]); ?>" class="list-item disabled expandable <?php if ($selected) echo 'expanded'; ?> tooltip-container">
    <?= fa_image_tag('code', ['class' => 'icon']); ?>
    <span class="name"><?= __('Code'); ?></span>
    <?php // echo fa_image_tag('angle-down', ['class' => 'expander']); ?>
    <span class="tooltip from-above">
        <?= __('This functionality is not available in the alpha'); ?>
    </span>
</a>
<div class="submenu list-mode">
    <a href="<?= make_url('livelink_project_commits', ['project_key' => $selected_project->getKey()]); ?>" class="list-item">
        <?= fa_image_tag('code-branch', ['class' => 'icon']); ?>
        <span class="name"><?= __('Commits'); ?></span>
    </a>
</div>
