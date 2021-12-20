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
        <span class="title-name">
            <?php if ($article->isCategory()) echo fa_image_tag('layer-group', ['class' => 'icon category']); ?>
            <span><?= ($article->isMainPage()) ? __('Overview') : $article->getName(); ?></span>
        </span>
    </div>
    <div class="details-container">
        <?php if ($article->getAuthor() instanceof \pachno\core\entities\common\Identifiable): ?>
            <div class="avatar-container">
                <span class="icon"><?php echo image_tag($article->getAuthor()->getAvatarURL(), ['class' => 'avatar small'], true); ?></span>
            </div>
        <?php endif; ?>
        <div class="information">
            <span>
                <?= fa_image_tag('history', ['class' => 'icon']); ?>
                <span><?= \pachno\core\framework\Context::getI18n()->formatTime($article->getPostedDate(), 3); ?></span>
            </span>
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
</div>
