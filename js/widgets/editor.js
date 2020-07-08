import $ from "jquery";
import Pachno from "../classes/pachno";
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import List from '@editorjs/list';
import LinkTool from '@editorjs/link';
import Checklist from '@editorjs/checklist';
import Quote from '@editorjs/quote';
import CodeTool from '@editorjs/code';
import TableTool from '@editorjs/table';
import Marker from '@editorjs/marker';
import Warning from '@editorjs/warning';
import InlineCode from '@editorjs/inline-code';
import Delimiter from '@editorjs/delimiter';

import EasyMDE from "easymde";
import {EVENTS as WidgetEvents} from "./index";

const editors = {};

const initializeEditorJsArea = function () {
    const $editor_element = $(this);
    const editor_element = $editor_element[0];
    if ($editor_element.data('processed')) {
        return;
    }

    $editor_element.data('processed', true);

    const $form = $editor_element.parents('form');
    const form_id = $form.attr('id');
    const content_html = $editor_element.html();

    $editor_element.html('');
    $editor_element.addClass('active');

    let content;
    try {
        content = (content_html != '') ? JSON.parse(content_html) : {};
    } catch (error) {
        console.error('Error parsing existing content:', content_html);
        content = {};
    }
    const input_name = $editor_element.data('input-name');
    const $buttons = $form.find(".enable-on-editor-ready");

    const editor = new EditorJS({
        holder: editor_element,
        autofocus: false,
        data: content,
        placeholder: $editor_element.data('placeholder'),
        tools: {
            header: Header,
            link: LinkTool,
            inlineCode: InlineCode,
            delimiter: Delimiter,
            warning: {
                class: Warning,
                inlineToolbar: true,
                config: {
                    titlePlaceholder: $editor_element.data('warning-title-placeholder'),
                    messagePlaceholder: $editor_element.data('warning-message-placeholder')
                }
            },
            checklist: {
                class: Checklist,
                inlineToolbar: true
            },
            list: {
                class: List,
                inlineToolbar: true
            },
            code: {
                class: CodeTool,
                inlineToolbar: false
            },
            table: {
                class: TableTool,
                inlineToolbar: true
            },
            quote: {
                class: Quote,
                inlineToolbar: true,
                config: {
                    quotePlaceholder: $editor_element.data('quote-placeholder'),
                    captionPlaceholder: $editor_element.data('quote-caption-placeholder')
                }
            }
        }
    });

    editor.isReady.then(() => {
        $buttons.removeProp('disabled');

        editors[$editor_element.attr('id')] = editor;
        Pachno.on(Pachno.EVENTS.formSubmit, (PachnoApplication, data) => {
            return new Promise(resolve => {
                if (data.form_id != form_id) {
                    return resolve();
                }

                editor.save().then((data) => {
                    resolve({
                        form_data: {
                            input_name,
                            data
                        }
                    })
                });
            });
        });
    });
};

const initializeEasyMde = function () {
    const $editor_element = $(this);
    const editor_element = $editor_element[0];
    if ($editor_element.data('processed')) {
        return;
    }

    $editor_element.data('processed', true);
    const editor = new EasyMDE({
        element: editor_element,
        forceSync: true,
        status: [{
            className: "statustext",
            defaultValue: function (el) { el.innerHTML = $editor_element.data('status-text'); }
        }, {
            className: "markdown-help",
            defaultValue: function (el) { el.innerHTML = "<a href='https://guides.github.com/features/mastering-markdown/' target='_blank'><i class='fab fa-markdown'></i></a>"; }
        }],
        uploadImage: true,
        imageUploadEndpoint: $editor_element.data('upload-url'),
        toolbar: [
            "heading", "bold", "italic",
            "|",
            "unordered-list", "ordered-list",
            "|",
            "quote", "code", "link",
            "|",
            "image"
        ]
    });
}

const setupListeners = function() {
    Pachno.on(Pachno.EVENTS.ready, () => {
        $('.wysiwyg-editor:not([data-processed])').each(initializeEditorJsArea);
        $('.markuppable:not([data-processed])').each(initializeEasyMde);
    });
    Pachno.on(WidgetEvents.update, () => {
        $('.markuppable:not([data-processed])').each(initializeEasyMde);
    });
};

export default setupListeners;