<?php

    use pachno\core\entities\Comment;
    use pachno\core\entities\User;
    use pachno\core\framework\Context;
    use pachno\core\entities\Article;
    use pachno\core\framework\Response;
use pachno\core\framework\Settings;

/**
     * @var Article $article
     * @var Response $pachno_response
     * @var User $pachno_user
     */

    if ($article->getProject() instanceof \pachno\core\entities\Project) {
        $pachno_response->setTitle($article->getProject()->getName() . ' ~ ' . $article->getName());
    } else {
        $pachno_response->setTitle($article->getName());
    }

?>
<div class="content-with-sidebar article-container">
<?php if ($article instanceof \pachno\core\entities\Article): ?>
    <?php include_component('sidebar', ['article' => $article]); ?>
    <div class="main_area" data-simplebar>
        <div class="fullpage_backdrop" id="copy_article_form_container" style="display: none;">
            <div class="fullpage_backdrop_content">
                <div class="fullpage_backdrop_content backdrop_box medium">
                    <div class="backdrop_detail_header">
                        <span><?= __('Copy page'); ?></span>
                        <a href="javascript:void(0);" class="closer"><?= fa_image_tag('times'); ?></a>
                    </div>
                    <div id="backdrop_detail_content" class="backdrop_detail_content">
                        <div class="form-container">
                            <form accept-charset="<?= Context::getI18n()->getCharset(); ?>" action="<?= $article->getLink('edit'); ?>" data-simple-submit id="copy_article_form">
                                <input type="hidden" name="copy" value="1">
                                <div class="form-row">
                                    <input class="fancy-checkbox" type="checkbox" name="copy_attachments" value="1" id="copy_article_include_attachments" checked>
                                    <label for="copy_article_include_attachments">
                                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                        <span><?= __('Include attachments when copying'); ?></span>
                                    </label>
                                </div>
                                <div class="form-row">
                                    <input class="fancy-checkbox" type="checkbox" name="copy_comments" value="1" id="copy_article_include_comments" checked>
                                    <label for="copy_article_include_comments">
                                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                        <span><?= __('Include comments when copying'); ?></span>
                                    </label>
                                </div>
                                <div class="form-row">
                                    <input class="fancy-checkbox" type="checkbox" name="copy_child_articles" value="1" id="copy_article_include_child_articles" checked>
                                    <label for="copy_article_include_child_articles">
                                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                        <span><?= __('Also copy sub-pages'); ?></span>
                                    </label>
                                </div>
                                <div class="form-row submit-contaner">
                                    <button type="submit" class="button primary">
                                        <?= fa_image_tag('copy'); ?><span><?= __('Copy page'); ?></span>
                                        <span class="indicator"><?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a name="top"></a>
        <?php if ($error): ?>
            <div class="redbox">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if ($message): ?>
            <div class="message-box type-info">
                <span class="message"><?php echo $message; ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($revision) && !$error): ?>
            <div class="lightyellowbox" style="margin: 0 0 5px 5px; font-size: 14px;">
                <?php echo __('You are now viewing a previous revision of this article - revision %revision_number %date, by %author', ['%revision_number' => '<b>'.$revision.'</b>', '%date' => '<span class="faded_out">[ '. Context::getI18n()->formatTime($article->getPostedDate(), 20).' ]</span>', '%author' => (($article->getAuthor() instanceof User) ? $article->getAuthor()->getName() : __('System'))]); ?><br>
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
            <?php if (isset($attachments)): ?>
                <div id="article_attachments" class="show-article-attachments">
                    <h4>
                        <?= fa_image_tag('paperclip', ['class' => 'icon']); ?>
                        <span class="name">
                            <span><?php echo __('Attachments'); ?></span>
                            <span class="count-badge" id="article-attachments-count"><?= count($attachments); ?></span>
                        </span>
                        <?php if ($article->canEdit() && Settings::isUploadsEnabled()): ?>
                            <button class="button secondary trigger-file-upload">
                                <span class="name"><?php echo __('Add attachment'); ?></span>
                            </button>
                        <?php elseif (!Settings::isUploadsEnabled()): ?>
                            <button class="button secondary disabled" onclick="Pachno.UI.Message.error('<?php echo __('File uploads are not enabled'); ?>');"><?php echo __('Add attachment'); ?></button>
                        <?php endif; ?>
                    </h4>
                    <?php include_component('publish/attachments', ['article' => $article, 'attachments' => $attachments]); ?>
                </div>
                <div class="upload-container fixed-position hidden" id="upload_drop_zone">
                    <div class="wrapper">
                        <span class="image-container"><?= image_tag('/unthemed/icon-upload.png', [], true); ?></span>
                        <span class="message"><?= $message ?? __('Drop the file to upload it'); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            <div id="article_comments">
                <h4>
                    <?= fa_image_tag('comment', ['class' => 'icon'], 'far'); ?>
                    <span class="name">
                        <span><?php echo __('Comments'); ?></span>
                        <span class="count-badge"><?= $comment_count; ?></span>
                    </span>
                    <div class="button-group">
                        <?php echo fa_image_tag('spinner', ['class' => 'fa-spin', 'style' => 'display: none;', 'id' => 'comments_loading_indicator']); ?>
                        <button class="secondary icon trigger-comment-sort" data-target-type="<?= Comment::TYPE_ARTICLE; ?>" data-target-id="<?= $article->getID(); ?>" id="sort-comments-button" style="<?php if (!$comment_count) echo 'display: none; '; ?>"><?= fa_image_tag('sort', ['class' => 'icon']); ?></button>
                        <?php if ($pachno_user->canPostComments(Comment::TYPE_ARTICLE, Context::getCurrentProject())): ?>
                            <button id="comment_add_button" class="button secondary highlight trigger-show-comment-post">
                                <?= fa_image_tag('comment', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Post a comment'); ?></span>
                            </button>
                        <?php endif; ?>
                    </div>
                </h4>
                <?php include_component('main/comments', [
                    'target_id' => $article->getID(),
                    'mentionable_target_type' => 'article',
                    'target_type' => Comment::TYPE_ARTICLE,
                    'can_post_comments' => $pachno_user->canPostComments(Comment::TYPE_ARTICLE, Context::getCurrentProject()),
                    'comment_count_div' => 'article_comment_count'
                ]); ?>
            </div>
        <?php endif; ?>
    </div>
    <script type="text/javascript">
        Pachno.on(Pachno.EVENTS.ready, function () {
            $('body').on('click', '.trigger-copy-popup', () => $('#copy_article_form_container').show() );

            const article = <?= json_encode($article->toJSON()); ?>;
            <?php if (Settings::isUploadsEnabled() && $article->canEdit()): ?>
                const uploader = new Uploader({
                    uploader_container: '#article_attachments',
                    mode: 'list',
                    only_images: false,
                    type: '<?= \pachno\core\entities\File::TYPE_ATTACHMENT; ?>',
                    data: {
                        article_id: <?= $article->getID(); ?>
                    }
                });
            <?php endif; ?>

            Pachno.on(Pachno.EVENTS.article.removeFile, function (PachnoApplication, data) {
                if (data.article_id != article.id)
                    return;

                $(`[data-attachment][data-file-id="${data.file_id}"]`).remove();
                Pachno.UI.Dialog.dismiss();

                Pachno.fetch(data.url, { method: 'DELETE' })
                    .then((json) => {
                        $('#article-attachments-count').html(json.attachments);
                    })
            });

            Pachno.on(Pachno.EVENTS.upload.complete, function (PachnoApplication, data) {
                if (data.article_id != article.id)
                    return;

                const count = parseInt($('#article-attachments-count').html());
                $('#article-attachments-count').html(count + 1);
            });

        });
    </script>
<?php else: ?>
    <div class="redbox" id="notfound_error">
        <div class="header"><?php echo __("This article can not be displayed"); ?></div>
        <div class="content"><?php echo __("This article either does not exist, has been deleted or you do not have permission to view it."); ?></div>
    </div>
<?php endif; ?>
