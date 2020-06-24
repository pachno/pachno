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
<?php if ($show_details && $show_article && ($article->hasContent() || !$article->isCategory())): ?>
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
<?php if (!$embedded && $show_article && !$article->isCategory() && !$article->isMainPage() && count($article->getCategories()) > 0): ?>
    <h2><?php echo __('Categories'); ?></h2>
    <?php $category_links = array(); ?>
    <?php foreach ($article->getCategories() as $category): ?>
        <a href="<?php $category->getCategory()->getLink(); ?>" class="card-badge">
            <?= fa_image_tag('layer-group', ['class' => 'icon']); ?>
            <span><?= $category->getCategory()->getName(); ?></span>
        </a>
    <?php endforeach; ?>
<?php endif; ?>
