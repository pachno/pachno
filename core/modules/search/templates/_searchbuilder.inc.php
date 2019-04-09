<?php

    $pachno_response->addJavascript('calendarview');

?>
<div class="interactive_searchbuilder" id="search_builder">
    <form accept-charset="<?= \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?= (\pachno\core\framework\Context::isProjectContext()) ? make_url('project_search_paginated', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())) : make_url('search_paginated'); ?>" method="get" id="find_issues_form" <?php if ($show_results): ?>data-results-loaded<?php endif; ?> <?php if ($search_object->getID()): ?>data-is-saved<?php endif; ?> data-history-url="<?= (\pachno\core\framework\Context::isProjectContext()) ? make_url('project_issues', array('project_key' => \pachno\core\framework\Context::getCurrentProject()->getKey())) : make_url('search'); ?>" data-dynamic-callback-url="<?= make_url('search_filter_getdynamicchoices'); ?>" onsubmit="Pachno.Search.liveUpdate(true);return false;">
        <div class="searchbuilder_filterstrip" id="searchbuilder_filterstrip">
            <?php include_component('search/interactivefilter', array('filter' => $search_object->getFilter('project_id'))); ?>
            <?php include_component('search/interactivefilter', array('filter' => $search_object->getFilter('issuetype'))); ?>
            <?php include_component('search/interactivefilter', array('filter' => $search_object->getFilter('status'))); ?>
            <?php //include_component('search/interactivefilter', array('filter' => $search_object->getFilter('category'))); ?>
            <input type="hidden" name="sortfields" value="<?= $search_object->getSortFieldsAsString(); ?>" id="search_sortfields_input">
            <input type="hidden" name="fs[text][o]" value="=">
            <input type="search" name="fs[text][v]" id="interactive_filter_text" value="<?= htmlentities($search_object->getSearchTerm(), ENT_QUOTES, \pachno\core\framework\Context::getI18n()->getCharset()); ?>" class="filter_searchfield" placeholder="<?= __('Enter a search term here'); ?>">
            <div class="dropper-container" id="interactive_filters_availablefilters_container">
                <a href="javascript:void(0)" class="button icon secondary dropper" id="interactive_plus_button"><?= fa_image_tag('plus'); ?></a>
                <div class="dropdown-container columns <?= (count($nondatecustomfields)) ? 'three-columns' : 'two-columns'; ?>">
                    <div class="list-mode column">
                        <div class="header"><?= __('People filters'); ?></div>
                        <div class="list-item" data-filter="posted_by" id="additional_filter_posted_by_link"><span class="name"><?= __('Posted by user'); ?></span></div>
                        <div class="list-item" data-filter="assignee_user" id="additional_filter_assignee_user_link"><span class="name"><?= __('Assigned to user'); ?></span></div>
                        <div class="list-item" data-filter="assignee_team" id="additional_filter_assignee_team_link"><span class="name"><?= __('Assigned to team'); ?></span></div>
                        <div class="list-item" data-filter="owner_user" id="additional_filter_owner_user_link"><span class="name"><?= __('Owned by user'); ?></span></div>
                        <div class="list-item" data-filter="owner_team" id="additional_filter_owner_team_link"><span class="name"><?= __('Owned by team'); ?></span></div>
                        <div class="header"><?= __('Time filters'); ?></div>
                        <div class="list-item" data-filter="posted" id="additional_filter_posted_link"><span class="name"><?= __('Created before / after'); ?></span></div>
                        <div class="list-item" data-filter="last_updated" id="additional_filter_last_updated_link"><span class="name"><?= __('Last updated before / after'); ?></span></div>
                        <div class="list-item" data-filter="time_spent" id="additional_filter_time_spent_link"><span class="name"><?= __('Time spent before / after'); ?></span></div>
                        <?php foreach ($datecustomfields as $field): ?>
                            <div class="list-item" data-filter="<?= $field->getKey(); ?>" id="additional_filter_<?= $field->getKey(); ?>_link"><span class="name"><?= __($field->getDescription()); ?></span></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="list-mode column">
                        <div class="header"><?= __('Project detail filters'); ?></div>
                        <?php if (\pachno\core\framework\Context::isProjectContext()): ?>
                            <div class="list-item" data-filter="subprojects" id="additional_filter_subprojects_link"><span class="name"><?= __('Including subproject(s)'); ?></span></div>
                        <?php else: ?>
                            <div class="list-item disabled">
                                <span class="name"><?= __('Including subproject(s)'); ?></span>
                                <div class="tooltip from-above leftie"><?= __('This filter is only available in project context'); ?></div>
                            </div>
                        <?php endif; ?>
                        <div class="list-item" data-filter="build" id="additional_filter_build_link"><span class="name"><?= __('Reported against a specific release'); ?></span></div>
                        <div class="list-item" data-filter="component" id="additional_filter_component_link"><span class="name"><?= __('Affecting a specific component'); ?></span></div>
                        <div class="list-item" data-filter="edition" id="additional_filter_edition_link"><span class="name"><?= __('Affecting a specific edition'); ?></span></div>
                        <div class="list-item" data-filter="milestone" id="additional_filter_milestone_link"><span class="name"><?= __('Targetting a specific milestone'); ?></span></div>
                        <div class="header"><?= __('Issue detail filters'); ?></div>
                        <div class="list-item" data-filter="priority" id="additional_filter_priority_link"><span class="name"><?= __('Priority'); ?></span></div>
                        <div class="list-item" data-filter="severity" id="additional_filter_severity_link"><span class="name"><?= __('Severity'); ?></span></div>
                        <div class="list-item" data-filter="resolution" id="additional_filter_resolution_link"><span class="name"><?= __('Resolution'); ?></span></div>
                        <div class="list-item" data-filter="reproducability" id="additional_filter_reproducability_link"><span class="name"><?= __('Reproducability'); ?></span></div>
                        <div class="list-item" data-filter="blocking" id="additional_filter_blocking_link"><span class="name"><?= __('Blocker status'); ?></span></div>
                        <div class="list-item" data-filter="relation" id="additional_filter_relation_link"><span class="name"><?= __('Relation'); ?></span></div>
                    </div>
                    <?php if (count($nondatecustomfields)): ?>
                        <div class="column list-mode">
                            <div class="header"><?= __('Other filters'); ?></div>
                            <?php foreach ($nondatecustomfields as $field): ?>
                                <div class="list-item" data-filter="<?= $field->getKey(); ?>" id="additional_filter_<?= $field->getKey(); ?>_link"><span class="name"><?= __($field->getDescription()); ?></span></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="dropper-container">
                <a href="javascript:void(0);" class="button icon secondary dropper" id="interactive_grouping_button"><?= fa_image_tag('columns'); ?></a>
                <div class="dropdown-container" id="search_columns_container" data-url="<?= make_url('search_save_column_settings'); ?>">
                    <div id="search_column_settings_container" class="list-mode">
                        <div class="header"><?= __('Select columns to show'); ?></div>
                        <input type="hidden" name="scs_current_template" value="" id="scs_current_template">
                        <div class="list-item filter-container">
                            <input type="search" class="interactive_menu_filter" placeholder="<?= __('Filter values'); ?>">
                        </div>
                        <div class="interactive_menu_values">
                            <?php foreach ($columns as $c_key => $c_name): ?>
                                <input id="search_column_<?= $c_key; ?>_toggler_checkbox" type="checkbox" value="<?= $c_key; ?>" name="columns[<?= $c_key; ?>]" class="fancycheckbox">
                                <label for="search_column_<?= $c_key; ?>_toggler_checkbox" data-value="<?= $c_key; ?>" class="list-item search_column filtervalue scs_<?= $c_key; ?>">
                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                    <span class="name value"><?= $c_name; ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?php if (!$pachno_user->isGuest()): ?>
                            <div class="button-group">
                                <?= fa_image_tag('spinner', ['id' => 'search_column_settings_indicator', 'class' => 'fa-spin', 'style' => 'display: none;']); ?>
                                <?= javascript_link_tag(__('Reset columns'), ['onclick' => 'Pachno.Search.resetColumns();return false;', 'class' => 'button secondary']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="dropper-container">
                <a href="javascript:void(0);" class="button icon secondary dropper" id="interactive_template_button"><?= fa_image_tag('sliders-h'); ?></a>
                <div class="dropdown-container interactive_filters_list columns two-columns">
                    <div class="list-mode column">
                        <div class="header"><?= __('Select how to present search results'); ?></div>
                        <?php foreach ($templates as $template_name => $template_details): ?>
                            <input type="radio" name="template" id="filter_selected_template_<?= $template_name; ?>" value="<?= $search_object->getTemplateName(); ?>" class="fancycheckbox" <?php if ($template_name == $search_object->getTemplateName()) echo 'checked'; ?>>
                            <label for="filter_selected_template_<?= $template_name; ?>" class="list-item multiline" data-template-name="<?= $template_name; ?>" data-parameter="<?= (int) $template_details['parameter']; ?>" data-parameter-text="<?= ($template_details['parameter']) ? __e($template_details['parameter_text']) : ''; ?>">
                                <?= fa_image_tag('check-circle', ['class' => 'checked icon'], 'far') . fa_image_tag('circle', ['class' => 'unchecked icon'], 'far'); ?>
                                <span class="name">
                                    <span class="title"><?= $template_details['title']; ?></span>
                                    <span class="description"><?= $template_details['description']; ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="list-mode column <?php if (!$templates[$search_object->getTemplateName()]['grouping']) echo 'nogrouping'; ?> <?php if ($templates[$search_object->getTemplateName()]['parameter']) echo 'parameter'; ?>" id="search_grouping_container">
                        <div class="parameterdetails">
                            <div class="header"><?= __('Special search parameters'); ?></div>
                            <div class="list-item text-input-container">
                                <input type="text" id="search_filter_parameter_input" data-maxlength="0" placeholder="<?= ($templates[$search_object->getTemplateName()]['parameter']) ? $templates[$search_object->getTemplateName()]['parameter_text'] : ''; ?>" value="<?= $search_object->getTemplateParameter(); ?>" name="template_parameter">
                            </div>
                        </div>
                        <div class="header"><?= __('Select how many issues to show per page'); ?></div>
                        <input type="hidden" name="issues_per_page" id="filter_issues_per_page" value="<?= $search_object->getIssuesPerPage(); ?>">
                        <div class="list-item">
                            <div class="slider-container">
                                <div class="slider" id="issues_per_page_slider">
                                    <div class="handle" id="issues_per_page_handle"></div>
                                </div>
                                <div id="issues_per_page_slider_value" class="value"><?= $search_object->getIssuesPerPage(); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="column list-mode">
                        <div class="header"><?= __('Search result grouping'); ?></div>
                        <div class="list-item disabled">
                            <?= fa_image_tag('info-circle', ['class' => 'icon']); ?>
                            <span class="name"><?= __('This search template does not support grouping'); ?></span>
                        </div>
                        <div class="list-item filter-container">
                            <input type="search" class="interactive_menu_filter" placeholder="<?= __('Filter values'); ?>">
                        </div>
                        <div id="filter_grouping_options">
                            <?php foreach (array('asc' => __('Ascending'), 'desc' => __('Descending')) as $dir => $dir_desc): ?>
                                <input type="radio" class="fancycheckbox" value="<?= $dir; ?>" name="grouporder" id="search_grouping_grouporder_<?= $dir; ?>" <?php if ($search_object->getGrouporder() == $dir) echo 'checked'; ?>>
                                <label data-sort-order="<?= $dir; ?>" for="search_grouping_grouporder_<?= $dir; ?>" class="list-item filtervalue sticky" style="<?php if (!$search_object->getGroupby()) echo 'display: none;'; ?>">
                                    <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span class="name value"><?= $dir_desc; ?></span>
                                </label>
                            <?php endforeach; ?>
                            <div class="separator"></div>
                            <input type="radio" value="" name="groupby" class="fancycheckbox" id="search_grouping_none" <?php if (!$search_object->getGroupby()) echo 'checked'; ?>>
                            <label for="search_grouping_none" data-groupby="" class="list-item groupby filtervalue">
                                <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                <span class="name"><?= __('No grouping'); ?></span>
                            </label>
                            <?php foreach ($groupoptions as $grouping => $group_desc): ?>
                                <input type="radio" value="<?= $grouping; ?>" name="groupby" class="fancycheckbox" id="search_grouping_groupby_<?= $grouping; ?>" <?php if ($search_object->getGroupby() == $grouping) echo 'checked'; ?>>
                                <label for="search_grouping_groupby_<?= $grouping; ?>" data-groupby="<?= $grouping; ?>" class="list-item groupby filtervalue">
                                    <span class="icon"><?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?></span>
                                    <span class="name"><?= $group_desc; ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!$pachno_user->isGuest()): ?>
                <div class="dropper-container">
                    <a href="javascript:void(0);" class="button icon secondary dropper" id="interactive_save_button" style="<?php if (!$show_results) echo 'display: none;'; ?>"><span class="expander"><?= fa_image_tag('save'); ?></span></a>
                    <div class="dropdown-container list-mode">
                        <div class="header"><?= __('Save or download search results'); ?></div>
                        <div class="list-item" onclick="$('saved_search_details').toggle();">
                            <?= fa_image_tag('bookmark', array('class' => 'icon')); ?>
                            <span class="name"><?= __('Save search filters'); ?></span>
                        </div>
                        <div class="list-item" onclick="Pachno.Search.download('ods');">
                            <?= fa_image_tag('download', array('class' => 'icon')); ?>
                            <span class="name"><?= __('Download as OpenDocument spreadsheet (.ods)'); ?></span>
                        </div>
                        <div class="list-item" onclick="Pachno.Search.download('xlsx');">
                            <?= fa_image_tag('file-excel', array('class' => 'icon')); ?>
                            <span class="name"><?= __('Download as Microsoft Excel spreadsheet (.xlsx)'); ?></span>
                        </div>
                        <div class="list-item" onclick="Pachno.Search.download('rss-square');">
                            <?= fa_image_tag('rss', array('class' => 'icon')); ?>
                            <span class="name"><?= __('Download as RSS feed'); ?></span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <button class="disabled" disabled id="interactive_save_button" style="<?php if (!$show_results) echo 'display: none;'; ?>">
                    <div class="tooltip from-above rightie" style="right: -5px; left: auto; margin-top: 10px;"><?= __('You have to be signed in to save this search'); ?></div>
                    <?= fa_image_tag('save', ['class' => 'icon']); ?>
                </button>
            <?php endif; ?>
            <div id="searchbuilder_filterstrip_filtercontainer">
                <?php foreach ($search_object->getFilters() as $filter): ?>
                    <?php if (is_array($filter)): ?>
                        <?php foreach ($filter as $filter_filter): ?>
                            <?php if (in_array($filter_filter->getFilterKey(), array('project_id', 'status', 'issuetype', 'category', 'text'))) continue; ?>
                            <?php //include_component('search/interactivefilter', array('filter' => $filter_filter)); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php if (in_array($filter->getFilterKey(), array('project_id', 'status', 'issuetype', 'category', 'text'))) continue; ?>
                        <?php //include_component('search/interactivefilter', compact('filter')); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </form>
    <div id="searchbuilder_filter_hiddencontainer" style="display: none;">
        <?php if (\pachno\core\framework\Context::isProjectContext()): ?>
            <?php if (!$search_object->hasFilter('subprojects')) // include_component('search/interactivefilter', array('filter' => \pachno\core\entities\SearchFilter::createFilter('subprojects'))); ?>
        <?php endif; ?>
        <?php foreach (array('priority', 'severity', 'reproducability', 'resolution', 'posted_by', 'assignee_user', 'assignee_team', 'owner_user', 'owner_team', 'milestone', 'edition', 'component', 'build', 'blocking', 'relation') as $key): ?>
            <?php if (!$search_object->hasFilter($key)) // include_component('search/interactivefilter', array('filter' => \pachno\core\entities\SearchFilter::createFilter($key))); ?>
        <?php endforeach; ?>
        <?php foreach (array('posted', 'last_updated', 'time_spent') as $key): ?>
            <?php // include_component('search/interactivefilter', array('filter' => \pachno\core\entities\SearchFilter::createFilter($key, array('operator' => '<=', 'value' => time())))); ?>
        <?php endforeach; ?>
        <?php foreach ($nondatecustomfields as $customtype): ?>
            <?php if ($customtype->getType() == \pachno\core\entities\CustomDatatype::DATE_PICKER || $customtype->getType() == \pachno\core\entities\CustomDatatype::DATETIME_PICKER) continue; ?>
            <?php if (!$search_object->hasFilter($customtype->getKey())) // include_component('search/interactivefilter', array('filter' => \pachno\core\entities\SearchFilter::createFilter($customtype->getKey()))); ?>
        <?php endforeach; ?>
        <?php foreach ($datecustomfields as $customtype): ?>
            <?php // include_component('search/interactivefilter', array('filter' => \pachno\core\entities\SearchFilter::createFilter($customtype->getKey(), array('operator' => '<=', 'value' => time())))); ?>
        <?php endforeach; ?>
    </div>
    <?php if (!$pachno_user->isGuest()): ?>
        <div class="fullpage_backdrop" style="display: none;" id="saved_search_details">
            <div class="backdrop_box large">
                <div class="backdrop_detail_header">
                    <span><?= __('Save this search'); ?></span>
                    <a href="javascript:void(0);" class="closer" onclick="$('saved_search_details').hide();"><?= fa_image_tag('times'); ?></a>
                </div>
                <form id="save_search_form" action="<?= make_url('search_save'); ?>" method="post" onsubmit="Pachno.Search.saveSearch();return false;">
                    <div class="backdrop_detail_content">
                        <?php if (\pachno\core\framework\Context::isProjectContext()): ?>
                            <input type="hidden" name="project_id" value="<?= \pachno\core\framework\Context::getCurrentProject()->getID(); ?>">
                            <p style="padding-bottom: 15px;" class="faded_out"><?= __('This saved search will be available under this project only. To make a non-project-specific search, use the main "%find_issues" page instead', array('%find_issues' => link_tag(make_url('search'), __('Find issues')))); ?></p>
                        <?php endif; ?>
                        <?php if ($search_object->getID()): ?>
                            <input type="hidden" name="saved_search_id" id="saved_search_id" value="<?= $search_object->getID(); ?>">
                        <?php endif; ?>
                        <table class="padded_table" style="width: 780px;">
                            <tr>
                                <td style="vertical-align: top; width: 200px; font-size: 1.15em;"><label for="saved_search_name"><?= __('Saved search name'); ?></label></td>
                                <td style="vertical-align: top;">
                                    <input type="text" name="name" id="saved_search_name"<?php if ($search_object->getID()): ?> value="<?= $search_object->getName(); ?>"<?php endif; ?> style="width: 576px; font-size: 1.2em; padding: 4px;">
                                    <?php if ($search_object->getID()): ?>
                                        <br>
                                        <input type="checkbox" id="update_saved_search" name="update_saved_search" checked><label style="font-size: 1em; font-weight: normal;" for="update_saved_search"><?= __('Update this saved search'); ?></label>
                                    <?php endif; ?>

                                </td>
                            </tr>
                            <tr>
                                <td><label for="saved_search_description" class="optional"><?= __('Description'); ?></label></td>
                                <td><input type="text" name="description" id="saved_search_description"<?php if ($search_object->getID()): ?> value="<?= $search_object->getDescription(); ?>"<?php endif; ?> style="width: 350px;"><br></td>
                            </tr>
                        </table>
                    </div>
                    <div class="backdrop_details_submit">
                        <span class="explanation">
                            <?php if ($pachno_user->canCreatePublicSearches()): ?>
                                <select name="is_public" id="saved_search_public">
                                    <option value="0"<?php if ($search_object->getID() && !$search_object->isPublic()): ?> selected<?php endif; ?>><?= __('Only visible for me'); ?></option>
                                    <option value="1"<?php if ($search_object->getID() && $search_object->isPublic()): ?> selected<?php endif; ?>><?= __('Shared with others'); ?></option>
                                </select>
                            <?php endif; ?>
                        </span>
                        <div class="submit_container">
                            <button type="submit" class="button"><?= image_tag('spinning_16.gif', array('style' => 'display: none;', 'id' => 'save_search_indicator')) . __('Save search'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
