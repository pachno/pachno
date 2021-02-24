<?php

    use pachno\core\entities\Article;

    /**
     * @var Article $category
     * @var Article $article
     * @var \pachno\core\entities\Article[] $children
     * @var int[] $parents
     */

?>
<div class="submenu" id="article-<?= $category->getId(); ?>-children-container">
    <?php foreach ($children as $child_article): ?>
        <?php include_component('publish/editcategorysidebarlink', array('parents' => $parents, 'article' => $article, 'category' => $child_article)); ?>
    <?php endforeach; ?>
    <?php include_component('publish/editcategorysidebaraddcategory', ['article' => $category, 'project' => $category->getProject()]); ?>
</div>
