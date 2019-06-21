<?php

use pachno\core\entities\CustomDatatype;
use pachno\core\entities\SearchFilter;
use pachno\core\framework\Context;

if ($filter instanceof SearchFilter): ?>
    <?php

        switch ($filter->getFilterKey())
        {
            case 'project_id':
                ?>
                <?php if (Context::isProjectContext()): ?>
                    <input type="hidden" name="fs[project_id][o]" value="=">
                    <input type="hidden" name="fs[project_id][v]" value="<?= Context::getCurrentProject()->getID(); ?>" id="filter_project_id_value_input">
                <?php else: ?>
                    <div class="fancy-dropdown-container filter" data-filterkey="project_id">
                        <div class="fancy-dropdown">
                            <input type="hidden" name="fs[project_id][o]" value="<?= $filter->getOperator(); ?>">
                            <input type="hidden" name="fs[project_id][v]" value="" id="filter_project_id_value_input">
                            <label><?= __('Project(s)'); ?></label>
                            <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode filter-values-container">
                                <div class="header"><?= __('Choose issues from project(s)'); ?></div>
                                <div class="list-item filter-container">
                                    <input type="search" placeholder="<?= __('Filter values'); ?>">
                                </div>
                                <?php foreach ($filter->getAvailableValues() as $project): ?>
                                    <input type="checkbox" value="<?= $project->getID(); ?>" class="fancy-checkbox" name="filters_project_id_value_<?= $project->getID(); ?>" id="filters_project_id_value_<?= $project->getID(); ?>" <?php if ($filter->hasValue($project->getID())) echo 'checked'; ?>>
                                    <label for="filters_project_id_value_<?= $project->getID(); ?>" class="list-item filtervalue">
                                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                        <?php if ($project->hasSmallIcon()): ?>
                                            <span class="icon"><?= image_tag($project->getSmallIconName(), [], $project->hasSmallIcon()); ?></span>
                                        <?php endif; ?>
                                        <span class="name value"><?= $project->getName(); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php
                break;
            case 'issuetype':
                ?>
                <div class="fancy-dropdown-container filter" data-filterkey="issuetype" data-value="<?= $filter->getValue(); ?>" data-all-value="<?= __('All'); ?>">
                    <div class="fancy-dropdown">
                        <input type="hidden" name="fs[issuetype][o]" value="<?= $filter->getOperator(); ?>">
                        <label><?= __('Issuetype'); ?></label>
                        <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode from-left">
                            <div class="header"><?= __('Filter on issuetype'); ?></div>
                            <div class="list-item filter-container">
                                <input type="search" placeholder="<?= __('Filter values'); ?>">
                            </div>
                            <div class="interactive_values_container">
                                <?php foreach ($filter->getAvailableValues() as $issuetype): ?>
                                    <?php /** @var \pachno\core\entities\Issuetype $issuetype */ ?>
                                    <input type="checkbox" class="fancy-checkbox" value="<?= $issuetype->getID(); ?>" name="filters_issuetype_value_<?= $issuetype->getID(); ?>" id="filters_issuetype_value_<?= $issuetype->getID(); ?>" <?php if ($filter->hasValue($issuetype->getID())) echo 'checked'; ?>>
                                    <label for="filters_issuetype_value_<?= $issuetype->getID(); ?>" class="list-item filtervalue">
                                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                        <?= fa_image_tag($issuetype->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $issuetype->getIcon()]); ?>
                                        <span class="name value"><?= __($issuetype->getName()); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                break;
            case 'posted_by':
            case 'owner_user':
            case 'assignee_user':
                include_component('search/interactivefilter_user', compact('filter'));
                break;
            case 'owner_team':
            case 'assignee_team':
                include_component('search/interactivefilter_team', compact('filter'));
                break;
            case 'status':
                include_component('search/interactivefilter_status', compact('filter'));
                break;
            case 'category':
                include_component('search/interactivefilter_category', compact('filter'));
                break;
            case 'build':
            case 'component':
            case 'edition':
            case 'milestone':
                include_component('search/interactivefilter_affected', compact('filter'));
                break;
            case 'subprojects':
                ?>
                <div class="fancy-dropdown-container filter" id="interactive_filter_subprojects" data-filterkey="subprojects" data-value="<?= $filter->getValue(); ?>" data-all-value="<?= __('All'); ?>">
                    <div class="fancy-dropdown">
                        <input type="hidden" name="fs[subprojects][o]" value="<?= $filter->getOperator(); ?>">
                        <input type="hidden" name="fs[subprojects][v]" value="" id="filter_subprojects_value_input">
                        <label><?= __('Subproject(s)'); ?></label>
                        <span class="value"><?php if (!$filter->hasValue()) echo __('All'); ?></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <div class="header"><?= __('Include issues from subproject(s)'); ?></div>
                            <div class="list-item filter-container">
                                <input type="search" placeholder="<?= __('Filter values'); ?>">
                            </div>
                            <div class="filter-values-container">
                                <input type="checkbox" value="all" name="filters_subprojects_value_exclusive_all" class="fancy-checkbox" id="filters_subprojects_value_all" <?php if ($filter->hasValue('all')) echo 'checked'; ?>>
                                <label for="filters_subprojects_value_all" class="list-item filtervalue" data-exclusive data-selection-group="1" data-exclude-group="2">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <span class="name value"><?= __('All'); ?></span>
                                </label>
                                <input type="checkbox" value="none" name="filters_subprojects_value_exclusive_none" class="fancy-checkbox" id="filters_subprojects_value_none" <?php if ($filter->hasValue('none')) echo 'checked'; ?>>
                                <label for="filters_subprojects_value_none" class="list-item filtervalue" data-exclusive data-selection-group="1" data-exclude-group="2">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <span class="name value"><?= __('None'); ?></span>
                                </label>
                                <li class="list-item separator"></li>
                                <?php foreach ($filter->getAvailableValues() as $subproject): ?>
                                    <input type="checkbox" value="<?= $subproject->getID(); ?>" name="filters_subprojects_value_<?= $subproject->getID(); ?>" class="fancy-checkbox" id="filters_subprojects_value_<?= $subproject->getID(); ?>" <?php if ($filter->hasValue($subproject->getID())) echo 'checked'; ?>>
                                    <label for="filters_subprojects_value_<?= $subproject->getID(); ?>" class="list-item filtervalue" data-selection-group="2" data-exclude-group="1">
                                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                        <span class="name value"><?= $subproject->getName(); ?>&nbsp;&nbsp;<span class="faded_out"><?= $subproject->getKey(); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="remove-button"><?= fa_image_tag('times'); ?></div>
                    </div>
                </div>
                <?php
                break;
            case 'blocking':
                ?>
                <div class="fancy-dropdown-container filter" id="interactive_filter_blocking" data-filterkey="blocking" data-default-value="<?= __('Any'); ?>">
                    <div class="fancy-dropdown">
                        <input type="hidden" name="fs[blocking][o]" value="<?= $filter->getOperator(); ?>">
                        <label><?= __('Blocker status'); ?></label>
                        <span class="value"><?php if (!$filter->hasValue()) echo __('Any'); ?></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <div class="header"><?= __('Filter on blocker status'); ?></div>
                            <input type="radio" name="fs[blocking][v]" value="1" class="fancy-checkbox" id="filters_blocking_value_yes" <?php if ($filter->hasValue('1')) echo ' checked'; ?>>
                            <label class="list-item" for="filters_blocking_value_yes">
                                <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                <span class="name value"><?= __('Only blocker issues'); ?></span>
                            </label>
                            <input type="radio" name="fs[blocking][v]" value="0" class="fancy-checkbox" id="filters_blocking_value_no" <?php if ($filter->hasValue('0')) echo ' checked'; ?>>
                            <label class="list-item" for="filters_blocking_value_no">
                                <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                <span class="name value"><?= __('Not blocker issues'); ?></span>
                            </label>
                        </div>
                        <div class="remove-button"><?= fa_image_tag('times'); ?></div>
                    </div>
                </div>
                <?php
                break;
            case 'priority':
            case 'resolution':
            case 'reproducability':
            case 'severity':
                include_component('search/interactivefilter_choice', compact('filter'));
                break;
            case 'posted':
            case 'last_updated':
            case 'time_spent':
                include_component('search/interactivefilter_date', compact('filter'));
                break;
            case 'relation':
                ?>
                <div class="fancy-dropdown-container filter" id="interactive_filter_relation" data-filterkey="relation">
                    <div class="fancy-dropdown">
                        <input type="hidden" name="fs[relation][o]" value="<?= $filter->getOperator(); ?>">
                        <input type="hidden" name="fs[relation][v]" value="" id="filter_relation_value_input">
                        <label><?= __('Relation'); ?></label>
                        <span class="value"><?php if (!$filter->hasValue()) echo __('Any'); ?></span>
                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                        <div class="dropdown-container list-mode">
                            <div class="header"><?= __('Filter on relation'); ?></div>
                            <div class="interactive_menu_values">
                                <input type="checkbox" class="fancy-checkbox" value="<?= SearchFilter::FILTER_RELATION_ONLY_CHILD; ?>" name="filters_relation_value" data-text="<?= __('Only child issues'); ?>" id="filters_relation_value_<?= SearchFilter::FILTER_RELATION_ONLY_CHILD; ?>" <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_ONLY_CHILD)) echo 'checked'; ?>>
                                <label for="filters_relation_value_<?= SearchFilter::FILTER_RELATION_ONLY_CHILD; ?>" class="list-item filtervalue">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <span class="name value"><?= __('Only child issues'); ?></span>
                                </label>
                                <input type="checkbox" class="fancy-checkbox" value="<?= SearchFilter::FILTER_RELATION_WITHOUT_CHILD; ?>" name="filters_relation_value" data-text="<?= __('Without child issues'); ?>" id="filters_relation_value_<?= SearchFilter::FILTER_RELATION_WITHOUT_CHILD; ?>" <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_WITHOUT_CHILD)) echo 'checked'; ?>>
                                <label for="filters_relation_value_<?= SearchFilter::FILTER_RELATION_WITHOUT_CHILD; ?>" class="list-item filtervalue">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <span class="name value"><?= __('Without child issues'); ?></span>
                                </label>
                                <input type="checkbox" class="fancy-checkbox" value="<?= SearchFilter::FILTER_RELATION_ONLY_PARENT; ?>" name="filters_relation_value" data-text="<?= __('Only parent issues'); ?>" id="filters_relation_value_<?= SearchFilter::FILTER_RELATION_ONLY_PARENT; ?>" <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_ONLY_PARENT)) echo 'checked'; ?>>
                                <label for="filters_relation_value_<?= SearchFilter::FILTER_RELATION_ONLY_PARENT; ?>" class="list-item filtervalue">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <span class="name value"><?= __('Only parent issues'); ?></span>
                                </label>
                                <input type="checkbox" class="fancy-checkbox" value="<?= SearchFilter::FILTER_RELATION_WITHOUT_PARENT; ?>" name="filters_relation_value" data-text="<?= __('Without parent issues'); ?>" id="filters_relation_value_<?= SearchFilter::FILTER_RELATION_WITHOUT_PARENT; ?>" <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_WITHOUT_PARENT)) echo 'checked'; ?>>
                                <label for="filters_relation_value_<?= SearchFilter::FILTER_RELATION_WITHOUT_PARENT; ?>" class="list-item filtervalue">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <span class="name value"><?= __('Without parent issues'); ?></span>
                                </label>
                                <input type="checkbox" class="fancy-checkbox" value="<?= SearchFilter::FILTER_RELATION_NEITHER_CHILD_NOR_PARENT; ?>" name="filters_relation_value" data-text="<?= __('Neither child nor parent issues'); ?>" id="filters_relation_value_<?= SearchFilter::FILTER_RELATION_NEITHER_CHILD_NOR_PARENT; ?>" <?php if ($filter->hasValue(SearchFilter::FILTER_RELATION_NEITHER_CHILD_NOR_PARENT)) echo 'checked'; ?>>
                                <label for="filters_relation_value_<?= SearchFilter::FILTER_RELATION_NEITHER_CHILD_NOR_PARENT; ?>" class="list-item filtervalue">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <span class="name value"><?= __('Neither child nor parent issues'); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="remove-button"><?= fa_image_tag('times'); ?></div>
                    </div>
                </div>
                <?php
                break;
            default:
                if (!in_array($filter->getFilterKey(), SearchFilter::getValidSearchFilters()))
                {
                    switch ($filter->getFilterType())
                    {
                        case CustomDatatype::DATE_PICKER:
                        case CustomDatatype::DATETIME_PICKER:
                            include_component('search/interactivefilter_date', compact('filter'));
                            break;
                        case CustomDatatype::RADIO_CHOICE:
                        case CustomDatatype::DROPDOWN_CHOICE_TEXT:
                            include_component('search/interactivefilter_choice', compact('filter'));
                            break;
                        case CustomDatatype::COMPONENTS_CHOICE:
                        case CustomDatatype::EDITIONS_CHOICE:
                        case CustomDatatype::RELEASES_CHOICE:
                        case CustomDatatype::MILESTONE_CHOICE:
                            include_component('search/interactivefilter_affected', compact('filter'));
                            break;
                        case CustomDatatype::USER_CHOICE:
                            include_component('search/interactivefilter_user', compact('filter'));
                            break;
                        case CustomDatatype::TEAM_CHOICE:
                            include_component('search/interactivefilter_team', compact('filter'));
                            break;
                        case CustomDatatype::CLIENT_CHOICE:
                            include_component('search/interactivefilter_client', compact('filter'));
                            break;
                        case CustomDatatype::INPUT_TEXT:
                        case CustomDatatype::INPUT_TEXTAREA_MAIN:
                        case CustomDatatype::INPUT_TEXTAREA_SMALL:
                            include_component('search/interactivefilter_text', compact('filter'));
                            break;
                    }
                }
        }

    ?>
<?php endif; ?>
