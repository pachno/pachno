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
                            <input class="button secondary highlight trigger-backdrop" type="button" value="<?php echo $transition->getName(); ?>" data-url="<?= make_url('get_partial_for_backdrop', ['key' => 'workflow_transition', 'transition_id' => $transition->getID()])."&project_key=".$issue->getProject()->getKey()."&issue_id=".$issue->getID(); ?>">
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
    <button class="button secondary highlight trigger-start-time-tracking">
        <?= fa_image_tag('play-circle', ['class' => 'icon']); ?>
        <span class="name"><?= __('Track time'); ?></span>
    </button>
</div>
