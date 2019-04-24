<?php

    /** @var AgileBoard $board */
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
                <div class="form-row">
                    <input type="text" class="name-input-enhance" value="<?php echo $board->getName(); ?>" name="name" id="agileboard_name_<?php echo $board->getID(); ?>" placeholder="<?php echo __('Type a short, descriptive name such as "Project planning board"'); ?>">
                    <label for="agileboard_name_<?php echo $board->getID(); ?>"><?php echo __('Name'); ?></label>
                </div>
                <div class="form-row">
                    <input class="fancycheckbox" type="checkbox" name="is_private" value="1" id="agileboard_is_private" <?php if ($board->isPrivate()) echo 'checked'; ?>>
                    <label for="agileboard_is_private">
                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                        <span><?= __('This board is only visible to me'); ?></span>
                    </label>
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
                <?php if ($board->getId()): ?>
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
                                    <label><?php echo __('Epic issue type'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <?php foreach ($issuetypes as $issuetype): ?>
                                            <input type="radio" value="<?php echo $issuetype->getID(); ?>" name="epic_issuetype_id" id="epic_issuetype_id_<?php echo $issuetype->getID(); ?>" class="fancycheckbox" <?php if ($board->getEpicIssuetypeID() == $issuetype->getID()) echo 'checked'; ?>>
                                            <label for="epic_issuetype_value_<?php echo $issuetype->getID(); ?>" class="list-item">
                                                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                <?= fa_image_tag($issuetype->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $issuetype->getIcon()]); ?>
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
                                <div class="fancydropdown" data-default-label="<?php echo __('Pick an issue type'); ?>">
                                    <label><?php echo __('Task issue type'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <?php foreach ($issuetypes as $issuetype): ?>
                                            <input type="checkbox" value="<?php echo $issuetype->getID(); ?>" name="task_issuetype_id" id="task_issuetype_id_<?php echo $issuetype->getID(); ?>" class="fancycheckbox" <?php if ($board->getTaskIssuetypeID() == $issuetype->getID()) echo 'checked'; ?>>
                                            <label for="task_issuetype_id_<?php echo $issuetype->getID(); ?>" class="list-item">
                                                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                <?= fa_image_tag($issuetype->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $issuetype->getIcon()]); ?>
                                                <span class="name value"><?php echo __($issuetype->getName()); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row header">
                        <h3><?php echo __('Whiteboard mode settings'); ?></h3>
                    </div>
                    <div class="form-row">
                        <div class="fancydropdown-container">
                            <div class="fancydropdown">
                                <label><?php echo __('Whiteboard swimlanes'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode from-left">
                                    <input type="radio" name="use_swimlane" value="0" class="fancycheckbox" <?php if (!$board->usesSwimlanes()) echo ' checked'; ?> id="agileboard_use_swimlane_<?= $board->getId(); ?>_0" onchange="Pachno.Project.Planning.toggleSwimlaneDetails(this);">
                                    <label for="agileboard_use_swimlane_<?= $board->getId(); ?>_0" class="list-item">
                                        <span class="icon"><?= fa_image_tag('ban'); ?></span>
                                        <span class="name value"><?php echo __("Don't use swimlanes"); ?></span>
                                    </label>
                                    <input type="radio" name="use_swimlane" value="<?= AgileBoard::SWIMLANES_ISSUES; ?>" class="fancycheckbox" <?php if (!$board->usesSwimlanes()) echo ' checked'; ?> id="agileboard_use_swimlane_<?= $board->getId(); ?>_<?= AgileBoard::SWIMLANES_ISSUES; ?>" onchange="Pachno.Project.Planning.toggleSwimlaneDetails(this);">
                                    <label for="agileboard_use_swimlane_<?= $board->getId(); ?>_<?= AgileBoard::SWIMLANES_ISSUES; ?>" class="list-item multiline">
                                        <span class="icon"><?php echo image_tag('swimlanes_issues.png'); ?></span>
                                        <span class="name">
                                            <span class="title value"><?php echo __('Issue swimlanes'); ?></span>
                                            <span class="description"><?php echo __('The board has a swimlane for each issue of one or more issue type(s).'); ?></span>
                                        </span>
                                    </label>
                                    <input type="radio" name="use_swimlane" value="<?= AgileBoard::SWIMLANES_GROUPING; ?>" class="fancycheckbox" <?php if (!$board->usesSwimlanes()) echo ' checked'; ?> id="agileboard_use_swimlane_<?= $board->getId(); ?>_<?= AgileBoard::SWIMLANES_GROUPING; ?>" onchange="Pachno.Project.Planning.toggleSwimlaneDetails(this);">
                                    <label for="agileboard_use_swimlane_<?= $board->getId(); ?>_<?= AgileBoard::SWIMLANES_GROUPING; ?>" class="list-item multiline">
                                        <span class="icon"><?php echo image_tag('swimlanes_grouping.png'); ?></span>
                                        <span class="name">
                                            <span class="title value"><?php echo __('Issue detail swimlanes'); ?></span>
                                            <span class="description"><?php echo __('The board is grouped in swimlanes where issues that share the same characteristics are grouped together.'); ?></span>
                                        </span>
                                    </label>
                                    <input type="radio" name="use_swimlane" value="<?= AgileBoard::SWIMLANES_EXPEDITE; ?>" class="fancycheckbox" <?php if (!$board->usesSwimlanes()) echo ' checked'; ?> id="agileboard_use_swimlane_<?= $board->getId(); ?>_<?= AgileBoard::SWIMLANES_EXPEDITE; ?>" onchange="Pachno.Project.Planning.toggleSwimlaneDetails(this);">
                                    <label for="agileboard_use_swimlane_<?= $board->getId(); ?>_<?= AgileBoard::SWIMLANES_EXPEDITE; ?>" class="list-item multiline">
                                        <span class="icon"><?php echo image_tag('swimlanes_expedite.png'); ?></span>
                                        <span class="name">
                                            <span class="title value"><?php echo __('Level of service swimlane'); ?></span>
                                            <span class="description"><?php echo __('No general grouping, but an increased level of service swimlane at the top for expediting issues'); ?></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row container" id="agileboard-swimlane-details-container">
                        <div class="form-row" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_container" style="<?php if (!$board->usesSwimlanes() || $board->getSwimlaneType() != AgileBoard::SWIMLANES_ISSUES) echo 'display: none;'; ?>">
                            <div class="helper-text"><?php echo __('The whiteboard will have separate swimlanes for all issues that is of a certain type. Specify which issuetype qualifies as a swimlane.'); ?></div>
                            <div class="fancydropdown-container">
                                <div class="fancydropdown" data-default-label="<?php echo __('Choose one or more issue types'); ?>">
                                    <label><?php echo __('Issue type(s)'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <?php foreach ($issuetypes as $issuetype): ?>
                                            <input type="checkbox" class="fancycheckbox" name="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_details[issuetype][<?= $issuetype->getId(); ?>]" value="<?= $issuetype->getId(); ?>" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_issuetype_<?= $issuetype->getId(); ?>" <?php if ($board->hasSwimlaneFieldValue($issuetype->getID())) echo ' checked'; ?>>
                                            <label for="filter_swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_issuetype_<?= $issuetype->getId(); ?>" class="list-item">
                                                <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                <?= fa_image_tag($issuetype->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $issuetype->getIcon()]); ?>
                                                <span class="name value"><?php echo __($issuetype->getName()); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row" id="agileboard_swimlane_0_container" style="<?php if ($board->usesSwimlanes()) echo 'display: none;'; ?>">
                            <div class="helper-text"><?php echo __('There will be no swimlanes on the board'); ?></div>
                        </div>
                        <div class="form-row" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_container" style="<?php if (!$board->usesSwimlanes() || $board->getSwimlaneType() != AgileBoard::SWIMLANES_GROUPING) echo 'display: none;'; ?>">
                            <div class="helper-text"><?php echo __('The whiteboard will have separate swimlanes / groups for issues that share the same characteristics. Specify which issue detail to group issues by.'); ?></div>
                            <div class="fancydropdown-container">
                                <div class="fancydropdown" data-default-label="<?php echo __('None selected'); ?>">
                                    <label><?php echo __('Group by'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <?php foreach ($swimlane_groups as $value => $description): ?>
                                            <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_GROUPING && $board->getSwimlaneIdentifier() == $value); ?>
                                            <input type="radio" class="fancycheckbox" name="swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier" value="<?php echo $value; ?>" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier_<?= $value; ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                            <label for="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier_<?= $value; ?>" class="list-item">
                                                <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                                <span class="name value"><?php echo $description; ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row container" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_container" style="<?php if (!$board->usesSwimlanes() || $board->getSwimlaneType() != AgileBoard::SWIMLANES_EXPEDITE) echo 'display: none;'; ?>">
                            <div class="form-row">
                                <div class="helper-text"><?php echo __('The whiteboard will have a separate swimlane at the top for prioritized issues, like a expedite line / fastlane. Select which issue details puts issues in this swimlane.'); ?></div>
                                <div class="fancydropdown-container">
                                    <div class="fancydropdown" data-default-label="<?php echo __('None selected'); ?>">
                                        <label><?php echo __('Group by'); ?></label>
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <?php foreach ($swimlane_groups as $value => $description): ?>
                                                <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == $value); ?>
                                                <input type="radio" class="fancycheckbox" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier" value="<?php echo $value; ?>" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?= $value; ?>" <?php if ($is_selected) echo 'checked'; ?> onchange="Pachno.Project.Planning.toggleSwimlaneExpediteDetails(this);">
                                                <label for="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?= $value; ?>" class="list-item">
                                                    <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                                    <span class="name value"><?php echo $description; ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_container_details">
                                <div class="fancydropdown-container" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_priority_values" style="<?php if (!($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'priority')) echo 'display: none;'; ?>">
                                    <div class="fancydropdown" data-default-label="<?php echo __('Choose one or more priorities'); ?>">
                                        <label><?php echo __('Field value(s)'); ?></label>
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <?php foreach ($priorities as $priority): ?>
                                                <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'priority' && $board->hasSwimlaneFieldValue($priority->getID())); ?>
                                                <input type="checkbox" class="fancycheckbox" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_details[priority][<?= $priority->getId(); ?>]" value="<?= $priority->getId(); ?>" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_priority_<?= $priority->getId(); ?>" <?php if ($is_selected) echo ' checked'; ?>>
                                                <label for="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_priority_<?= $priority->getId(); ?>" class="list-item">
                                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                    <span class="name value"><?php echo $priority->getName(); ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="fancydropdown-container" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_severity_values" style="<?php if (!($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'severity')) echo 'display: none;'; ?>">
                                    <div class="fancydropdown" data-default-label="<?php echo __('Choose one or more severities'); ?>">
                                        <label><?php echo __('Field value(s)'); ?></label>
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <?php foreach ($severities as $severity): ?>
                                                <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'severity' && $board->hasSwimlaneFieldValue($severity->getID())); ?>
                                                <input type="checkbox" class="fancycheckbox" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_details[severity][<?= $severity->getId(); ?>]" value="<?= $severity->getId(); ?>" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_severity_<?= $severity->getId(); ?>" <?php if ($is_selected) echo ' checked'; ?>>
                                                <label for="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_severity_<?= $severity->getId(); ?>" class="list-item">
                                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                    <span class="name value"><?php echo $severity->getName(); ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="fancydropdown-container" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_category_values" style="<?php if (!($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'category')) echo 'display: none;'; ?>">
                                    <div class="fancydropdown" data-default-label="<?php echo __('Choose one or more categories'); ?>">
                                        <label><?php echo __('Field value(s)'); ?></label>
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <?php foreach ($categories as $category): ?>
                                                <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'category' && $board->hasSwimlaneFieldValue($category->getID())); ?>
                                                <input type="checkbox" class="fancycheckbox" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_details[category][<?= $category->getId(); ?>]" value="<?= $category->getId(); ?>" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_category_<?= $category->getId(); ?>" <?php if ($is_selected) echo ' checked'; ?>>
                                                <label for="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_category_<?= $category->getId(); ?>" class="list-item">
                                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                    <span class="name value"><?php echo $category->getName(); ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row header">
                        <h3><?php echo __('Whiteboard custom issue fields info'); ?></h3>
                    </div>
                    <div class="form-row">
                        <?php if (!count($issuefields)): ?>
                            <div class="helper-text">
                                <?= fa_image_tag('info-circle'); ?>
                                <span><?php echo __('Any custom fields you add can be shown on the issue cards'); ?></span>
                            </div>
                        <?php else: ?>
                            <div id="issue_field_container" class="list-mode">
                                <div class="header"><?php echo __('Select issue field(s)'); ?></div>
                                <?php foreach ($issuefields as $issuefield): ?>
                                    <input type="checkbox" class="fancycheckbox" value="<?php echo $issuefield->getKey(); ?>" name="issue_field_details[issuetype][<?php echo $issuefield->getKey(); ?>]" id="agileboard_issuefield_value_<?php echo $issuefield->getKey(); ?>" <?php if ($board->hasIssueFieldValue($issuefield->getKey())) echo 'checked'; ?>>
                                    <label for="agileboard_issuefield_value_<?php echo $issuefield->getKey(); ?>">
                                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                        <span class="name"><?php echo __($issuefield->getName()); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="helper-text"><?php echo __('Specify which custom issue fields should also be visible for issue.'); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if ($board->getID()): ?>
                    <input type="hidden" name="board_id" value="<?php echo $board->getID(); ?>">
                <?php endif; ?>
                <div class="form-row submit-container">
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
