<div class="column" data-min-workitems="<?php echo $column->getMinWorkitems(); ?>" data-max-workitems="<?php echo $column->getMaxWorkitems(); ?>">
    <div class="header">
        <div class="name" title="<?php echo $column->getName(); ?>">
            <span><?php echo $column->getName(); ?></span>
            <div class="statuses-badge">
                <span class="column-count primary">-</span>
                <?php foreach ($column->getStatusIds() as $status_id): ?>
                    <?php if (isset($statuses[$status_id]) && $statuses[$status_id] instanceof \pachno\core\entities\Datatype): ?>
                        <div class="status-badge status-<?php echo $status_id; ?>" style="background-color: <?php echo $statuses[$status_id]->getColor(); ?>;color: <?php echo $statuses[$status_id]->getTextColor(); ?>;" title="<?php echo $statuses[$status_id]->getName(); ?>" data-status-id="<?php echo $status_id; ?>">-</div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if ($column->getMinWorkitems() && $column->getMaxWorkitems()): ?>
            <span class="column-count workitems"><?php echo __('%count (min %min_workitems - max %max_workitems)', array('%count' => '<span class="count"></span>', '%min_workitems' => $column->getMinWorkitems(), '%max_workitems' => $column->getMaxWorkitems())); ?></span>
        <?php elseif ($column->getMinWorkitems()): ?>
            <span class="column-count workitems"><?php echo __('%count (min %min_workitems)', array('%count' => '<span class="count"></span>', '%min_workitems' => $column->getMinWorkitems())); ?></span>
        <?php elseif ($column->getMaxWorkitems()): ?>
            <span class="column-count workitems"><?php echo __('%count of max %max_workitems', array('%count' => '<span class="count"></span>', '%max_workitems' => $column->getMaxWorkitems())); ?></span>
        <?php endif; ?>
        <span class="column-count under"><?php echo __('%count (under %min_workitems)', array('%count' => '<span class="under_count"></span>', '%min_workitems' => $column->getMinWorkitems())); ?></span>
        <span class="column-count over"><?php echo __('%count (over %max_workitems)', array('%count' => '<span class="over_count"></span>', '%max_workitems' => $column->getMaxWorkitems())); ?></span>
    </div>
</div>