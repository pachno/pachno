<?php
    $key = $filter->getFilterKey();
    switch ($key)
    {
        case 'priority':
            $title = __('Priority');
            $description = __('Filter on priority');
            break;
        case 'resolution':
            $title = __('Resolution');
            $description = __('Filter on resolution');
            break;
        case 'severity':
            $title = __('Severity');
            $description = __('Filter on severity');
            break;
        case 'reproducability':
            $title = __('Reproducability');
            $description = __('Filter on reproducability');
            break;
        default:
            $title = __($filter->getFilterTitle());
            $description = __("Filter on %customfield", array('%customfield' => $filter->getFilterTitle()));
            break;
    }
?>
<div class="fancy-dropdown-container filter" id="interactive_filter_<?php echo $filter->getFilterKey(); ?>" data-filterkey="<?php echo $filter->getFilterKey(); ?>" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('Any'); ?>">
    <div class="fancy-dropdown">
        <input type="hidden" name="fs[<?php echo $key; ?>][o]" value="<?php echo $filter->getOperator(); ?>">
        <input type="hidden" name="fs[<?php echo $key; ?>][v]" value="" id="filter_<?php echo $key; ?>_value_input">
        <label><?php echo $title; ?></label>
        <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
        <div class="dropdown-container list-mode">
            <div class="header"><?php echo $description; ?></div>
            <div class="list-item filter-container">
                <input type="search" placeholder="<?php echo __('Filter values'); ?>">
            </div>
            <div class="interactive_menu_values">
                <?php foreach ($filter->getAvailableValues() as $value): ?>
                    <input type="checkbox" class="fancy-checkbox" value="<?php echo $value->getID(); ?>"
                           name="filters_<?php echo $key; ?>_value_<?php echo $value->getID(); ?>"
                           data-text="<?php echo __($value->getName()); ?>"
                           id="filters_<?php echo $key; ?>_value_<?php echo $value->getID(); ?>" <?php if ($filter->hasValue($value->getID())) echo 'checked'; ?>>
                    <label for="filters_<?php echo $key; ?>_value_<?php echo $value->getID(); ?>" class="list-item filtervalue<?php if ($filter->hasValue($value->getID())) echo ' selected'; ?>">
                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                        <span class="name value"><?php echo __($value->getName()); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="remove-button icon"><?php echo fa_image_tag('times'); ?></div>
    </div>
</div>
