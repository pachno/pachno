<?php

    use pachno\core\entities\Article;

    /**
     * @var Article $article
     * @var Article[] $parent_articles
     * @var int[] $article_counts
     */

?>
<?php if ($article->getParentArticle() instanceof Article): ?>
    <a href="javascript:void(0);" onclick="$('#parent_article_id_input').setValue(0);$('#parent_selector_container').hide();$('#parent_move_message').show();" class="list-item multiline">
        <?= fa_image_tag('unlink', ['class' => 'icon']); ?>
        <span class="name">
            <span class="title"><?= __('Move to separate article'); ?></span>
            <span class="description"><?= __('Removes the link to any parent article'); ?></span>
        </span>
        <span class="icon">
            <?= fa_image_tag('file-export'); ?>
        </span>
    </a>
<?php endif; ?>
<?php if (count($parent_articles) && $article->getParentArticle() instanceof Article): ?>
    <span class="list-item separator"></span>
<?php endif; ?>
<?php foreach ($parent_articles as $parent_article): ?>
    <a href="javascript:void(0);" onclick="$('#parent_article_id_input').setValue('<?php echo $parent_article->getID(); ?>');$('#parent_selector_container').hide();$('#parent_move_message').show();" class="list-item <?php if ($parent_article->isCategory() || isset($article_counts[$parent_article->getID()])) echo 'multiline'; ?>">
        <?php if ($parent_article->isCategory()): ?>
            <?= fa_image_tag('layer-group', ['class' => 'icon']); ?>
        <?php else: ?>
            <?= (isset($article_counts[$parent_article->getID()])) ? fa_image_tag('book', ['class' => 'icon']) : fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
        <?php endif; ?>
        <span class="name">
            <?php if ($parent_article->isCategory() || isset($article_counts[$parent_article->getID()])): ?>
                <span class="title">
                    <?php include_component('publish/articleparent', ['article' => $parent_article, 'include_link' => false]); ?>
                </span>
                <span class="description">
                    <span class="count-badge">
                        <?php if ($parent_article->isCategory()): ?>
                            <?= (isset($article_counts[$parent_article->getID()])) ? __('%num articles in this category', ['%num' => $article_counts[$parent_article->getID()]]) : __('No other articles in this category'); ?>
                        <?php else: ?>
                            <?= __('%num other articles', ['%num' => $article_counts[$parent_article->getID()]]); ?>
                        <?php endif; ?>
                    </span>
                </span>
            <?php else: ?>
                <?= $parent_article->getName(); ?>
            <?php endif; ?>
        </span>
        <span class="icon">
            <?= fa_image_tag('file-export'); ?>
        </span>
    </a>
<?php endforeach; ?>
