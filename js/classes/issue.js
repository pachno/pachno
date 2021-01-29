import UI from "../helpers/ui";
import $ from "jquery";
import Pachno from "./pachno";
import {getEditor} from "../widgets/editor";

class Issue {
    constructor(json, board_id, create_element = true) {
        this.board_id = board_id;
        this.updateFromJson(json);
        if (create_element) {
            this.element = this.createHtmlElement();
        }
        this.setupListeners();
    }

    updateFromJson(json) {
        this.id = json.id;
        this.issue_no = json.issue_no;
        this.created_at = json.created_at;
        this.created_at_iso = json.created_at_iso;
        this.updated_at = json.updated_at;
        this.updated_at_iso = json.updated_at_iso;
        this.updated_at_full = json.updated_at_full;
        this.updated_at_friendly = json.updated_at_friendly;
        this.updated_at_datetime = json.updated_at_datetime;

        this.card_url = json.card_url;
        this.href = json.href;
        this.more_actions_url = json.more_actions_url;
        this.save_url = json.save_url;

        this.blocking = json.blocking;
        this.closed = json.closed;
        this.deleted = json.deleted;
        this.state = json.state;

        this.description = json.description;
        this.description_formatted = json.description_formatted;
        this.reproduction_steps = json.reproduction_steps;
        this.reproduction_steps_formatted = json.reproduction_steps_formatted;

        this.assignee = json.assignee;
        this.category = json.category;
        this.issue_type = json.issue_type;
        this.milestone = json.milestone;
        this.parent_issue_id = json.parent_issue_id;
        this.priority = json.priority;
        this.posted_by = json.posted_by;
        this.severity = json.severity;
        this.reproducability = json.reproducability;
        this.resolution = json.resolution;
        this.status = json.status;
        this.title = json.title;
        this.percent_complete = json.percent_complete;

        this.number_of_files = json.number_of_files;
        this.number_of_comments = json.number_of_comments;

        this.processed = false;
    }

    postAndUpdate(field, value) {
        const issue = this;
        Pachno.trigger(Pachno.EVENTS.issueUpdate, {id: this.id});

        return new Promise(function (resolve, reject) {
            Pachno.fetch(issue.save_url, {
                    method: 'POST',
                    data: {
                        field,
                        value
                    }
                })
                .then((json) => {
                    Pachno.trigger(Pachno.EVENTS.issueUpdateDone, {id: issue.id});
                    Pachno.trigger(Pachno.EVENTS.issueUpdateJson, {json: json.issue});

                    resolve();
                });
        })
    }

    setupListeners() {
        const $body = $('body');
        const issue = this;

        $body.off('click', `input[data-trigger-issue-update][data-issue-id=${this.id}]`);
        $body.on('click', `input[data-trigger-issue-update][data-issue-id=${this.id}]`, function () {
            const $element = $(this);
            $element.addClass('submitting');
            issue.postAndUpdate($element.data('field'), $element.val())
                .then(() => {
                    $element.removeClass('submitting');
                })
        });

        $body.off('click', `.editable[data-editable-field][data-issue-id=${this.id}]`);
        $body.on('click', `.editable[data-editable-field][data-issue-id=${this.id}]`, function () {
            const $element = $(this);
            const $textarea = $(`[data-editable-textarea][data-issue-id=${issue.id}][data-field=${$element.data('field')}]`);
            const editor = getEditor($textarea.attr('id'));

            $element.addClass('editing');
            setTimeout(() => {
                editor.focus();
                editor.codemirror.focus();
            }, 250);
        });

        $body.off('click', `[data-trigger-save][data-issue-id=${this.id}]`);
        $body.on('click', `[data-trigger-save][data-issue-id=${this.id}]`, function () {
            const $element = $(this);
            const $textarea = $(`[data-editable-textarea][data-issue-id=${issue.id}][data-field=${$element.data('field')}]`);
            const $value_container = $(`[data-editable-field][data-issue-id=${issue.id}][data-field=${$element.data('field')}]`);
            const editor = getEditor($textarea.attr('id'));
            issue.postAndUpdate($element.data('field'), editor.value())
                .then(() => {
                    $value_container.removeClass('editing');
                })
        });

        $body.off('click', `[data-trigger-cancel-editing][data-issue-id=${this.id}]`);
        $body.on('click', `[data-trigger-cancel-editing][data-issue-id=${this.id}]`, function () {
            const $element = $(this);
            const $value_container = $(`[data-editable-field][data-issue-id=${issue.id}][data-field=${$element.data('field')}]`);
            const $textarea = $(`[data-editable-textarea][data-issue-id=${issue.id}][data-field=${$element.data('field')}]`);
            const editor = getEditor($textarea.attr('id'));
            editor.value(issue[$element.data('field')]);
            $value_container.removeClass('editing');
        });

        Pachno.on(Pachno.EVENTS.issueUpdateJson, function (PachnoApplication, data) {
            if (data.json.id != issue.id) {
                return
            }

            issue.updateFromJson(data.json);
            issue.updateVisibleValues(data.json);
        });
    }

