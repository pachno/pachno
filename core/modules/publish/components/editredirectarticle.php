<?php

    use pachno\core\entities\Project;
    use pachno\core\framework\Context;
    use pachno\core\framework\Event;
    use pachno\core\entities\Article;

    /**
     * @var Article $article
     * @var string $form_url
     */

?>
<div class="backdrop_box medium edit_agileboard">
    <div class="backdrop_detail_header">
        <span><?php echo ($article->getId()) ? __('Edit named link') : __('Create new named link'); ?></span>
        <a href="javascript:void(0);" class="closer"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form action="<?php echo make_url('publish_edit_redirect_article', ['article_id' => $article->getId()]); ?>" id="edit_redirect_article_form" method="post" data-simple-submit data-auto-close <?php if (!$article->getID()): ?> data-update-container="#redirect-article-list" data-update-insert<?php else: ?> data-update-container="#redirect_article_<?= $article->getID(); ?>" data-update-replace<?php endif; ?>>
                <input type="hidden" name="project_id" value="<?= ($article->getProject() instanceof Project) ? $article->getProject()->getID() : 0; ?>">
                <div class="form-row">
                    <input type="text" id="redirect_article_<?php echo $article->getID(); ?>_name" name="slug" value="<?php echo __e($article->getRedirectSlug()); ?>">
                    <label style for="redirect_article_<?php echo $article->getID(); ?>_name"><?php echo __('Redirect slug'); ?></label>
                    <div class="helper-text">
                        <?php echo __('Enter a short, meaningful name for the link. Letters, underscores and dashes only - no spaces.'); ?>
                    </div>
                </div>
                <div class="form-row">
                    <label><?= __('Redirects to'); ?></label>
                    <div id="edit-redirect-articles-results" class="configurable-components-list">
                        <?php if ($article->getRedirectArticle() instanceof Article): ?>
                            <div class="configurable-component">
                                <input type="radio" name="redirect_article_id" value="<?= $article->getRedirectArticle()->getID(); ?>" checked class="fancy-checkbox">
                                <div class="row">
                                    <div class="icon"><?= fa_image_tag('file-alt'); ?></div>
                                    <div class="name"><?= $article->getRedirectArticle()->getName(); ?></div>
                                </div>
                            </div>
                        <?php else: ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary">
                        <?php echo fa_image_tag('spinner', ['class' => 'indicator fa-spin icon']); ?>
                        <span><?php echo __('Save'); ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
