<?php

    /**
     * @var \pachno\core\entities\AgileBoard $board
     */

?>
<div class="fancy-tabs">
    <a class="tab" href="<?= make_url('agile_board', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>">
        <span class="icon"><?= fa_image_tag('stream'); ?></span>
        <span class="name"><span class="label-generic"><?= __('Planning'); ?></span><span class="label-scrum"><?= __('Backlog'); ?></span><span class="label-kanban"><?= __('Backlog'); ?></span></span>
    </a>
    <a class="tab selected" href="<?= make_url('agile_whiteboard', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>">
        <span class="icon"><?= fa_image_tag('columns'); ?></span>
        <span class="name"><?= __('Board view'); ?></span>
    </a>
</div>