<div id="workflow-actions" class="workflow-actions-container">
    <ul>
        <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
            <li class="workflow">
                <?php if (!$issue->isEditable()): ?>
                    <div class="not-editable">
                        <?= fa_image_tag('lock'); ?>
                        <span class="name"><?= __('Locked'); ?></span>
                        <span class="tooltip from-above"><?= __('Most details of this issue cannot be edited because the workflow defines this step as "locked"'); ?></span>
                    </div>
                <?php endif; ?>
                <?php $cc = 1; $num_transitions = count($issue->getAvailableWorkflowTransitions()); ?>
                <?php foreach ($issue->getAvailableWorkflowTransitions() as $transition): ?>
                    <div class="tooltip-container">
                        <div class="tooltip from-above rightie">
                            <?php echo $transition->getDescription(); ?>
                        </div>
                        <?php if ($transition->hasTemplate()): ?>
                            <input class="button secondary highlight" type="button" value="<?php echo $transition->getName(); ?>" onclick="Pachno.Issues.showWorkflowTransition(<?php echo $transition->getID(); ?>);">
                        <?php else: ?>
                            <form action="<?php echo make_url('transition_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'transition_id' => $transition->getID())); ?>" method="post">
                                <input type="submit" class="button secondary highlight" value="<?php echo $transition->getName(); ?>">
                            </form>
                        <?php endif; ?>
                        <?php $cc++; ?>
                    </div>
                <?php endforeach; ?>
            </li>
        <?php endif; ?>
        <li class="dropper-container">
            <button class="dropper button secondary icon" id="more_actions_<?php echo $issue->getID(); ?>_button"><?= fa_image_tag('ellipsis-v'); ?></button>
            <?php include_component('main/issuemoreactions', array('issue' => $issue, 'times' => false, 'show_workflow_transitions' => false)); ?>
        </li>
    </ul>
</div>
