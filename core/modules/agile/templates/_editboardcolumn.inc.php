<?php

    use pachno\core\entities\BoardColumn,
        pachno\core\entities\AgileBoard;

    if (! isset($column_id)) $column_id = $column->getColumnOrRandomID();

?>
<div id="editagileboard_column_<?php echo $column_id; ?>" class="edit-column column">
    <div class="form-container">
        <div class="form-row header">
            <div class="draggable"><?php echo fa_image_tag('arrows-alt'); ?></div>
            <a class="remover button secondary icon" href="javascript:void(0);" onclick="$(this).up('.column').remove();"><?php echo fa_image_tag('times'); ?></a>
            <input type="hidden" name="columns[<?php echo $column_id; ?>][column_id]" value="<?php echo ($column->getID()) ? $column->getID() : ''; ?>">
            <input type="hidden" class="sortorder" name="columns[<?php echo $column_id; ?>][sort_order]" value="<?php echo $column->getSortOrder(); ?>">
        </div>
        <div class="form-row">
            <input type="text" class="column-name" name="columns[<?php echo $column_id; ?>][name]" id="boardcolumn_<?php echo $column_id; ?>_name_input" value="<?php echo \pachno\core\framework\Response::escape($column->getName()); ?>" placeholder="<?php echo __('Column status (ex: New, Done)'); ?>">
            <label for="boardcolumn_<?php echo $column_id; ?>_name_input"><?php echo __('Column name'); ?></label>
        </div>
        <?php if ($column->getBoard()->getType() == AgileBoard::TYPE_KANBAN): ?>
            <div class="form-row">
                <input type="text" class="column-workload" name="columns[<?php echo $column_id; ?>][min_workitems]" id="boardcolumn_<?php echo $column_id; ?>_min_workitems_input" value="<?php echo $column->getMinWorkitems(); ?>" placeholder="0">
                <label for="boardcolumn_<?php echo $column_id; ?>_min_workitems_input" class="workload-label"><?php echo __('Min workload'); ?></label>
                <input type="text" class="column-workload" name="columns[<?php echo $column_id; ?>][max_workitems]" id="boardcolumn_<?php echo $column_id; ?>_max_workitems_input" value="<?php echo $column->getMaxWorkitems(); ?>" placeholder="0">
                <label for="boardcolumn_<?php echo $column_id; ?>_max_workitems_input" class="workload-label"><?php echo __('Max workload'); ?></label>
            </div>
        <?php endif; ?>
        <div class="form-row">
            <div class="fancydropdown-container">
                <div class="fancydropdown" id="boardcolumn_<?php echo $column_id; ?>_status" data-filterkey="editagileboard_column_<?php echo $column_id; ?>_status" data-value="<?php echo join(',', $column->getStatusIds()); ?>">
                    <label><?php echo __('Status(es)'); ?></label>
                    <span class="value"></span>
                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                    <div class="dropdown-container list-mode from-left">
                        <?php foreach ($statuses as $status): ?>
                            <input type="checkbox" value="<?php echo $status->getID(); ?>" name="columns[<?php echo $column_id; ?>][status_ids][<?php echo $status->getID(); ?>]" id="editagileboard_column_<?php echo $column_id; ?>_statuss_<?php echo $status->getID(); ?>" class="fancycheckbox" <?php if ($column->hasStatusId($status->getID())) echo 'checked'; ?>>
                            <label for="editagileboard_column_<?php echo $column_id; ?>_statuss_<?php echo $status->getID(); ?>" class="list-item">
                                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                <span class="name value"><?php echo __($status->getName()); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>