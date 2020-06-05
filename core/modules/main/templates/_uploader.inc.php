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
        <span class="message"><?= __('Drop the file to upload it'); ?></span>
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
<script>
    Pachno.on(Pachno.EVENTS.ready, function () {
        const $upload_drop_zone = $('#upload_drop_zone');
        const $file_upload_list = $('#file_upload_list');
        const $upload_status_container = $('#upload_status_container');

        const uploadFile = function (url, file) {
            return new Promise((resolve, reject) => {
                $upload_status_container.addClass('active');
                $upload_status_container.addClass('expanded');
                let fileSize = 'unknown';
                if (file.size > 1024 * 1024) {
                    fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
                } else {
                    fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
                }
                const is_image = (file.type.indexOf("image") == 0);

                const element = '<div class="list-item multiline">' +
                    '<span class="icon"><i class="fa-spin fas fa-circle-notch indicator"></i><i class="fas fa-check confirmed"></i><i class="fas fa-exclamation-circle error-icon"></i></span>' +
                    '<span class="name"><span class="title">' + file.name + '</span><span class="description">' + fileSize + '</span></span>' +
                    '<div class="progress-container"><span class="progress"></span></div>' +
                    '</div>';

                $file_upload_list.prepend(element);

                const $inserted_element = $('#file_upload_list').children().first();
                // if (is_image) {
                //     var image_elm = $inserted_element.down('img');
                //     var reader = new FileReader();
                //     reader.onload = function (e) {
                //         image_elm.src = e.target.result;
                //     };
                //     reader.readAsDataURL(file);
                // }
                const $progress_element = $inserted_element.find('.progress');
                let formData = new FormData();
                formData.append(file.name.replace('[', '(').replace(']', ')'), file);

                let xhr = new XMLHttpRequest();
                xhr.open('POST', url, true);
                xhr.onload = function () {
                    const data = JSON.parse(this.response);
                    if (data.file_id != undefined) {
                        $inserted_element.addClass('confirmed');
                        Pachno.trigger('upload-completed', data);
                        resolve();
                    } else {
                        $upload_status_container.addClass('expanded');
                        $inserted_element.addClass('error');
                        Pachno.UI.Message.error(data.error);
                        reject();
                    }
                };

                xhr.upload.onprogress = function (e) {
                    if (e.lengthComputable) {
                        const percent = (e.loaded / e.total) * 100;
                        $progress_element.css({width: percent + '%'});
                        if (percent == 100) {
                            $progress_element.addClass('completed');
                            $('#file_upload_dummy').val(null);
                        }
                    }
                };

                if ($('#dynamic_uploader_submit') && !$('#dynamic_uploader_submit').disabled) $('#dynamic_uploader_submit').prop('disabled', true);
                if ($('#report_issue_submit_button') && !$('#report_issue_submit_button').disabled) $('#report_issue_submit_button').prop('disabled', true);
                xhr.send(formData);
            });
        };

        const uploadFiles = function (files, show_hint) {
            const url = $('#file_upload_dummy').data('upload-url');
            const uploads = [];
            if (show_hint === true) {
                $('#upload_drop_hint').addClass('active');
            }
            if (files.length > 0) {
                for (const file of files) {
                    uploads.push(uploadFile(url, file));
                }
            }
            Promise.all(uploads).catch(error => Pachno.UI.Message.error);
        };

        const dragOverFiles = function (event) {
            if (event !== undefined) {
                event.stopPropagation();
                event.preventDefault();
            }
            if (event !== undefined && event.type === "dragover") {
                $upload_drop_zone.removeClass("hidden");
                event.originalEvent.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
            } else {
                $upload_drop_zone.addClass("hidden");
            }
        };

        const dropFiles = function (event) {
            const files = event.originalEvent.target.files || event.originalEvent.dataTransfer.files;
            dragOverFiles(event);
            uploadFiles(files);
        };

        const selectFiles = function () {
            const files = $(this)[0].files;
            if (files !== undefined) {
                uploadFiles(files, true);
            }
        };

        const $body = $('body');
        if ('ondrop' in document.createElement('span')) {
            $body.on('dragover', dragOverFiles)
            $body.on('dragleave', dragOverFiles)
            $upload_drop_zone.on('drop', dropFiles);
        }
        $body.on('change', '#file_upload_dummy', selectFiles)
        $body.on('click', '.trigger-file-upload', function (event) { event.preventDefault(); $('#file_upload_dummy_label').trigger('click');});
    });
</script>

<?php /* if (isset($target)): ?>
    </div>
    <div class="backdrop_details_submit">
        <span class="explanation"></span>
        <div class="submit_container">
            <button type="submit" class="button" id="dynamic_uploader_submit"><?= image_tag('spinning_16.gif', array('id' => 'attachments_indicator', 'style' => 'display: none;')) . __('Save attachments'); ?></button>
        </div>
    </div>
</form>
<?php endif; */ ?>

