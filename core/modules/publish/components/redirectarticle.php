<?php

    use pachno\core\entities\Article;
    use pachno\core\framework\Context;
    use pachno\core\entities\User;

    /**
     * @var Article $article
     * @var User $pachno_user
     */

?>
<div class="row redirect-article" data-article data-redirect-article data-id="<?= $article->getID(); ?>" id="redirect_article_<?= $article->getId(); ?>">
    <div class="column info-icons"><?= fa_image_tag('file-export'); ?></div>
    <div class="column name-container"><a href="<?= $article->getRedirectUrl(); ?>"><?= $article->getRedirectUrl(); ?></a></div>
    <div class="column">
        <?php if ($article->getRedirectArticle() instanceof Article): ?>
            <a href="<?= $article->getRedirectArticle()->getLink(); ?>" class="article-link">
                <?= fa_image_tag('file-alt', ['class' => 'icon']); ?>
                <span class="name"><?= $article->getRedirectArticle()->getName(); ?></span>
            </a>
        <?php else: ?>
            -
        <?php endif; ?>
    </div>
    <?php if ($pachno_user->canCreateArticlesInProject(Context::getCurrentProject())): ?>
        <div class="column actions">
            <button class="button secondary icon trigger-backdrop" data-url="<?php echo make_url('get_partial_for_backdrop', ['key' => 'publish_edit_redirect_article', 'article_id' => $article->getID()]); ?>">
                <?= fa_image_tag('edit', ['class' => 'icon']); ?>
            </button>
            <button class="button secondary icon danger" onclick="Pachno.UI.Dialog.show('<?= __('Delete this named link?'); ?>', '<?= __('The old link will stop working, but you can recreate it later if you want to.'); ?>', {yes: {click: function () { Pachno.trigger(Pachno.EVENTS.article.delete, { url: '<?= make_url('publish_article_delete', ['article_id' => $article->getID()]); ?>', article_id: <?= $article->getID(); ?> }) }}, no: {click: Pachno.UI.Dialog.dismiss}})">
                <span class="icon"><?= fa_image_tag('times'); ?></span>
            </button>
        </div>
    <?php endif; ?>
</div>