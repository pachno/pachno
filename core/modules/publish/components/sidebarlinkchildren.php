<?php

    use pachno\core\entities\Article;

    /**
     * @var Article $main_article
     * @var Article $article
     * @var Article[] $children
     * @var int[] $parents
     */

?>
<div id="article-<?= $main_article->getId(); ?>-children-container" class="submenu dynamic_menu populate-once" data-menu-url="<?= make_url('publish_api_article_menu', ['article_id' => $main_article->getID(), 'selected_article_id' => ($article instanceof Article) ? $article->getID() : 0]); ?>" <?php if (isset($loaded)) echo ' data-is-loaded=1'; ?>>
    <?php if (!$is_selected && !isset($loaded) && !count($children)): ?>
        <span class="list-item">
            <span class="icon">
                <?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?>
            </span>
            <span class="name"></span>
        </span>
    <?php else: ?>
        <?php foreach ($children as $child_article): ?>
            <?php include_component('publish/sidebarlink', array('parents' => $parents, 'article' => $article, 'main_article' => $child_article)); ?>
        <?php endforeach; ?>
        <?php if ($main_article->isCategory() && $pachno_user->canCreateCategoriesInProject(\pachno\core\framework\Context::getCurrentProject())): ?>
            <?php include_component('publish/sidebaraddcategory', ['article' => $main_article, 'project' => $main_article->getProject()]); ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
