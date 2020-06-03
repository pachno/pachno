<?php

    /**
     * @var \pachno\core\entities\Article $article
     * @var \pachno\core\framework\Response $pachno_response
     */

    use pachno\core\entities\Article;

    if ($article->getID()) {
        $back_link = $article->getLink();
    } elseif ($article->getParentArticle() instanceof Article) {
        $back_link = $article->getParentArticle()->getLink();
    } else {
        $back_link = \pachno\core\modules\publish\Publish::getArticleLink('Main Page', \pachno\core\framework\Context::getCurrentProject());
    }

    $pachno_response->setTitle(__('Editing %article_name', array('%article_name' => $article->getName())));
    $pachno_response->setFullscreen(true);

?>
<div class="main_area edit-article" id="article-editor-main-container" data-simplebar>
    <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo $article->getLink('edit'); ?>" method="post" id="edit_article_form">
        <div id="article-header-container">
            <div class="logo-back-container">
                <?php echo link_tag($back_link, fa_image_tag('chevron-left') . '<span>'.__('Back').'</span>', ['class' => 'button secondary highlight']); ?>
            </div>
            <div class="actions-container">
                <div class="button-group">
                    <?php if (!isset($convert)): ?>
                        <button class="secondary" type="button">
                            <?= fa_image_tag('paperclip', ['class' => 'icon']); ?><span class="name"><?= __('Attach a file'); ?></span>
                        </button>
                        <span class="separator"></span>
                    <?php endif; ?>
                    <button class="button icon secondary" type="button" onclick="$('#editor-container').toggleClass('wider');return false;"><?= fa_image_tag('arrows-alt-h'); ?></button>
                    <span class="separator"></span>
                    <?php if (!isset($convert)): ?>
                        <button class="button icon secondary" type="button" onclick="$('#parent_selector_container').show();return false;"><?= fa_image_tag('file-export'); ?></button>
                        <button class="button icon secondary" type="button" onclick="$('#category_selector_container').show();return false;"><?= fa_image_tag('layer-group'); ?></button>
                        <span class="separator"></span>
                    <?php endif; ?>
                    <button class="button secondary" type="submit" onclick="$('#article_preview').value = 1;">
                        <?= fa_image_tag('eye', ['class' => 'icon']); ?>
                        <span class="name"><?= __('Preview'); ?></span>
                    </button>
                    <button class="button primary enable-on-editor-ready" id="article-publish-button" disabled type="submit">
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                        <?php if (isset($convert)): ?>
                            <span><?= __('Convert page'); ?></span>
                        <?php else: ?>
                            <span><?php echo ($article->getId()) ? __('Publish changes') : __('Publish page'); ?></span>
                        <?php endif; ?>
                    </button>
                </div>
            </div>
        </div>
        <div class="message-box type-warning" id="parent_move_message" style="display: none;">
            <span class="icon"><?= fa_image_tag('info-circle'); ?></span>
            <span class="message"><?= __('The page will be moved when you publish the changes'); ?></span>
            <span class="actions">
                <button type="button" class="button secondary highlight" onclick="$('#parent_article_id_input').val($('#parent_article_id_input').data('original-id'));$('#parent_move_message').hide();return false;"><?= __('Undo'); ?></button>
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
                    <a href="#edit_article" class="button secondary highlight" onclick="$('#article_content').focus();"><?php echo __('Continue editing'); ?></a>
                </span>
            </div>
            <?php include_component('articledisplay', array('article' => $article, 'show_article' => $preview, 'show_category_contains' => false, 'show_actions' => true, 'mode' => 'view')); ?>
        <?php endif; ?>
        <input type="hidden" name="preview" value="0" id="article_preview">
        <input type="hidden" name="article_id" value="<?php echo ($article->getId()) ? $article->getID() : 0; ?>">
        <input type="hidden" id="parent_article_id_input" name="parent_article_id" value="<?php echo ($article->getParentArticle() instanceof Article) ? $article->getParentArticle()->getID() : 0; ?>" data-original-id="<?php echo ($article->getParentArticle() instanceof Article) ? $article->getParentArticle()->getID() : 0; ?>">
        <input type="hidden" name="last_modified" value="<?php echo ($article->getId()) ? $article->getPostedDate() : 0; ?>">
        <input type="hidden" name="article_type" value="<?php echo $article->getArticleType(); ?>">
        <div class="editor-container" id="editor-container">
            <?php if ($article->getContentSyntax() !== \pachno\core\framework\Settings::SYNTAX_EDITOR_JS): ?>
                <div class="message-box type-warning">
                    <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                    <span class="message">
                        <span class="title"><?php echo __('This article is using an older syntax which is no longer supported'); ?></span>
                        <span><?php echo __('You should convert it to the new format, but doing so will require a bit of formatting effort. In the meantime, you can continue using the legacy editing experience for minor changes.'); ?></span>
                    </span>
                    <span class="actions">
                    <a href="?convert=true" class="button secondary highlight"><?php echo __('Start conversion'); ?></a>
                </span>
                </div>
                <?php if ($article->getContentSyntax() !== \pachno\core\framework\Settings::SYNTAX_MD): ?>
                    <div class="message-box type-warning">
                        <span class="icon"><?= fa_image_tag('info-circle'); ?></span>
                        <span class="message">
                            <span class="title"><?php echo __('This syntax is no longer supported'); ?></span>
                            <span><?php echo __('The mediawiki syntax is no longer supported. Formatting tools have been disabled for this article.'); ?></span>
                        </span>
                    </div>
                <?php endif; ?>
            <?php elseif (isset($convert)): ?>
                <div class="message-box type-warning">
                    <span class="icon"><?= fa_image_tag('exchange-alt'); ?></span>
                    <span class="message">
                        <span class="title"><?php echo __('Convert page'); ?></span>
                        <span><?php echo __('The page syntax has been changed to support the new, improved editing experience, but no changes have been saved yet.'); ?></span>
                        <span><?php echo __('When you are happy with the formatting, you can click the %convert_page button to convert the page.', ['%convert_page' => __('Convert page')]); ?></span>
                    </span>
                </div>
            <?php endif; ?>
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
                <input type="hidden" name="article_content_syntax" value="<?= $article->getContentSyntax(); ?>">
                <input type="text" name="article_name" id="article_name" required value="<?= ($article->getName() !== 'Main Page') ? __e($article->getName()) : 'Overview'; ?>" placeholder="<?= __('Type the page title here'); ?>" <?php if ($article->isMainPage()) echo ' disabled'; ?>>
            </div>
            <?php if ($article->getContentSyntax() == \pachno\core\framework\Settings::SYNTAX_EDITOR_JS): ?>
                <div class="editor-input-container-wrapper article">
                    <div class="editor-input-container wysiwyg-editor" data-input-name="article_content" data-placeholder="<?= __("Click here to start writing. When writing, press [tab] to see writing options"); ?>"><?= $article->getContent(); ?></div>
                </div>
            <?php else: ?>
                <?php include_component('main/textarea', [
                    'area_name' => 'article_content',
                    'invisible' => true,
                    'target_type' => 'article',
                    'target_id' => $article->getID(),
                    'area_id' => 'article_content',
                    'placeholder' => __e(__('Start writing your page content here')).'&#10;'.__e(__('Link to issues by just typing them, or other users by @-ing them')),
                    'syntax' => $article->getContentSyntax(),
                    'value' => htmlspecialchars($article->getContent())
                ]); ?>
            <?php endif; ?>
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
                    <a href="javascript:void(0);" onclick="$('#parent_selector_container').hide();" class="closer"><?php echo fa_image_tag('times'); ?></a>
                </div>
                <div class="backdrop_detail_content">
                    <div class="form-row unified">
                        <input type="search" name="find_article" id="parent_article_name_search">
                        <input type="submit" class="button secondary highlight" value="<?php echo __('Find'); ?>">
                    </div>
                    <?php echo image_tag('spinning_32.gif', array('id' => 'parent_selector_container_indicator', 'style' => 'display: none;')); ?>
                    <div id="parent_articles_list" class="list-mode">

                    </div>
                </div>
            </div>
        </form>
    </div>
    <input type="hidden" id="article_serialized" value="">
