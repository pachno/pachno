<?php

    use pachno\core\entities\BoardColumn;

    /**
     * @var BoardColumn $column
     */

?>
<div class="column form-container" data-column-id="<?= $column->getID(); ?>" data-min-workitems="<?php echo $column->getMinWorkitems(); ?>" data-max-workitems="<?php echo $column->getMaxWorkitems(); ?>" data-status-ids="<?= implode(',', $column->getStatusIds()); ?>">
    <div class="row" title="<?php echo $column->getName(); ?>">
        <form class="name" method="POST" action="<?php echo make_url('agile_whiteboardcolumn', array('project_key' => $column->getBoard()->getProject()->getKey(), 'board_id' => $column->getBoard()->getID(), 'column_id' => $column->getID())); ?>" data-interactive-form id="column_<?= $column->getID(); ?>_header_form">
            <div class="form-row">
                <input type="text" name="name" value="<?= $column->getName(); ?>" class="invisible column-header" id="column_<?= $column->getID(); ?>_name_input">
                <label for="column_<?= $column->getID(); ?>_name_input"><?= __('Column name'); ?></label>
            </div>
        </form>
        <div class="statuses-badge">
            <?php if (count($column->getStatusIds()) > 1): ?>
                <span class="column-count primary">-</span>
            <?php endif; ?>
            <?php foreach ($column->getStatusIds() as $status_id): ?>
                <?php if (isset($statuses[$status_id]) && $statuses[$status_id] instanceof \pachno\core\entities\Datatype): ?>
                    <div class="column-count status-badge status-<?php echo $status_id; ?>" style="background-color: <?php echo $statuses[$status_id]->getColor(); ?>;color: <?php echo $statuses[$status_id]->getTextColor(); ?>;" title="<?php echo $statuses[$status_id]->getName(); ?>" data-status-id="<?php echo $status_id; ?>">-</div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
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