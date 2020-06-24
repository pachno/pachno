<?php

    use pachno\core\entities\AgileBoard;
    use pachno\core\entities\BoardColumn;

    /**
     * @var BoardColumn $column
     * @var AgileBoard $board
     */

?>
<div class="column form-container" id="add-next-column-input-container">
    <div class="row">
        <div class="form name">
            <div class="form-row">
                <span class="input invisible trigger-whiteboard-toggle-add-next-column">
                    <span class="placeholder"><?= fa_image_tag('plus'); ?><span><?= __('Add another column'); ?></span></span>
                </span>
            </div>
        </div>
    </div>
    <div class="card">
        <form method="POST" action="<?= make_url('agile_whiteboardcolumn', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID(), 'column_id' => 0]); ?>" id="add-another-column-form" data-simple-submit>
            <div class="form-row">
                <input type="text" name="name" id="next-column-name" placeholder="<?= __('Give your column a name, like "Todo"'); ?>">
            </div>
            <div class="form-row">
                <label><?php echo __('Status for this column'); ?></label>
                <div class="fancy-dropdown-container" data-default-label="<?= __('Pick a status for this column'); ?>">
                    <div class="fancy-dropdown">
                        <span class="value"></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <?php foreach ($board->getProject()->getAvailableStatuses() as $index => $status): ?>
                                <input type="checkbox" value="<?php echo $status->getID(); ?>" name="status_ids[<?= $status->getID(); ?>]" id="add_next_column_status_<?php echo $status->getID(); ?>" class="fancy-checkbox" <?php if ($index == 0) echo 'checked'; ?> data-status-id="<?= $status->getID(); ?>" <?php if (in_array($status->getId(), $board->getStatusIds())) echo 'disabled'; ?>>
                                <label for="add_next_column_status_<?php echo $status->getID(); ?>" class="list-item <?php if (in_array($status->getId(), $board->getStatusIds())) echo 'disabled'; ?>">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <span class="name value"><?php echo __($status->getName()); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row submit-container">
                <button type="button" class="secondary trigger-whiteboard-toggle-add-next-column"><?= __('Cancel'); ?></button>
                <button type="submit" class="primary secondary highlight">
                    <span class="name"><?= __('Save'); ?></span>
                    <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                </button>
            </div>
        </form>
    </div>
</div>
