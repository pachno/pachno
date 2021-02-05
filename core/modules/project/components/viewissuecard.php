<?php

    use pachno\core\entities\Issue;
    use pachno\core\framework\Settings;
    use pachno\core\framework\Context;

    /**
     * @var string $set_field_route
     * @var Issue $issue
     */

?>
<div class="backdrop_box huge" id="issue-card-popup">
    <div class="backdrop_detail_header">
        <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
            <?php include_component('project/viewissueworkflowbuttons', ['issue' => $issue, 'showLockedStatus' => false]); ?>
        <?php else: ?>
            <span class="title-crumbs">
                <?php include_component('project/issueparent_crumbs', array('issue' => $issue)); ?>
            </span>
        <?php endif; ?>
        <div class="dropper-container">
            <button class="dropper button secondary" id="more_actions_<?= $issue->getID(); ?>_button"><span><?= __('Actions'); ?></span><?= fa_image_tag('chevron-down'); ?></button>
            <?php include_component('main/issuemoreactions', array('issue' => $issue, 'times' => false, 'show_workflow_transitions' => false)); ?>
        </div>
        <?php include_component('project/issuefavorite', array('issue' => $issue)); ?>
        <button class="closer"><?= fa_image_tag('times'); ?></button>
    </div>
    <div id="backdrop_detail_content" class="backdrop_detail_content with-sidebar sidebar-right">
        <div class="content">
            <div class="form-container">
                <div class="form">
                    <div class="row">
                        <div class="column large">
                            <?php if ($issue->isWorkflowTransitionsAvailable()): ?>
                                <div class="form-row">
                                    <span class="title-crumbs">
                                        <?php include_component('project/issueparent_crumbs', array('issue' => $issue)); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <form class="form-row header" id="issue-card-title-form" action="<?= $set_field_route . '?field=title'; ?>" method="post" data-interactive-form>
                                <input name="value" class="name-input-enhance invisible" id="edit-issue-name" type="text" value="<?= $issue->getTitle(); ?>" placeholder="<?= __('Enter a short description of this issue here'); ?>">
                            </form>
                            <div class="form-row">
                                <div class="created-times">
                                    <div id="posted_at_field">
                                        <label><?= __('Posted at'); ?></label>
                                        <time datetime="<?= Context::getI18n()->formatTime($issue->getPosted(), 24); ?>" title="<?= Context::getI18n()->formatTime($issue->getPosted(), 21); ?>" class="value-container"><?= Context::getI18n()->formatTime($issue->getPosted(), 20); ?></time>
                                    </div>
                                    <div id="updated_at_field">
                                        <label><?= __('Last updated'); ?></label>
                                        <time datetime="<?= Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 24); ?>" title="<?= Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 21); ?>" class="value-container"><?= Context::getI18n()->formatTime($issue->getLastUpdatedTime(), 20); ?></time>
                                    </div>
                                </div>
                            </div>
                            <form class="row" id="issue-card-description-form" action="<?= $set_field_route . '?field=description'; ?>" method="post" data-simple-submit data-field="description">
                                <div class="form-row header">
                                    <h5>
                                        <?= fa_image_tag('align-left', ['class' => 'icon']); ?><span><?= __('Description'); ?></span>
                                        <?php if (!$issue->isEditable()): ?>
                                            <div class="not-editable">
                                                <?= fa_image_tag('lock'); ?>
                                                <span class="name"><?= __('Locked'); ?></span>
                                                <span class="tooltip from-above"><?= __('This field cannot be edited because the workflow defines this step as "locked"'); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </h5>
                                </div>
                                <div class="form-row">
                                    <div class="formatted-text-container content <?php if ($issue->isEditable()) echo 'editable'; ?>">
                                        <?php echo $issue->getParsedDescription(['issue' => $issue]); ?>
                                    </div>
                                    <?php if ($issue->isEditable()): ?>
                                        <div class="editor-container">
                                            <?php include_component('main/textarea', ['area_id' => 'description_input_area', 'area_name' => 'value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'invisible' => true, 'markuppable' => true, 'syntax' => Settings::SYNTAX_MD, 'value' => $issue->getDescription()]); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-row submit-container">
                                    <button type="button" class="button secondary"><?= __('Cancel'); ?></button>
                                    <button type="submit" class="button primary">
                                        <span class="name"><?= __('Save'); ?></span>
                                        <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                                    </button>
                                </div>
                            </form>
                            <form class="row <?php if (!$issue->isReproductionStepsVisible()) echo 'hidden'; ?>" id="issue-card-reproduction_steps-form" action="<?= $set_field_route . '?field=reproduction_steps'; ?>" method="post" data-simple-submit data-field="reproduction_steps">
                                <div class="form-row header">
                                    <h5>
                                        <?= fa_image_tag('list-ol', ['class' => 'icon']); ?><span><?= __('How to reproduce'); ?></span>
                                        <?php if (!$issue->isEditable()): ?>
                                            <div class="not-editable">
                                                <?= fa_image_tag('lock'); ?>
                                                <span class="name"><?= __('Locked'); ?></span>
                                                <span class="tooltip from-above"><?= __('This field cannot be edited because the workflow defines this step as "locked"'); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </h5>
                                </div>
                                <div class="form-row">
                                    <div class="formatted-text-container content <?php if ($issue->isEditable()) echo 'editable'; ?>">
                                        <?php echo $issue->getParsedReproductionSteps(['issue' => $issue]); ?>
                                    </div>
                                    <div class="editor-container">
                                        <?php include_component('main/textarea', ['area_id' => 'reproduction_steps_input_area', 'area_name' => 'value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'invisible' => true, 'markuppable' => true, 'syntax' => Settings::SYNTAX_MD, 'value' => $issue->getReproductionSteps()]); ?>
                                    </div>
                                </div>
                                <div class="form-row submit-container">
                                    <button type="button" class="button secondary"><?= __('Cancel'); ?></button>
                                    <button type="submit" class="button primary">
                                        <span class="name"><?= __('Save'); ?></span>
                                        <?= fa_image_tag('spinner', ['class' => 'fa-spin icon indicator']); ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_component('project/issuecomments', ['issue' => $issue]); ?>
        </div>
        <div class="sidebar" id="issue-card-issuefields-container">
            <?php include_component('main/viewissuefields', ['issue' => $issue, 'include_status' => true]); ?>
        </div>
    </div>
</div>
