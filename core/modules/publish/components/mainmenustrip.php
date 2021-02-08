<?php

use pachno\core\framework;

/**
 * @var \pachno\core\entities\User $pachno_user
 * @var \pachno\core\modules\publish\Publish $publish
 * @var \pachno\core\entities\Article[] $articles
 */
?>
<div class="dropper-container">
    <button class="secondary dropper">
        <?php if (framework\Context::isProjectContext()): ?>
            <span class="icon"><?php echo image_tag(framework\Context::getCurrentProject()->getIconName(), ['alt' => "[img]"], true); ?></span>
            <span class="name"><?= framework\Context::getCurrentProject()->getName(); ?></span>
        <?php else: ?>
            <?= fa_image_tag('atlas', ['class' => 'icon']); ?>
            <span class="name"><?= __('Site documentation'); ?></span>
        <?php endif; ?>
        <?= fa_image_tag('chevron-down', ['class' => 'icon toggler']); ?>
    </button>
    <div class="dropdown-container from-left">
        <div class="list-mode">
            <div class="header"><?= __('Project documentation'); ?></div>
            <?php foreach ($pachno_user->getAssociatedProjects() as $project): ?>
                <?php if (!isset($articles[$project->getID()])) continue; ?>
                <a class="list-item <?php if (framework\Context::getCurrentProject() instanceof \pachno\core\entities\Project && framework\Context::getCurrentProject()->getID() === $project->getID()) echo 'selected'; ?>" href="<?= $articles[$project->getID()]->getLink(); ?>">
                    <span class="icon"><?php echo image_tag($project->getIconName(), ['alt' => "[img]"], true); ?></span>
                    <span class="name"><?= $project->getName(); ?></span>
                </a>
            <?php endforeach; ?>
            <div class="list-item separator"></div>
            <a class="list-item <?php if (!framework\Context::getCurrentProject() instanceof \pachno\core\entities\Project) echo 'selected'; ?>" href="<?= $main_article->getLink(); ?>">
                <?php echo fa_image_tag('book', ['class' => 'icon']); ?>
                <span class="name"><?= __('Site documentation'); ?></span>
            </a>
        </div>
    </div>
</div>
<div class="spacer"></div>
<?php if ($pachno_user->isAuthenticated()): ?>
    <?php include_component('publish/headeractions'); ?>
<?php endif; ?>
