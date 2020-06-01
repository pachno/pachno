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
<?php if ($show_details && $show_article): ?>
    <div class="details-container">
        <div class="avatar-container">
            <?php if ($article->getAuthor() instanceof \pachno\core\entities\common\Identifiable): ?>
                <span class="icon"><?php echo image_tag($article->getAuthor()->getAvatarURL(), ['class' => 'avatar small'], true); ?></span>
            <?php else: ?>
                <span class="icon"><?php echo fa_image_tag('file', [], 'far'); ?></span>
            <?php endif; ?>
        </div>
        <div class="information">
            <span><?php echo __('Last updated %time', ['%time' => \pachno\core\framework\Context::getI18n()->formatTime($article->getPostedDate(), 3)]); ; ?></span>
            <?php if ($article->getAuthor() instanceof \pachno\core\entities\common\Identifiable): ?>
                <span><?php echo __('Authored by %user', ['%user' => '<a href="javascript:void(0);" onclick="Pachno.UI.Backdrop.show(\'' . make_url('get_partial_for_backdrop', ['key' => 'usercard', 'user_id' => $article->getAuthor()->getID()]) . '\');" class="faded_out">' . $article->getAuthor()->getName() . '</a>']); ; ?></span>
            <?php else: ?>
                <span><?php echo __('System-generated article'); ; ?></span>
            <?php endif; ?>
        </div>
        <?php if (isset($redirected_from) && $redirected_from instanceof Article): ?>
            <div class="redirected_from">&rarr; <?php echo __('Redirected from %article_name', array('%article_name' => link_tag($redirected_from->getLink('edit'), $redirected_from->getName()))); ?></div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php if ($show_article): ?>
    <div class="article syntax_<?php echo \pachno\core\framework\Settings::getSyntaxClass($article->getContentSyntax()); ?>">
        <div class="content"><?php echo $article->getParsedContent(['embedded' => $embedded, 'article' => $article]); ?></div>
    </div>
<?php endif; ?>
<?php if ($article->isCategory() && !$embedded && $show_category_contains): ?>
    <h2>
        <span class="name"><?php echo __('In this category'); ?></span>
        <span class="button-group"><button class="icon secondary"><?= fa_image_tag('ellipsis-v', ['class' => 'icon']); ?></button></span>
    </h2>
    <?php if (count($article->getCategoryArticles()) > 0): ?>
        <div class="article-pages-list">
            <?php foreach ($article->getCategoryArticles() as $categoryarticle): ?>
                <div class="article-page">
                    <h3>
                        <span class="name multiline">
                            <span><?= $categoryarticle->getArticle()->getName(); ?></span>
                            <span class="date-container">
                                <?= fa_image_tag('clock', ['class' => 'icon']); ?>
                                <span><?= Context::getI18n()->formatTime($categoryarticle->getArticle()->getLastUpdatedDate(), 20); ?></span>
                            </span>
                        </span>
                    </h3>
                    <?php echo link_tag($categoryarticle->getArticle()->getLink(), $categoryarticle->getArticle()->getName()); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="faded_out"><?php echo __('There are no pages in this category'); ?></div>
    <?php endif; ?>
<?php endif; ?>
<?php if (!$embedded && $show_article && count($article->getCategories()) > 0): ?>
    <h2><?php echo __('Categories:'); ?></h2>
    <?php $category_links = array(); ?>
    <?php foreach ($article->getCategories() as $category): ?>
        <?php $category_links[] = link_tag($category->getCategory()->getLink(), $category->getCategory()->getName()); ?>
    <?php endforeach; ?>
    <?php echo implode(', ', $category_links); ?>
<?php endif; ?>
