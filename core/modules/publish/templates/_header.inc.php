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
                <span class="separator"></span>
                <?php if ($article->canEdit()): ?>
                    <a href="<?= $article->getLink('edit'); ?>" class="button secondary">
                        <?= fa_image_tag('edit', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Edit'); ?></span>
                    </a>
                <?php endif; ?>
                <?php if ($article->isMainPage()): ?>
                    <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'project_key' => $article->getProject()->getKey()]); ?>" class="button primary">
                        <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Create page'); ?></span>
                    </a>
                <?php else: ?>
                    <div class="dropper-container">
                        <a class="button dropper primary">
                            <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Create'); ?></span>
                        </a>
                        <div class="dropdown-container">
                            <div class="list-mode">
                                <?php if ($article->isCategory()): ?>
                                    <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID(), 'project_key' => $article->getProject()->getKey()]); ?>" class="list-item">
                                        <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                        <span class="name"><?= __('Create a page'); ?></span>
                                    </a>
                                    <span class="separator"></span>
                                    <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID(), 'project_key' => $article->getProject()->getKey()]); ?>" class="list-item">
                                        <?= fa_image_tag('layer-group', ['class' => 'icon']); ?>
                                        <span class="name"><?= __('Create a sub-category'); ?></span>
                                    </a>
                                <?php else: ?>
                                    <?php if (Context::isProjectContext()): ?>
                                        <?php if ($article->getParentArticle() instanceof Article): ?>
                                            <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getParentArticle()->getID(), 'project_key' => $article->getProject()->getKey()]); ?>" class="list-item">
                                                <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                                <span class="name"><?= __('Create another page here'); ?></span>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!$article->isMainPage()): ?>
                                            <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID(), 'project_key' => $article->getProject()->getKey()]); ?>" class="list-item">
                                                <?= fa_image_tag('book', ['class' => 'icon']); ?>
                                                <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                                <span class="name"><?= __('Create new page under this page'); ?></span>
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if ($article->getParentArticle() instanceof Article): ?>
                                            <a href="<?= make_url('publish_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getParentArticle()->getID()]); ?>" class="list-item">
                                                <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                                <span class="name"><?= __('Create another page here'); ?></span>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!$article->isMainPage()): ?>
                                            <a href="<?= make_url('publish_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID()]); ?>" class="list-item">
                                                <?= fa_image_tag('book', ['class' => 'icon']); ?>
                                                <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                                <span class="name"><?= __('Create new page under this page'); ?></span>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!isset($embedded) || !$embedded): ?>
                    <div class="dropper-container">
                        <a class="button dropper icon secondary"><?= fa_image_tag('ellipsis-v'); ?></a>
                        <div class="dropdown-container">
                            <div class="list-mode">
                                <a href="<?= $article->getLink('history'); ?>" class="list-item">
                                    <?= fa_image_tag('history', ['class' => 'icon']); ?>
                                    <span class="name"><?= __('History'); ?></span>
                                </a>
                                <?php if ($article->canEdit()): ?>
                                    <a href="<?= $article->getLink('permissions'); ?>" class="list-item">
                                        <?= fa_image_tag('lock', ['class' => 'icon']); ?>
                                        <span class="name"><?= __('Permissions'); ?></span>
                                    </a>
                                <?php endif; ?>
                                <div class="list-item separator"></div>
                                <?php if ($article->canDelete()): ?>
                                    <?= javascript_link_tag(fa_image_tag('times', ['class' => 'icon']) . '<span class="name">'.__('Delete this article').'</span>', ['onclick' => "Pachno.UI.Dialog.show('".__('Please confirm')."', '".__('Do you really want to delete this article?')."', {yes: {click: function () { Pachno.Main.deleteArticle('".make_url('publish_article_delete', ['article_id' => $article->getID()])."') }}, no: {click: Pachno.UI.Dialog.dismiss}})", 'class' => 'list-item danger']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
