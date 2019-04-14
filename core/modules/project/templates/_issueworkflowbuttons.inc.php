<div id="workflow-actions" class="workflow-actions-container">
    <ul class="workflow_actions simple_list">
        <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
            <?php $cc = 1; $num_transitions = count($issue->getAvailableWorkflowTransitions()); ?>
            <?php foreach ($issue->getAvailableWorkflowTransitions() as $transition): ?>
                <li class="workflow">
                    <div class="tooltip from-above rightie">
                        <?php echo $transition->getDescription(); ?>
                    </div>
                    <?php if ($transition->hasTemplate()): ?>
                        <input class="button<?php if ($cc == 1): ?> first<?php endif; if ($cc == $num_transitions): ?> last<?php endif; ?>" type="button" value="<?php echo $transition->getName(); ?>" onclick="Pachno.Issues.showWorkflowTransition(<?php echo $transition->getID(); ?>);">
                    <?php else: ?>
                        <form action="<?php echo make_url('transition_issue', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'transition_id' => $transition->getID())); ?>" method="post">
                            <input type="submit" class="button<?php if ($cc == 1): ?> first<?php endif; if ($cc == $num_transitions): ?> last<?php endif; ?>" value="<?php echo $transition->getName(); ?>">
                        </form>
                    <?php endif; ?>
                </li>
                <?php $cc++; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <li class="dropper-container">
            <input class="dropper button" id="more_actions_<?php echo $issue->getID(); ?>_button" type="button" value="<?php echo ($issue->isWorkflowTransitionsAvailable()) ? __('More actions') : __('Actions'); ?>">
            <?php include_component('main/issuemoreactions', array('issue' => $issue, 'times' => false)); ?>
        </li>
    </ul>
</div>
