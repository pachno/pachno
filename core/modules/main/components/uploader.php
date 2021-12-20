<?php /* if (isset($target)): ?>
<form action="<?= make_url('update_attachments', ['target' => $mode, 'target_id' => $target->getID()]); ?>" method="post" onsubmit="Pachno.Main.updateAttachments(this);return false;">
    <div class="backdrop_detail_content">
<?php endif; */ ?>
<?php

    use pachno\core\framework\Context;

?>
<div class="upload-container fixed-position hidden" id="upload_drop_zone">
    <div class="wrapper">
        <span class="image-container"><?= image_tag('/unthemed/icon-upload.png', [], true); ?></span>
        <span class="message"><?= $message ?? __('Drop the file to upload it'); ?></span>
    </div>
</div>
<div class="upload-status-container fixed-position expandable" id="upload_status_container">
    <h3>
        <span><?= __('Upload progress'); ?></span>
        <button class="secondary icon expander"><?= fa_image_tag('chevron-up'); ?></button>
    </h3>
    <div id="upload_drop_hint" class="drop-hint">
        <span class="icon"><?= fa_image_tag('file-upload'); ?></span>
        <span class="message"><?= __('You can also drag and drop files onto the page to upload them'); ?></span>
    </div>
    <div
        id="file_upload_list"
        class="list-mode"
        data-filename-label="<?= htmlentities(__('File'), ENT_COMPAT, Context::getI18n()->getCharset()); ?>"
        data-description-label="<?= htmlentities(__('Description'), ENT_COMPAT, Context::getI18n()->getCharset()); ?>"
        data-description-placeholder="<?= htmlentities(__('Enter a short file description here'), ENT_COMPAT, Context::getI18n()->getCharset()); ?>"
    >
    </div>
</div>
