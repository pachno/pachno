<?php

    use pachno\core\entities\Article;
    use pachno\core\entities\Project;
    use pachno\core\framework\Context;

    /**
     * @var Article $article
     * @var Project $project
     */

    $article_id = (isset($article)) ? $article->getID() : 0;
    $url = ($project instanceof Project) ? make_url('publish_project_article_edit', ['article_id' => 0, 'project_key' => $project->getKey()]) : make_url('publish_article_edit', ['article_id' => 0]);

?>
<div class="list-item form-container">
    <form class="form" id="add-article-<?= $article_id; ?>-category-form" data-parent-article-id="<?= $article_id; ?>" action="<?= $url; ?>?is_category=1&parent_article_id=<?= $article_id; ?>&return_value=sidebarlink" data-interactive-form data-add-category-form data-article-id="<?= $article_id; ?>">
        <div class="form-row add-placeholder">
            <label for="add-article-<?= $article_id; ?>-category-form-input" class="icon"><?= fa_image_tag('plus'); ?></label>
            <input id="add-article-<?= $article_id; ?>-category-form-input" type="text" name="article_name" class="invisible" placeholder="<?= __('Add a category'); ?>">
        </div>
    </form>
</div>
