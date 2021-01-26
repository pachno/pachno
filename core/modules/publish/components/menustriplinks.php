<?php

    use pachno\core\entities;
    use pachno\core\framework\Context;
    use pachno\core\modules\publish\Publish;

    /**
     * @var Publish $publish
     * @var entities\Article $overview_article
     * @var entities\Article[] $top_level_categories
     * @var entities\Article[] $top_level_categories
     */

    $publish = \pachno\core\framework\Context::getModule('publish');

?>
<div class="list-item <?php if (Context::getRouting()->getCurrentRoute()->getModuleName() == 'publish'): ?>selected <?php endif; ?>">
    <?php if (!isset($wiki_url)): ?>
        <a href="<?= (isset($project_url)) ? $project_url : $url; ?>">
            <?= fa_image_tag('book', ['class' => 'icon']); ?>
            <span class="name"><?= $publish->getMenuTitle($project instanceof entities\Project); ?></span>
        </a>
    <?php else: ?>
        <a href="<?= $wiki_url; ?>" target="_blank">
            <span class="name"><?= $publish->getMenuTitle($project instanceof entities\Project); ?></span>
        </a>
    <?php endif; ?>
    <div class="dropper-container pop-out-expander">
        <?= fa_image_tag('angle-right', ['class' => 'dropper']); ?>
        <div class="dropdown-container interactive_filters_list list-mode from-left slide-out">
            <a class="list-item" href="javascript:void(0);">
                <span class="icon"><?= fa_image_tag('angle-double-left'); ?></span>
                <span class="name"><?= __('Back'); ?></span>
            </a>
            <a href="<?= $overview_article->getLink(); ?>" class="list-item">
                <?= fa_image_tag('file-invoice', ['class' => 'icon']); ?>
                <span class="name"><span class="title"><?= __('Overview'); ?></span></span>
            </a>
            <div class="header">
                <span class="name"><?= __('Categories'); ?></span>
            </div>
            <?php foreach ($top_level_categories as $article): ?>
                <a href="<?= $article->getLink(); ?>" class="list-item">
                    <?= fa_image_tag('layer-group', ['class' => 'icon']); ?>
                    <span class="name"><span class="title"><?= $article->getName(); ?></span></span>
                </a>
            <?php endforeach; ?>
            <div class="header">
                <span class="name"><?= __('Pages'); ?></span>
            </div>
            <?php $prev_has_children = false; ?>
            <?php foreach ($top_level_articles as $article): ?>
                <?php if ($prev_has_children && !$article->hasChildren()): ?>
                    <span class="list-item separator"></span>
                <?php endif; ?>
                <a href="<?= $article->getLink(); ?>" class="list-item">
                    <?php if ($article->hasChildren()): ?>
                        <?= fa_image_tag('book', ['class' => 'icon']); ?>
                    <?php else: ?>
                        <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                    <?php endif; ?>
                    <span class="name"><?= $article->getName(); ?></span>
                </a>
                <?php $prev_has_children = $article->hasChildren(); ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
