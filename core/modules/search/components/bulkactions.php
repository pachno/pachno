<?php

use pachno\core\entities\Status;
use pachno\core\framework\Context;

/** @var \pachno\core\entities\User $pachno_user */

$actions = [
    'set_status' => __('Set status'),
    'set_resolution' => __('Set resolution'),
    'set_priority' => __('Set priority'),
    'set_category' => __('Set category'),
    'set_severity' => __('Set severity'),
    'perform_workflow_step' => __('Perform workflow step'),
];

    if (Context::isProjectContext()) {
        $actions['milestone'] = __('Assign to milestone');
    }

?>
<div class="bulk-action-container">
    <form method="post" onsubmit="Pachno.Search.bulkUpdate('<?php echo make_url('issues_bulk_update'); ?>');return false;" id="search-bulk-action-form">
        <?php if (Context::isProjectContext()): ?>
            <input type="hidden" name="project_key" value="<?php echo Context::getCurrentProject()->getKey(); ?>">
        <?php endif; ?>
        <div class="search-bulk-actions unavailable" id="search-bulk-actions">
            <div class="fancy-dropdown-container">
                <div class="fancy-dropdown">
                    <label><?php echo __('With selected issue(s): %action', ['%action' => '']); ?></label>
                    <span class="value"><?= __('Do nothing'); ?></span>
                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                    <div class="dropdown-container list-mode">
                        <input type="radio" class="fancy-checkbox bulk-action-checkbox" name="search_bulk_action" value="" id="bulk-action-selector-nothing" checked>
                        <label for="bulk-action-selector-nothing" class="list-item">
                            <span class="name value"><?= __('Do nothing'); ?></span>
                        </label>
                        <?php foreach ($actions as $action => $description): ?>
                            <input type="radio" class="fancy-checkbox bulk-action-checkbox" name="search_bulk_action" value="<?= $action; ?>" id="bulk-action-selector-<?= $action; ?>">
                            <label for="bulk-action-selector-<?= $action; ?>" class="list-item">
                                <span class="name value"><?= $description; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php if (Context::isProjectContext()): ?>
                <span class="bulk_action_subcontainer" id="bulk_action_subcontainer_assign_milestone" style="display: none;">
                    <select name="milestone" id="bulk_action_assign_milestone" class="focusable" onchange="Pachno.Search.bulkChanger('<?php echo $mode; ?>'); if ($(this).val() == 'new') { ['bulk_action_assign_milestone_top_name', 'bulk_action_assign_milestone_bottom_name'].each(function(element) { $(element).show(); }); } else { ['bulk_action_assign_milestone_top_name', 'bulk_action_assign_milestone_bottom_name'].each(function(element) { $(element).hide(); }); }">
                        <option value="0"><?php echo __('No milestone'); ?></option>
                        <option value="new"><?php echo __('Create new milestone from selected issues'); ?></option>
                        <?php foreach (Context::getCurrentProject()->getMilestonesForIssues() as $milestone_id => $milestone): ?>
                            <option id="bulk_action_assign_milestone_<?php echo $milestone_id; ?>" value="<?php echo $milestone_id; ?>"><?php echo $milestone->getName(); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="milestone_name" style="display: none;" id="bulk_action_assign_milestone_name">
                </span>
            <?php endif; ?>
            <div class="fancy-dropdown-container bulk_action_subcontainer" id="bulk_action_subcontainer_set_status" style="display: none;">
                <div class="fancy-dropdown">
                    <label><?= __('Choose a new status'); ?></label>
                    <span class="value"><?= __('Do nothing'); ?></span>
                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                    <div class="dropdown-container list-mode">
                        <input name="status" id="bulk_action_set_status" checked class="fancy-checkbox" value="0">
                        <label class="list-item" for="bulk_action_set_status">
                            <span class="name"><?php echo __('Do nothing'); ?></span>
                        </label>
                        <?php foreach (Status::getAll() as $status_id => $status): ?>
                            <?php if (!$status->canUserSet($pachno_user)) continue; ?>
                            <input name="status" id="bulk_action_set_status" class="fancy-checkbox" value="<?php echo $status_id; ?>">
                            <label for="bulk_action_set_status" class="list-item">
                                <span class="name"><?php echo $status->getName(); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="fancy-dropdown-container bulk_action_subcontainer" id="bulk_action_subcontainer_set_resolution" style="display: none;">
                <div class="fancy-dropdown">
                    <label><?= __('Choose a new resolution'); ?></label>
                    <span class="value"><?= __('Do nothing'); ?></span>
                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                    <div class="dropdown-container list-mode">
                        <input name="resolution" id="bulk_action_set_resolution" checked class="fancy-checkbox" value="0">
                        <label class="list-item" for="bulk_action_set_resolution">
                            <span class="name"><?php echo __('Do nothing'); ?></span>
                        </label>
                        <?php foreach (\pachno\core\entities\Resolution::getAll() as $resolution_id => $resolution): ?>
                            <?php if (!$resolution->canUserSet($pachno_user)) continue; ?>
                            <input name="resolution" id="bulk_action_set_resolution" class="fancy-checkbox" value="<?php echo $resolution_id; ?>">
                            <label for="bulk_action_set_resolution" class="list-item">
                                <span class="name"><?php echo $resolution->getName(); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="fancy-dropdown-container bulk_action_subcontainer" id="bulk_action_subcontainer_set_priority" style="display: none;">
                <div class="fancy-dropdown">
                    <label><?= __('Choose a new priority'); ?></label>
                    <span class="value"><?= __('Do nothing'); ?></span>
                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                    <div class="dropdown-container list-mode">
                        <input name="priority" id="bulk_action_set_priority" checked class="fancy-checkbox" value="0">
                        <label class="list-item" for="bulk_action_set_priority">
                            <span class="name"><?php echo __('Do nothing'); ?></span>
                        </label>
                        <?php foreach (\pachno\core\entities\Priority::getAll() as $priority_id => $priority): ?>
                            <?php if (!$priority->canUserSet($pachno_user)) continue; ?>
                            <input name="priority" id="bulk_action_set_priority" class="fancy-checkbox" value="<?php echo $priority_id; ?>">
                            <label for="bulk_action_set_priority" class="list-item">
                                <span class="name"><?php echo $priority->getName(); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="fancy-dropdown-container bulk_action_subcontainer" id="bulk_action_subcontainer_set_category" style="display: none;">
                <div class="fancy-dropdown">
                    <label><?= __('Choose a new category'); ?></label>
                    <span class="value"><?= __('Do nothing'); ?></span>
                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                    <div class="dropdown-container list-mode">
                        <input name="category" id="bulk_action_set_category" checked class="fancy-checkbox" value="0">
                        <label class="list-item" for="bulk_action_set_category">
                            <span class="name"><?php echo __('Do nothing'); ?></span>
                        </label>
                        <?php foreach (\pachno\core\entities\Category::getAll() as $category_id => $category): ?>
                            <?php if (!$category->canUserSet($pachno_user)) continue; ?>
                            <input name="category" id="bulk_action_set_category" class="fancy-checkbox" value="<?php echo $category_id; ?>">
                            <label for="bulk_action_set_category" class="list-item">
                                <span class="name"><?php echo $category->getName(); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="fancy-dropdown-container bulk_action_subcontainer" id="bulk_action_subcontainer_set_severity" style="display: none;">
                <div class="fancy-dropdown">
                    <label><?= __('Choose a new severity'); ?></label>
                    <span class="value"><?= __('Do nothing'); ?></span>
                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                    <div class="dropdown-container list-mode">
                        <input name="severity" id="bulk_action_set_severity" checked class="fancy-checkbox" value="0">
                        <label class="list-item" for="bulk_action_set_severity">
                            <span class="name"><?php echo __('Do nothing'); ?></span>
                        </label>
                        <?php foreach (\pachno\core\entities\Severity::getAll() as $severity_id => $severity): ?>
                            <?php if (!$severity->canUserSet($pachno_user)) continue; ?>
                            <input name="severity" id="bulk_action_set_severity" class="fancy-checkbox" value="<?php echo $severity_id; ?>">
                            <label for="bulk_action_set_severity" class="list-item">
                                <span class="name"><?php echo $severity->getName(); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <span class="bulk_action_subcontainer" id="bulk_action_subcontainer_perform_workflow_step" style="display: none;">
                <input type="hidden" id="bulk_action_subcontainer_perform_workflow_step_url" value="<?php echo make_url('get_partial_for_backdrop', array('key' => 'bulk_workflow')); ?>">
            </span>
            <input type="submit" class="button disabled" value="<?php echo __('Apply'); ?>" id="bulk_action_submit">
        </div>
    </form>
    <?php if ($mode == 'bottom'): ?>
        <script type="text/javascript">
            require(['domReady', 'pachno/index'], function (domReady, pachno_index_js) {
                domReady(function () {
                    pachno_index_js.Search.checkToggledCheckboxes();
                });
            });
        </script>
    <?php endif; ?>
</div>
