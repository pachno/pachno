<?php

    use pachno\core\framework\Context;

    /** @var AgileBoard $board */
    use pachno\core\entities\AgileBoard;

?>
<div class="backdrop_box large edit_agileboard">
    <div class="backdrop_detail_header">
        <?php if ($board->getID()): ?>
            <?= fa_image_tag('angle-double-right', ['class' => 'icon closer']); ?>
        <?php endif; ?>
        <span><?php echo ($board->getId()) ? __('Edit board') : __('Create board'); ?></span>
        <?php if (!$board->getID()): ?>
            <?= fa_image_tag('times', ['class' => 'icon closer']); ?>
        <?php endif; ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form accept-charset="<?php echo Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('agile_board', ['project_key' => $board->getProject()->getKey(), 'board_id' => $board->getID()]); ?>" method="post" id="edit-agileboard-form" <?= ($board->getID()) ? 'data-interactive-form' : 'data-simple-submit'; ?>>
                <div class="form-row">
                    <input type="text" class="name-input-enhance" value="<?php echo $board->getName(); ?>" name="name" id="agileboard_name_<?php echo $board->getID(); ?>" placeholder="<?php echo __('Type a short, descriptive name such as "Project planning board"'); ?>">
                    <label for="agileboard_name_<?php echo $board->getID(); ?>"><?php echo __('Name'); ?></label>
                    <input class="fancy-checkbox" type="checkbox" name="is_private" value="1" id="agileboard_is_private" <?php if ($board->isPrivate()) echo 'checked'; ?>>
                    <label for="agileboard_is_private">
                        <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                        <span><?= __('Make this board private (only visible to me)'); ?></span>
                    </label>
                </div>
                <div class="form-row color-picker">
                    <label><?= __('Background color'); ?></label>
                    <div class="fancy-checkbox-grid">
                        <input type="radio" name="background_color" value="" class="fancy-checkbox" id="background_color_none"<?php if ($board->getBackgroundColor() == ''): ?> checked<?php endif; ?>>
                        <label class="empty" for="background_color_none"><?php echo fa_image_tag('check-circle', ['class' => 'checked']); ?><span><?= __('None'); ?></span></label>
                        <?php foreach (AgileBoard::getAvailableColors() as $index => $color): ?>
                            <input type="radio" name="background_color" value="<?= $color; ?>" class="fancy-checkbox" id="background_color_<?= $index; ?>"<?php if ($board->getBackgroundColor() == $color): ?> checked<?php endif; ?>>
                            <label for="background_color_<?= $index; ?>" style="background-color: <?= $color; ?>"><?php echo fa_image_tag('check-circle', ['class' => 'checked']); ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-row">
                    <div class="fancy-dropdown-container">
                        <div class="fancy-dropdown">
                            <label><?php echo __('Type of board'); ?></label>
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <input class="fancy-checkbox trigger-whiteboard-type-change" type="radio" name="type" value="<?php echo AgileBoard::TYPE_GENERIC; ?>" id="agileboard_type_checkbox_<?php echo AgileBoard::TYPE_GENERIC; ?>" <?php if ($board->getType() == AgileBoard::TYPE_GENERIC) echo 'checked'; ?>>
                                <label for="agileboard_type_checkbox_<?php echo AgileBoard::TYPE_GENERIC; ?>" class="list-item multiline">
                                    <span class="icon"><?php echo fa_image_tag('columns'); ?></span>
                                    <span class="name">
                                        <span class="title value"><?php echo __('Generic planning board'); ?></span>
                                        <span class="description">
                                            <?php echo __('A generic planning board for planning and categorizing work.'); ?>
                                        </span>
                                    </span>
                                </label>
                                <input class="fancy-checkbox trigger-whiteboard-type-change" type="radio" name="type" value="<?php echo AgileBoard::TYPE_SCRUM; ?>" id="agileboard_type_checkbox_<?php echo AgileBoard::TYPE_SCRUM; ?>" <?php if ($board->getType() == AgileBoard::TYPE_SCRUM) echo 'checked'; ?>>
                                <label for="agileboard_type_checkbox_<?php echo AgileBoard::TYPE_SCRUM; ?>" class="list-item multiline">
                                    <span class="icon"><?php echo fa_image_tag('redo-alt'); ?></span>
                                    <span class="name">
                                        <span class="title value"><?php echo __('Scrum board'); ?></span>
                                        <span class="description">
                                            <?php echo __('A board tailored for scrum/scrum-like workflows - with story cards, stories, epics and estimates'); ?>
                                        </span>
                                    </span>
                                </label>
                                <input class="fancy-checkbox trigger-whiteboard-type-change" type="radio" name="type" value="<?php echo AgileBoard::TYPE_KANBAN; ?>" id="agileboard_type_checkbox_<?php echo AgileBoard::TYPE_KANBAN; ?>" <?php if ($board->getType() == AgileBoard::TYPE_KANBAN) echo 'checked'; ?> disabled>
                                <label for="agileboard_type_checkbox_<?php echo AgileBoard::TYPE_KANBAN; ?>" class="list-item multiline disabled">
                                    <span class="icon"><?php echo fa_image_tag('th-list'); ?></span>
                                    <span class="name">
                                        <span class="title value"><?php echo __('Kanban board'); ?><span class="count-badge"><?= __('Disabled in this release'); ?></span></span>
                                        <span class="description">
                                            <?php echo __('A Kanban-style board with workload limits.'); ?>
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
                        <div class="fancy-dropdown-container from-bottom">
                            <div class="fancy-dropdown" data-default-label="<?php echo __('None selected'); ?>">
                                <label><?php echo __('Backlog search'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach ($autosearches as $value => $description): ?>
                                        <?php $is_selected = ($board->usesAutogeneratedSearchBacklog() && $board->getAutogeneratedSearch() == $value); ?>
                                        <input type="radio" class="fancy-checkbox" value="predefined_<?php echo $value; ?>" name="backlog_search" id="backlog_search_predefined_<?php echo $value; ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                        <label for="backlog_search_predefined_<?php echo $value; ?>" class="list-item">
                                            <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                            <span class="name value"><?php echo $description; ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                    <?php if (count($savedsearches['public']) > 0): ?>
                                        <?php foreach ($savedsearches['public'] as $savedsearch): ?>
                                            <?php $is_selected = ($board->usesSavedSearchBacklog() && $board->getBacklogSearch()->getID() == $savedsearch->getID()); ?>
                                            <input type="radio" class="fancy-checkbox" value="saved_<?php echo $savedsearch->getID(); ?>" name="backlog_search" id="backlog_search_saved_<?php echo $savedsearch->getID(); ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                            <label for="backlog_search_saved_<?php echo $savedsearch->getID(); ?>" class="list-item">
                                                <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                                <span class="name value"><?php echo $savedsearch->getName(); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <div class="separator"></div>
                                    <?php if (count($savedsearches['user']) > 0): ?>
                                        <?php foreach ($savedsearches['user'] as $savedsearch): ?>
                                            <?php $is_selected = ($board->usesSavedSearchBacklog() && $board->getBacklogSearch()->getID() == $savedsearch->getID()); ?>
                                            <input type="radio" class="fancy-checkbox" value="saved_<?php echo $savedsearch->getID(); ?>" name="backlog_search" id="backlog_search_saved_<?php echo $savedsearch->getID(); ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                            <label for="backlog_search_saved_<?php echo $savedsearch->getID(); ?>" class="list-item">
                                                <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                                <span class="name value"><?php echo $savedsearch->getName(); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="helper-text">
                                <?php echo __('The backlog search is used to define which issues are available in the backlog and on the board.'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="column <?php if ($board->getType() == AgileBoard::TYPE_GENERIC) echo 'hidden'; ?>" id="edit_agile_board_epic_issue_type_column">
                            <div class="form-row">
                                <div class="fancy-dropdown-container from-bottom">
                                    <div class="fancy-dropdown" data-default-label="<?= __('Pick an issue type'); ?>">
                                        <label><?php echo __('Epic issue type'); ?></label>
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <input type="radio" value="0" name="epic_issuetype_id" id="epic_issuetype_id_0" class="fancy-checkbox" <?php if (!$board->getEpicIssuetypeID()) echo 'checked'; ?>>
                                            <label for="epic_issuetype_id_0" class="list-item">
                                                <span class="name value"><?php echo __("Don't use epics"); ?></span>
                                            </label>
                                            <div class="list-item separator"></div>
                                            <?php foreach ($issuetypes as $issuetype): ?>
                                                <input type="radio" value="<?php echo $issuetype->getID(); ?>" name="epic_issuetype_id" id="epic_issuetype_id_<?php echo $issuetype->getID(); ?>" class="fancy-checkbox" <?php if ($board->getEpicIssuetypeID() == $issuetype->getID()) echo 'checked'; ?>>
                                                <label for="epic_issuetype_id_<?php echo $issuetype->getID(); ?>" class="list-item">
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
                                <div class="fancy-dropdown-container from-bottom">
                                    <div class="fancy-dropdown" data-default-label="<?php echo __('Pick an issue type'); ?>">
                                        <label><?php echo __('Task issue type'); ?></label>
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <?php foreach ($issuetypes as $issuetype): ?>
                                                <input type="checkbox" value="<?php echo $issuetype->getID(); ?>" name="task_issuetype_id" id="task_issuetype_id_<?php echo $issuetype->getID(); ?>" class="fancy-checkbox" <?php if ($board->getTaskIssuetypeID() == $issuetype->getID()) echo 'checked'; ?>>
                                                <label for="task_issuetype_id_<?php echo $issuetype->getID(); ?>" class="list-item">
                                                    <?= fa_image_tag($issuetype->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $issuetype->getIcon()]); ?>
                                                    <span class="name value"><?php echo __($issuetype->getName()); ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row header">
                        <h3><?php echo __('Whiteboard mode settings'); ?></h3>
                    </div>
                    <div class="form-row">
                        <div class="fancy-dropdown-container from-bottom">
                            <div class="fancy-dropdown">
                                <label><?php echo __('Whiteboard swimlanes'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <input type="radio" name="use_swimlane" value="0" class="fancy-checkbox trigger-swimlane-toggle" <?php if (!$board->usesSwimlanes()) echo ' checked'; ?> id="agileboard_use_swimlane_<?= $board->getId(); ?>_0">
                                    <label for="agileboard_use_swimlane_<?= $board->getId(); ?>_0" class="list-item">
                                        <span class="icon"><?= fa_image_tag('ban'); ?></span>
                                        <span class="name value"><?php echo __("Don't use swimlanes"); ?></span>
                                    </label>
                                    <div class="list-item separator"></div>
                                    <input type="radio" name="use_swimlane" value="<?= AgileBoard::SWIMLANES_EPICS; ?>" class="fancy-checkbox trigger-swimlane-toggle" <?php if ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EPICS) echo ' checked'; ?> id="agileboard_use_swimlane_<?= $board->getId(); ?>_<?= AgileBoard::SWIMLANES_EPICS; ?>">
                                    <label for="agileboard_use_swimlane_<?= $board->getId(); ?>_<?= AgileBoard::SWIMLANES_EPICS; ?>" class="list-item multiline">
                                        <span class="icon large"><?= image_tag('/unthemed/icon_issue_swimlanes.png', [], true); ?></span>
                                        <span class="name">
                                            <span class="title value"><?php echo __('Epics swimlanes'); ?></span>
                                            <span class="description"><?php echo __('The board has a swimlane for each epic with issues assigned in the selected milestone'); ?></span>
                                        </span>
                                    </label>
                                    <input type="radio" name="use_swimlane" value="<?= AgileBoard::SWIMLANES_ISSUES; ?>" class="fancy-checkbox trigger-swimlane-toggle" <?php if ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_ISSUES) echo ' checked'; ?> id="agileboard_use_swimlane_<?= $board->getId(); ?>_<?= AgileBoard::SWIMLANES_ISSUES; ?>">
                                    <label for="agileboard_use_swimlane_<?= $board->getId(); ?>_<?= AgileBoard::SWIMLANES_ISSUES; ?>" class="list-item multiline">
                                        <span class="icon large"><?= image_tag('/unthemed/icon_issue_swimlanes.png', [], true); ?></span>
                                        <span class="name">
                                            <span class="title value"><?php echo __('Issue swimlanes'); ?></span>
                                            <span class="description"><?php echo __('The board has a swimlane for each issue of one or more issue type(s)'); ?></span>
                                        </span>
                                    </label>
                                    <input type="radio" name="use_swimlane" value="<?= AgileBoard::SWIMLANES_GROUPING; ?>" class="fancy-checkbox trigger-swimlane-toggle" <?php if ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_GROUPING) echo ' checked'; ?> id="agileboard_use_swimlane_<?= $board->getId(); ?>_<?= AgileBoard::SWIMLANES_GROUPING; ?>">
                                    <label for="agileboard_use_swimlane_<?= $board->getId(); ?>_<?= AgileBoard::SWIMLANES_GROUPING; ?>" class="list-item multiline">
                                        <span class="icon large"><?= image_tag('/unthemed/icon_issue_details_swimlanes.png', [], true); ?></span>
                                        <span class="name">
                                            <span class="title value"><?php echo __('Issue detail swimlanes'); ?></span>
                                            <span class="description"><?php echo __('The board is grouped in swimlanes where issues that share the same characteristics are grouped together'); ?></span>
                                        </span>
                                    </label>
                                    <input type="radio" name="use_swimlane" value="<?= AgileBoard::SWIMLANES_EXPEDITE; ?>" class="fancy-checkbox trigger-swimlane-toggle" <?php if ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE) echo ' checked'; ?> id="agileboard_use_swimlane_<?= $board->getId(); ?>_<?= AgileBoard::SWIMLANES_EXPEDITE; ?>">
                                    <label for="agileboard_use_swimlane_<?= $board->getId(); ?>_<?= AgileBoard::SWIMLANES_EXPEDITE; ?>" class="list-item multiline">
                                        <span class="icon large"><?= image_tag('/unthemed/icon_level_of_service_swimlanes.png', [], true); ?></span>
                                        <span class="name">
                                            <span class="title value"><?php echo __('Level of service swimlane'); ?></span>
                                            <span class="description"><?php echo __('No general grouping, but an increased level of service swimlane at the top for expediting issues'); ?></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row container" id="agileboard-swimlane-details-container">
                        <div class="form-row" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_container" style="<?php if (!$board->usesSwimlanes() || $board->getSwimlaneType() != AgileBoard::SWIMLANES_ISSUES) echo 'display: none;'; ?>">
                            <input type="hidden" name="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_identifier" value="<?= AgileBoard::SWIMLANE_IDENTIFIER_ISSUETYPE; ?>">
                            <div class="helper-text"><span><?php echo __('The whiteboard will have separate swimlanes for all issues that is of a certain type. Specify which issuetype qualifies as a swimlane.'); ?></span></div>
                            <div class="fancy-dropdown-container from-bottom">
                                <div class="fancy-dropdown" data-default-label="<?php echo __('Choose one or more issue types'); ?>">
                                    <label><?php echo __('Issue type(s)'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <?php foreach ($issuetypes as $issuetype): ?>
                                            <input type="checkbox" class="fancy-checkbox" name="swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_details[<?= AgileBoard::SWIMLANE_IDENTIFIER_ISSUETYPE; ?>][<?= $issuetype->getId(); ?>]" value="<?= $issuetype->getId(); ?>" id="filter_swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_<?= AgileBoard::SWIMLANE_IDENTIFIER_ISSUETYPE; ?>_<?= $issuetype->getId(); ?>" <?php if ($board->hasSwimlaneFieldValue($issuetype->getID())) echo ' checked'; ?>>
                                            <label for="filter_swimlane_<?php echo AgileBoard::SWIMLANES_ISSUES; ?>_<?= AgileBoard::SWIMLANE_IDENTIFIER_ISSUETYPE; ?>_<?= $issuetype->getId(); ?>" class="list-item">
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
                            <div class="helper-text"><span><?php echo __('The whiteboard will have separate swimlanes / groups for issues that share the same characteristics. Specify which issue detail to group issues by.'); ?></span></div>
                            <div class="fancy-dropdown-container from-bottom">
                                <div class="fancy-dropdown" data-default-label="<?php echo __('None selected'); ?>">
                                    <label><?php echo __('Group by'); ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <?php foreach ($swimlane_groups as $value => $description): ?>
                                            <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_GROUPING && $board->getSwimlaneIdentifier() == $value); ?>
                                            <input type="radio" class="fancy-checkbox" name="swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier" value="<?php echo $value; ?>" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier_<?= $value; ?>" <?php if ($is_selected) echo 'checked'; ?>>
                                            <label for="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_GROUPING; ?>_identifier_<?= $value; ?>" class="list-item">
                                                <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                                <span class="name value"><?php echo $description; ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row container" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_container" style="<?php if (!$board->usesSwimlanes() || $board->getSwimlaneType() != AgileBoard::SWIMLANES_EXPEDITE) echo 'display: none;'; ?>">
                            <div class="form-row">
                                <div class="helper-text"><?php echo __('The whiteboard will have a separate swimlane at the top for prioritized issues, like a expedite line / fastlane. Select which issue details puts issues in this swimlane.'); ?></div>
                                <div class="fancy-dropdown-container from-bottom">
                                    <div class="fancy-dropdown" data-default-label="<?php echo __('None selected'); ?>">
                                        <label><?php echo __('Group by'); ?></label>
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <?php foreach ($swimlane_groups as $value => $description): ?>
                                                <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == $value); ?>
                                                <input type="radio" class="fancy-checkbox trigger-toggle-swimlane-expedite-details" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier" value="<?php echo $value; ?>" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_<?= $value; ?>" <?php if ($is_selected) echo 'checked'; ?>>
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
                                <div class="fancy-dropdown-container from-bottom" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_priority_values" style="<?php if (!($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'priority')) echo 'display: none;'; ?>">
                                    <div class="fancy-dropdown" data-default-label="<?php echo __('Choose one or more priorities'); ?>">
                                        <label><?php echo __('Field value(s)'); ?></label>
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <?php foreach ($priorities as $priority): ?>
                                                <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'priority' && $board->hasSwimlaneFieldValue($priority->getID())); ?>
                                                <input type="checkbox" class="fancy-checkbox" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_details[priority][<?= $priority->getId(); ?>]" value="<?= $priority->getId(); ?>" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_priority_<?= $priority->getId(); ?>" <?php if ($is_selected) echo ' checked'; ?>>
                                                <label for="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_priority_<?= $priority->getId(); ?>" class="list-item">
                                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                    <span class="name value"><?php echo $priority->getName(); ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="fancy-dropdown-container from-bottom" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_severity_values" style="<?php if (!($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'severity')) echo 'display: none;'; ?>">
                                    <div class="fancy-dropdown" data-default-label="<?php echo __('Choose one or more severities'); ?>">
                                        <label><?php echo __('Field value(s)'); ?></label>
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <?php foreach ($severities as $severity): ?>
                                                <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'severity' && $board->hasSwimlaneFieldValue($severity->getID())); ?>
                                                <input type="checkbox" class="fancy-checkbox" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_details[severity][<?= $severity->getId(); ?>]" value="<?= $severity->getId(); ?>" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_severity_<?= $severity->getId(); ?>" <?php if ($is_selected) echo ' checked'; ?>>
                                                <label for="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_severity_<?= $severity->getId(); ?>" class="list-item">
                                                    <?= fa_image_tag('check-square', ['class' => 'checked'], 'far') . fa_image_tag('square', ['class' => 'unchecked'], 'far'); ?>
                                                    <span class="name value"><?php echo $severity->getName(); ?></span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="fancy-dropdown-container from-bottom" id="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_category_values" style="<?php if (!($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'category')) echo 'display: none;'; ?>">
                                    <div class="fancy-dropdown" data-default-label="<?php echo __('Choose one or more categories'); ?>">
                                        <label><?php echo __('Field value(s)'); ?></label>
                                        <span class="value"></span>
                                        <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                        <div class="dropdown-container list-mode">
                                            <?php foreach ($categories as $category): ?>
                                                <?php $is_selected = ($board->usesSwimlanes() && $board->getSwimlaneType() == AgileBoard::SWIMLANES_EXPEDITE && $board->getSwimlaneIdentifier() == 'category' && $board->hasSwimlaneFieldValue($category->getID())); ?>
                                                <input type="checkbox" class="fancy-checkbox" name="swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_details[category][<?= $category->getId(); ?>]" value="<?= $category->getId(); ?>" id="agileboard_swimlane_<?php echo AgileBoard::SWIMLANES_EXPEDITE; ?>_identifier_category_<?= $category->getId(); ?>" <?php if ($is_selected) echo ' checked'; ?>>
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
                                    <input type="checkbox" class="fancy-checkbox" value="<?php echo $issuefield->getKey(); ?>" name="issue_field_details[<?php echo $issuefield->getKey(); ?>]" id="agileboard_issuefield_value_<?php echo $issuefield->getKey(); ?>" <?php if ($board->hasIssueFieldValue($issuefield->getKey())) echo 'checked'; ?>>
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
                <?php else: ?>
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
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
<script>
    $('body').off('click', '.trigger-swimlane-toggle');
    $('body').on('click', '.trigger-swimlane-toggle', function () {
        const $selected_item = $(this);
        $('#agileboard-swimlane-details-container').children().hide();
        $('#agileboard_swimlane_' + $selected_item.val() + '_container').show();
    });
    $('body').off('click', '.trigger-whiteboard-type-change');
    $('body').on('click', '.trigger-whiteboard-type-change', function () {
        const $selected_item = $(this);
        if ($selected_item.val() == '<?= AgileBoard::TYPE_GENERIC; ?>') {
            $('#edit_agile_board_epic_issue_type_column').addClass('hidden');
        } else {
            $('#edit_agile_board_epic_issue_type_column').removeClass('hidden');
        }
    });
    $('body').off('click', '.trigger-toggle-swimlane-expedite-details');
    $('body').on('click', '.trigger-toggle-swimlane-expedite-details', function () {
        const $selected_item = $(this);
        $('#agileboard_swimlane_expedite_container_details').children().hide();
        $('#swimlane_expedite_identifier_' + $selected_item.val() + '_values').show();
    });
</script>
