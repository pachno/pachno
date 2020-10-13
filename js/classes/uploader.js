import UI from "../helpers/ui";

class Uploader {
    constructor(options) {
        this.uploader_container = options.uploader_container;
        this.mode = options.mode;
        this.dropzone = options.dropzone;
        this.input_name = options.input_name;
        this.file_upload_list = options.file_upload_list;
        this.only_images = options.only_images;
        this.type = options.type;
        this.form_data = options.data;

        this.file_input_element = $('#file_upload_dummy');
        this.upload_url = options.url || this.file_input_element.data('upload-url');

        const $body = $('body');
        if (this.dropzone !== undefined && 'ondrop' in document.createElement('span')) {
            $body.on('dragover', (event) => this.dragOverFiles(event))
            $body.on('dragleave', (event) => this.dragOverFiles(event))
            this.dropzone.on('drop', (event) => this.dropFiles(event));
        }
        $body.on('change', '#file_upload_dummy', (event) => this.selectFiles(event))
        this.uploader_container.on('click', '.trigger-file-upload', (event) => { event.preventDefault(); this.file_input_element.trigger('click');});
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
        return new Promise((resolve, reject) => {
            debugger;
            const is_image = (file.type.indexOf("image") == 0);

            if (this.only_images && !is_image) {
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

            const $input_element = $(`<input type="radio" name="${this.input_name}">`);
            const $label_element = $(`<label><img class="icon_preview" src=""><i class="fa-spin fas fa-circle-notch indicator"></i></label>`);

            $input_element.insertBefore(this.uploader_container.find('.trigger-file-upload'));
            $label_element.insertBefore(this.uploader_container.find('.trigger-file-upload'));

            if (is_image) {
                const $image_preview = $label_element.find('img');
                const reader = new FileReader();
                reader.onload = function (e) {
                    $image_preview.attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
            let formData = new FormData();
            formData.append(file.name.replace('[', '(').replace(']', ')'), file);
            formData.append('type', this.type);
            const options = {
                body: formData,
                method: 'POST'
            };

            if (this.form_data !== undefined) {
                if (this.form_data.project_id !== undefined) {
                    formData.append('project_id', this.form_data.project_id);
                }
            }

            fetch(url, options)
                .then((_response) => {
                    const contentType = _response.headers.get("content-type");
                    const is_json = (contentType && contentType.indexOf("application/json") !== -1);

                    return new Promise((_resolve, _reject) => {
                        if (_response.ok && is_json) {
                            _response.json().then(json => {
                                _resolve(json);
                            });
                        } else {
                            _response.json().then(json => {
                                UI.Message.error(json.error, json.message);
                                if (options.failure && options.failure.callback) {
                                    options.failure.callback(json);
                                }
                            });
                            _reject(_response);
                        }
                    });
                }).then((json, responseText) => {
                    const data = json.file;
                    $label_element.addClass('confirmed');
                    $label_element.find('.indicator').remove();
                    $input_element.attr('value', data.id);
                    $input_element.attr('id', `${this.input_name}_${data.id}`);
                    $label_element.attr('for', `${this.input_name}_${data.id}`);
                    resolve();
                }).catch((error) => {
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
        if (files.length > 0) {
            for (const file of files) {
                uploads.push(this.uploadFile(url, file));
            }
        }
        Promise.all(uploads).catch(error => Pachno.UI.Message.error);
    }
}

export default Uploader;
window.Uploader = Uploader;
