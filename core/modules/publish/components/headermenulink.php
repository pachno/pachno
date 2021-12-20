<?php

    /**
     * @var \pachno\core\framework\Routing $pachno_routing
     * @var \pachno\core\entities\User $pachno_user
     */

    use pachno\core\entities\tables\Articles;
    use pachno\core\framework\Context;

    $url = Articles::getTable()->getOrCreateMainPage()->getLink();

?>
<?php if ($pachno_user->canReadArticlesInProject(Context::getCurrentProject())): ?>
    <a class="<?php if ($pachno_routing->getCurrentRoute()->getModuleName() === 'publish') echo 'selected'; ?>" href="<?= $url; ?>">
        <?= fa_image_tag('book', ['class' => 'icon']); ?>
        <span class="name"><?= __('Documentation'); ?></span>
    </a>
<?php endif; ?>