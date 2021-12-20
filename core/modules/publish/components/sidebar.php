<?php

    use pachno\core\entities\Article;
    use pachno\core\entities\Project;
    use pachno\core\framework\Context;
    use pachno\core\framework\Routing;

    /**
     * @var Routing $pachno_routing
     * @var Article $article
     * @var Article $overview_article
     * @var Article[] $top_level_articles
     * @var Article[] $top_level_categories
     * @var int[] $parents
     * @var \pachno\core\entities\User $pachno_user
     */

?>
<nav class="project-context sidebar">
    <div class="scroll-container">
        <div class="list-mode" data-simplebar>
            <div class="list-item filter-container">
                <label for="documentation-search-input" class="icon"><?= fa_image_tag('search'); ?></label>
                <input id="documentation-search-input" type="search" name="value" placeholder="<?= __('Search documentation') ;?>">
            </div>
            <?php if (Context::getCurrentProject() instanceof Project): ?>
                <div class="list-item dropper-container">
                    <a class="dropper" href="javascript:void(0)">
                        <span class="icon"><?= fa_image_tag('angle-double-left'); ?></span>
                        <span class="name"><?= __('Back'); ?></span>
                    </a>
                    <div class="dropdown-container list-mode from-left slide-out">
                        <?php include_component('project/sidebarlinks', ['project' => Context::getCurrentProject()]); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php include_component('publish/sidebarlink', [
                'parents' => [],
                'article' => $article,
                'main_article' => $overview_article
            ]); ?>
            <?php if (Context::getCurrentProject() instanceof Project): ?>
                <a href="<?= make_url("publish_project_redirect_articles", ['project_key' => Context::getCurrentProject()->getKey()]); ?>" class="list-item <?php if ($pachno_routing->getCurrentRoute()->getName() === 'publish_project_redirect_articles') echo 'selected'; ?>">
                    <?= fa_image_tag('share-square', ['class' => 'icon'], 'far'); ?>
                    <span class="name"><?= __('Named links'); ?></span>
                </a>
            <?php else: ?>
                <a href="<?= make_url("publish_global_redirect_articles"); ?>" class="list-item <?php if ($pachno_routing->getCurrentRoute()->getName() === 'publish_global_redirect_articles') echo 'selected'; ?>">
                    <?= fa_image_tag('share-square', ['class' => 'icon'], 'far'); ?>
                    <span class="name"><?= __('Named links'); ?></span>
                </a>
            <?php endif; ?>
            <?php if ($article instanceof Article && count($article->getTableOfContents()) > 1): ?>
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
            <div class="header expandable <?php if ($article instanceof Article && ($article->isMainPage() || $article->isCategory())) echo 'expanded'; ?>">
                <span class="name"><?= __('Categories'); ?></span>
                <button class="button secondary icon expander"><?= fa_image_tag('caret-square-down', [], 'far'); ?></button>
            </div>
            <div class="expandable-menu" id="article-0-children-container">
                <?php foreach ($top_level_categories as $top_level_category): ?>
                    <?php include_component('publish/sidebarlink', [
                        'parents' => $parents,
                        'article' => $article,
                        'main_article' => $top_level_category]); ?>
                <?php endforeach; ?>
                <?php if ($pachno_user->canCreateCategoriesInProject(Context::getCurrentProject())): ?>
                    <?php include_component('publish/sidebaraddcategory', ['project' => Context::getCurrentProject()]); ?>
                <?php endif; ?>
            </div>
            <div class="header expandable expanded">
                <span class="name"><?= __('Pages'); ?></span>
                <?php if (Context::getCurrentProject() instanceof Project && $pachno_user->canCreateArticlesInProject(Context::getCurrentProject())): ?>
                    <a href="<?= make_url('publish_project_article_edit', ['article_id' => ($article instanceof Article) ? $article->getID() : 0, 'project_key' => Context::getCurrentProject()->getKey()]); ?>" class="button secondary icon">
                        <?= fa_image_tag('plus'); ?>
                    </a>
                <?php elseif ($pachno_user->canCreateArticlesInProject()): ?>
                    <a href="<?= make_url('publish_article_edit', ['article_id' => 0, 'parent_article_id' => ($article instanceof Article) ? $article->getID() : 0]); ?>" class="button secondary icon">
                        <?= fa_image_tag('plus'); ?>
                    </a>
                <?php endif; ?>
                <button class="button secondary icon expander"><?= fa_image_tag('caret-square-down', [], 'far'); ?></button>
            </div>
            <div class="expandable-menu">
                <?php foreach ($top_level_articles as $top_level_article): ?>
                    <?php include_component('publish/sidebarlink', [
                        'parents' => $parents,
                        'article' => $article,
                        'main_article' => $top_level_article]); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</nav>
<script type="text/javascript">
    Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
        const json = data.json;
        const $form = $(`#${data.form}`);
        if ($form.data('add-category-form') !== undefined) {
            const parent_id = $form.data('article-id');
            $form.trigger('reset');
            $(json.component).insertBefore($(`#article-${parent_id}-children-container > .form-container`));
        }
    });

</script>
