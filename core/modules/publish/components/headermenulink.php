<?php

/**
 * @var \pachno\core\framework\Routing $pachno_routing
 */

use pachno\core\entities\tables\Articles;

$url = Articles::getTable()->getArticleByName('Main Page')->getLink();

?>
<a class="<?php if ($pachno_routing->getCurrentRoute()->getModuleName() === 'publish' && !\pachno\core\framework\Context::isProjectContext()) echo 'selected'; ?>" href="<?= $url; ?>">
    <?= fa_image_tag('book', ['class' => 'icon']); ?>
    <span class="name"><?= __('Documentation'); ?></span>
</a>
