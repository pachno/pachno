<?php

    use pachno\core\entities\Article;
    use pachno\core\entities\Project;

    /**
     * @var Article $article
     * @var Article $overview_article
     * @var Article[] $top_level_articles
     * @var Article[] $top_level_categories
     * @var Article[] $parents
     */

    $level = 0;
    $first = true;

?>
<nav class="project-context sidebar">
    <div class="list-mode">
        <?php if ($article->getProject() instanceof Project): ?>
            <?php include_component('project/projectheader', ['subpage' => __('Documentation'), 'show_back' => true]); ?>
        <?php endif; ?>
        <?php include_component('publish/manualsidebarlink', [
            'parents' => [],
            'article' => $article,
            'main_article' => $overview_article,
            'level' => 0,
            'first' => true
        ]); ?>
        <div class="header"><?= __('Categories'); ?></div>
        <?php foreach ($top_level_categories as $top_level_category): ?>
            <?php if (array_key_exists($top_level_category->getID(), $parents)): ?>
                <?php include_component('publish/manualsidebarlink', compact('parents', 'article', 'main_article', 'level', 'first')); ?>
            <?php else: ?>
                <?php include_component('publish/manualsidebarlink', [
                    'parents' => [],
                    'article' => $article,
                    'main_article' => $top_level_category,
                    'level' => 0,
                    'first' => true
                ]); ?>
            <?php endif; ?>
        <?php endforeach; ?>
        <div class="header"><?= __('Pages'); ?></div>
        <?php foreach ($top_level_articles as $top_level_article): ?>
            <?php if (array_key_exists($top_level_article->getID(), $parents)): ?>
                <?php include_component('publish/manualsidebarlink', compact('parents', 'article', 'main_article', 'level', 'first')); ?>
            <?php else: ?>
                <?php include_component('publish/manualsidebarlink', [
                    'parents' => [],
                    'article' => $article,
                    'main_article' => $top_level_article,
                    'level' => 0,
                    'first' => true
                ]); ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</nav>
