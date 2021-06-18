<?php

use pachno\core\entities\Article;
    use pachno\core\entities\Project;
    use pachno\core\framework\Context;
    use pachno\core\framework\Routing;

/**
 * @var ?Article $article
 * @var Routing $pachno_routing
 */

?>
<div class="action-container">
    <?php if (!$article instanceof Article): ?>
        <?php /* if (in_array($pachno_routing->getCurrentRoute()->getName(), ['publish_project_redirect_articles', 'publish_global_redirect_articles'])): ?>
            <button class="button secondary highlight trigger-backdrop" data-url="<?php echo make_url('get_partial_for_backdrop', ['key' => 'publish_edit_redirect_article', 'project_id' => Context::isProjectContext() ? Context::getCurrentProject()->getID() : 0]); ?>">
                <?php echo fa_image_tag('link', ['class' => 'icon']); ?>
                <span class="name"><?= __('Create new named link'); ?></span>
            </button>
        <?php endif; */ ?>
        <?php if (Context::getCurrentProject() instanceof Project): ?>
            <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'project_key' => Context::getCurrentProject()->getKey()]); ?>" class="button primary">
                <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                <span class="name"><?= __('Create page'); ?></span>
            </a>
        <?php else: ?>
            <a href="<?= make_url('publish_article_edit', ['article_id' => 0]); ?>" class="button primary">
                <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                <span class="name"><?= __('Create page'); ?></span>
            </a>
        <?php endif; ?>
    <?php elseif ($article->canEdit()): ?>
        <a href="<?= $article->getLink('edit'); ?>" class="button secondary">
            <?= fa_image_tag('edit', ['class' => 'icon']); ?>
            <span class="name"><?= __('Edit'); ?></span>
        </a>
        <?php if ($article->isMainPage()): ?>
            <?php if ($article->getProject() instanceof Project): ?>
                <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'project_key' => $article->getProject()->getKey()]); ?>" class="button primary">
                    <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Create page'); ?></span>
                </a>
            <?php else: ?>
                <a href="<?= make_url('publish_article_edit', ['article_id' => 0]); ?>" class="button primary">
                    <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Create page'); ?></span>
                </a>
            <?php endif; ?>
        <?php else: ?>
            <div class="dropper-container">
                <a class="button dropper primary">
                    <?= fa_image_tag('plus-square', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Create page'); ?></span>
                    <?= fa_image_tag('angle-down', ['class' => 'icon']); ?>
                </a>
                <div class="dropdown-container">
                    <div class="list-mode">
                        <?php if ($article->isCategory()): ?>
                            <?php if ($article->getProject() instanceof Project): ?>
                                <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID(), 'project_key' => $article->getProject()->getKey()]); ?>?category_id=<?= $article->getID(); ?>" class="list-item multiline">
                                    <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                    <span class="name">
                                        <span class="title"><?= __('Create a page here'); ?></span>
                                        <span class="description"><?= __('Create a page in this category'); ?></span>
                                    </span>
                                </a>
                                <span class="separator"></span>
                                <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID(), 'project_key' => $article->getProject()->getKey()]); ?>?is_category=1&parent_article_id=<?= $article->getID(); ?>" class="list-item multiline">
                                    <?= fa_image_tag('layer-group', ['class' => 'icon']); ?>
                                    <span class="name">
                                        <span class="title"><?= __('Create a sub-category'); ?></span>
                                        <span class="description"><?= __('Create a new category under this category'); ?></span>
                                    </span>
                                </a>
                            <?php else: ?>
                                <a href="<?= make_url('publish_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID()]); ?>?category_id=<?= $article->getID(); ?>" class="list-item multiline">
                                    <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                    <span class="name">
                                        <span class="title"><?= __('Create a page here'); ?></span>
                                        <span class="description"><?= __('Create a page in this category'); ?></span>
                                    </span>
                                </a>
                                <span class="separator"></span>
                                <a href="<?= make_url('publish_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID()]); ?>?is_category=1&parent_article_id=<?= $article->getID(); ?>" class="list-item multiline">
                                    <?= fa_image_tag('layer-group', ['class' => 'icon']); ?>
                                    <span class="name">
                                        <span class="title"><?= __('Create a sub-category'); ?></span>
                                        <span class="description"><?= __('Create a new category under this category'); ?></span>
                                    </span>
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if (Context::isProjectContext()): ?>
                                <?php if ($article->getParentArticle() instanceof Article): ?>
                                    <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'project_key' => $article->getProject()->getKey()]); ?>?parent_article_id=<?= $article->getParentArticle()->getID(); ?>" class="list-item multiline">
                                        <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                        <span class="name">
                                            <span class="title"><?= __('Create another page here'); ?></span>
                                            <span class="description"><?= __('Create another page under "%parent_name"', ['%parent_name' => $article->getParentArticle()->getName()]); ?></span>
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'project_key' => $article->getProject()->getKey()]); ?>" class="list-item multiline">
                                        <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                        <span class="name">
                                            <span class="title"><?= __('Create another page here'); ?></span>
                                            <span class="description"><?= __('Create another page at the top level'); ?></span>
                                        </span>
                                    </a>
                                <?php endif; ?>
                                <?php if (!$article->isMainPage()): ?>
                                    <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'project_key' => $article->getProject()->getKey()]) . '?parent_article_id=' . $article->getID(); ?>" class="list-item multiline">
                                        <?= fa_image_tag('book', ['class' => 'icon']); ?>
                                        <span class="name">
                                            <span class="title"><?= __('Create a new sub-page'); ?></span>
                                            <span class="description"><?= __('Create a new page under the current page'); ?></span>
                                        </span>
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($article->getParentArticle() instanceof Article): ?>
                                    <a href="<?= make_url('publish_article_edit', ['article_id' => 0]); ?>?parent_article_id=<?= $article->getParentArticle()->getID(); ?>" class="list-item multiline">
                                        <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                        <span class="name">
                                            <span class="title"><?= __('Create another page here'); ?></span>
                                            <span class="description"><?= __('Create another page under "%parent_name"', ['%parent_name' => $article->getParentArticle()->getName()]); ?></span>
                                        </span>
                                    </a>
                                <?php endif; ?>
                                <?php if (!$article->isMainPage()): ?>
                                    <a href="<?= make_url('publish_article_edit', ['article_id' => 0]); ?>?parent_article_id=<?= $article->getID(); ?>" class="list-item multiline">
                                        <?= fa_image_tag('book', ['class' => 'icon']); ?>
                                        <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                        <span class="name">
                                            <span class="title"><?= __('Create a new sub-page'); ?></span>
                                            <span class="description"><?= __('Create a new page under the current page'); ?></span>
                                        </span>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($article instanceof Article && $article->getID()): ?>
        <div class="toggle-favourite-container tooltip-container">
            <?php if ($pachno_user->isGuest()): ?>
                <button class="button secondary disabled" disabled>
                    <?= fa_image_tag('star', ['class' => 'unsubscribed']); ?>
                </button>
                <div class="tooltip from-above from-right">
                    <?= __('Please log in to subscribe to updates for this article'); ?>
                </div>
            <?php else: ?>
                <div class="tooltip from-above from-right">
                    <?= __('Click the star to toggle whether you want to be notified whenever this article updates or changes'); ?><br>
                </div>
                <?= fa_image_tag('spinner', array('id' => 'article_favourite_indicator_'.$article->getId(), 'style' => 'display: none;', 'class' => 'fa-spin')); ?>
                <?php include_component('main/favouritetoggle', ['url' => make_url('publish_toggle_favourite_article', ['article_id' => $article->getID(), 'user_id' => $pachno_user->getID()]), 'include_user' => false, 'starred' => $pachno_user->isArticleStarred($article->getID())]); ?>
            <?php endif; ?>
        </div>
        <div class="dropper-container">
            <a class="button dropper icon secondary"><?= fa_image_tag('ellipsis-v'); ?></a>
            <div class="dropdown-container">
                <div class="list-mode">
                    <div class="header"><?= __('Download this page'); ?></div>
                    <a href="javascript:void(0);" class="list-item disabled">
                        <?= fa_image_tag('file-pdf', ['class' => 'icon'], 'far'); ?>
                        <span class="name"><?= __('Download as pdf'); ?></span>
                    </a>
                    <a href="javascript:void(0);" class="list-item disabled">
                        <?= fa_image_tag('file-word', ['class' => 'icon'], 'far'); ?>
                        <span class="name"><?= __('Download as .odt'); ?></span>
                    </a>
                    <a href="javascript:void(0);" class="list-item disabled">
                        <?= fa_image_tag('file-word', ['class' => 'icon'], 'far'); ?>
                        <span class="name"><?= __('Download as .docx'); ?></span>
                    </a>
                    <?php if ($article->canEdit()): ?>
                        <div class="list-item separator"></div>
                        <a href="javascript:void(0);" class="list-item disabled">
                            <?= fa_image_tag('file', ['class' => 'icon'], 'far'); ?>
                            <span class="name"><?= __('Convert to template'); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if ($pachno_user->canCreateArticlesInProject($article->getProject())): ?>
                        <div class="list-item separator"></div>
                        <a href="javascript:void(0);" class="list-item trigger-backdrop" data-url="<?php echo make_url('get_partial_for_backdrop', ['key' => 'publish_edit_redirect_article', 'redirect_article_id' => $article->getID(), 'project_id' => Context::isProjectContext() ? Context::getCurrentProject()->getID() : 0]); ?>">
                            <?= fa_image_tag('link', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Create named link'); ?></span>
                        </a>
                        <div class="list-item trigger-copy-popup">
                            <?= fa_image_tag('copy', ['class' => 'icon'], 'far'); ?>
                            <span class="name"><?= __('Copy page'); ?></span>
                        </div>
                    <?php endif; ?>
                    <a href="javascript:void(0);" class="list-item disabled">
                        <?= fa_image_tag('history', ['class' => 'icon']); ?>
                        <span class="name"><?= __('History'); ?></span>
                    </a>
                    <?php if ($article->canEdit()): ?>
                        <a href="javascript:void(0);" class="list-item disabled">
                            <?= fa_image_tag('lock', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Permissions'); ?></span>
                        </a>
                        <div class="list-item separator"></div>
                        <?php if ($article->isMainPage()): ?>
                            <div class="list-item disabled">
                                <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                                <span class="name"><?= __('This page cannot be deleted'); ?></span>
                            </div>
                        <?php else: ?>
                            <a href="javascript:void(0);" onclick="Pachno.UI.Dialog.show('<?= ($article->isCategory()) ? __('Delete this category?') : __('Delete this page?'); ?>', '<?= $article->isCategory() ? __('Do you really want to delete this category?') : __('Do you really want to delete this page?'); ?>', {yes: {click: function () { Pachno.trigger(Pachno.EVENTS.article.delete, { url: '<?= make_url('publish_article_delete', ['article_id' => $article->getID()]); ?>', article_id: <?= $article->getID(); ?> }) }}, no: {click: Pachno.UI.Dialog.dismiss}})" class="list-item danger">
                                <?= fa_image_tag('times', ['class' => 'icon']); ?>
                                <span class="name"><?= $article->isCategory() ? __('Delete this category') : __('Delete this page'); ?></span>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
