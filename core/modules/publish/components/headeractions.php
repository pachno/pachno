<?php

use pachno\core\entities\Article;
use pachno\core\framework\Context;

/**
 * @var Article $article
 */

?>
<div class="action-container">
    <?php if ($article->canEdit()): ?>
        <a href="<?= $article->getLink('edit'); ?>" class="button secondary">
            <?= fa_image_tag('edit', ['class' => 'icon']); ?>
            <span class="name"><?= __('Edit'); ?></span>
        </a>
        <?php if ($article->isMainPage()): ?>
            <?php if ($article->getProject() instanceof \pachno\core\entities\Project): ?>
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
                    <?= fa_image_tag('plus', ['class' => 'icon']); ?>
                    <span class="name"><?= __('Create'); ?></span>
                </a>
                <div class="dropdown-container">
                    <div class="list-mode">
                        <?php if ($article->isCategory()): ?>
                            <?php if ($article->getProject() instanceof \pachno\core\entities\Project): ?>
                                <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID(), 'project_key' => $article->getProject()->getKey()]); ?>" class="list-item">
                                    <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                    <span class="name"><?= __('Create a page'); ?></span>
                                </a>
                                <span class="separator"></span>
                                <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID(), 'project_key' => $article->getProject()->getKey()]); ?>" class="list-item">
                                    <?= fa_image_tag('layer-group', ['class' => 'icon']); ?>
                                    <span class="name"><?= __('Create a sub-category'); ?></span>
                                </a>
                            <?php else: ?>
                                <a href="<?= make_url('publish_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID()]); ?>" class="list-item">
                                    <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                    <span class="name"><?= __('Create a page'); ?></span>
                                </a>
                                <span class="separator"></span>
                                <a href="<?= make_url('publish_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID()]); ?>" class="list-item">
                                    <?= fa_image_tag('layer-group', ['class' => 'icon']); ?>
                                    <span class="name"><?= __('Create a sub-category'); ?></span>
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if (Context::isProjectContext()): ?>
                                <?php if ($article->getParentArticle() instanceof Article): ?>
                                    <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getParentArticle()->getID(), 'project_key' => $article->getProject()->getKey()]); ?>" class="list-item">
                                        <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                        <span class="name"><?= __('Create another page here'); ?></span>
                                    </a>
                                <?php endif; ?>
                                <?php if (!$article->isMainPage()): ?>
                                    <a href="<?= make_url('publish_project_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID(), 'project_key' => $article->getProject()->getKey()]); ?>" class="list-item">
                                        <?= fa_image_tag('book', ['class' => 'icon']); ?>
                                        <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                        <span class="name"><?= __('Create new page under this page'); ?></span>
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($article->getParentArticle() instanceof Article): ?>
                                    <a href="<?= make_url('publish_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getParentArticle()->getID()]); ?>" class="list-item">
                                        <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                        <span class="name"><?= __('Create another page here'); ?></span>
                                    </a>
                                <?php endif; ?>
                                <?php if (!$article->isMainPage()): ?>
                                    <a href="<?= make_url('publish_article_edit', ['article_id' => 0, 'parent_article_id' => $article->getID()]); ?>" class="list-item">
                                        <?= fa_image_tag('book', ['class' => 'icon']); ?>
                                        <?= fa_image_tag('file-alt', ['class' => 'icon'], 'far'); ?>
                                        <span class="name"><?= __('Create new page under this page'); ?></span>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($article->getID()): ?>
        <div class="dropper-container">
            <a class="button dropper icon secondary"><?= fa_image_tag('ellipsis-v'); ?></a>
            <div class="dropdown-container">
                <div class="list-mode">
                    <a href="<?= $article->getLink('history'); ?>" class="list-item disabled">
                        <?= fa_image_tag('history', ['class' => 'icon']); ?>
                        <span class="name"><?= __('History'); ?></span>
                    </a>
                    <?php if ($article->canEdit()): ?>
                        <a href="<?= $article->getLink('permissions'); ?>" class="list-item disabled">
                            <?= fa_image_tag('lock', ['class' => 'icon']); ?>
                            <span class="name"><?= __('Permissions'); ?></span>
                        </a>
                    <?php endif; ?>
                    <div class="list-item separator"></div>
                    <a href="<?= $article->getLink('edit'); ?>?copy=true" class="list-item disabled">
                        <?= fa_image_tag('copy', ['class' => 'icon'], 'far'); ?>
                        <span class="name"><?= __('Copy article'); ?></span>
                    </a>
                    <div class="list-item separator"></div>
                    <?php if ($article->canDelete()): ?>
                        <?= javascript_link_tag(fa_image_tag('times', ['class' => 'icon']) . '<span class="name">'.__('Delete this article').'</span>', ['onclick' => "Pachno.UI.Dialog.show('".__('Please confirm')."', '".__('Do you really want to delete this article?')."', {yes: {click: function () { Pachno.Main.deleteArticle('".make_url('publish_article_delete', ['article_id' => $article->getID()])."') }}, no: {click: Pachno.UI.Dialog.dismiss}})", 'class' => 'list-item danger disabled']); ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
