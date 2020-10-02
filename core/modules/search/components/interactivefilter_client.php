<div class="filter interactive_dropdown" id="interactive_filter_<?php echo $filter->getFilterKey(); ?>" data-filterkey="<?php echo $filter->getFilterKey(); ?>" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?php echo __('Anyone'); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][o]" value="<?php echo $filter->getOperator(); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][v]" value="" id="filter_<?php echo $filter->getFilterKey(); ?>_value_input">
    <label><?php echo __($filter->getFilterTitle()); ?></label>
    <span class="value"><?php if (!$filter->hasValue()) echo __('Any client'); ?></span>
    <div class="interactive_menu">
        <h1><?php echo __('Select client(s)'); ?></h1>
        <input type="search" data-callback-url="<?php echo make_url('search_filter_findclients', array('filterkey' => $filter->getFilterKey())); ?>" placeholder="<?php echo __('Search for a client'); ?>"><?php echo image_tag('spinning_16.gif', array('class' => 'filter_indicator')); ?>
        <div class="interactive_values_container">
            <ul class="interactive_menu_values filter_callback_results">
            </ul>
            <ul class="interactive_menu_values filter_existing_values">
                <?php foreach ($filter->getAvailableValues() as $client): ?>
                    <input type="checkbox" class="fancy-checkbox" value="<?php echo $client->getID(); ?>" name="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $client->getID(); ?>" data-text="<?php echo $client->getName(); ?>" id="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $client->getID(); ?>" <?php if ($filter->hasValue($client->getID())) echo 'checked'; ?>>
                    <label for="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $client->getID(); ?>" class="list-item filtervalue <?php if ($filter->hasValue($client->getID())) echo 'selected'; ?>">
                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                        <span class="name value"><?php echo $client->getName(); ?></span>
                    </label>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="remove-button"><?php echo fa_image_tag('times'); ?></div>
</div>
