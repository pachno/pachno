<?php

    use pachno\core\entities\Article;
    use pachno\core\entities\Project;

    /**
     * @var Article $article
     */

?>
<nav class="project-context sidebar">
    <div class="list-mode">
        <?php if ($article->getProject() instanceof Project) include_component('project/projectheader', ['subpage' => __('Documentation')]); ?>
        <?php $level = 0; ?>
        <?php $first = true; ?>
        <?php include_component('publish/manualsidebarlink', compact('parents', 'article', 'main_article', 'level', 'first')); ?>
    </div>
</nav>
