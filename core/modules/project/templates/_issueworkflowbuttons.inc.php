<div id="workflow-actions" class="workflow-actions-container">
    <ul class="workflow_actions simple-list">
        <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
            <?php $cc = 1; $num_transitions = count($issue->getAvailableWorkflowTransitions()); ?>
            <?php foreach ($issue->getAvailableWorkflowTransitions() as $transition): ?>
                <li class="workflow">
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
                </li>
                <?php $cc++; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <li class="dropper-container">
            <button class="dropper button secondary icon" id="more_actions_<?php echo $issue->getID(); ?>_button"><?= fa_image_tag('ellipsis-v'); ?></button>
            <?php include_component('main/issuemoreactions', array('issue' => $issue, 'times' => false)); ?>
        </li>
    </ul>
</div>
