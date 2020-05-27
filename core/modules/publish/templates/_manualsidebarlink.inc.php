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

?>
<a href="<?= $main_article->getLink(); ?>" data-article-id="<?= $main_article->getID(); ?>" class="list-item <?php if ($has_children) echo ' expandable'; ?> <?php if ($is_parent && !$is_selected) echo ($has_children) ? ' expanded' : ' selected'; ?> <?php if ($is_selected) echo ($has_children) ? ' expanded selected' : ' selected'; ?> <?php if ($main_article->isCategory()) echo 'multiline'; ?>">
    <?php if ($main_article->isCategory()): ?>
        <?= fa_image_tag('layer-group', ['class' => 'icon']); ?>
        <span class="name">
            <span class="title"><?= $main_article->getName(); ?></span>
        </span>
        <?php if ($has_children): ?>
            <?= fa_image_tag('angle-down', ['class' => 'expander dynamic_menu_link']); ?>
        <?php endif; ?>
    <?php else: ?>
        <?php if ($has_children): ?>
            <?= fa_image_tag('book', ['class' => 'icon']); ?>
        <?php else: ?>
            <?= ($main_article->getName() == 'Main Page') ? fa_image_tag('file-invoice', ['class' => 'icon']) : fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
        <?php endif; ?>
        <span class="name"><?= ($main_article->getName() == 'Main Page') ? __('Overview') : $main_article->getName(); ?></span>
        <?php if ($is_parent || $has_children): ?>
            <?= fa_image_tag('angle-down', ['class' => 'expander dynamic_menu_link']); ?>
        <?php endif; ?>
    <?php endif; ?>
</a>
<?php if ($has_children): ?>
    <?php include_component('publish/manualsidebarlinkchildren', compact('main_article', 'parents', 'article', 'is_selected', 'is_parent', 'has_children', 'children')); ?>
<?php endif; ?>
