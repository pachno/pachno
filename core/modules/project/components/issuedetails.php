<?php

    use pachno\core\entities\Issue;
    use pachno\core\framework\Context;
    use pachno\core\framework\Settings;

    /**
     * @var Issue $issue
     */

?>
<div class="issue-details">
    <div id="description_field" class="fields-list-container <?php if (!$issue->isDescriptionVisible()) echo 'not-visible'; ?> viewissue_description">
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
            <?php include_component('main/textarea', ['area_name' => 'value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => 'description_form_value', 'field' => 'description', 'syntax' => Settings::getSyntaxClass($issue->getDescriptionSyntax()), 'height' => '250px', 'width' => '100%', 'value' => htmlentities($issue->getDescription(), ENT_COMPAT, Context::getI18n()->getCharset())]); ?>
            <div class="textarea_save_container">
                <button class="button secondary" data-trigger-cancel-editing data-field="description" data-issue-id="<?= $issue->getId(); ?>"><?= __('Cancel'); ?></button>
                <button class="button primary" data-trigger-save data-field="description" data-issue-id="<?= $issue->getId(); ?>"><?= __('Save'); ?></button>
            </div>
        <?php endif; ?>
    </div>
    <div id="reproduction_steps_field" class="fields-list-container <?php if (!$issue->isReproductionStepsVisible()) echo 'not-visible'; ?>">
        <div class="header" id="reproduction_steps_header">
            <span class="icon"><?= fa_image_tag('list-ol'); ?></span>
            <span class="name"><?php echo __('How to reproduce'); ?></span>
        </div>
        <div id="reproduction_steps_name" class="content <?php if ($issue->isEditable() && $issue->canEditReproductionSteps()) echo ' editable'; ?>" data-editable-field data-dynamic-field-value data-field="reproduction_steps" data-issue-id="<?= $issue->getId(); ?>">
            <?php if ($issue->getReproductionSteps()): ?>
                <?php echo $issue->getParsedReproductionSteps(); ?>
            <?php endif; ?>
        </div>
        <?php if ($issue->isEditable() && $issue->canEditReproductionSteps()): ?>
            <?php include_component('main/textarea', ['area_name' => 'value', 'target_type' => 'issue', 'target_id' => $issue->getID(), 'area_id' => 'reproduction_steps_form_value', 'field' => 'reproduction_steps', 'syntax' => Settings::getSyntaxClass($issue->getReproductionStepsSyntax()), 'height' => '250px', 'width' => '100%', 'value' => htmlentities($issue->getReproductionSteps(), ENT_COMPAT, Context::getI18n()->getCharset())]); ?>
            <div class="textarea_save_container">
                <button class="button secondary" data-trigger-cancel-editing data-field="reproduction_steps" data-issue-id="<?= $issue->getId(); ?>"><?= __('Cancel'); ?></button>
                <button class="button primary" data-trigger-save data-field="reproduction_steps" data-issue-id="<?= $issue->getId(); ?>"><?= __('Save'); ?></button>
            </div>
        <?php endif; ?>
    </div>
    <div id="viewissue_attached_information_container" class="fields-list-container">
        <div class="header">
            <span class="icon"><?= fa_image_tag('paperclip'); ?></span>
            <span class="name"><?php echo __('Attachments'); ?><span id="viewissue_uploaded_attachments_count" class="count-badge" data-dynamic-field-value data-field="number_of_files" data-issue-id="<?= $issue->getId(); ?>"><?= count($issue->getFiles()); ?></span></span>
            <button type="button" class="button secondary trigger-file-upload">
                <?= fa_image_tag('file-upload', ['class' => 'icon']); ?>
                <span class="name"><?= __('Add file'); ?></span>
            </button>
        </div>
        <div id="viewissue_attached_information" class="attachments-list">
            <ul class="attached_items" id="viewissue_uploaded_links" style="display: none;">
                <?php foreach ($issue->getLinks() as $link_id => $link): ?>
                    <?php include_component('main/attachedlink', array('issue' => $issue, 'link' => $link, 'link_id' => $link['id'])); ?>
                <?php endforeach; ?>
            </ul>
            <div class="attachments-container" id="viewissue_uploaded_files">
                <?php foreach (array_reverse($issue->getFiles()) as $file_id => $file): ?>
                    <?php if (!$file->isImage()): ?>
                        <?php include_component('main/attachedfile', array('base_id' => 'viewissue_files', 'mode' => 'issue', 'issue' => $issue, 'file' => $file)); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php foreach (array_reverse($issue->getFiles()) as $file_id => $file): ?>
                    <?php if ($file->isImage()): ?>
                        <?php include_component('main/attachedfile', array('mode' => 'issue', 'issue' => $issue, 'file' => $file)); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <div class="file-upload-placeholder"></div>
            </div>
        </div>
        <?php // include_component('main/uploader', array('mode' => 'issue', 'event_value' => "{ mode: 'issue', issue_id: '" . $issue->getId() . "'}")); ?>
        <div class="upload-container fixed-position hidden" id="upload_drop_zone">
            <div class="wrapper">
                <span class="image-container"><?= image_tag('/unthemed/icon-upload.png', [], true); ?></span>
                <span class="message"><?= $message ?? __('Drop the file to upload it'); ?></span>
            </div>
        </div>
    </div>
    <div id="viewissue_related_information_container" class="fields-list-container <?php if (!$issue->countChildIssues()) echo 'not-visible'; ?>">
        <div class="header">
            <span class="icon"><?= fa_image_tag('list-alt', [], 'far'); ?></span>
            <span class="name"><?= __('Subtasks'); ?><span id="viewissue_related_issues_count" class="count-badge"><?= $issue->countChildIssues(); ?></span></span>
        </div>
        <div id="viewissue_related_information" class="related-issues content">
            <?php include_component('main/relatedissues', array('issue' => $issue)); ?>
        </div>
    </div>
    <?php include_component('main/issuemaincustomfields', ['issue' => $issue]); ?>
</div>