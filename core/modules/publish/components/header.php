<?php

    use pachno\core\framework\Context;
    use pachno\core\entities\Article;
    use pachno\core\modules\publish\Publish;

    /**
     * @var Article $article
     * @var Publish $publish
     */

    $article_name = $article->getName();
    $publish = Context::getModule('publish');

?>
<div class="header-container <?= $mode; ?>">
    <div class="title-container article-title">
        <div>
            <span class="title-name">
                <?php if ($article->isCategory()) echo fa_image_tag('layer-group', ['class' => 'icon category']); ?>
                <span><?= ($article->isMainPage()) ? __('Overview') : $article->getName(); ?></span>
            </span>
        </div>
    </div>
    <?php if ($article->getID() || $mode == 'edit'): ?>
        <?php if ($show_actions): ?>
            <div class="button-group">
                <div class="toggle-favourite">
                    <?php if ($pachno_user->isGuest()): ?>
                        <button class="button secondary disabled" disabled>
                            <?= fa_image_tag('star', ['class' => 'unsubscribed']); ?>
                        </button>
                        <div class="tooltip from-above leftie">
                            <?= __('Please log in to subscribe to updates for this article'); ?>
                        </div>
                    <?php else: ?>
                        <div class="tooltip from-above leftie">
                            <?= __('Click the star to toggle whether you want to be notified whenever this article updates or changes'); ?><br>
                        </div>
                        <?= fa_image_tag('spinner', array('id' => 'article_favourite_indicator_'.$article->getId(), 'style' => 'display: none;', 'class' => 'fa-spin')); ?>
                        <button class="button icon secondary" id="article_favourite_faded_<?= $article->getId(); ?>" style="<?= ($pachno_user->isArticleStarred($article->getID())) ? 'display: none;' : ''; ?>" onclick="Pachno.Main.toggleFavouriteArticle('<?= make_url('publish_toggle_favourite_article', ['article_id' => $article->getID(), 'user_id' => $pachno_user->getID()]); ?>', <?= $article->getID(); ?>);">
                            <?= fa_image_tag('star', ['class' => 'unsubscribed']); ?>
                        </button>
                        <button class="button icon secondary" id="article_favourite_normal_<?= $article->getId(); ?>" style="<?= (!$pachno_user->isArticleStarred($article->getID())) ? 'display: none;' : ''; ?>" onclick="Pachno.Main.toggleFavouriteArticle('<?= make_url('publish_toggle_favourite_article', ['article_id' => $article->getID(), 'user_id' => $pachno_user->getID()]); ?>', <?= $article->getID(); ?>);">
                            <?= fa_image_tag('star', ['class' => 'subscribed']); ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
