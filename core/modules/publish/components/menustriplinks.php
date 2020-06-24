<?php

    use pachno\core\entities\Project;
    use pachno\core\framework\Context;
    use pachno\core\modules\publish\Publish;

    /**
     * @var Publish $publish
     */

    $publish = \pachno\core\framework\Context::getModule('publish');

?>
<?php if (!isset($wiki_url)): ?>
    <a href="<?= (isset($project_url)) ? $project_url : $url; ?>" class="list-item expandable <?php if (Context::getRouting()->getCurrentRoute()->getModuleName() == 'publish'): ?>selected expanded<?php endif; ?>">
        <?= fa_image_tag('book', ['class' => 'icon']); ?>
        <span class="name"><?= $publish->getMenuTitle($project instanceof Project); ?></span>
        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
    </a>
<?php else: ?>
    <a href="<?= $wiki_url; ?>" class="list-item expandable" target="_blank">
        <span class="name"><?= $publish->getMenuTitle($project instanceof Project); ?></span>
        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
    </a>
<?php endif; ?>
<div id="wiki_dropdown_menu" class="submenu list-mode">
    <?php foreach ($top_level_articles as $article): ?>
        <a href="<?= $article->getLink(); ?>" class="list-item">
            <span class="name"><?= $article->getName(); ?></span>
        </a>
    <?php endforeach; ?>
</div>
