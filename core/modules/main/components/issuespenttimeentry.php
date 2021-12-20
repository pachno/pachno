<?php

    use pachno\core\entities\IssueSpentTime;

    /**
     * @var IssueSpentTime $entry
     */

?>
<div id="issue_spenttime_<?= $entry->getID(); ?>" data-spent-time data-id="<?= $entry->getId(); ?>">
    <div class="column name-container"><?= \pachno\core\framework\Context::getI18n()->formatTime($entry->getEditedAt(), 14); ?></div>
    <div class="column"><?php include_component('main/userdropdown', ['user' => $entry->getUser()]); ?></div>
    <div class="column"><?= ($entry->getActivityType() instanceof \pachno\core\entities\ActivityType) ? $entry->getActivityType()->getName() : '-'; ?></div>
    <div class="column"><?= Issue::getFormattedTime($entry->getSpentTime(), true); ?></div>
    <div class="column actions">
        <a href="javascript:void(0);" class="icon-link" onclick="Pachno.UI.Backdrop.show('<?= make_url('get_partial_for_backdrop', array('key' => 'issue_spenttime', 'entry_id' => $entry->getID())); ?>');"><?= fa_image_tag('edit'); ?></a>
        <a href="javascript:void(0);" class="icon-link" onclick="Pachno.UI.Dialog.show('<?= __('Do you really want to remove this time entry?'); ?>', '<?= __('Removing this entry will change the number of points, minutes, hours, days, weeks or months spent on this issue.'); ?>', {yes: {click: function() {Pachno.Issues.deleteTimeEntry('<?= make_url('issue_deletetimespent', array('project_key' => $entry->getIssue()->getProject()->getKey(), 'issue_id' => $entry->getIssueID(), 'entry_id' => $entry->getID())); ?>', <?= $entry->getID(); ?>); }}, no: { click: Pachno.UI.Dialog.dismiss }});return false;"><?= fa_image_tag('times', ['class' => 'delete']); ?></a>
    </div>
</div>
<?php if ($entry->getComment()): ?>
    <tr id="issue_spenttime_<?= $entry->getID(); ?>_comment">
        <td>&nbsp;</td>
        <td colspan="3" class="faded_out" style="font-size: 0.9em; font-style: italic;">
            <?= htmlentities($entry->getComment(), ENT_COMPAT, \pachno\core\framework\Context::getI18n()->getCharset()); ?>
        </td>
    </tr>
<?php endif; ?>
