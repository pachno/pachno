<?php
    
    use pachno\core\entities\WorkflowStep;
    use pachno\core\entities\WorkflowTransition;
    
    /**
     * @var WorkflowTransition $transition
     * @var WorkflowStep $step
     */
    
    $has_transition = $step->hasOutgoingTransition($transition);
    
?>
<a href="javascript:void(0);" class="filtervalue list-item multiline <?php if ($has_transition) echo 'disabled'; ?>" data-add-workflow-transition data-id="<?= $transition->getID(); ?>" data-url="<?= make_url('configure_workflow_step', ['workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getID()]); ?>?transition_id=<?= $transition->getID(); ?>">
    <span class="name">
        <span class="title"><?= $transition->getName(); ?></span>
        <span class="description">
            <span class="icon"><?= fa_image_tag('arrow-right'); ?></span>
            <span><?php include_component('configuration/transitionstatusbadges', ['transition' => $transition]); ?></span>
        </span>
    </span>
</a>
