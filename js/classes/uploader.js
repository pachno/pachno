import UI from "../helpers/ui";
import Pachno from "./pachno";

class Uploader {
    constructor(options) {
        this.uploader_container = options.uploader_container;
        this.mode = options.mode;
        this.dropzone = options.dropzone || $('#upload_drop_zone');
        this.input_name = options.input_name;
        this.file_upload_list = options.file_upload_list;
        this.only_images = options.only_images;
        this.type = options.type;
        this.form_data = options.data;

        this.upload_url = options.url || Pachno.upload_url;

        const $body = $('body');
        if (this.dropzone !== undefined && 'ondrop' in document.createElement('span')) {
            $body.on('dragover', (event) => this.dragOverFiles(event))
            $body.on('dragleave', (event) => this.dragOverFiles(event))
            this.dropzone.on('drop', (event) => this.dropFiles(event));
        }
        const uploader = this;

        if (this.uploader_container !== undefined) {
            this.file_input_element = $('<input type="file">');
            $body.append(this.file_input_element);
            this.file_input_element.off('change');
            this.file_input_element.on('change', (event) => uploader.selectFiles(event));
            $body.off('click', this.uploader_container + ' .trigger-file-upload');
            $body.on('click', this.uploader_container + ' .trigger-file-upload', (event) => { event.preventDefault(); this.file_input_element.trigger('click');});
        }
    }

    dragOverFiles(event) {
        if (event !== undefined) {
            event.stopPropagation();
            event.preventDefault();
        }
        if (event !== undefined && event.type === "dragover") {
            this.dropzone.removeClass("hidden");
            event.originalEvent.dataTransfer.dropEffect = 'copy'; // Explicitly show this is a copy.
        } else {
            this.dropzone.addClass("hidden");
        }
    }

    dropFiles(event) {
        const files = event.originalEvent.target.files || event.originalEvent.dataTransfer.files;
        this.dragOverFiles(event);
        this.uploadFiles(files);
    }

    selectFiles(event) {
        const files = $(event.target)[0].files;
        if (files !== undefined) {
            this.uploadFiles(files, true);
        }
    }

    uploadFile(url, file) {
        const uploader = this;
        return new Promise((resolve, reject) => {
            const is_image = (file.type.indexOf("image") == 0);

            if (uploader.only_images && !is_image) {
                console.error('Not an image', file);
            }

            // $upload_status_container.addClass('active');
            // $upload_status_container.addClass('expanded');
            let fileSize = 'unknown';
            if (file.size > 1024 * 1024) {
                fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
            } else {
                fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
            }

            // <input type="radio" name="project_icon" value="<?= $icon; ?>" id="project_icon_<?= $index; ?>" <?php if ($icon == $project->getIconName()) echo ' checked'; ?>>
            // <label for="project_icon_<?= $index; ?>"><?= image_tag($icon, [], true); ?></label>

            let $input_element, $label_element;
            const $container = $(uploader.uploader_container);

            if (uploader.mode === 'grid') {
                $input_element = $(`<input type="radio" name="${uploader.input_name}">`);
                $label_element = $(`<label><img class="icon_preview" src=""><i class="fa-spin fas fa-circle-notch indicator"></i></label>`);

                $input_element.insertBefore($container.find('.file-upload-placeholder'));
                $label_element.insertBefore($container.find('.file-upload-placeholder'));
            } else if (uploader.mode === 'table') {
                $label_element = $(`<div class="row"><div class="column info-icons">${UI.fa_image_tag('spinner', { classes: 'fa-spin icon'})}</div><div class="column name-container">${file.name}</div><div class="column numeric">${fileSize}</div></div>`)
                $container.find('.file-list-container').append($label_element);
            } else if (uploader.mode === 'list') {
                let link_element;
                if (is_image) {
                    link_element = `<a href="javascript:void(0);" class="preview"><img src=""></a><div class="information">${file.name}</div>`
                } else {
                    link_element = `<a href="javascript:void(0);">${UI.fa_image_tag('spinner', { classes: 'fa-spin icon' })}<span class="name">${file.name}</span></a>`;
                }
                $label_element = $(`<div class="attachment">${link_element}<div class="information">${fileSize}</div><div class="actions-container"></div></div>`);
                $label_element.insertBefore($container.find('.file-upload-placeholder'));
            }

            if (is_image) {
                const $image_preview = $label_element.find('img');
                const reader = new FileReader();
                $label_element.addClass('type-image');
                reader.onload = function (e) {
                    $image_preview.attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
            let file_key = file.name.replace('[', '(').replace(']', ')');
            let data = {
                'type': uploader.type,
            }
            data[file_key] = file;

            if (uploader.form_data !== undefined) {
                if (uploader.form_data.project_id !== undefined) {
                    data.project_id = uploader.form_data.project_id;
                }
                if (uploader.form_data.issue_id !== undefined) {
                    data.issue_id = uploader.form_data.issue_id;
                }
                if (uploader.form_data.article_id !== undefined) {
                    data.article_id = uploader.form_data.article_id;
                }
                if (uploader.form_data.build_id !== undefined) {
                    data.build_id = uploader.form_data.build_id;
                }
            }

            const options = {
                data,
                method: 'POST'
            };
            Pachno.fetch(url, options)
                .then((json) => {
                    if (json.element !== undefined) {
                        $label_element.replaceWith(json.element);
                        Pachno.trigger(Pachno.EVENTS.upload.complete, { ...uploader.form_data, mode: uploader.mode });
                    } else if (uploader.mode === 'grid') {
                        const data = json.file;
                        $label_element.addClass('confirmed');
                        $label_element.find('.indicator').remove();
                        $input_element.attr('value', data.id);
                        $input_element.attr('id', `${uploader.input_name}_${data.id}`);
                        $label_element.attr('for', `${uploader.input_name}_${data.id}`);
                    }
                    resolve();
                }).catch((error) => {
                    Pachno.UI.Message.error(error);
                    $label_element.remove();
                    if (uploader.mode === 'grid') {
                        $input_element.remove();
                    }
                    reject(error);
                });
        });
    }

    uploadFiles(files, show_hint) {
        const url = $('#file_upload_dummy').data('upload-url');
        const uploads = [];
        if (show_hint === true) {
            $('#upload_drop_hint').addClass('active');
        }
        const uploader = this;
        if (files.length > 0) {
            for (const file of files) {
                uploads.push(uploader.uploadFile(url, file));
            }
        }
        Promise.all(uploads).catch(error => Pachno.UI.Message.error);
    }
}

export default Uploader;
window.Uploader = Uploader;
