<?php

    use pachno\core\entities\Article;

    /**
     * @var Article $article
     */

    $include_link = $include_link ?? true;
?>
<?php if ($article->getParentArticle() instanceof Article): ?>
    <?php include_component('publish/articleparent', ['article' => $article->getParentArticle(), 'include_link' => $include_link]); ?>
    <span class="separator"><?= fa_image_tag('chevron-right'); ?></span>
<?php endif; ?>
<?php if ($include_link): ?>
    <a href="<?= $article->getLink(); ?>" class="article-name">
        <?php if ($article->isCategory()) echo fa_image_tag('layer-group', ['class' => 'icon']); ?>
        <span><?= $article->getName(); ?></span>
    </a>
<?php else: ?>
    <span class="article-name"><?= $article->getName(); ?></span>
<?php endif; ?>
