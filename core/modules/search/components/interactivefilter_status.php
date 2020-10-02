<div class="fancy-dropdown-container filter" data-filterkey="<?php echo $filter->getFilterKey(); ?>" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('All'); ?>">
    <div class="fancy-dropdown">
        <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][o]" value="<?php echo $filter->getOperator(); ?>">
        <label><?php

                switch ($filter->getFilterKey())
                {
                    case 'status':
                        echo __('Status');
                        break;
                    default:
                        echo __($filter->getFilterTitle());
                        break;
                }

        ?></label>
        <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
        <div class="dropdown-container list-mode">
            <div class="header"><?= ($filter->getFilterKey() == 'status') ? __('Filter on status') : __("Filter on %customfield", array('%customfield' => $filter->getFilterTitle())); ?></div>
            <div class="list-item filter-container">
                <input type="search" placeholder="<?php echo __('Filter values'); ?>">
            </div>
            <?php if ($filter->getFilterKey() == 'status'): ?>
                <input type="radio" value="open" class="fancy-checkbox" name="fs[<?php echo $filter->getFilterKey(); ?>][v]" id="filters_<?php echo $filter->getFilterKey(); ?>_value_open" <?php if ($filter->hasValue('open')) echo 'checked'; ?>>
                <label for="filters_<?php echo $filter->getFilterKey(); ?>_value_open" data-value="open" class="list-item filtervalue <?php if ($filter->hasValue('open')) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                    <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                    <span class="name value"><?php echo __('Only open issues'); ?></span>
                </label>
                <input type="radio" value="closed" class="fancy-checkbox" name="fs[<?php echo $filter->getFilterKey(); ?>][v]" id="filters_<?php echo $filter->getFilterKey(); ?>_value_closed" <?php if ($filter->hasValue('closed')) echo 'checked'; ?>>
                <label for="filters_<?php echo $filter->getFilterKey(); ?>_value_closed" data-value="closed" class="list-item filtervalue <?php if ($filter->hasValue('closed')) echo ' selected'; ?>" data-exclusive data-selection-group="1">
                    <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                    <span class="name value"><?php echo __('Only closed issues'); ?></span>
                </label>
                <div class="list-item separator"></div>
            <?php endif; ?>
            <div class="filter-values-container">
                <?php foreach ($filter->getAvailableValues() as $status): ?>
                    <input type="checkbox" class="fancy-checkbox" name="fs[<?php echo $filter->getFilterKey(); ?>][v]" value="<?= $status->getID(); ?>" id="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $status->getID(); ?>" <?php if ($filter->hasValue($status->getID())) echo ' checked'; ?>>
                    <label class="list-item filtervalue" for="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $status->getID(); ?>">
                        <span class="name value"><?php echo __($status->getName()); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
