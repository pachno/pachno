
<div class="form-container">
    <form id="<?php echo $field . '_' . $issue_id; ?>_form" method="post" accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('edit_issue', ['project_key' => $project_key, 'issue_id' => $issue_id]); ?>" data-simple-submit data-update-issues data-auto-close>
        <input type="hidden" name="field" value="estimated_time">
        <div class="form-row">
            <div class="helper-text">
                <div class="image-container"><?= image_tag('/unthemed/onboarding_time_tracking.png', [], true); ?></div>
                <span class="message">
                    <?= __('Update the issue estimate below'); ?>
                </span>
            </div>
        </div>
        <div class="form-row">
            <label for="<?php echo $field . '_' . $issue_id; ?>_input"><?= __('Estimate'); ?></label>
            <input type="text" name="<?php echo $field; ?>" id="<?php echo $field . '_' . $issue_id; ?>_input" placeholder="<?php echo ($field == 'estimated_time') ? __('Enter your estimate here') : __('Enter time spent here'); ?>">
        </div>
        <?php if ($issue->hasChildIssues()): ?>
            <div class="form-row">
                <div class="message-box type-warning">
                    <?= fa_image_tag('exclamation-triangle', ['class' => 'icon']); ?>
                    <span class="message"><?php echo __('Note that the total estimated effort of parent issues is the sum of its child issues. This estimate will be replaced if any child issues are updated.'); ?></span>
                </div>
            </div>
        <?php endif; ?>
        <div class="form-row">
            <div class="helper-text">
                <?php if (isset($board)): ?>
                    <?php if ($board->getType() == AgileBoard::TYPE_SCRUM && $board->getTaskIssueTypeID() == $issue->getIssuetype()->getID()): ?>
                        <?php echo __('Enter a value in plain text, like "1 hour", "7 hours", or similar. Time units not supported by project will not be parsed.'); ?>.
                    <?php elseif ($board->getType() == AgileBoard::TYPE_SCRUM && $board->getTaskIssueTypeID() != $issue->getEpicIssuetypeID()): ?>
                        <?php echo __('Enter a value in plain text, like "1 point", "11 points", or similar. Time units not supported by project will not be parsed.'); ?>.
                    <?php elseif ($board->getType() == AgileBoard::TYPE_GENERIC): ?>
                        <?php echo __('Enter a value in plain text, like "1 week, 2 hours", "1 day", or similar. Time units not supported by project will not be parsed.'); ?>.
                    <?php endif; ?>
                <?php else: ?>
                    <?php echo __('Enter a value in plain text, like "1 week, 2 hours", "3 months and 1 day", or similar. Time units not supported by project will not be parsed.'); ?>.
                <?php endif; ?>
            </div>
        </div>
        <div class="form-row">
            <div class="helper-text">
                <?php echo __('%enter_a_value_in_plain_text or specify below', array('%enter_a_value_in_plain_text' => '')); ?>
            </div>
        </div>
        <div class="row">
            <?php foreach ($issue->getProject()->getTimeUnits() as $time_unit): ?>
                <div class="column auto">
                    <div class="form-row">
                        <input type="text" class="number small" value="<?php echo $times[$time_unit]; ?>" name="<?php echo $time_unit; ?>" id="<?php echo $field . '_' . $issue_id . '_' . $time_unit; ?>_input">
                        <label for="<?php echo $field . '_' . $issue_id . '_' . $time_unit; ?>_input"><?php echo __('%number_of ' . $time_unit, array('%number_of' => '')); ?></label>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="column auto">
                <div class="form-row">
                    <input type="text" class="number small" value="<?php echo $points; ?>" name="points" id="<?php echo $field . '_' . $issue_id; ?>_points_input">
                    <label for="<?php echo $field . '_' . $issue_id; ?>_points_input"><?php echo __('%number_of points', array('%number_of' => '')); ?></label>
                </div>
            </div>
        </div>
        <div class="form-row submit-container">
            <button type="submit" class="button primary">
                <span><?= __('Save'); ?></span>
                <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
            </button>
        </div>
    </form>
</div>
