import $ from "jquery";
import Pachno from "../../classes/pachno";
import EditorJS from '@editorjs/editorjs';

import Checklist from '@editorjs/checklist';
import CodeTool from '@editorjs/code';
import CodeMirror from '@editorjs/codemirror';
import Delimiter from '@editorjs/delimiter';
import Header from '@editorjs/header';
import InlineCode from '@editorjs/inline-code';
import LinkTool from '@editorjs/link';
import Underline from '@editorjs/underline';
import Hyperlink from 'editorjs-hyperlink';
import Marker from '@editorjs/marker';
import List from './list';
import Quote from '@editorjs/quote';
import TableTool from 'editorjs-table';
import Warning from '@editorjs/warning';
import ImageTool from '@editorjs/image';

import Mention from './mention';
import MentionableParagraph from './paragraph';

import EasyMDE from "easymde";
import {EVENTS as WidgetEvents} from "../index";

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
    const content_html = $editor_element.find('textarea').val();

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
            underline: Underline,
            inlineCode: InlineCode,
            marker: Marker,
            hyperlink: {
                class: Hyperlink,
                config: {
                    shortcut: 'CMD+L',
                    target: '_blank',
                    rel: 'nofollow',
                    availableTargets: ['_blank', '_self'],
                    availableRels: ['author', 'noreferrer'],
                    validate: false,
                }
            },
            delimiter: Delimiter,
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: 'http://localhost:8008/uploadFile', // Your backend file uploader endpoint
                        byUrl: 'http://localhost:8008/fetchUrl', // Your endpoint that provides uploading by Url
                    }
                }
            },
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
                inlineToolbar: false,
                toolbox: {
                    title: Pachno.T.common.editorjs.tools.code
                }
            },
            codeBlock: {
                class: CodeMirror,
                inlineToolbar: false,
                toolbox: {
                    title: Pachno.T.common.editorjs.tools.codeMirror
                }
            },
            table: {
                class: TableTool,
                inlineToolbar: true,
                config: {
                    rows: 2,
                    cols: 3,
                },
            },
            paragraph: {
                class: MentionableParagraph,
                inlineToolbar: true
            },
            mention: {
                class: Mention,
                inlineToolbar: false
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
        $buttons.removeAttr('disabled');
        // const undo = new Undo({ editor });
        // undo.initialize(content);

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
        autofocus: true,
        status: [{
            className: "statustext",
            defaultValue: function (el) {
                el.innerHTML = $editor_element.data('status-text');
            }
        }, {
            className: "markdown-help",
            defaultValue: function (el) {
                el.innerHTML = "<a href='https://guides.github.com/features/mastering-markdown/' target='_blank'><i class='fab fa-markdown'></i></a>";
            }
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
    editors[$editor_element.attr('id')] = editor;
}

/**
 *
 * @param editor
 * @returns {EasyMDE|EditorJS}
 */
export const getEditor = function (editor) {
    return editors[editor];
}

const setupListeners = function () {
    Pachno.on(Pachno.EVENTS.ready, () => {
        $('.wysiwyg-editor:not([data-processed])').each(initializeEditorJsArea);
        $('.markuppable:not([data-processed])').each(initializeEasyMde);
    });
    Pachno.on(WidgetEvents.update, () => {
        $('.markuppable:not([data-processed])').each(initializeEasyMde);
    });

    const $body = $('body');
    $body.on('click', function (event) {
        let remove_mentions = true;

        if (event !== undefined) {
            if (event.target.classList.contains('inline-mention')) {
                remove_mentions = false;
            }
        }

        if (remove_mentions) {
            $('.dropper-container.mentions-container').remove();
        }
    })
};

export default setupListeners;