<?php

    use pachno\core\entities\AgileBoard;

?>
<div class="backdrop_box large edit_agileboard">
    <div class="backdrop_detail_header">
        <span><?php echo ($board->getId()) ? __('Edit board') : __('Create board'); ?></span>
        <a href="javascript:void(0);" class="closer" onclick="Pachno.Main.Helpers.Backdrop.reset();"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('agile_board', array('project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID())); ?>" method="post" id="edit-agileboard-form" onsubmit="Pachno.Project.Planning.saveAgileBoard(this);return false;" data-board-id="<?php echo (int) $board->getId(); ?>">
                <input type="hidden" name="is_private" value="<?php echo (int) $board->isPrivate(); ?>">
                <input type="hidden" name="type" value="<?php echo $board->getType(); ?>" id="agileboard_type_input">
                <input type="hidden" name="swimlane" value="<?php echo $board->getSwimlaneType(); ?>" id="swimlane_input">
                <input type="hidden" name="use_swimlane" value="<?php echo (int) $board->usesSwimlanes(); ?>" id="use_swimlane_input">
                <div class="form-row">
                    <input type="text" class="name-input-enhance" value="<?php echo $board->getName(); ?>" name="name" id="agileboard_name_<?php echo $board->getID(); ?>" placeholder="<?php echo __('Type a short, descriptive name such as "Project planning board"'); ?>">
                    <label for="agileboard_name_<?php echo $board->getID(); ?>"><?php echo __('Name'); ?></label>
                </div>
                <div class="form-row">
                    <div class="fancydropdown-container">
                        <div class="fancydropdown">
                            <label><?php echo __('Board type'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <input class="fancycheckbox" type="radio" name="type" value="<?php echo AgileBoard::TYPE_GENERIC; ?>" id="agileboard_type_checkbox_<?php echo AgileBoard::TYPE_GENERIC; ?>" <?php if ($board->getType() == AgileBoard::TYPE_GENERIC) echo 'checked'; ?>>
                                <label for="agileboard_type_checkbox_<?php echo AgileBoard::TYPE_GENERIC; ?>" class="list-item multiline">
                                    <span class="icon"><?php echo fa_image_tag('columns'); ?></span>
                                    <span class="name">
                                        <span class="title value"><?php echo __('Generic planning board'); ?></span>
                                        <span class="description">
                                            <?php echo __('Just a generic planning board for planning upcoming milestones.'); ?>
                                        </span>
                                    </span>
                                </label>
                                <input class="fancycheckbox" type="radio" name="type" value="<?php echo AgileBoard::TYPE_SCRUM; ?>" id="agileboard_type_checkbox_<?php echo AgileBoard::TYPE_SCRUM; ?>" <?php if ($board->getType() == AgileBoard::TYPE_SCRUM) echo 'checked'; ?>>
                                <label for="agileboard_type_checkbox_<?php echo AgileBoard::TYPE_SCRUM; ?>" class="list-item multiline">
                                    <span class="icon"><?php echo fa_image_tag('redo-alt'); ?></span>
                                    <span class="name">
                                        <span class="title value"><?php echo __('Scrum board'); ?></span>
                                        <span class="description">
                                            <?php echo __('Board tailored for scrum-style workflows, card view as scrum stories with estimates.'); ?>
                                        </span>
                                    </span>
                                </label>
                                <input class="fancycheckbox" type="radio" name="type" value="<?php echo AgileBoard::TYPE_KANBAN; ?>" id="agileboard_type_checkbox_<?php echo AgileBoard::TYPE_KANBAN; ?>" <?php if ($board->getType() == AgileBoard::TYPE_KANBAN) echo 'checked'; ?>>
                                <label for="agileboard_type_checkbox_<?php echo AgileBoard::TYPE_KANBAN; ?>" class="list-item multiline">
                                    <span class="icon"><?php echo fa_image_tag('th-list'); ?></span>
                                    <span class="name">
                                        <span class="title value"><?php echo __('Kanban board'); ?></span>
                                        <span class="description">
                                            <?php echo __('Kanban board with workload limits and powerful plan mode.'); ?>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <input type="text" value="<?php echo $board->getDescription(); ?>" name="description" id="agileboard_description_<?php echo $board->getID(); ?>" placeholder="<?php echo __('Type a short description to be shown in the board list'); ?>">
                    <label for="agileboard_description_<?php echo $board->getID(); ?>"><?php echo __('Description'); ?></label>
                </div>
                <div class="form-row">
                    <div class="fancydropdown-container">
                        <div class="fancydropdown" data-default-label="<?php echo __('None selected'); ?>">
                            <label><?php echo __('Backlog search'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <?php foreach ($autosearches as $value => $description): ?>
                                    <?php $is_selected = ($board->usesAutogeneratedSearchBacklog() && $board->getAutogeneratedSearch() == $value); ?>
                                    <input type="radio" class="fancycheckbox" value="predefined_<?php echo $value; ?>" name="backlog_search" id="backlog_search_predefined_<?php echo $value; ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                    <label for="backlog_search_predefined_<?php echo $value; ?>" class="list-item">
                                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                        <span class="name value"><?php echo $description; ?></span>
                                    </label>
                                <?php endforeach; ?>
                                <?php if (count($savedsearches['public']) > 0): ?>
                                    <?php foreach ($savedsearches['public'] as $savedsearch): ?>
                                        <?php $is_selected = ($board->usesSavedSearchBacklog() && $board->getBacklogSearch()->getID() == $savedsearch->getID()); ?>
                                        <input type="radio" class="fancycheckbox" value="saved_<?php echo $savedsearch->getID(); ?>" name="backlog_search" id="backlog_search_saved_<?php echo $savedsearch->getID(); ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                        <label for="backlog_search_saved_<?php echo $savedsearch->getID(); ?>" class="list-item">
                                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                            <span class="name value"><?php echo $savedsearch->getName(); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="list-item disabled"><?php echo __('There are no public saved searches for this project'); ?></div>
                                <?php endif; ?>
                                <div class="separator"></div>
                                <?php if (count($savedsearches['user']) > 0): ?>
                                    <?php foreach ($savedsearches['user'] as $savedsearch): ?>
                                        <?php $is_selected = ($board->usesSavedSearchBacklog() && $board->getBacklogSearch()->getID() == $savedsearch->getID()); ?>
                                        <input type="radio" class="fancycheckbox" value="saved_<?php echo $savedsearch->getID(); ?>" name="backlog_search" id="backlog_search_saved_<?php echo $savedsearch->getID(); ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                        <label for="backlog_search_saved_<?php echo $savedsearch->getID(); ?>" class="list-item">
                                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                            <span class="name value"><?php echo $savedsearch->getName(); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="list-item disabled"><?php echo __('You have no saved searches for this project'); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="helper-text">
                            <?php echo __('The backlog search is used to define which issues are available in the backlog and on the board.'); ?>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="form-row">
                        <div class="fancydropdown-container">
                            <div class="fancydropdown" data-default-label="<?= __('Pick an issue type'); ?>">
                                <label><?php echo __('Epic issuetype'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach ($issuetypes as $issuetype): ?>
                                        <input type="radio" value="<?php echo $issuetype->getID(); ?>" name="epic_issuetype_id" id="epic_issuetype_id_<?php echo $issuetype->getID(); ?>" class="fancycheckbox" <?php if ($board->getEpicIssuetypeID() == $issuetype->getID()) echo 'checked'; ?>>
                                        <label for="epic_issuetype_value_<?php echo $issuetype->getID(); ?>" class="list-item">
                                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                            <span class="name value"><?php echo __($issuetype->getName()); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="form-row">
                        <div class="fancydropdown-container">
                            <div class="fancydropdown">
                                <label><?php echo __('Task issuetype'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach ($issuetypes as $issuetype): ?>
                                        <input type="checkbox" value="<?php echo $issuetype->getID(); ?>" name="task_issuetype_id" id="task_issuetype_id_<?php echo $issuetype->getID(); ?>" class="fancycheckbox" <?php if ($board->getTaskIssuetypeID() == $issuetype->getID()) echo 'checked'; ?>>
                                        <label for="task_issuetype_id_<?php echo $issuetype->getID(); ?>" class="list-item">
                                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                            <span class="name value"><?php echo __($issuetype->getName()); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($board->getID()): ?>
                    <h2><?php echo __('Whiteboard mode settings'); ?></h2>
                    <table class="sectioned_table">
                        <tr>
                            <td>
                                <label for="agileboard_swimlane_<?php echo $board->getID(); ?>"><?php echo __('Whiteboard swimlanes'); ?></label>
                                <a href="javascript:void(0)" class="fancydropdown changeable" id="swimlane_<?php echo $board->getID(); ?>"><?php

                                    if (!$board->usesSwimlanes())
                                    {
                                        echo __('Not used');
                                    }
                                    else
                                    {
                                        switch ($board->getSwimlaneType())
                                        {
                                            case AgileBoard::SWIMLANES_ISSUES:
                                                echo __('Issue swimlanes');
                                                break;
                                            case AgileBoard::SWIMLANES_GROUPING:
                                                echo __('Issues detail swimlanes');
                                                break;
                                            case AgileBoard::SWIMLANES_EXPEDITE:
                                                echo __('Level of service swimlane');
                                                break;
                                        }
                                    }

                                ?></a>
                                <ul data-input="use_swimlane_input" class="dropdown-container" data-callback="Pachno.Project.Planning.toggleSwimlaneDetails">
                                    <li data-input-value="0" data-swimlane-type="none" data-display-name="<?php echo __('Not used'); ?>" class="fancydropdown-item novalue <?php if (!$board->usesSwimlanes()) echo ' selected'; ?>" onclick="Pachno.Project.Planning.toggleSwimlaneDetails(this);"><p><?php echo __("Don't use swimlanes"); ?></p></li>
                                    <li data-input-value="1" data-swimlane-type="<?php echo AgileBoard::SWIMLANES_ISSUES; ?>" data-display-name="<?php echo __('Issue swimlanes'); ?>" class="fancydropdown-item <?php if ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_ISSUES) echo 'selected'; ?>" onclick="Pachno.Project.Planning.toggleSwimlaneDetails(this);">
                                        <h1><?php echo __('Issue swimlanes'); ?></h1>
                                        <?php echo image_tag('swimlanes_issues.png'); ?>
                                        <p>
                                            <?php echo __('The board has a swimlane for each issue of one or more issue type(s).'); ?>
                                        </p>
                                    </li>
                                    <li data-input-value="1" data-swimlane-type="<?php echo AgileBoard::SWIMLANES_GROUPING; ?>" data-display-name="<?php echo __('Issue detail swimlanes'); ?>" class="fancydropdown-item <?php if ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_GROUPING) echo 'selected'; ?>" onclick="Pachno.Project.Planning.toggleSwimlaneDetails(this);">
                                        <h1><?php echo __('Issue detail swimlanes'); ?></h1>
                                        <?php echo image_tag('swimlanes_grouping.png'); ?>
                                        <p>
                                            <?php echo __('The board is grouped in swimlanes where issues that share the same characteristics are grouped together.'); ?>
                                        </p>
                                    </li>
                                    <li data-input-value="1" data-swimlane-type="<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>" data-display-name="<?php echo __('Level of service swimlane'); ?>" class="fancydropdown-item <?php if ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE) echo 'selected'; ?>" onclick="Pachno.Project.Planning.toggleSwimlaneDetails(this);">
                                        <h1><?php echo __('Level of service swimlane'); ?></h1>
                                        <?php echo image_tag('swimlanes_expedite.png'); ?>
                                        <p>
                                            <?php echo __('No general grouping, but an increased level of service swimlane at the top for expediting issues'); ?>
                                        </p>
                                    </li>
                                </ul>
                            </td>
                            <td id="swimlane_details_container">
                                <div id="swimlane_none_container" style="<?php if ($board->usesSwimlanes()) echo 'display: none;'; ?>">
                                    <div class="description"><?php echo __('There will be no swimlanes on the board'); ?></div>
                                </div>
                                <div id="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_container" style="<?php if (!$board->usesSwimlanes() || $board->getSwimlaneType() != AgileBoard::SWIMLANES_ISSUES) echo 'display: none;'; ?>">
                                    <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_identifier" value="issuetype" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_identifier_input">
                                    <div class="fancyfilter filter interactive_dropdown" data-filterkey="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_issuetype" data-value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" data-no-selection-value="<?php echo __('None selected'); ?>">
                                        <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_details[issuetype]" value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_issuetype_value_input">
                                        <label><?php echo __('Issuetype(s)'); ?></label>
                                        <span class="value"><?php if (!$board->hasSwimlaneFieldValues()) echo __('None selected'); ?></span>
                                        <div class="interactive_menu">
                                            <h1><?php echo __('Select issuetype(s)'); ?></h1>
                                            <div class="interactive_values_container">
                                                <ul class="interactive_menu_values">
                                                    <?php foreach ($issuetypes as $issuetype): ?>
                                                        <li data-value="<?php echo $issuetype->getID(); ?>" class="filtervalue<?php if ($board->hasSwimlaneFieldValue($issuetype->getID())) echo ' selected'; ?>">
                                                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                            <input type="checkbox" value="<?php echo $issuetype->getID(); ?>" name="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_issuetype_<?php echo $issuetype->getID(); ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_issuetype_<?php echo $issuetype->getID(); ?>" data-text="<?php echo __($issuetype->getName()); ?>" id="filters_issuetype_value_<?php echo $issuetype->getID(); ?>" <?php if ($board->hasSwimlaneFieldValue($issuetype->getID())) echo 'checked'; ?>>
                                                            <label name="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_issuetype_<?php echo $issuetype->getID(); ?>"><?php echo __($issuetype->getName()); ?></label>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="description"><?php echo __('The whiteboard will have separate swimlanes for all issues that is of a certain type. Specify which issuetype qualifies as a swimlane.'); ?></p>
                                </div>
                                <div id="swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_container" style="<?php if (!$board->usesSwimlanes() || $board->getSwimlaneType() != AgileBoard::SWIMLANES_GROUPING) echo 'display: none;'; ?>">
                                    <div class="fancyfilter filter interactive_dropdown" data-filterkey="swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier" data-value="<?php echo $board->getSwimlaneIdentifier(); ?>" data-no-selection-value="<?php echo __('None selected'); ?>">
                                        <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier" value="<?php echo $board->getSwimlaneIdentifier(); ?>" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier_value_input">
                                        <label><?php echo __('Group by'); ?></label>
                                        <span class="value"><?php if (!$board->getSwimlaneIdentifier()) echo __('None selected'); ?></span>
                                        <div class="interactive_menu">
                                            <h1><?php echo __('Select detail to group by'); ?></h1>
                                            <div class="interactive_values_container">
                                                <ul class="interactive_menu_values">
                                                    <?php foreach ($swimlane_groups as $value => $description): ?>
                                                        <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_GROUPING && $board->getSwimlaneIdentifier() == $value); ?>
                                                        <li data-value="<?php echo $value; ?>" class="filtervalue<?php if ($is_selected) echo ' selected'; ?>" data-exclusive>
                                                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                            <input type="checkbox" value="<?php echo $value; ?>" name="swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier_<?php echo $value; ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier_<?php echo $value; ?>" data-text="<?php echo $description; ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                                            <label for="swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier_<?php echo $value; ?>"><?php echo $description; ?></label>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="description"><?php echo __('The whiteboard will have separate swimlanes / groups for issues that share the same characteristics. Specify which issue detail to group issues by.'); ?></p>
                                </div>
                                <div id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_container" style="<?php if (!$board->usesSwimlanes() || $board->getSwimlaneType() != AgileBoard::SWIMLANES_EXPEDITE) echo 'display: none;'; ?>">
                                    <div class="fancyfilter filter interactive_dropdown" data-filterkey="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier" data-value="<?php echo $board->getSwimlaneIdentifier(); ?>" data-no-selection-value="<?php echo __('None selected'); ?>">
                                        <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier" value="<?php echo $board->getSwimlaneIdentifier(); ?>" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_value_input">
                                        <label><?php echo __('Issue detail'); ?></label>
                                        <span class="value"><?php if (!$board->getSwimlaneIdentifier()) echo __('None selected'); ?></span>
                                        <div class="interactive_menu">
                                            <h1><?php echo __('Select issue field for expedite swimlane'); ?></h1>
                                            <div class="interactive_values_container">
                                                <ul class="interactive_menu_values">
                                                    <?php foreach ($swimlane_groups as $value => $description): ?>
                                                        <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == $value); ?>
                                                        <li data-value="<?php echo $value; ?>" class="filtervalue<?php if ($is_selected) echo ' selected'; ?>" data-exclusive onclick="Pachno.Project.Planning.toggleSwimlaneExpediteDetails(this);">
                                                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                            <input type="checkbox" value="<?php echo $value; ?>" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $value; ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $value; ?>" data-text="<?php echo $description; ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                                            <label for="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $value; ?>"><?php echo $description; ?></label>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_container_details">
                                        <div class="fancyfilter filter interactive_dropdown" data-filterkey="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_priority" data-value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" data-no-selection-value="<?php echo __('None selected'); ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_priority_values" style="<?php if (!($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'priority')) echo 'display: none;'; ?>">
                                            <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_details[priority]" value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_priority_value_input">
                                            <label><?php echo __('Field value(s)'); ?></label>
                                            <span class="value"><?php if (!$board->hasSwimlaneFieldValues()) echo __('None selected'); ?></span>
                                            <div class="interactive_menu">
                                                <h1><?php echo __('Select values for expedite issues'); ?></h1>
                                                <div class="interactive_values_container">
                                                    <ul class="interactive_menu_values">
                                                        <?php foreach ($priorities as $priority): ?>
                                                            <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'priority' && $board->hasSwimlaneFieldValue($priority->getID())); ?>
                                                            <li data-value="<?php echo $priority->getID(); ?>" class="filtervalue<?php if ($is_selected) echo ' selected'; ?>">
                                                                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                                <input type="checkbox" value="<?php echo $priority->getID(); ?>" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $priority->getID(); ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $priority->getID(); ?>" data-text="<?php echo $priority->getName(); ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                                                <label for="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $priority->getID(); ?>"><?php echo $priority->getName(); ?></label>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="fancyfilter filter interactive_dropdown" data-filterkey="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_severity" data-value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" data-no-selection-value="<?php echo __('None selected'); ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_severity_values" style="<?php if (!($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'severity')) echo 'display: none;'; ?>">
                                            <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_details[severity]" value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_severity_value_input">
                                            <label><?php echo __('Field value(s)'); ?></label>
                                            <span class="value"><?php if (!$board->hasSwimlaneFieldValues()) echo __('None selected'); ?></span>
                                            <div class="interactive_menu">
                                                <h1><?php echo __('Select values for expedite issues'); ?></h1>
                                                <div class="interactive_values_container">
                                                    <ul class="interactive_menu_values">
                                                        <?php foreach ($severities as $severity): ?>
                                                            <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'severity' && $board->hasSwimlaneFieldValue($severity->getID())); ?>
                                                            <li data-value="<?php echo $severity->getID(); ?>" class="filtervalue<?php if ($is_selected) echo ' selected'; ?>">
                                                                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                                <input type="checkbox" value="<?php echo $severity->getID(); ?>" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $severity->getID(); ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $severity->getID(); ?>" data-text="<?php echo $severity->getName(); ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                                                <label for="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $severity->getID(); ?>"><?php echo $severity->getName(); ?></label>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="fancyfilter filter interactive_dropdown" data-filterkey="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_category" data-value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" data-no-selection-value="<?php echo __('None selected'); ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_category_values" style="<?php if (!($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'category')) echo 'display: none;'; ?>">
                                            <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_details[category]" value="<?php echo join(',', $board->getSwimlaneFieldValues()); ?>" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_category_value_input">
                                            <label><?php echo __('Field value(s)'); ?></label>
                                            <span class="value"><?php if (!$board->hasSwimlaneFieldValues()) echo __('None selected'); ?></span>
                                            <div class="interactive_menu">
                                                <h1><?php echo __('Select values for expedite issues'); ?></h1>
                                                <div class="interactive_values_container">
                                                    <ul class="interactive_menu_values">
                                                        <?php foreach ($categories as $category): ?>
                                                            <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'category' && $board->hasSwimlaneFieldValue($category->getID())); ?>
                                                            <li data-value="<?php echo $category->getID(); ?>" class="filtervalue<?php if ($is_selected) echo ' selected'; ?>">
                                                                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                                <input type="checkbox" value="<?php echo $category->getID(); ?>" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $category->getID(); ?>" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $category->getID(); ?>" data-text="<?php echo $category->getName(); ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                                                <label for="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?php echo $category->getID(); ?>"><?php echo $category->getName(); ?></label>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="description"><?php echo __('The whiteboard will have a separate swimlane at the top for prioritized issues, like a expedite line / fastlane. Select which issue details puts issues in this swimlane.'); ?></p>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <h2><?php echo __('Whiteboard custom issue fields info'); ?></h2>
                    <table class="sectioned_table">
                        <tr>
                            <td id="issue_field_details_container">
                                <div id="issue_field_container">
                                    <div class="fancyfilter filter interactive_dropdown" data-filterkey="issue_field_issuetype" data-value="<?php echo join(',', $board->getIssueFieldValues()); ?>" data-no-selection-value="<?php echo __('None selected'); ?>">
                                        <input type="hidden" name="issue_field_details[issuetype]" value="<?php echo join(',', $board->getIssueFieldValues()); ?>" id="filter_issue_field_issuetype_value_input">
                                        <label><?php echo __('Issue field(s)'); ?></label>
                                        <span class="value"><?php if (!$board->hasIssueFieldValues()) echo __('None selected'); ?></span>
                                        <div class="interactive_menu">
                                            <h1><?php echo __('Select issue field(s)'); ?></h1>
                                            <div class="interactive_values_container">
                                                <ul class="interactive_menu_values">
                                                    <?php foreach ($issuefields as $issuefield): ?>
                                                        <li data-value="<?php echo $issuefield->getKey(); ?>" class="filtervalue<?php if ($board->hasIssueFieldValue($issuefield->getKey())) echo ' selected'; ?>">
                                                            <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                            <input type="checkbox" value="<?php echo $issuefield->getKey(); ?>" name="issue_field_issuefield_<?php echo $issuefield->getKey(); ?>" id="issue_field_issuefield_<?php echo $issuefield->getKey(); ?>" data-text="<?php echo __($issuefield->getName()); ?>" id="filters_issuefield_value_<?php echo $issuefield->getKey(); ?>" <?php if ($board->hasIssueFieldValue($issuefield->getKey())) echo 'checked'; ?>>
                                                            <label name="issue_field_issuefield_<?php echo $issuefield->getKey(); ?>"><?php echo __($issuefield->getName()); ?></label>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="description"><?php echo __('Specify which custom issue fields should also be visible for issue.'); ?></p>
                                </div>
                            </td>
                        </tr>
                    </table>
                <?php endif; ?>
                <div class="form-row submit-container">
                    <?php if ($board->getID()): ?>
                        <input type="hidden" name="board_id" value="<?php echo $board->getID(); ?>">
                    <?php endif; ?>
                    <button class="button primary" type="submit" id="agileboard_save_button">
                        <?php if ($board->getId()): ?>
                            <?= fa_image_tag('save'); ?><span><?= __('Save board'); ?></span>
                        <?php else: ?>
                            <?= fa_image_tag('plus-square'); ?><span><?= __('Create board'); ?></span>
                        <?php endif; ?>
                        <span class="indicator"><?= fa_image_tag('spinner', ['class' => 'fa-spin']); ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
