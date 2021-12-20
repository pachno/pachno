<?php

    use pachno\core\entities\Issue;
    use pachno\core\entities\IssueSpentTime;
    use pachno\core\entities\User;
    use pachno\core\framework\Context;

    /**
     * @var Issue $issue
     * @var IssueSpentTime $entry
     * @var User $pachno_user
     * @var string $url
     */

?>
<form class="form-container row" id="edit_spent_time_entry_<?= $entry->getID(); ?>" action="<?= $url; ?>" method="post" <?php if (!$entry->getID()): ?>data-simple-submit data-update-container="#timespent_list" data-update-insert data-update-insert-form-list<?php else: ?>data-interactive-form data-spent-time-entry data-spent-time-entry-id="<?= $entry->getID(); ?>"<?php endif; ?> data-update-issues>
    <div class="column">
        <span><?= Context::getI18n()->formatTime(NOW, 14); ?></span>
    </div>
    <div class="column name-container list">
        <div class="line <?php if ($entry->getID() && !$entry->isAutomatic() && !$entry->isMultiTime()) echo 'hidden'; ?>">
            <button type="button" class="button secondary icon toggle-line">
                <?= fa_image_tag('list-ol', ['class' => 'icon']); ?>
            </button>
            <input type="text" id="issue_<?php echo $issue->getID(); ?>_timeentry" name="timespent_manual" placeholder="<?php echo __("'1 hour', '1 day, 3 hours' or similar"); ?>." value="<?php if ($entry->getID()) echo Issue::getFormattedTime($entry->getSpentTime(), true, false); ?>">
        </div>
        <div class="line <?php if (!$entry->getID() || $entry->isAutomatic() || $entry->isMultiTime()) echo 'hidden'; ?>">
            <button type="button" class="button secondary icon toggle-line">
                <?= fa_image_tag('keyboard', ['class' => 'icon']); ?>
            </button>
            <input type="text" name="timespent_specified_value" class="number small" value="<?= $entry->getPrintableValue(); ?>">
            <div class="fancy-dropdown-container">
                <div class="fancy-dropdown" data-default-label="<?= __('Choose from this list') ?>">
                    <label></label>
                    <span class="value"></span>
                    <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                    <div class="dropdown-container list-mode">
                        <?php foreach ($issue->getSpentTimeUnitsWithPoints() as $time => $description): ?>
                            <input class="fancy-checkbox" type="radio" id="timespent_<?= $entry->getId(); ?>_new_type_<?= $time; ?>" name="timespent_specified_type" value="<?php echo $time; ?>" <?php if ($time == $entry->getSelectedTimeEntry()) echo 'checked'; ?>>
                            <label for="timespent_<?= $entry->getId(); ?>_new_type_<?= $time; ?>" class="list-item">
                                <span class="name value"><?php echo $description; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="column">
        <?php if ($entry->getID()): ?>
            <?php include_component('main/userdropdown', ['user' => $entry->getUser()]); ?>
        <?php else: ?>
            <?= __('You'); ?>
        <?php endif; ?>
    </div>
    <div class="column">
        <div class="fancy-dropdown-container">
            <div class="fancy-dropdown" data-default-label="<?= __('Not specified'); ?>">
                <span class="value"></span>
                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                <div class="dropdown-container list-mode">
                    <?php foreach (\pachno\core\entities\ActivityType::getAll() as $activitytype): ?>
                        <input name="timespent_activitytype" id="timespent_<?= $entry->getId(); ?>_activitytype_<?= $activitytype->getID(); ?>" value="<?php echo $activitytype->getID(); ?>" <?php if ($activitytype->getID() == $entry->getActivityTypeID()) echo ' checked'; ?>>
                        <label for="timespent_<?= $entry->getId(); ?>_activitytype_<?= $activitytype->getID(); ?>" class="list-item">
                            <span class="name value"><?php echo $activitytype->getName(); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="column actions">
        <?php if ($entry->getID()): ?>
            <a class="button secondary icon danger" href="javascript:void(0);" onclick="Pachno.UI.Dialog.show('<?= __('Remove spent time entry?'); ?>', '<?= __('Please confirm that you want to remove this spent time entry'); ?>', {yes: {click: function() {Pachno.trigger(Pachno.EVENTS.issue.removeSpentTime, { url: '<?= make_url('issue_edittimespent', ['project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'entry_id' => $entry->getId()]); ?>', id: <?= $entry->getID(); ?>, issue_id: <?= $issue->getID(); ?> })}}, no: { click: Pachno.UI.Dialog.dismiss }});">
                <span class="icon"><?= fa_image_tag('times'); ?></span>
            </a>
        <?php else: ?>
            <button type="submit" class="button secondary">
                <span><?php echo __('Add'); ?></span>
                <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
            </button>
        <?php endif; ?>
    </div>
    <input type="hidden" name="completed" value="1">
</form>
