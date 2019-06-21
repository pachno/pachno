<div class="fancy-dropdown-container filter" data-filterkey="<?php echo $filter->getFilterKey(); ?>" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('All'); ?>">
    <div class="fancy-dropdown">
        <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][o]" value="<?php echo $filter->getOperator(); ?>">
        <label><?php echo __('Category'); ?></label>
        <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
        <div class="dropdown-container list-mode">
            <div class="header"><?php echo __('Filter on category'); ?></div>
            <div class="list-item filter-container">
                <input type="search" placeholder="<?php echo __('Filter values'); ?>">
            </div>
            <div class="interactive_menu_values">
                <?php foreach ($filter->getAvailableValues() as $category): ?>
                    <input type="checkbox" value="<?php echo $category->getID(); ?>" class="fancy-checkbox" name="fs[<?php echo $filter->getFilterKey(); ?>][v][<?php echo $category->getID(); ?>]" id="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $category->getID(); ?>" <?php if ($filter->hasValue($category->getID())) echo 'checked'; ?>>
                    <label for="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $category->getID(); ?>" class="list-item filtervalue">
                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                        <span class="name value"><?php echo __($category->getName()); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
