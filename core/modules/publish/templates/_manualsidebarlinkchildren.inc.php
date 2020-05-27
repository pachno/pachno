<?php

    use pachno\core\entities\Article;

    /**
     * @var Article $main_article
     * @var Article $article
     * @var Article[] $children
     * @var int[] $parents
     */

?>
<div class="submenu dynamic_menu populate-once" data-menu-url="<?= make_url('publish_api_article_menu', ['article_id' => $main_article->getID(), 'selected_article_id' => $article->getID()]); ?>" <?php if (isset($loaded)) echo ' data-is-loaded=1'; ?>>
    <?php if (!isset($loaded) && !count($children)): ?>
        <span class="list-item">
            <span class="icon">
                <?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?>
            </span>
            <span class="name"></span>
        </span>
    <?php endif; ?>
    <?php foreach ($children as $child_article): ?>
        <?php include_component('publish/manualsidebarlink', array('parents' => $parents, 'article' => $article, 'main_article' => $child_article)); ?>
    <?php endforeach; ?>
</div>