    updateVisibleValues(json) {
        const $value_fields = $(`[data-dynamic-field-value][data-issue-id=${this.id}]`);
        const visible_fields = json.visible_fields;
        const available_fields = json.available_fields;

        for (const element of $value_fields) {
            const $element = $(element);
            const field = $element.data('field');

            if (!this[field]) {
                $element.addClass('no-value');
            } else {
                $element.removeClass('no-value');
            }
            switch (field) {
                case 'priority':
                case 'resolution':
                case 'category':
                case 'milestone':
                case 'reproducability':
                case 'severity':
                    if (this[field]?.name !== undefined) {
                        $element.html(this[field].name);
                    } else {
                        $element.html(Pachno.T.issue.value_not_set);
                    }
                    break;
                case 'description':
                    $element.html(this.description_formatted);
                    break;
                case 'reproduction_steps':
                    $element.html(this.reproduction_steps_formatted);
                    break;
                case 'updated_at':
                    $element.html(this.updated_at_friendly);
                    $element.prop('title', this.updated_at_full);
                    $element.prop('datetime', this.updated_at_datetime);
                    break;
                case 'percent_complete':
                    $($element.find('.percent_filled')).css({ width: this.percent_complete + '%'});
                    break;
            }
        }

        for (const field of available_fields) {
            const $field = $(`#${field}_field`);
            if (!$field.length) {
                continue;
            }

            if (visible_fields.contains(field) || (this[field] !== undefined && this[field] !== null)) {
                $field.removeClass('hidden');
            } else {
                $field.addClass('hidden');
            }
        }
    }

    createHtmlElement() {
        let classes = [];
        if (this.closed) classes.push('issue_closed');
        if (this.blocking) classes.push('blocking');

        let html = `
<div id="whiteboard_issue_${this.id}" class="whiteboard-issue trigger-backdrop ${classes.join(',')}" data-issue-id="${this.id}" data-url="${this.card_url}/board_id/${this.board_id}">
    <div class="issue-header">
        <span class="issue-number">${this.issue_no}</span>
        <span class="issue-title">${this.title}</span>
        <div class="dropper-container">
            <button class="button icon dropper dynamic_menu_link" type="button">${UI.fa_image_tag('ellipsis-v')}</button>
            <div class="dropdown-container dynamic_menu" data-menu-url="${this.more_actions_url}">
                <div class="list-mode">
                    <div class="list-item disabled">
                        <span class="icon">${UI.fa_image_tag('spinner', {'classes': 'fa-spin'})}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="issue-info">
    </div>
</div>
`;
        let $html = $(html);
        let $info = $html.find('.issue-info');
        if (this.number_of_files > 0) {
            $info.append(`<span class="attachments">${UI.fa_image_tag('paperclip')}<span>${this.number_of_files}</span></span>`);
        }
        if (this.number_of_comments > 0) {
            $info.append(`<span class="attachments">${UI.fa_image_tag('comments', [], 'far')}<span>${this.number_of_comments}</span></span>`);
        }

        $info.append(`<span class="status-badge" style="background-color: ${this.status.color}; color: ${this.status.text_color};"><span>${this.status.name}</span></span>`);

        if (this.assignee !== undefined && this.assignee !== null) {
            if (this.assignee.type == 'user') {
                $info.append(`<span class="assignee"><span class="avatar medium"><img src="${this.assignee.avatar_url_small}"></span></span>`)
            }
        }
        return $html;
    }
}

export default Issue;
window.Issue = Issue;