<?php

    /**
     * @var \pachno\core\entities\Article $article
     * @var \pachno\core\framework\Response $pachno_response
     */

//    include_component('publish/wikibreadcrumbs', array('article_name' => $article->getName(), 'edit' => true));
    use pachno\core\entities\Article;

    $pachno_response->setTitle(__('Editing %article_name', array('%article_name' => $article->getName())));
    $pachno_response->setFullscreen(true);

?>
<div class="main_area edit-article" id="article-editor-main-container">
    <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo $article->getLink('edit'); ?>" method="post" id="edit_article_form">
        <div id="article-header-container">
            <div class="logo-back-container">
                <?php echo link_tag((($article->getId()) ? $article->getLink() : make_url('publish')), fa_image_tag('chevron-left') . '<span>'.__('Back').'</span>', ['class' => 'button secondary highlight']); ?>
                <div id="article-editor-header" class="toolbar-container"></div>
            </div>
            <div class="actions-container">
                <div class="button-group">
                    <button class="button icon secondary" type="button" onclick="jQuery('#editor-container').toggleClass('wider');return false;"><?= fa_image_tag('arrows-alt-h'); ?></button>
                    <span class="separator"></span>
                    <button class="button icon secondary" type="button" onclick="jQuery('#parent_selector_container').show();return false;"><?= fa_image_tag('file-export'); ?></button>
                    <span class="separator"></span>
                    <button class="button icon secondary" type="submit" onclick="$('article_preview').value = 1;"><?= fa_image_tag('eye'); ?></button>
                    <button class="button primary" id="save_button" type="submit"><?php echo ($article->getId()) ? __('Publish changes') : __('Publish page'); ?></button>
                </div>
            </div>
        </div>
        <div class="message-box type-warning" id="parent_move_message" style="display: none;">
            <span class="icon"><?= fa_image_tag('info-circle'); ?></span>
            <span class="message"><?= __('The page will be moved when you publish the changes'); ?></span>
            <span class="actions">
                <button type="button" class="button secondary highlight" onclick="jQuery('#parent_article_id_input').val(jQuery('#parent_article_id_input').data('original-id'));$('parent_move_message').hide();return false;"><?= __('Undo'); ?></button>
            </span>
        </div>
        <?php if (isset($error)): ?>
            <div class="message-box type-error">
                <span class="icon"><?= fa_image_tag('exclamation-triangle'); ?></span>
                <span class="message"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>
        <?php if (isset($preview) && $preview): ?>
            <div class="message-box type-info">
                <span class="icon"><?= fa_image_tag('info-circle'); ?></span>
                <span class="message">
                    <?php echo __('This is a preview of the page'); ?><br>
                    <b><?php echo __('The article has not been saved yet'); ?>
                </span>
                <span class="actions">
                    <a href="#edit_article" class="button secondary highlight" onclick="$('article_content').focus();"><?php echo __('Continue editing'); ?></a>
                </span>
            </div>
            <?php include_component('articledisplay', array('article' => $article, 'show_article' => $preview, 'show_category_contains' => false, 'show_actions' => true, 'mode' => 'view')); ?>
        <?php endif; ?>
        <?php // include_component('publish/header', array('article' => $article, 'show_actions' => true, 'mode' => 'edit')); ?>
        <input type="hidden" name="preview" value="0" id="article_preview">
        <input type="hidden" name="article_id" value="<?php echo ($article->getId()) ? $article->getID() : 0; ?>">
        <input type="hidden" id="parent_article_id_input" name="parent_article_id" value="<?php echo ($article->getParentArticle() instanceof Article) ? $article->getParentArticle()->getID() : 0; ?>" data-original-id="<?php echo ($article->getParentArticle() instanceof Article) ? $article->getParentArticle()->getID() : 0; ?>">
        <input type="hidden" name="last_modified" value="<?php echo ($article->getId()) ? $article->getPostedDate() : 0; ?>">
        <input type="hidden" name="article_type" value="<?php echo $article->getArticleType(); ?>">
        <div class="editor-container" id="editor-container">
            <div id="article_edit_header_information" class="title-crumbs">
                <?php if ($article->getProject() instanceof \pachno\core\entities\Project): ?>
                    <span class="project-logo">
                        <?php echo image_tag($selected_project->getIconName(), ['alt' => '[LOGO]'], true); ?>
                    </span>
                    <span class="article-name"><?= $article->getProject()->getName(); ?></span>
                    <span class="separator"><?= fa_image_tag('chevron-right'); ?></span>
                <?php endif; ?>
                <?php if ($article->getParentArticle() instanceof Article): ?>
                    <?php include_component('publish/articleparent', ['article' => $article->getParentArticle()]); ?>
                    <span class="separator"><?= fa_image_tag('chevron-right'); ?></span>
                <?php endif; ?>
            </div>
            <div class="article-name-container">
                <input type="text" name="article_name" id="article_name" value="<?= ($article->getName() !== 'Main Page') ? __e($article->getName()) : 'Overview'; ?>" placeholder="<?= __('Type the page title here'); ?>" <?php if ($article->getName() == 'Main Page') echo ' disabled'; ?>>
            </div>
            <?php include_component('main/textarea', [
                'area_name' => 'article_content',
                'invisible' => true,
                'target_type' => 'article',
                'target_id' => $article->getID(),
                'area_id' => 'article_content',
                'placeholder' => __e(__('Start writing your page content here')).'&#10;'.__e(__('Link to issues by just typing them, or other users by @-ing them')),
                'syntax' => $article->getContentSyntax(),
                'markuppable' => !($article->getContentSyntax(true) == \pachno\core\framework\Settings::SYNTAX_PT),
                'value' => htmlspecialchars($article->getContent())
            ]); ?>
        </div>
        <div class="form-row" style="display: none;">
            <label><?php echo __('Comment'); ?></label>
            <span>
                <input type="text" name="change_reason" id="change_reason" maxlength="255" value="<?php if (isset($change_reason)) echo $change_reason; ?>" placeholder="<?php echo __('Reason for the change (max. 255 characters)'); ?>">
            </span>
        </div>
    </form>
    <div class="form-container" style="display: none;" id="parent_selector_container">
        <form class="fullpage_backdrop" onsubmit="Pachno.Main.loadParentArticles(this);return false;" action="<?php echo make_url('publish_article_parents', array('article_id' => $article->getId())); ?>">
            <div class="backdrop_box medium">
                <div class="backdrop_detail_header">
                    <span><?php echo __('Move page'); ?></span>
                    <a href="javascript:void(0);" onclick="$('parent_selector_container').hide();" class="closer"><?php echo fa_image_tag('times'); ?></a>
                </div>
                <div class="backdrop_detail_content">
                    <div class="form-row unified">
                        <input type="search" name="find_article" id="parent_article_name_search">
                        <input type="submit" class="button secondary highlight" value="<?php echo __('Find'); ?>">
                    </div>
                    <?php echo image_tag('spinning_32.gif', array('id' => 'parent_selector_container_indicator', 'style' => 'display: none;')); ?>
                    <div id="parent_articles_list" class="list-mode"></div>
                </div>
            </div>
        </form>
    </div>
    <input type="hidden" id="article_serialized" value="">
</div>
<script type="text/javascript">
    require(['domReady', 'pachno/index', 'jquery'], function (domReady, pachno_index_js, jquery) {
        domReady(function () {
            $('edit_article_form').on('submit', function(event) {
                var ok = true;
                <?php if (\pachno\core\framework\Context::getModule('publish')->getSetting('require_change_reason') != 0): ?>
                if ($('article_preview').value != 1 && $('change_reason').value.length == 0) {
                    $('change_reason').focus();
                    Pachno.Main.Helpers.Message.error('<?php echo __('Comment required') ?>', '<?php echo __('Please provide a comment describing the edit.') ?>');
                    ok = false;
                }
                <?php endif; ?>
                if (ok)
                    Event.stopObserving(window, 'beforeunload');
                else
                    Event.stop(event);
                return ok;
            });
        });
    });

</script>
