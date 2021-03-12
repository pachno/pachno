<?php

    /** @var \pachno\core\entities\Commit $commit */
    /** @var \pachno\core\entities\Project $project */

?>
<div class="header">
    <?= \pachno\core\framework\Context::getI18n()->formatTime($commit->getDate(), 20); ?>
</div>