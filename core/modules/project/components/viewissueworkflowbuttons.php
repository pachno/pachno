<?php

    use pachno\core\entities\Issue;

    /**
     * @var Issue $issue
     */

?>
<div id="workflow-actions" class="workflow-actions-container">
    <div id="workflow-list" class="workflow-list" data-issue-workflow-transitions-container data-issue-id="<?= $issue->getId(); ?>"></div>
    <button class="button secondary highlight trigger-start-time-tracking disabled" disabled>
        <?= fa_image_tag('play-circle', ['class' => 'icon']); ?>
        <span class="name"><?= __('Track time'); ?></span>
    </button>
</div>
