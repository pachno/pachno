<?php

    use pachno\core\entities\AgileBoard;
    use pachno\core\entities\Milestone;
    
    /**
     * @var AgileBoard $board
     * @var Milestone $milestone
     */

?>
<div class="backdrop_box large" id="milestone_finish_container">
    <div class="backdrop_detail_header">
        <span><?php
            switch ($board->getType())
            {
                case AgileBoard::TYPE_GENERIC:
                    echo __('Mark milestone as finished');
                    break;
                case AgileBoard::TYPE_SCRUM:
                case AgileBoard::TYPE_KANBAN:
                    echo __('Mark sprint as finished');
                    break;
            }
        ?></span>
        <?= fa_image_tag('times', ['class' => 'icon closer']); ?>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content edit_milestone">
        <div class="form-container">
            <form accept-charset="<?php echo \pachno\core\framework\Context::getI18n()->getCharset(); ?>" action="<?php echo make_url('agile_markmilestonefinished', ['project_key' => $milestone->getProject()->getKey(), 'board_id' => $board->getID(), 'milestone_id' => $milestone->getID()]); ?>" method="post" id="mark_milestone_finished_form" data-milestone-id="<?php echo $milestone->getID(); ?>" data-simple-submit data-auto-close>
                <div class="form-row">
                    <div class="helper-text">
                        <?php
                            switch ($board->getType())
                            {
                                case AgileBoard::TYPE_GENERIC:
                                    echo __('Milestone %milestone_name will be marked as finished.', array('%milestone_name' => $milestone->getName()));
                                    break;
                                case AgileBoard::TYPE_SCRUM:
                                case AgileBoard::TYPE_KANBAN:
                                    echo __('Sprint %milestone_name will be marked as finished.', array('%milestone_name' => $milestone->getName()));
                                    break;
                            }
                        ?>
                    </div>
                </div>
                <div class="form-row">
                    <label for="reached_date_<?php echo $milestone->getID(); ?>"><?php echo __('Milestone reached'); ?></label>
                    <div class="fancy-dropdown-container row-mode">
                        <div class="fancy-dropdown small">
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode from-left">
                                <?php for ($cc = 1;$cc <= 12;$cc++): ?>
                                    <input type="radio" class="fancy-checkbox" name="milestone_finish_reached_month" id="milestone_finish_reached_month_<?= $milestone->getID(); ?>_<?= $cc; ?>" value="<?= $cc; ?>" <?php if ($milestone->getReachedMonth() == $cc || (!$milestone->hasReachedDate() && $cc == date('m'))) echo 'checked'; ?>>
                                    <label for="milestone_finish_reached_month_<?= $milestone->getID(); ?>_<?= $cc; ?>" class="list-item">
                                        <span class="name value"><?= strftime('%B', mktime(0, 0, 0, $cc, 1)); ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="fancy-dropdown small">
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode from-left">
                                <?php for ($cc = 1;$cc <= 31;$cc++): ?>
                                    <input type="radio" class="fancy-checkbox" name="milestone_finish_reached_day" id="milestone_finish_reached_day_<?= $milestone->getID(); ?>_<?= $cc; ?>" value="<?= $cc; ?>" <?php if ($milestone->getReachedDay() == $cc || (!$milestone->hasReachedDate() && $cc == date('d'))) echo 'checked'; ?>>
                                    <label for="milestone_finish_reached_day_<?= $milestone->getID(); ?>_<?= $cc; ?>" class="list-item">
                                        <span class="name value"><?= $cc; ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="fancy-dropdown small">
                            <span class="value"></span>
                            <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                            <div class="dropdown-container list-mode">
                                <?php for ($cc = 1990;$cc <= (date("Y") + 10);$cc++): ?>
                                    <input type="radio" class="fancy-checkbox" name="milestone_finish_reached_year" id="milestone_finish_reached_year_<?= $milestone->getID(); ?>_<?= $cc; ?>" value="<?= $cc; ?>" <?php if ($milestone->getReachedYear() == $cc || (!$milestone->hasReachedDate() && $cc == date('Y'))) echo 'checked'; ?>>
                                    <label for="milestone_finish_reached_year_<?= $milestone->getID(); ?>_<?= $cc; ?>" class="list-item">
                                        <span class="name value"><?= $cc; ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($milestone->countOpenIssues()): ?>
                    <div id="milestone_include_issues" class="form-row">
                        <div class="message-box type-info">
                            <?php echo __('There are %number issue(s) which are not currently resolved. Please select what to do with these issues, below.', array('%number' => $milestone->countOpenIssues())); ?>
                        </div>
                        <label for="select_unresolved_issues_action"><?php echo __('Unresolved issues action'); ?></label>
                        <div class="list-mode">
                            <input type="radio" class="fancy-checkbox trigger-change-milestone-reassign" name="unresolved_issues_action" id="unresolved_issues_action_keep" value="keep">
                            <label for="unresolved_issues_action_keep" class="list-item">
                                <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                <span class="name"><?= __("Don't do anything"); ?></span>
                            </label>
                            <input type="radio" class="fancy-checkbox trigger-change-milestone-reassign" name="unresolved_issues_action" id="unresolved_issues_action_backlog" value="backlog">
                            <label for="unresolved_issues_action_backlog" class="list-item">
                                <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                <span class="name"><?= __("Move to the backlog"); ?></span>
                            </label>
                            <input type="radio" class="fancy-checkbox trigger-change-milestone-reassign" name="unresolved_issues_action" id="unresolved_issues_action_reassign" value="reassign">
                            <label for="unresolved_issues_action_reassign" class="list-item">
                                <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                <span class="name"><?php
                                    switch ($board->getType())
                                    {
                                        case AgileBoard::TYPE_GENERIC:
                                            echo __('Assign to an existing, unfinished milestone');
                                            break;
                                        case AgileBoard::TYPE_SCRUM:
                                        case AgileBoard::TYPE_KANBAN:
                                            echo __('Assign to an existing, unfinished sprint');
                                            break;
                                    }
                                ?></span>
                            </label>
                            <div class="fancy-dropdown-container" id="reassign_select" style="display: none;">
                                <div class="fancy-dropdown auto-size">
                                    <label><?php
                                            switch ($board->getType())
                                            {
                                                case AgileBoard::TYPE_GENERIC:
                                                    echo __('Select milestone');
                                                    break;
                                                case AgileBoard::TYPE_SCRUM:
                                                case AgileBoard::TYPE_KANBAN:
                                                    echo __('Select sprint');
                                                    break;
                                            }
                                        ?></label>
                                    <span class="value"></span>
                                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                    <div class="dropdown-container list-mode">
                                        <?php foreach ($board->getMilestones() as $upcoming_milestone): ?>
                                            <?php if ($upcoming_milestone->getID() == $milestone->getID()) continue; ?>
                                            <input type="radio" class="fancy-checkbox" name="assign_issues_milestone_id" id="assign_issues_milestone_id_<?= $upcoming_milestone->getId(); ?>" value="<?= $upcoming_milestone->getId(); ?>">
                                            <label for="assign_issues_milestone_id_<?= $upcoming_milestone->getId(); ?>" class="list-item">
                                                <span class="name value"><?= $upcoming_milestone->getName(); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <input type="radio" class="fancy-checkbox trigger-change-milestone-reassign" name="unresolved_issues_action" id="unresolved_issues_action_add_new" value="add_new">
                            <label for="unresolved_issues_action_add_new" class="list-item">
                                <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                <span class="name"><?php
                                    switch ($board->getType())
                                    {
                                        case AgileBoard::TYPE_GENERIC:
                                            echo __('Assign to a new milestone');
                                            break;
                                        case AgileBoard::TYPE_SCRUM:
                                        case AgileBoard::TYPE_KANBAN:
                                            echo __('Assign to a new sprint');
                                            break;
                                    }
                                ?></span>
                            </label>
                            <input id="add_new_select" style="display: none;" type="text" name="name" placeholder="<?php
                                switch ($board->getType())
                                {
                                    case AgileBoard::TYPE_GENERIC:
                                        echo __('Enter the name of the new milestone here');
                                        break;
                                    case AgileBoard::TYPE_SCRUM:
                                    case AgileBoard::TYPE_KANBAN:
                                        echo __('Enter the name of the new sprint here');
                                        break;
                                }
                            ?>">
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-row submit-container">
                    <button class="button primary" type="submit">
                        <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?>
                        <span><?= __('Confirm'); ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('body').off('click', '.trigger-change-milestone-reassign');
    $('body').on('click', '.trigger-change-milestone-reassign', function () {
        let $reassign = $('#reassign_select');
        let $add_new = $('#add_new_select');
        switch ($(this).val()) {
            case 'reassign':
                $reassign.show();
                $add_new.hide();
                break;
            case 'keep':
            case 'backlog':
                $reassign.hide();
                $add_new.hide();
                break;
            case 'add_new':
                $reassign.hide();
                $add_new.show();
                $add_new.focus();
                break;
        }
    });
</script>
