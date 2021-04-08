<div id="status-field" class="dropper-container status-field">
    <div class="status-badge dropper" style="
        background-color: <?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getColor() : '#FFF'; ?>;
        color: <?php echo ($issue->getStatus() instanceof \pachno\core\entities\Datatype) ? $issue->getStatus()->getTextColor() : '#333'; ?>;
    <?php if (!$issue->getStatus() instanceof \pachno\core\entities\Datatype): ?> display: none;<?php endif; ?>
        " id="status_<?php echo $issue->getID(); ?>_color">
        <span id="status_content"><?php if ($issue->getStatus() instanceof \pachno\core\entities\Datatype) echo __($issue->getStatus()->getName()); ?></span>
    </div>
    <?php if ($issue->canEditStatus()): ?>
        <div class="dropdown-container">
            <div class="list-mode" id="status_change">
                <div class="header">
                    <span class="name"><?= __('Change status'); ?></span>
                </div>
                <?php foreach ($statuses as $status): ?>
                    <div class="list-item">
                        <a href="javascript:void(0);" onclick="Pachno.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'status', 'status_id' => $status->getID())); ?>', 'status');">
                            <div class="status-badge" style="background-color: <?php echo $status->getColor(); ?>;color: <?php echo $status->getTextColor(); ?>;">
                                <span><?php echo __($status->getName()); ?></span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
