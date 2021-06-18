<?php

    /**
     * @var \pachno\core\entities\Article $article
     * @var \pachno\core\entities\Article[] $top_level_categories
     * @var \pachno\core\framework\Response $pachno_response
     * @var \pachno\core\entities\Project $selected_project
     * @var int[] $parents
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
                        <button class="button secondary toggle-attachments-sidebar" type="button">
                            <?= fa_image_tag('paperclip', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Attachments'); ?></span>
                            <span class="article-attachments-count count-badge"><?= count($article->getFiles()); ?></span>
                        </button>
                        <?php if (!$article->isCategory() && !$article->isMainPage()): ?>
                            <button class="button secondary toggle-category-sidebar" type="button">
                                <?= fa_image_tag('layer-group', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Categories'); ?></span>
                                <span id="number_of_categories" class="count-badge"><?= count($article->getCategories()); ?></span>
                            </button>
                        <?php endif; ?>
                        <span class="separator"></span>
                    <?php endif; ?>
                    <button class="button icon secondary" type="button" onclick="$('#editor-container').toggleClass('wider');return false;"><?= fa_image_tag('arrows-alt-h'); ?></button>
                    <button class="button primary enable-on-editor-ready" id="article-publish-button" disabled type="submit">
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                        <?php if (isset($convert)): ?>
                            <span><?= __('Convert page'); ?></span>
                        <?php else: ?>
                            <span><?php echo ($article->getId()) ? __('Save changes') : __('Save page'); ?></span>
                        <?php endif; ?>
                    </button>
                    <div class="dropper-container">
                        <a class="button dropper icon secondary"><?= fa_image_tag('ellipsis-v'); ?></a>
                        <div class="dropdown-container">
                            <div class="list-mode">
                                <a href="javascript:void(0);" class="list-item" type="button" onclick="$('#parent_selector_container').show();return false;">
                                    <?= fa_image_tag('file-export', ['class' => 'icon']); ?>
                                    <span class="name"><?= __('Move page'); ?></span>
                                </a>
                                <div class="list-item separator"></div>
                                <?php if ($article->isMainPage()): ?>
                                    <div class="list-item disabled">
                                        <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                                        <span class="name"><?= __('This page cannot be deleted'); ?></span>
                                    </div>
                                <?php else: ?>
                                    <?= javascript_link_tag(fa_image_tag('times', ['class' => 'icon']) . '<span class="name">'.__('Delete this page').'</span>', ['onclick' => "Pachno.UI.Dialog.show('".__('Please confirm')."', '".__('Do you really want to delete this article?')."', {yes: {click: function () { Pachno.Main.deleteArticle('".make_url('publish_article_delete', ['article_id' => $article->getID()])."') }}, no: {click: Pachno.UI.Dialog.dismiss}})", 'class' => 'list-item danger disabled']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (!$article->isCategory() && !$article->isMainPage()): ?>
            <div class="fullpage_backdrop docked-right" id="article-categories-sidebar" style="display: none;">
                <div class="fullpage_backdrop_content">
                    <div class="fullpage_backdrop_content backdrop_box medium">
                        <div class="backdrop_detail_header">
                            <?= fa_image_tag('angle-double-right', ['class' => 'icon closer toggle-category-sidebar']); ?>
                            <span><?= __('Categories for this page'); ?></span>
                        </div>
                        <div id="backdrop_detail_content" class="backdrop_detail_content">
                            <div class="list-mode">
                                <div class="expandable-menu" id="article-0-children-container">
                                    <?php foreach ($top_level_categories as $top_level_category): ?>
                                        <?php include_component('publish/editcategorysidebarlink', [
                                            'parents' => $parents,
                                            'article' => $article,
                                            'category' => $top_level_category]); ?>
                                    <?php endforeach; ?>
                                    <?php if ($pachno_user->canCreateCategoriesInProject(\pachno\core\framework\Context::getCurrentProject())): ?>
                                        <?php include_component('publish/editcategorysidebaraddcategory', ['project' => $article->getProject()]); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="fullpage_backdrop docked-right" id="article-attachments-sidebar" style="display: none;">
            <div class="fullpage_backdrop_content">
                <div class="fullpage_backdrop_content backdrop_box medium">
                    <div class="backdrop_detail_header">
                        <?= fa_image_tag('angle-double-right', ['class' => 'icon closer toggle-attachments-sidebar']); ?>
                        <span>
                            <span><?= __('Attached files'); ?></span>
                            <span class="count-badge article-attachments-count" id="article-attachments-count"><?= count($attachments); ?></span>
                        </span>
                        <?php if ($article->canEdit()): ?>
                            <button class="button secondary highlight trigger-file-upload" type="button">
                                <span class="name"><?php echo __('Add attachment'); ?></span>
                            </button>
                        <?php elseif (!\pachno\core\framework\Settings::isUploadsEnabled()): ?>
                            <button class="button secondary disabled" onclick="Pachno.UI.Message.error('<?php echo __('File uploads are not enabled'); ?>');"><?php echo __('Attach a file'); ?></button>
                        <?php endif; ?>
                    </div>
                    <div id="backdrop_detail_content" class="backdrop_detail_content">
                        <?php $attachments = $article->getFiles(); ?>
                        <div id="article_attachments">
                            <?php include_component('publish/attachments', ['article' => $article, 'attachments' => $attachments]); ?>
                        </div>
                        <div class="upload-container fixed-position hidden" id="upload_drop_zone">
                            <div class="wrapper">
                                <span class="image-container"><?= image_tag('/unthemed/icon-upload.png', [], true); ?></span>
                                <span class="message"><?= $message ?? __('Drop the file to upload it'); ?></span>
                            </div>
                        </div>
                    </div>
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
                <input type="text" name="article_name" id="article_name" required value="<?= ($article->getName() !== 'Main Page') ? __e($article->getName()) : 'Overview'; ?>" placeholder="<?= ($article->isCategory()) ? __('Type the category title here') : __('Type the page title here'); ?>" <?php if ($article->isMainPage()) echo ' disabled'; ?>>
            </div>
            <?php if ($article->getContentSyntax() == \pachno\core\framework\Settings::SYNTAX_EDITOR_JS): ?>
                <div class="editor-input-container-wrapper article">
                    <div class="editor-input-container wysiwyg-editor" id="article-editor" data-article-id="<?= $article->getId(); ?>" data-input-name="article_content" data-placeholder="<?= __("Click here to start writing. When writing, press [tab] to see writing options"); ?>"><textarea><?= $article->getContent(); ?></textarea></div>
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
    <div class="fullpage_backdrop" style="display: none;" id="parent_selector_container">
        <div class="fullpage_backdrop_content">
            <div class="backdrop_box medium">
                <div class="backdrop_detail_header">
                    <span><?php echo __('Move page'); ?></span>
                    <a href="javascript:void(0);" onclick="$('#parent_selector_container').hide();" class="closer"><?php echo fa_image_tag('times'); ?></a>
                </div>
                <div class="backdrop_detail_content">
                    <div class="form-container">
                        <form id="move-page-form" action="<?php echo make_url('publish_article_parents', ['article_id' => $article->getId()]); ?>" data-simple-submit data-update-container="#parent_articles_list">
                            <div class="form-row unified">
                                <input type="search" name="find_article" id="parent_article_name_search">
                                <button type="submit" class="button secondary highlight">
                                    <?= fa_image_tag('search', ['class' => 'icon']); ?>
                                    <span class="name"><?= __('Find'); ?></span>
                                    <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                                </button>
                            </div>
                            <?php echo image_tag('spinning_32.gif', array('id' => 'parent_selector_container_indicator', 'style' => 'display: none;')); ?>
                            <div id="parent_articles_list" class="list-mode"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="article_serialized" value="">
</div>
<script type="text/javascript">
    Pachno.on(Pachno.EVENTS.ready, () => {
        <?php if ($article->getContentSyntax() != \pachno\core\framework\Settings::SYNTAX_EDITOR_JS): ?>
            const $publishButton = $('#article-publish-button');
            $publishButton.removeProp('disabled');
        <?php endif; ?>

        $('body').on('click', '.toggle-category-sidebar', () => {
            $('#article-categories-sidebar').toggle();
        });

        $('body').on('click', '.toggle-attachments-sidebar', () => {
            $('#article-attachments-sidebar').toggle();
        });

        $('body').on('change', 'input[data-category-id]', function (event) {
            $('#number_of_categories').html($('input[data-category-id]:checked').length);
        });

        if (!$('.wysiwyg-editor').length) {
            $('.enable-on-editor-ready').removeAttr('disabled');
        }

        const $form = $('#edit_article_form');
        const $nameInput = $('#article_name');
        $nameInput.focus();
        $form.on('submit', function(event) {
            const $publishButton = $('#article-publish-button');
            $publishButton.prop('disabled', true);
            $form.addClass('submitting');

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
                            options.data = { article_content };
                        }
                    <?php endif; ?>

                    Pachno.fetch($form.attr('action'), options);
                })
            return false;
        });

        const article = <?= json_encode($article->toJSON()); ?>;
        <?php if (\pachno\core\framework\Settings::isUploadsEnabled() && $article->canEdit()): ?>
        const uploader = new Uploader({
            uploader_container: '#article-attachments-sidebar',
            mode: 'list',
            only_images: false,
            type: '<?= \pachno\core\entities\File::TYPE_ATTACHMENT; ?>',
            data: {
                article_id: <?= $article->getID(); ?>
            }
        });

        Pachno.on(Pachno.EVENTS.upload.complete, function (PachnoApplication, data) {
            if (data.article_id != article.id)
                return;

            const count = parseInt($('.article-attachments-count').html());
            $('.article-attachments-count').html(count + 1);
        });
        <?php endif; ?>

        Pachno.on(Pachno.EVENTS.article.removeFile, function (PachnoApplication, data) {
            if (data.article_id != article.id)
                return;

            $(`[data-attachment][data-file-id="${data.file_id}"]`).remove();
            Pachno.UI.Dialog.dismiss();

            Pachno.fetch(data.url, { method: 'DELETE' })
                .then((json) => {
                    $('.article-attachments-count').html(json.attachments);
                })
        });

    });

</script>
