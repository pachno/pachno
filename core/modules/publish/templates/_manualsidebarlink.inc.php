<?php

    use pachno\core\entities\Article;

    /**
     * @var Article $main_article
     */

    $children = $main_article->getChildArticles();
    $is_parent = array_key_exists($main_article->getID(), $parents);
    $is_selected = $main_article->getID() == $article->getID() || ($main_article->isRedirect() && $main_article->getRedirectArticleName() == $article->getTitle());

    $is_first = $first;
    $first = false;

    $project_key = (\pachno\core\framework\Context::isProjectContext()) ? \pachno\core\framework\Context::getCurrentProject()->getKey() . ':' : '';
//    $article_name = (strpos(mb_strtolower($main_article->getTitle()), 'category:') !== false) ? substr($main_article->getTitle(), 9+mb_strlen($project_key)) : substr($main_article->getTitle(), mb_strlen($project_key));

?>
<?php /* <li class="<?= (isset($level) && $level >= 1) ? 'child' : 'parent'; ?> <?php if ($is_parent && !$is_selected) echo 'parent'; ?> <?php if ($is_selected) echo 'selected'; ?>"> */ ?>
<a href="<?= $main_article->getLink(); ?>" class="list-item <?php if ($is_parent && !$is_selected) echo 'expandable expanded'; ?> <?php if ($is_selected) echo 'selected'; ?>">
    <?php if ($main_article->isCategory()): ?>
        <?= fa_image_tag('layer-group', ['class' => 'icon']); ?>
    <?php elseif (!empty($children)): ?>
        <?= fa_image_tag('book', ['class' => 'icon']); ?>
    <?php else: ?>
        <?= ($main_article->getName() == 'Main Page') ? fa_image_tag('file-invoice', ['class' => 'icon']) : fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
    <?php endif; ?>
    <span class="name"><?= ($main_article->getName() == 'Main Page') ? __('Overview') : $main_article->getName(); ?></span>
    <?php if ($is_parent || count($children)): ?>
        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
    <?php endif; ?>
</a>
<?php if (($is_parent || $is_selected) && count($children)): ?>
    <div class="submenu">
        <?php foreach ($children as $child_article): ?>
            <?php include_component('publish/manualsidebarlink', array('parents' => $parents, 'first' => $first, 'article' => $article, 'main_article' => $child_article, 'level' => $level + 1)); ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
