<?php
    
    use pachno\core\entities\Status;
    use pachno\core\framework\Context;
    use pachno\core\entities;

    /** @var entities\WorkflowStep $step */

?>
<div class="backdrop_box large edit_issuetype">
    <div class="backdrop_detail_header">
        <span><?= __('Add workflow step'); ?></span>
        <a href="javascript:void(0);" class="closer"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form accept-charset="<?= Context::getI18n()->getCharset(); ?>" action="<?= make_url('configure_workflow_step', ['step_id' => $step->getID(), 'workflow_id' => $step->getWorkflow()->getID()]); ?>" data-simple-submit data-auto-close data-update-insert-form-list data-update-container="#workflow-steps-list" data-update-insert id="edit_workflow_step_<?= $step->getID(); ?>_form">
                <input type="hidden" name="workflow_id" value="<?= $step->getWorkflow(); ?>">
                <div class="column small">
                    <div class="form-row">
                        <div class="fancy-dropdown-container">
                            <div class="fancy-dropdown invisible embedded">
                                <label><?php echo __('Issue status'); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <?php foreach (Status::getAll() as $status): ?>
                                        <?php if ($status->getID() != $step->getLinkedStatusID() && $step->getWorkflow()->hasStatusId($status->getID())): ?>
                                        <label class="list-item disabled">
                                            <span class="name"><span class="status-badge disabled"><span class="value"><?php echo $status->getName(); ?></span></span></span>
                                        </label>
                                        <?php else: ?>
                                            <input type="radio" name="status_id" value="<?= $status->getId(); ?>" id="edit_step_details_<?= $step->getId(); ?>_status_<?= $status->getID(); ?>" class="fancy-checkbox" <?php if ($step->getLinkedStatusID() == $status->getId()) echo 'checked'; ?>>
                                            <label class="list-item" for="edit_step_details_<?= $step->getId(); ?>_status_<?= $status->getID(); ?>">
                                                <span class="name"><span class="status-badge" style="background-color: <?php echo $status->getColor(); ?>; color: <?php echo $status->getTextColor(); ?>;"><span class="value"><?php echo $status->getName(); ?></span></span></span>
                                            </label>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row error-container">
                    <div class="error"></div>
                </div>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary">
                        <span><?= __('Add workflow step'); ?></span>
                        <?= fa_image_tag('spinner', ['class' => 'indicator fa-spin']); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
