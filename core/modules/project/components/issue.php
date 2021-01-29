<?php

    /**
     * @var \pachno\core\entities\Issue $issue
     * @var \pachno\core\entities\User $pachno_user
     */

?>
<div id="issue_<?php echo $issue->getID(); ?>" class="viewissue-container <?php if ($issue->isBlocking()) echo ' blocking'; ?>">
    <?php include_component('project/viewissueheader', ['issue' => $issue]); ?>
    <div id="issue-container" class="issue-card">
        <div id="issue-main-container" class="issue-card-main">
            <?php include_component('project/viewissuemessages', compact('issue')); ?>
            <div class="card-header">
                <?php include_component('project/viewissueworkflowbuttons', ['issue' => $issue]); ?>
                <div class="dropper-container">
                    <button class="dropper button secondary icon" id="more_actions_<?php echo $issue->getID(); ?>_button"><?= fa_image_tag('ellipsis-v'); ?></button>
                    <?php include_component('main/issuemoreactions', array('issue' => $issue, 'times' => false, 'show_workflow_transitions' => false)); ?>
                </div>
            </div>
            <?php \pachno\core\framework\Event::createNew('core', 'viewissue::afterWorkflowButtons', $issue)->trigger(); ?>
            <div class="issue-details">
                <div id="description_field"<?php if (!$issue->isDescriptionVisible()): ?> style="display: none;"<?php endif; ?> class="fields-list-container viewissue_description">
                    <div class="header" id="description_header">
                        <span class="icon"><?= fa_image_tag('align-left'); ?></span>
                        <span class="name"><?php echo __('Description'); ?></span>
                    </div>
                    <div id="description_name" class="content <?php if ($issue->isEditable() && $issue->canEditDescription()) echo ' editable'; ?>" data-editable-field data-dynamic-field-value data-field="description" data-issue-id="<?= $issue->getId(); ?>">
                        <?php if ($issue->getDescription()): ?>
                            <?php echo $issue->getParsedDescription(); ?>
                        <?php endif; ?>
                    </div>
                    <?php if ($issue->isEditable() && $issue->canEditDescription()): ?>
                        <?php include_component('main/textarea', ['area_name' => 'value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => 'description_form_value', 'field' => 'description', 'syntax' => \pachno\core\framework\Settings::getSyntaxClass($issue->getDescriptionSyntax()), 'height' => '250px', 'width' => '100%', 'value' => htmlentities($issue->getDescription(), ENT_COMPAT, \pachno\core\framework\Context::getI18n()->getCharset())]); ?>
                        <div class="textarea_save_container">
                            <button class="button secondary" data-trigger-cancel-editing data-field="description" data-issue-id="<?= $issue->getId(); ?>"><?= __('Cancel'); ?></button>
                            <button class="button primary" data-trigger-save data-field="description" data-issue-id="<?= $issue->getId(); ?>"><?= __('Save'); ?></button>
                        </div>
                    <?php endif; ?>
                </div>
                <div id="reproduction_steps_field"<?php if (!$issue->isReproductionStepsVisible()): ?> style="display: none;"<?php endif; ?> class="fields-list-container">
                    <div class="header" id="reproduction_steps_header">
                        <?php if (false && $issue->isEditable() && $issue->canEditReproductionSteps()): ?>
                            <?php echo fa_image_tag('edit', ['class' => 'dropdown', 'id' => 'reproduction_steps_edit', 'onclick' => "$('#reproduction_steps_change').show(); $('#reproduction_steps_name').hide(); $('#no_reproduction_steps').hide();", 'title' => __('Click here to edit reproduction steps')]); ?>
                        <?php endif; ?>
                        <span class="icon"><?= fa_image_tag('list-ol'); ?></span>
                        <span class="name"><?php echo __('How to reproduce'); ?></span>
                    </div>
                    <div id="reproduction_steps_name" class="content <?php if ($issue->isEditable() && $issue->canEditReproductionSteps()) echo ' editable'; ?>" data-editable-field data-dynamic-field-value data-field="reproduction_steps" data-issue-id="<?= $issue->getId(); ?>">
                        <?php if ($issue->getReproductionSteps()): ?>
                            <?php echo $issue->getParsedReproductionSteps(); ?>
                        <?php endif; ?>
                    </div>
                    <?php if ($issue->isEditable() && $issue->canEditReproductionSteps()): ?>
                        <?php include_component('main/textarea', ['area_name' => 'value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => 'reproduction_steps_form_value', 'field' => 'reproduction_steps', 'syntax' => \pachno\core\framework\Settings::getSyntaxClass($issue->getReproductionStepsSyntax()), 'height' => '250px', 'width' => '100%', 'value' => htmlentities($issue->getReproductionSteps(), ENT_COMPAT, \pachno\core\framework\Context::getI18n()->getCharset())]); ?>
                        <div class="textarea_save_container">
                            <button class="button secondary" data-trigger-cancel-editing data-field="reproduction_steps" data-issue-id="<?= $issue->getId(); ?>"><?= __('Cancel'); ?></button>
                            <button class="button primary" data-trigger-save data-field="reproduction_steps" data-issue-id="<?= $issue->getId(); ?>"><?= __('Save'); ?></button>
                        </div>
                    <?php endif; ?>
                </div>
                <div id="viewissue_attached_information_container" class="fields-list-container">
                    <div class="header">
                        <span class="icon"><?= fa_image_tag('paperclip'); ?></span>
                        <span class="name"><?php echo __('Attachments'); ?><span id="viewissue_uploaded_attachments_count" class="count-badge"><?= (count($issue->getLinks()) + count($issue->getFiles())); ?></span></span>
                    </div>
                    <div id="viewissue_attached_information" class="attachments-list content">
                        <ul class="attached_items" id="viewissue_uploaded_links">
                            <?php foreach ($issue->getLinks() as $link_id => $link): ?>
                                <?php include_component('main/attachedlink', array('issue' => $issue, 'link' => $link, 'link_id' => $link['id'])); ?>
                            <?php endforeach; ?>
                        </ul>
                        <ul class="attached_items" id="viewissue_uploaded_files">
                            <?php foreach (array_reverse($issue->getFiles()) as $file_id => $file): ?>
                                <?php if (!$file->isImage()): ?>
                                    <?php include_component('main/attachedfile', array('base_id' => 'viewissue_files', 'mode' => 'issue', 'issue' => $issue, 'file' => $file)); ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php foreach (array_reverse($issue->getFiles()) as $file_id => $file): ?>
                                <?php if ($file->isImage()): ?>
                                    <?php include_component('main/attachedfile', array('base_id' => 'viewissue_files', 'mode' => 'issue', 'issue' => $issue, 'file' => $file)); ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div id="viewissue_related_information_container" class="fields-list-container <?php if (!$issue->countChildIssues()) echo 'not-visible'; ?>">
                    <div class="header">
                        <span class="icon"><?= fa_image_tag('list-alt', [], 'far'); ?></span>
                        <span class="name"><?= __('Child issues'); ?><span id="viewissue_related_issues_count" class="count-badge"><?= $issue->countChildIssues(); ?></span></span>
                    </div>
                    <div id="viewissue_related_information" class="related-issues content">
                        <?php include_component('main/relatedissues', array('issue' => $issue)); ?>
                    </div>
                </div>
                <?php include_component('main/issuemaincustomfields', ['issue' => $issue]); ?>
            </div>
            <?php \pachno\core\framework\Event::createNew('core', 'viewissue::afterMainDetails', $issue)->trigger(); ?>
            <?php /*
                    <div class="fancy-tabs" id="viewissue_activity">
                        <?php \pachno\core\framework\Event::createNew('core', 'viewissue_before_tabs', $issue)->trigger(); ?>
                        <a id="tab_viewissue_history" class="tab" href="javascript:void(0);" onclick="Pachno.UI.tabSwitcher('tab_viewissue_history', 'viewissue_activity');">
                            <?= fa_image_tag('history', ['class' => 'icon']); ?>
                            <span class="name"><?= __('History'); ?></span>
                        </a>
                    </div>
                    <div id="viewissue_activity_panes" class="fancypanes">
                        <?php \pachno\core\framework\Event::createNew('core', 'viewissue_after_tabs', $issue)->trigger(); ?>
                        <div id="tab_viewissue_history_pane" style="display:none;">
                            <div class="viewissue_history">
                                <div id="viewissue_log_items">
                                    <ul>
                                        <?php $previous_time = null; ?>
                                        <?php $include_user = true; ?>
                                        <?php foreach (array_reverse($issue->getLogEntries()) as $item): ?>
                                            <?php if (!$item instanceof \pachno\core\entities\LogItem) continue; ?>
                                            <?php include_component('main/issuelogitem', compact('item', 'previous_time', 'include_user')); ?>
                                            <?php $previous_time = $item->getTime(); ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div> */ ?>
            <?php include_component('project/issuecomments', ['issue' => $issue]); ?>
        </div>
        <div class="issue-fields">
            <?php include_component('main/viewissuefields', ['issue' => $issue]); ?>
        </div>
    </div>
</div>