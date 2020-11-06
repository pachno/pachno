<div class="fancy-dropdown-container filter " id="interactive_filter_<?php echo $filter->getFilterKey(); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][o]" value="<?php echo $filter->getOperator(); ?>">
    <div class="fancy-dropdown" data-default-label="<?php echo __('Any'); ?>">
        <label><?php

            switch ($filter->getFilterKey())
            {
                case 'posted_by':
                    echo __('Posted by');
                    break;
                case 'owner_user':
                    echo __('Owned by user');
                    break;
                case 'assignee_user':
                    echo __('Assigned user');
                    break;
                default:
                    echo __($filter->getFilterTitle());
                    break;
            }

        ?></label>
        <span class="value"><?php if (!$filter->hasValue()) echo __('Anyone'); ?></span>
        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
        <div class="dropdown-container list-mode">
            <div class="header"><?php echo __('Select user(s)'); ?></div>
            <div class="list-item filter-container">
                <input type="search" data-callback-url="<?php echo make_url('search_filter_findusers', array('filterkey' => $filter->getFilterKey())); ?>" placeholder="<?php echo __('Search for a user'); ?>">
            </div>
            <div class="interactive_menu_values">
                <div class="filter_callback_results">
                </div>
                <div class="interactive_menu_values filter_existing_values">
                    <?php foreach ($filter->getAvailableValues() as $user): ?>
                        <input type="checkbox" value="<?php echo $user->getID(); ?>" class="fancy-checkbox" name="fs[<?php echo $filter->getFilterKey(); ?>][v][<?php echo $user->getID(); ?>]" data-text="<?php echo ($user->getID() == $pachno_user->getID()) ? __('Yourself') : $user->getNameWithUsername(); ?>" id="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $user->getID(); ?>" <?php if ($filter->hasValue($user->getID())) echo 'checked'; ?>>
                        <label for="filters_<?php echo $filter->getFilterKey(); ?>_value_<?php echo $user->getID(); ?>" class="list-item filtervalue">
                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                            <span class="name value"><?php echo ($user->getID() == $pachno_user->getID()) ? __('Yourself') : $user->getNameWithUsername(); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="remove-button icon"><?= fa_image_tag('times'); ?></div>
    </div>
</div>
