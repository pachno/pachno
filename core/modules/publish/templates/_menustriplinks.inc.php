<?php

use pachno\core\entities\Project;
use pachno\core\framework\Context;

?>
<?php if (!isset($wiki_url)): ?>
    <a href="<?= (isset($project_url)) ? $project_url : $url; ?>" class="list-item expandable <?php if (Context::getRouting()->getCurrentRouteModule() == 'publish'): ?>selected expanded<?php endif; ?>">
        <?= fa_image_tag('newspaper', ['class' => 'icon']); ?>
        <span class="name"><?= Context::getModule('publish')->getMenuTitle($project instanceof Project); ?></span>
        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
    </a>
<?php else: ?>
    <a href="<?= $wiki_url; ?>" class="list-item expandable" target="_blank">
        <span class="name"><?= Context::getModule('publish')->getMenuTitle($project instanceof Project); ?></span>
        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
    </a>
<?php endif; ?>
<div id="wiki_dropdown_menu" class="submenu list-mode">
    <div class="header"><?= __('Quick links'); ?></div>
    <a href="<?= make_url('publish_article', ['article_name' => 'MainPage']); ?>" class="list-item">
        <span class="name"><?= Context::getModule('publish')->getMenuTitle(false); ?></span>
    </a>
    <?php if ($project instanceof Project): ?>
        <a href="<?= make_url('publish_article', ['article_name' => $project->getKey().':MainPage']); ?>" class="list-item">
            <span class="name"><?= Context::getModule('publish')->getMenuTitle($project instanceof Project); ?></span>
        </a>
    <?php endif; ?>
    <?php if (count(Project::getAllRootProjects(false)) > (int) ($project instanceof Project)): ?>
        <div class="header"><?= __('Project wikis'); ?></div>
        <?php foreach (Project::getAllRootProjects(false) as $root_project): ?>
            <?php if (!$root_project->hasAccess() || $root_project->isArchived() || (isset($project_url) && $root_project->getID() == $project->getID())) continue; ?>
            <?php if (!$root_project->hasWikiURL()): ?>
                <a href="<?= make_url('publish_article', ['article_name' => $root_project->getKey().':MainPage']); ?>" class="list-item">
                    <?= image_tag($root_project->getSmallIconName(), ['class' => 'icon'], $root_project->hasSmallIcon()); ?>
                    <span class="name"><?= $root_project->getName(); ?></span>
                </a>
            <?php else: ?>
                <a href="<?= $root_project->getWikiURL(); ?>" target="_blank" class="list-item">
                    <?= image_tag($root_project->getSmallIconName(), ['class' => 'icon'], $root_project->hasSmallIcon()); ?>
                    <span class="name"><?= $root_project->getName(); ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
