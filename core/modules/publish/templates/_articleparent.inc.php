<?php

    use pachno\core\entities\Article;

    /**
     * @var Article $article
     */

?>
<?php if ($article->getParentArticle() instanceof Article): ?>
    <?php include_component('publish/articleparent', ['article' => $article->getParentArticle()]); ?>
    <span class="separator"><?= fa_image_tag('chevron-right'); ?></span>
<?php endif; ?>
<a href="<?= $article->getLink(); ?>" class="article-name"><?= $article->getName(); ?></a>
