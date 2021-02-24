<?php

    use pachno\core\entities\Article;

    /**
     * @var Article $main_article
     * @var Article $article
     * @var Article[] $children
     * @var int[] $parents
     * @var bool $has_children
     * @var bool $is_parent
     * @var bool $is_selected
     */

    $new_article_url = ($main_article->getProject() instanceof \pachno\core\entities\Project) ? make_url('publish_project_article_edit', ['article_id' => 0, 'project_key' => $main_article->getProject()->getKey()]) . '?parent_article_id=' . $main_article->getID() : make_url('publish_article_edit', ['article_id' => 0]) . '?parent_article_id=' . $main_article->getID();

?>
<div data-article-id="<?= $main_article->getID(); ?>" class="list-item <?php if ($main_article->isCategory() || $has_children) echo ' expandable'; ?> <?php if ($is_parent && !$is_selected) echo ($main_article->isCategory() || $has_children) ? ' expanded' : ' selected'; ?> <?php if ($is_selected) echo ($main_article->isCategory() || $has_children) ? ' expanded selected' : ' selected'; ?> <?php if ($main_article->isCategory()) echo 'multiline'; ?>">
    <?php if ($main_article->isCategory()): ?>
        <?= fa_image_tag('layer-group', ['class' => 'icon']); ?>
        <a class="name" href="<?= $main_article->getLink(); ?>">
            <span class="title"><?= $main_article->getName(); ?></span>
        </a>
        <?= fa_image_tag('angle-down', ['class' => 'expander dynamic_menu_link']); ?>
    <?php else: ?>
        <?php if ($has_children): ?>
            <?= fa_image_tag('book', ['class' => 'icon']); ?>
        <?php else: ?>
            <?= ($main_article->isMainPage()) ? fa_image_tag('file-invoice', ['class' => 'icon']) : fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
        <?php endif; ?>
        <a class="name" href="<?= $main_article->getLink(); ?>"><?= ($main_article->isMainPage()) ? __('Overview') : $main_article->getName(); ?></a>
        <?php if (!$main_article->isMainPage()): ?>
            <a href="<?= $new_article_url; ?>" class="button secondary icon new-page-button">
                <?= fa_image_tag('plus'); ?>
            </a>
        <?php endif; ?>
        <?php if ($is_parent || $has_children): ?>
            <?= fa_image_tag('angle-down', ['class' => 'expander dynamic_menu_link']); ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php if ($main_article->isCategory() || $has_children): ?>
    <?php include_component('publish/manualsidebarlinkchildren', compact('main_article', 'parents', 'article', 'is_selected', 'is_parent', 'has_children', 'children')); ?>
<?php endif; ?>
