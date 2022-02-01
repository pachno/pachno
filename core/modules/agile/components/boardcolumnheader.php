<?php
    
    use pachno\core\entities\AgileBoard;
    use pachno\core\entities\BoardColumn;
    use pachno\core\entities\Status;
    
    /**
     * @var BoardColumn $column
     * @var AgileBoard $board
     */

?>
<div class="column form-container" id="whiteboard-column-header-<?= $column->getID(); ?>" data-whiteboard-column data-column-id="<?= $column->getID(); ?>" data-min-workitems="<?php echo $column->getMinWorkitems(); ?>" data-max-workitems="<?php echo $column->getMaxWorkitems(); ?>" data-status-ids="<?= implode(',', $column->getStatusIds()); ?>" data-url="<?= $column->getUrl(); ?>" data-sort-order="<?= $column->getSortOrder(); ?>">
    <div class="row" title="<?php echo $column->getName(); ?>">
        <form class="name" method="POST" action="<?php echo make_url('agile_whiteboardcolumn', array('project_key' => $column->getBoard()->getProject()->getKey(), 'board_id' => $column->getBoard()->getID(), 'column_id' => $column->getID())); ?>" data-interactive-form id="column_<?= $column->getID(); ?>_header_form" data-column-form>
            <div class="form-row name-container">
                <input type="text" name="name" value="<?= $column->getName(); ?>" class="invisible column-header" id="column_<?= $column->getID(); ?>_name_input">
                <label for="column_<?= $column->getID(); ?>_name_input"><?= __('Column name'); ?></label>
            </div>
            <div class="form-row">
                <div class="statuses-badge">
                    <?php foreach ($column->getStatusIds() as $status_id): ?>
                        <?php if (isset($statuses[$status_id]) && $statuses[$status_id] instanceof Status): ?>
                            <div class="column-count status-badge status-<?php echo $status_id; ?>" style="background-color: <?php echo $statuses[$status_id]->getColor(); ?>;color: <?php echo $statuses[$status_id]->getTextColor(); ?>;" title="<?php echo $statuses[$status_id]->getName(); ?>" data-status-id="<?php echo $status_id; ?>">-</div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="form-row">
                <div class="dropper-container settings">
                    <button class="button dropper icon" type="button"><?php echo fa_image_tag('ellipsis-v'); ?></button>
                    <div class="dropdown-container">
                        <div class="list-mode">
                            <a class="list-item <?php if ($column->getSortOrder() > 1): ?>trigger-move-column-left<?php else: ?>disabled<?php endif; ?>" href="javascript:void(0);">
                                <?= fa_image_tag('arrow-left', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Move column left'); ?></span>
                            </a>
                            <a class="list-item <?php if ($column->getSortOrder() < count($board->getColumns())): ?>trigger-move-column-right<?php else: ?>disabled<?php endif; ?>" href="javascript:void(0);">
                                <?= fa_image_tag('arrow-right', ['class' => 'icon']); ?>
                                <span class="name"><?= __('Move column right'); ?></span>
                            </a>
                            <div class="list-item separator"></div>
                            <div class="list-item header">
                                <?= __('Status for this column'); ?>
                            </div>
                            <?php foreach ($board->getProject()->getAvailableStatuses() as $status_id => $status): ?>
                                <input type="checkbox" value="<?php echo $status->getID(); ?>" name="status_ids[<?= $status->getID(); ?>]" id="column_<?= $column->getId(); ?>_status_<?php echo $status->getID(); ?>" class="fancy-checkbox" <?php if (in_array($status_id, $column->getStatusIds())) echo 'checked'; ?> data-status-id="<?= $status->getID(); ?>" <?php if (in_array($status->getId(), $board->getStatusIds()) && !in_array($status_id, $column->getStatusIds())) echo 'disabled'; ?>>
                                <label for="column_<?= $column->getId(); ?>_status_<?php echo $status->getID(); ?>" class="list-item <?php if (in_array($status->getId(), $board->getStatusIds()) && !in_array($status_id, $column->getStatusIds())) echo 'disabled'; ?>">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <span class="status-badge" style="background-color: <?php echo $status->getColor(); ?>;color: <?php echo $status->getTextColor(); ?>;">
                                        <span><?php echo __($status->getName()); ?></span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                            <div class="list-item separator"></div>
                            <a href="javascript:void(0);" class="list-item danger trigger-delete-column" data-column-id="<?= $column->getId(); ?>" data-url="<?= $column->getUrl(); ?>">
                                <span class="icon"><?= fa_image_tag('times'); ?></span>
                                <span class="name"><?= __('Delete this column'); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php if ($column->getMinWorkitems() || $column->getMaxWorkitems()): ?>
        <?php if ($column->getMinWorkitems() && $column->getMaxWorkitems()): ?>
            <span class="column-count workitems"><?php echo __('%count (min %min_workitems - max %max_workitems)', array('%count' => '<span class="count"></span>', '%min_workitems' => $column->getMinWorkitems(), '%max_workitems' => $column->getMaxWorkitems())); ?></span>
        <?php elseif ($column->getMinWorkitems()): ?>
            <span class="column-count workitems"><?php echo __('%count (min %min_workitems)', array('%count' => '<span class="count"></span>', '%min_workitems' => $column->getMinWorkitems())); ?></span>
        <?php elseif ($column->getMaxWorkitems()): ?>
            <span class="column-count workitems"><?php echo __('%count of max %max_workitems', array('%count' => '<span class="count"></span>', '%max_workitems' => $column->getMaxWorkitems())); ?></span>
        <?php endif; ?>
        <?php if ($column->getMinWorkitems()): ?>
            <span class="column-count under"><?php echo __('%count (under %min_workitems)', array('%count' => '<span class="under_count"></span>', '%min_workitems' => $column->getMinWorkitems())); ?></span>
        <?php endif; ?>
        <?php if ($column->getMaxWorkitems()): ?>
            <span class="column-count over"><?php echo __('%count (over %max_workitems)', array('%count' => '<span class="over_count"></span>', '%max_workitems' => $column->getMaxWorkitems())); ?></span>
        <?php endif; ?>
    <?php endif; ?>
</div>