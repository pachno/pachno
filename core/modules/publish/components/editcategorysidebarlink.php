<?php

    use pachno\core\entities\Article;

    /**
     * @var Article $category
     * @var Article $article
     * @var \pachno\core\entities\ArticleCategoryLink[] $children
     * @var int[] $parents
     * @var bool $is_parent
     */

?>
<input class="fancy-checkbox" type="checkbox" name="categories[<?= $category->getID(); ?>]" value="<?= $category->getID(); ?>" id="article_category_checkbox_<?= $category->getID(); ?>" <?php if (isset($article) && $article->hasCategory($category)) echo 'checked'; ?> data-category-id="<?= $category->getID(); ?>">
<label data-category-id="<?= $category->getID(); ?>" class="list-item expandable <?php echo ($is_parent && count($children)) ? ' expanded' : ' '; ?> multiline" for="article_category_checkbox_<?= $category->getID(); ?>">
    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
    <span class="name">
        <span class="title"><?= $category->getName(); ?></span>
    </span>
    <?= fa_image_tag('angle-down', ['class' => 'expander dynamic_menu_link']); ?>
</label>
<?php include_component('publish/editcategorysidebarlinkchildren', [
    'category' => $category,
    'article' => (isset($article)) ? $article : null,
    'is_selected' => false,
    'is_parent' => $is_parent,
    'parents' => $parents,
    'loaded' => true,
    'children' => $category->getChildren()
]); ?>
