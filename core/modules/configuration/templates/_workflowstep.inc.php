<?php

    use pachno\core\framework\Context;

    /** @var \pachno\core\entities\WorkflowStep $step */

?>
<div class="configurable-component form-container workflow-step" data-workflow-step data-id="<?= $step->getID(); ?>" data-options-url="<?= make_url('configure_workflow_step', ['workflow_id' => $step->getWorkflow()->getID(), 'step_id' => $step->getId()]); ?>">
    <div class="row">
        <div class="icon handle"><?= fa_image_tag('grip-vertical'); ?></div>
        <div class="name">
            <span class="status-badge" style="background-color: <?php echo ($step->getLinkedStatus() instanceof \pachno\core\entities\Status) ? $step->getLinkedStatus()->getColor() : '#FFF'; ?>;"><span><?php echo ($step->getLinkedStatus() instanceof \pachno\core\entities\Status) ? $step->getLinkedStatus()->getName() : __('Unknown'); ?></span></span>
        </div>
        <button class="icon open">
            <?= fa_image_tag('angle-right'); ?>
        </button>
    </div>
</div>
