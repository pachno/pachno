<?php

/** @var \pachno\core\entities\WorkflowScheme $scheme */
/** @var \pachno\core\entities\Issuetype[] $issue_types */

?>
<div class="backdrop_box large">
    <div class="backdrop_detail_header">
        <span><?php echo ($scheme->getId()) ? __('Edit workflow scheme') : __('Create new workflow scheme'); ?></span>
        <a href="javascript:void(0);" class="closer"><?= fa_image_tag('times'); ?></a>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content">
        <div class="form-container">
            <form action="<?php echo make_url('configure_workflow_scheme', ['scheme_id' => $scheme->getID()]); ?>" data-auto-close data-simple-submit <?= ($scheme->getID()) ? ' data-update-container="#workflow_scheme_'.$scheme->getID().'" data-update-replace' : ' data-update-container="#workflow-schemes-list" data-update-insert'; ?> method="post" id="workflow_scheme_form">
                <?php if (isset($clone)): ?>
                    <input type="hidden" name="clone" value="1">
                <?php endif; ?>
                <div class="form-row">
                    <input type="text" class="name-input-enhance" value="<?php echo $scheme->getName(); ?>" name="name" id="edit_scheme_<?php echo $scheme->getID(); ?>_name" placeholder="<?php echo __('Type a short, descriptive name such as "Service desk workflow"'); ?>">
                    <label for="edit_scheme_<?php echo $scheme->getID(); ?>_name"><?php echo __('Name'); ?></label>
                </div>
                <div class="form-row">
                    <input type="text" value="<?php echo $scheme->getDescription(); ?>" name="description" id="edit_scheme_<?php echo $scheme->getID(); ?>_description" placeholder="<?php echo __('Add an optional description'); ?>">
                    <label for="edit_scheme_<?php echo $scheme->getID(); ?>_description"><?php echo __('Description (optional)'); ?></label>
                </div>
                <div class="form-row header">
                    <h3><?php echo __('Issue type workflow associations'); ?></h3>
                </div>
                <?php foreach ($issue_types as $issue_type): ?>
                    <div class="form-row">
                        <div class="fancy-dropdown-container">
                            <div class="fancy-dropdown">
                                <label><?= fa_image_tag($issue_type->getFontAwesomeIcon(), ['class' => 'issuetype-icon issuetype-' . $issue_type->getType()]); ?><?php echo $issue_type->getName(); ?></label>
                                <span class="value"></span>
                                <?= fa_image_tag('angle-down', ['class' => 'expander']); ?>
                                <div class="dropdown-container list-mode">
                                    <input class="fancy-checkbox" type="radio" name="workflow_id[<?php echo $issue_type->getID(); ?>]" id="edit_workflow_id_<?php echo $issue_type->getID(); ?>" value=""<?php if (!$scheme->hasWorkflowAssociatedWithIssuetype($issue_type)) echo 'checked'; ?>>
                                    <label for="edit_workflow_id_<?php echo $issue_type->getID(); ?>" class="list-item">
                                        <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                        <span class="name value"><?php echo __('Use default workflow'); ?></span>
                                    </label>
                                    <?php foreach (\pachno\core\entities\tables\Workflows::getTable()->getAll() as $workflow): ?>
                                        <input class="fancy-checkbox" type="radio" name="workflow_id[<?php echo $issue_type->getID(); ?>]" id="edit_workflow_id_<?php echo $issue_type->getID(); ?>_<?php echo $workflow->getID(); ?>" value="<?php echo $workflow->getID(); ?>"<?php if ($scheme->hasWorkflowAssociatedWithIssuetype($issue_type) && $scheme->getWorkflowForIssuetype($issue_type)->getID() == $workflow->getID()) echo 'checked'; ?>>
                                        <label for="edit_workflow_id_<?php echo $issue_type->getID(); ?>_<?php echo $workflow->getID(); ?>" class="list-item">
                                            <?= fa_image_tag('check-circle', ['class' => 'checked'], 'far') . fa_image_tag('circle', ['class' => 'unchecked'], 'far'); ?>
                                            <span class="name value"><?php echo $workflow->getName(); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="form-row submit-container">
                    <button type="submit" class="button primary">
                        <span><?php echo __('Save workflow associations'); ?></span>
                        <?= fa_image_tag('spinner', ['class' => 'fa-spin indicator']); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
