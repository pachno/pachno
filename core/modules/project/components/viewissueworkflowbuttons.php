<div id="workflow-actions" class="workflow-actions-container">
    <ul>
        <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
            <li class="workflow">
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
    </ul>
</div>
