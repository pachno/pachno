<div class="fancy-dropdown-container filter " id="interactive_filter_<?php echo $filter->getFilterKey(); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][o]" value="<?php echo $filter->getOperator(); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][v]" value="" id="filter_<?php echo $filter->getFilterKey(); ?>_value_input">
    <div class="fancy-dropdown" data-default-label="<?php echo __('Any'); ?>">
        <label><?php

            switch ($filter->getFilterKey())
            {
                case 'owner_team':
                    echo __('Owned by team');
                    break;
                case 'assignee_team':
                    echo __('Assigned team');
                    break;
                default:
                    echo __($filter->getFilterTitle());
                    break;
            }

        ?></label>
        <span class="value"><?php if (!$filter->hasValue()) echo __('Any team'); ?></span>
        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
        <div class="dropdown-container list-mode">
            <div class="header"><?php echo __('Select team(s)'); ?></div>
            <div class="list-item filter-container">
                <input type="search" data-callback-url="<?php echo make_url('search_filter_findteams', array('filterkey' => $filter->getFilterKey())); ?>" placeholder="<?php echo __('Search for a team'); ?>">
            </div>
            <div class="interactive_menu_values">
                <div class="filter_callback_results">
                </div>
                <div class="interactive_menu_values filter_existing_values">
                    <?php foreach ($filter->getAvailableValues() as $team): ?>
                        <input type="checkbox" class="fancy-checkbox" value="<?php echo $team->getID(); ?>" name="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $team->getID(); ?>" data-text="<?php echo $team->getName(); ?>" id="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $team->getID(); ?>" <?php if ($filter->hasValue($team->getID())) echo 'checked'; ?>>
                        <label for="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $team->getID(); ?>" class="list-item filtervalue <?php if ($filter->hasValue($team->getID())) echo 'selected'; ?>">
                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                            <span class="name value"><?php echo $team->getName(); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="remove-button icon"><?php echo fa_image_tag('times'); ?></div>
    </div>
</div>
