<?php

    use pachno\core\entities\Comment;
    use pachno\core\framework\Context;
    use pachno\core\entities\Article;
    use pachno\core\framework\Response;

    /**
     * @var Article $article
     * @var Response $pachno_response
     */

    $pachno_response->setTitle($article->getName());

?>
<div class="content-with-sidebar article-container">
<?php if ($article instanceof \pachno\core\entities\Article): ?>
    <?php include_component('manualsidebar', ['article' => $article]); ?>
    <div class="main_area" data-simplebar>
        <a name="top"></a>
        <?php if ($error): ?>
            <div class="redbox">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if ($message): ?>
            <div class="greenbox" style="margin: 0 0 5px 5px; font-size: 14px;">
                <b><?php echo $message; ?></b>
            </div>
        <?php endif; ?>
        <?php if (isset($revision) && !$error): ?>
            <div class="lightyellowbox" style="margin: 0 0 5px 5px; font-size: 14px;">
                <?php echo __('You are now viewing a previous revision of this article - revision %revision_number %date, by %author', ['%revision_number' => '<b>'.$revision.'</b>', '%date' => '<span class="faded_out">[ '. Context::getI18n()->formatTime($article->getPostedDate(), 20).' ]</span>', '%author' => (($article->getAuthor() instanceof \pachno\core\entities\User) ? $article->getAuthor()->getName() : __('System'))]); ?><br>
                <b><?php echo link_tag(make_url('publish_article', ['article_name' => $article->getName()]), __('Show current version')); ?></b>
            </div>
        <?php endif; ?>
        <?php include_component('articledisplay', ['article' => $article, 'show_article' => $article->hasContent(), 'redirected_from' => $redirected_from]); ?>
        <?php if ($article->isCategory()): ?>
            <div class="article-pages-list">
                <h2>
                    <span class="name"><?php echo __('In this category'); ?></span>
                    <span class="button-group"><button class="icon secondary"><?= fa_image_tag('sort-numeric-up', ['class' => 'icon']); ?></button></span>
                </h2>
                <?php if (count($article->getCategoryArticles()) > 0): ?>
                    <?php foreach ($article->getCategoryArticles() as $categoryarticle): ?>
                        <a class="article-page" href="<?= $categoryarticle->getArticle()->getLink(); ?>">
                            <h3>
                                <span>
                                    <span class="date-container count-badge">
                                        <?= fa_image_tag('calendar-alt', ['class' => 'icon'], 'far'); ?>
                                        <span><?= Context::getI18n()->formatTime($categoryarticle->getArticle()->getLastUpdatedDate(), 20); ?></span>
                                    </span>
                                    <span><?= $categoryarticle->getArticle()->getName(); ?></span>
                                </span>
                            </h3>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="faded_out"><?php echo __('There are no pages in this category'); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if ($article->getID()): ?>
            <?php $attachments = array_reverse($article->getFiles()); ?>
            <div id="article_attachments">
                <h4>
                    <?= fa_image_tag('paperclip', ['class' => 'icon']); ?>
                    <span class="name">
                        <span><?php echo __('Attachments'); ?></span>
                        <span class="count-badge"><?= count($attachments); ?></span>
                    </span>
                    <?php if (\pachno\core\framework\Settings::isUploadsEnabled() && $article->canEdit()): ?>
                        <button class="button secondary trigger-file-upload"><?php echo __('Add attachment'); ?></button>
                    <?php else: ?>
                        <button class="button secondary disabled" onclick="Pachno.UI.Message.error('<?php echo __('File uploads are not enabled'); ?>');"><?php echo __('Attach a file'); ?></button>
                    <?php endif; ?>
                </h4>
                <?php include_component('publish/attachments', ['article' => $article, 'attachments' => $attachments]); ?>
            </div>
            <div id="article_comments">
                <h4>
                    <?= fa_image_tag('comment', ['class' => 'icon'], 'far'); ?>
                    <span class="name">
                        <span><?php echo __('Comments'); ?></span>
                        <span class="count-badge"><?= $comment_count; ?></span>
                    </span>
                    <div class="button-group">
                        <?php echo fa_image_tag('spinner', ['class' => 'fa-spin', 'style' => 'display: none;', 'id' => 'comments_loading_indicator']); ?>
                        <button class="secondary icon" id="sort-comments-button" style="<?php if (!$comment_count) echo 'display: none; '; ?>" onclick="Pachno.Main.Comment.toggleOrder('<?= Comment::TYPE_ARTICLE; ?>', '<?= $article->getID(); ?>')"><?= fa_image_tag('sort', ['class' => 'icon']); ?></button>
                        <?php if ($pachno_user->canPostComments() && ((Context::isProjectContext() && !Context::getCurrentProject()->isArchived()) || !Context::isProjectContext())): ?>
                            <button id="comment_add_button" class="button secondary" onclick="Pachno.Main.Comment.showPost();"><span><?php echo __('Add comment'); ?></span></button>
                        <?php endif; ?>
                    </div>
                </h4>
                <?php include_component('main/comments', ['target_id' => $article->getID(), 'mentionable_target_type' => 'article', 'target_type' => Comment::TYPE_ARTICLE, 'show_button' => false, 'comment_count_div' => 'article_comment_count']); ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if (\pachno\core\framework\Settings::isUploadsEnabled() && $article->canEdit()): ?>
        <?php include_component('main/uploader'); ?>
    <?php endif; ?>
<?php else: ?>
    <div class="redbox" id="notfound_error">
        <div class="header"><?php echo __("This article can not be displayed"); ?></div>
        <div class="content"><?php echo __("This article either does not exist, has been deleted or you do not have permission to view it."); ?></div>
    </div>
<?php endif; ?>
