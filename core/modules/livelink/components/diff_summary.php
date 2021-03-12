<?php

    /** @var \pachno\core\helpers\Diffable $diffable */

?>
<?php if ($diffable->getLinesRemoved() || $diffable->getLinesAdded()): ?>
    <span class="item diff-summary">
        <span class="lines-removed" title="<?= __('%num line(s) removed', ['%num' => $diffable->getLinesRemoved()]); ?>">-<?= $diffable->getLinesRemoved(); ?></span>
        <span class="lines-added" title="<?= __('%num line(s) added', ['%num' => $diffable->getLinesAdded()]); ?>">+<?= $diffable->getLinesAdded(); ?></span>
    </span>
<?php endif; ?>
