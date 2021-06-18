<?php

    use pachno\core\entities\Article;
    use pachno\core\entities\Project;
    use pachno\core\framework\Context;
    use pachno\core\entities\User;

    /**
     * @var Article[] $articles
     * @var User $pachno_user
     */

?>
<div class="content-with-sidebar article-container">
    <?php include_component('publish/sidebar'); ?>
    <div class="main_area">
        <div id="redirect-article-list-container">
            <h1><?php echo (Context::getCurrentProject() instanceof Project) ? __('Configure project named links') : __('Configure named links'); ?></h1>
            <div class="helper-text">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_redirect_articles.png', [], true); ?></div>
                <div class="description">
                    <?= __('Named links redirect from a short, custom text link to an article. Named links can be updated to point to new articles without the link changing'); ?>
                </div>
            </div>
            <div class="flexible-table" id="redirect-article-list">
                <div class="row header">
                    <div class="column header info-icons"></div>
                    <div class="column header name-container"><?= __('Short link'); ?></div>
                    <div class="column header"><?= __('Links to'); ?></div>
                    <?php if ($pachno_user->canCreateArticlesInProject(Context::getCurrentProject())): ?>
                        <div class="column header actions"></div>
                    <?php endif; ?>
                </div>
                <?php foreach ($articles as $article): ?>
                    <?php include_component('publish/redirectarticle', ['article' => $article]); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>