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
<div class="content-with-sidebar">
<?php if ($article instanceof \pachno\core\entities\Article): ?>
    <?php include_component('manualsidebar', ['article' => $article]); ?>
    <?php //include_component('leftmenu', ['article' => $article]); ?>
    <div class="main_area article">
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
        <?php if ($article->getID()): ?>
            <?php include_component('articledisplay', ['article' => $article, 'show_article' => true, 'redirected_from' => $redirected_from]); ?>
        <?php else: ?>
            <div class="article">
                <?php include_component('publish/header', ['article' => $article, 'show_actions' => true, 'mode' => 'view']); ?>
                <?php if (Context::isProjectContext() && Context::getCurrentProject()->isArchived()): ?>
                    <?php include_component('publish/placeholder', ['article_name' => $article->getName(), 'nocreate' => true]); ?>
                <?php else: ?>
                    <?php include_component('publish/placeholder', ['article_name' => $article->getName()]); ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if (!$article->getID() && ((Context::isProjectContext() && !Context::getCurrentProject()->isArchived()) || (!Context::isProjectContext() && Context::getModule('publish')->canUserEditArticle($article->getName())))): ?>
            <div class="publish_article_actions">
                <form action="<?php echo make_url('publish_article_edit', ['article_name' => $article->getName()]); ?>" method="get" style="float: left; margin-right: 10px;">
                    <input class="button button-green" type="submit" value="<?php echo __('Create this article'); ?>">
                </form>
            </div>
        <?php endif; ?>
        <?php if ($article->getID()): ?>
            <?php $attachments = array_reverse($article->getFiles()); ?>
            <div id="article_attachments">
                <?php /*if (\pachno\core\framework\Settings::isUploadsEnabled() && $article->canEdit()): ?>
                    <?php include_component('main/uploader', array('article' => $article, 'mode' => 'article')); ?>
                <?php endif;*/ ?>
                <h4>
                    <span class="header-text"><?php echo __('Article attachments'); ?></span>
                    <?php if (\pachno\core\framework\Settings::isUploadsEnabled() && $article->canEdit()): ?>
                        <button class="button" onclick="Pachno.Main.showUploader('<?php echo make_url('get_partial_for_backdrop', ['key' => 'uploader', 'mode' => 'article', 'article_name' => $article->getName()]); ?>');"><?php echo __('Attach a file'); ?></button>
                    <?php else: ?>
                        <button class="button disabled" onclick="Pachno.Main.Helpers.Message.error('<?php echo __('File uploads are not enabled'); ?>');"><?php echo __('Attach a file'); ?></button>
                    <?php endif; ?>
                </h4>
                <?php include_component('publish/attachments', ['article' => $article, 'attachments' => $attachments]); ?>
            </div>
            <div id="article_comments">
                <h4>
                    <span class="header-text">
                        <?php echo __('Article comments (%count)', ['%count' => Comment::countComments($article->getID(), Comment::TYPE_ARTICLE)]); ?>
                    </span>
                    <div class="action-buttons">
                        <div class="dropper_container">
                            <?php echo fa_image_tag('spinner', ['class' => 'fa-spin', 'style' => 'display: none;', 'id' => 'comments_loading_indicator']); ?>
                            <span class="dropper"><?= fa_image_tag('cog') . __('Options'); ?></span>
                            <ul class="more_actions_dropdown dropdown_box popup_box leftie" id="comment_dropdown_options">
                                <li><a href="javascript:void(0);" onclick="Pachno.Main.Comment.toggleOrder('<?= Comment::TYPE_ARTICLE; ?>', '<?= $article->getID(); ?>');"><?php echo __('Sort comments in opposite direction'); ?></a></li>
                            </ul>
                        </div>
                    </div>
                    <?php if ($pachno_user->canPostComments() && ((Context::isProjectContext() && !Context::getCurrentProject()->isArchived()) || !Context::isProjectContext())): ?>
                        <button id="comment_add_button" class="button" onclick="Pachno.Main.Comment.showPost();"><?php echo __('Post comment'); ?></button>
                    <?php endif; ?>
                </h4>
                <?php //include_component('main/comments', ['target_id' => $article->getID(), 'mentionable_target_type' => 'article', 'target_type' => Comment::TYPE_ARTICLE, 'show_button' => false, 'comment_count_div' => 'article_comment_count']); ?>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="redbox" id="notfound_error">
        <div class="header"><?php echo __("This article can not be displayed"); ?></div>
        <div class="content"><?php echo __("This article either does not exist, has been deleted or you do not have permission to view it."); ?></div>
    </div>
<?php endif; ?>
