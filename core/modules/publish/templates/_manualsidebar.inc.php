<?php

    use pachno\core\entities\Article;
    use pachno\core\entities\Project;

    /**
     * @var Article $article
     * @var Article $overview_article
     * @var Article[] $top_level_articles
     * @var Article[] $top_level_categories
     * @var int[] $parents
     */

?>
<nav class="project-context sidebar">
    <div class="scroll-container">
        <div class="list-mode" data-simplebar>
            <?php if ($article->getProject() instanceof Project): ?>
                <?php include_component('project/projectheader', ['subpage' => __('Documentation'), 'show_back' => true]); ?>
            <?php endif; ?>
            <?php include_component('publish/manualsidebarlink', [
                'parents' => [],
                'article' => $article,
                'main_article' => $overview_article
            ]); ?>
            <?php if ($article->getProject() instanceof Project): ?>
                <a href="<?= make_url("publish_project_redirect_articles", ['project_key' => $article->getProject()->getKey()]); ?>" class="list-item">
                    <?= fa_image_tag('share-square', ['class' => 'icon'], 'far'); ?>
                    <span class="name"><?= __('Named links'); ?></span>
                </a>
            <?php else: ?>
                <a href="<?= make_url("publish_redirect_articles"); ?>" class="list-item">
                    <?= fa_image_tag('share-square', ['class' => 'icon'], 'far'); ?>
                    <span class="name"><?= __('Named links'); ?></span>
                </a>
            <?php endif; ?>
            <?php if (count($article->getTableOfContents()) > 1): ?>
                <div class="header expandable expanded">
                    <span class="name"><?= __('On this page'); ?></span>
                    <button class="button secondary icon expander"><?= fa_image_tag('caret-square-down', [], 'far'); ?></button>
                </div>
                <div class="expandable-menu">
                    <?php foreach ($article->getTableOfContents() as $header): ?>
                        <a href="#<?= $header['id']; ?>" class="list-item">
                            <?= fa_image_tag('bookmark', ['class' => 'icon'], 'far'); ?>
                            <span class="name"><?= trim($header['content']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="header expandable expanded">
                <span class="name"><?= __('Categories'); ?></span>
                <button class="button secondary icon expander"><?= fa_image_tag('caret-square-down', [], 'far'); ?></button>
            </div>
            <div class="expandable-menu">
                <?php foreach ($top_level_categories as $top_level_category): ?>
                    <?php include_component('publish/manualsidebarlink', [
                        'parents' => $parents,
                        'article' => $article,
                        'main_article' => $top_level_category]); ?>
                <?php endforeach; ?>
            </div>
            <div class="header expandable expanded">
                <span class="name"><?= __('Pages'); ?></span>
                <button class="button secondary icon"><?= fa_image_tag('search'); ?></button>
                <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID(), 'project_key' => $article->getProject()->getKey()]); ?>" class="button secondary icon">
                    <?= fa_image_tag('plus'); ?>
                </a>
                <button class="button secondary icon expander"><?= fa_image_tag('caret-square-down', [], 'far'); ?></button>
            </div>
            <div class="expandable-menu">
                <?php foreach ($top_level_articles as $top_level_article): ?>
                    <?php include_component('publish/manualsidebarlink', [
                        'parents' => $parents,
                        'article' => $article,
                        'main_article' => $top_level_article]); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</nav>
