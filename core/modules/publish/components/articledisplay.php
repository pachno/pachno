<?php

    use pachno\core\entities\Article;
    use pachno\core\framework\Context;

    /**
     * @var Article $article
     */

?>
<?php if ($show_title): ?>
    <?php include_component('publish/header', array('article_name' => $article->getName(), 'article' => $article, 'show_actions' => $show_actions, 'mode' => $mode, 'embedded' => $embedded)); ?>
<?php endif; ?>
<?php if ($show_article): ?>
    <div class="article syntax_<?php echo \pachno\core\framework\Settings::getSyntaxClass($article->getContentSyntax()); ?>">
        <div class="content"><?php echo $article->getParsedContent(['embedded' => $embedded, 'article' => $article]); ?></div>
    </div>
<?php endif; ?>
<?php if (!$embedded && $show_article && !$article->isCategory() && !$article->isMainPage() && count($article->getCategories()) > 0): ?>
    <div id="article_categories">
        <h4>
            <span class="name"><?php echo __('Categories'); ?></span>
        </h4>
        <?php $category_links = array(); ?>
        <?php foreach ($article->getCategories() as $categoryLink): ?>
            <a href="<?= $categoryLink->getCategory()->getLink(); ?>" class="card-badge">
                <?= fa_image_tag('layer-group', ['class' => 'icon']); ?>
                <span><?= $categoryLink->getCategory()->getName(); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
