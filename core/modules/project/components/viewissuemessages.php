<div id="issue-messages-container" class="messages-container">
    <?php if (isset($error) && $error): ?>
        <div class="message error" id="viewissue_error">
            <?php if ($error == 'transition_error'): ?>
                <div class="header"><?php echo __('There was an error trying to move this issue to the next step in the workflow'); ?></div>
                <div class="content"><?php include_component('main/issue_transition_error'); ?></div>
            <?php else: ?>
                <div class="header"><?php echo __('There was an error trying to save changes to this issue'); ?></div>
                <div class="content">
                    <?php if (isset($workflow_error) && $workflow_error): ?>
                        <?php echo __('No workflow step matches this issue after changes are saved. Please either use the workflow action buttons, or make sure your changes are valid within the current project workflow for this issue type.'); ?>
                    <?php else: ?>
                        <?php echo $error; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if (isset($issue_saved)): ?>
        <div class="message successful" id="viewissue_saved">
            <span class="content"><?php echo __('Your changes have been saved'); ?></span>
        </div>
    <?php endif; ?>
    <?php if (isset($issue_message)): ?>
        <div class="message successful" id="viewissue_saved">
            <span class="content"><?php echo $issue_message; ?></span>
        </div>
    <?php endif; ?>
    <?php if (isset($issue_file_uploaded)): ?>
        <div class="message successful" id="viewissue_saved">
            <span class="content"><?php echo __('The file was attached to this issue'); ?></span>
        </div>
    <?php endif; ?>
    <?php if ($issue->isBeingWorkedOn() && $issue->isOpen()): ?>
        <div class="message information" id="viewissue_being_worked_on">
            <span class="content">
                <?php if ($issue->getUserWorkingOnIssue()->getID() == $pachno_user->getID()): ?>
                    <?php echo __('You have been working on this issue since %time', array('%time' => \pachno\core\framework\Context::getI18n()->formatTime($issue->getWorkedOnSince(), 6))); ?>
                <?php elseif ($issue->getAssignee() instanceof \pachno\core\entities\Team): ?>
                    <?php echo __('%teamname has been working on this issue since %time', array('%teamname' => $issue->getAssignee()->getName(), '%time' => \pachno\core\framework\Context::getI18n()->formatTime($issue->getWorkedOnSince(), 6))); ?>
                <?php else: ?>
                    <?php echo __('%user has been working on this issue since %time', array('%user' => $issue->getUserWorkingOnIssue()->getNameWithUsername(), '%time' => \pachno\core\framework\Context::getI18n()->formatTime($issue->getWorkedOnSince(), 6))); ?>
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>
    <div class="message error" id="blocking_div"<?php if (!$issue->isBlocking()): ?> style="display: none;"<?php endif; ?>>
        <span class="content"><?php echo __('This issue is blocking the next release'); ?></span>
    </div>
    <?php if ($issue->isDuplicate()): ?>
        <div class="message information" id="viewissue_duplicate">
            <?php echo fa_image_tag('info-circle', ['class' => 'icon']); ?>
            <span class="content"><?php echo __('This issue is a duplicate of issue %link_to_duplicate_issue', array('%link_to_duplicate_issue' => link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getDuplicateOf()->getFormattedIssueNo())), $issue->getDuplicateOf()->getFormattedIssueNo(true)) . ' - "' . $issue->getDuplicateOf()->getTitle() . '"')); ?></span>
        </div>
    <?php endif; ?>
    <?php if ($issue->isClosed()): ?>
        <div class="message information" id="viewissue_closed">
            <?php echo fa_image_tag('info-circle', ['class' => 'icon']); ?>
            <span class="content"><?php echo __('This issue has been closed with status "%status_name" and resolution "%resolution".', array('%status_name' => (($issue->getStatus() instanceof \pachno\core\entities\Status) ? $issue->getStatus()->getName() : __('Not determined')), '%resolution' => (($issue->getResolution() instanceof \pachno\core\entities\Resolution) ? $issue->getResolution()->getName() : __('Not determined')))); ?></span>
        </div>
    <?php endif; ?>
    <?php if ($issue->getProject()->isArchived()): ?>
        <div class="message important" id="viewissue_archived">
            <?php echo fa_image_tag('info-triangle', ['class' => 'icon']); ?>
            <span class="content"><?php echo __('The project this issue belongs to has been archived, and so this issue is now read only'); ?></span>
        </div>
    <?php endif; ?>
</div>
