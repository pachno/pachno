<div class="fancy-dropdown-container filter " id="interactive_filter_<?php echo $filter->getFilterKey(); ?>" data-filterkey="<?php echo $filter->getFilterKey(); ?>" data-value="<?php echo $filter->getValue(); ?>" data-all-value="<?= __('All'); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][o]" value="<?php echo $filter->getOperator(); ?>">
    <input type="hidden" name="fs[<?php echo $filter->getFilterKey(); ?>][v]" value="" id="filter_<?php echo $filter->getFilterKey(); ?>_value_input">
    <div class="fancy-dropdown">
        <label><?php

                switch ($filter->getFilterKey())
                {
                    case 'build':
                        echo __('Affects release(s)');
                        break;
                    case 'component':
                        echo __('Affects component(s)');
                        break;
                    case 'edition':
                        echo __('Affects edition(s)');
                        break;
                    case 'milestone':
                        echo __('Targetted milestone(s)');
                        break;
                    default:
                        echo __($filter->getFilterTitle());
                        break;
                }

        ?></label>
        <span class="value"><?php if (!$filter->hasValue()) echo __('Any'); ?></span>
        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
        <div class="dropdown-container list-mode">
            <div class="header"><?php

                    switch ($filter->getFilterKey())
                    {
                        case 'build':
                            echo __('Filter on affected release(s)');
                            break;
                        case 'component':
                            echo __('Filter on affected component(s)');
                            break;
                        case 'edition':
                            echo __('Filter on affected edition(s)');
                            break;
                        case 'milestone':
                            echo __('Filter on targetted milestone(s)');
                            break;
                        default:
                            echo __("Filter on %customfield", array('%customfield' => $filter->getFilterTitle()));
                            break;
                    }

            ?></div>
            <div class="list-item filter-container">
                <input type="search" placeholder="<?php echo __('Filter values'); ?>">
            </div>
            <div class="interactive_values_container">
                <div class="interactive_menu_values">
                    <?php include_component('search/interactivefilterdynamicchoicelist', array('filter' => $filter, 'items' => $filter->getAvailableValues())); ?>
                </div>
            </div>
        </div>
        <div class="remove-button"><?php echo fa_image_tag('times'); ?></div>
    </div>
</div>
