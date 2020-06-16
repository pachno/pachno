<input type="radio" name="selected_milestone" id="selected_milestone_<?= $milestone->getId(); ?>" value="<?= $milestone->getID(); ?>" class="fancy-checkbox" <?php if ($selected_milestone instanceof Milestone && $selected_milestone->getID() == $milestone->getID()) echo 'checked'; ?>>
<label class="list-item multiline" for="selected_milestone_<?= $milestone->getId(); ?>">
    <span class="icon"><?= fa_image_tag('money-check'); ?></span>
    <span class="name">
        <span class="title value"><?= $milestone->getName(); ?></span>
        <span class="description">
            <span><?= __('Start date'); ?></span>
            <span><?= ($milestone->getStartingDate()) ? \pachno\core\framework\Context::getI18n()->formatTime($milestone->getStartingDate(), 22, true, true) : '-'; ?></span>
            <span><?= __('End date'); ?></span>
            <span><?= ($milestone->getScheduledDate()) ? \pachno\core\framework\Context::getI18n()->formatTime($milestone->getScheduledDate(), 22, true, true) : '-'; ?></span>
        </span>
    </span>
</label>