</div>
<script type="text/javascript">
    Pachno.on(Pachno.EVENTS.ready, (PachnoApplication) => {
        <?php if ($article->getContentSyntax() != \pachno\core\framework\Settings::SYNTAX_EDITOR_JS): ?>
            const $publishButton = $('#article-publish-button');
            $publishButton.removeAttr('disabled');
        <?php endif; ?>

        const $form = $('#edit_article_form');
        const $nameInput = $('#article_name');
        $nameInput.focus();
        $form.on('submit', function(event) {
            var ok = true;

            const $publishButton = $('#article-publish-button');
            $publishButton.attr('disabled', true);
            $form.addClass('submitting');

            <?php if (\pachno\core\framework\Context::getModule('publish')->getSetting('require_change_reason') != 0): ?>
            if ($('#article_preview').val() != 1 && $('#change_reason').val().length == 0) {
                $('#change_reason').focus();
                Pachno.UI.Message.error('<?php echo __('Comment required') ?>', '<?php echo __('Please provide a comment describing the edit.') ?>');
                ok = false;
            }
            <?php endif; ?>
            if (!ok) {
                Event.stop(event);
                event.preventDefault();
                event.stopPropagation();
                $form.removeClass('submitting');
                $publishButton.removeAttr('disabled');
                return;
            }

            Pachno.trigger(Pachno.EVENTS.formSubmit, { form_id: 'edit_article_form' })
                .then(results => {
                    let options = {
                        method: 'POST',
                        form: 'edit_article_form'
                    };

                    <?php if ($article->getContentSyntax() == \pachno\core\framework\Settings::SYNTAX_EDITOR_JS): ?>
                        if (results === undefined) {
                            return;
                        }

                        const form_element = results.find(result => result.form_data !== undefined && result.form_data.input_name === 'article_content');
                        if (form_element !== undefined) {
                            const article_content = JSON.stringify(form_element.form_data.data);
                            options.additional_params = { article_content };
                        }
                    <?php endif; ?>

                    Pachno.fetch($form.attr('action'), options);
                })
            return false;
        });
    });

</script>
