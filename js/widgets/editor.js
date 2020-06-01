import $ from "jquery";
import Pachno from "../classes/pachno";
import Pen from "../pen";
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import List from '@editorjs/list';
import LinkTool from '@editorjs/link';
import Checklist from '@editorjs/checklist';
import Quote from '@editorjs/quote';
import Paragraph from '@editorjs/paragraph';
import CodeTool from '@editorjs/code';
import TableTool from '@editorjs/table';
import Marker from '@editorjs/marker';
import Warning from '@editorjs/warning';
import InlineCode from '@editorjs/inline-code';
import Delimiter from '@editorjs/delimiter';

const setupListeners = function() {
    Pachno.on(Pachno.EVENTS.ready, () => {
        $('.wysiwyg-editor:not([data-processed])').each(function () {
            const $editor_element = $(this);
            const editor_element = $editor_element[0];

            const editor = new EditorJS({
                holder: editor_element,
                autofocus: false,
                placeholder: $editor_element.data('placeholder'),
                tools: {
                    header: {
                        class: Header,
                        inlineToolbar: ['link']
                    },
                    list: List,
                    link: LinkTool,
                    marker: Marker,
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
                    paragraph: {
                        class: Paragraph,
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
            })
        });
    });
};

export default setupListeners;