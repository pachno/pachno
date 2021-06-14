import UI from "../helpers/ui";
import $ from "jquery";
import Pachno from "./pachno";
import Uploader from "./uploader";
import {TYPES as QuicksearchTypes} from "./quicksearch";
import {getEditor} from "../widgets/editor";
import {throttle} from 'throttle-debounce';
import {SwimlaneTypes} from "./board";

class Issue {
    constructor(json, board_id, create_element = true) {
        this.board_id = board_id;
        this.updateFromJson(json);
        if (create_element) {
            this.element = this.createHtmlElement();
        }
        this.clone_element = undefined;
        this.event_throttled = false;
        this.setupListeners();
        this.uploader = new Uploader({
            uploader_container: `#viewissue_attached_information_container[data-issue-id='${this.id}']`,
            mode: 'list',
            only_images: false,
            type: 'attachment',
            data: {
                issue_id: this.id
            }
        });
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
        this.choices_url = json.choices_url;
        this.backdrop_url = json.backdrop_url;
        this.cover_image = json.cover_image;

        this.project = json.project;
        this.transitions = json.transitions;
        for (const index in this.transitions) {
            if (this.transitions.hasOwnProperty(index)) {
                for (const status_index in this.transitions[index].status_ids) {
                    if (this.transitions[index].status_ids.hasOwnProperty(status_index)) {
                        this.transitions[index].status_ids[status_index] = parseInt(this.transitions[index].status_ids[status_index]);
                    }
                }
            }
        }
        this.available_statuses = json.available_statuses;

        this.blocking = json.blocking;
        this.locked = json.locked;
        this.closed = json.closed;
        this.deleted = json.deleted;
        this.state = json.state;
        this.editable = json.editable;

        this.description = json.description;
        this.description_formatted = json.description_formatted;
        this.reproduction_steps = json.reproduction_steps;
        this.reproduction_steps_formatted = json.reproduction_steps_formatted;

        this.assigned_to = json.assigned_to;
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

        this.number_of_files = parseInt(json.number_of_files);
        this.number_of_comments = parseInt(json.number_of_comments);
        this.number_of_subscribers = parseInt(json.number_of_subscribers);
        this.number_of_child_issues = parseInt(json.number_of_child_issues);
        this.number_of_affected_items = parseInt(json.number_of_affected_items);

        this.processed = false;
    }

    postAndUpdate(field, value, additional_value) {
        const issue = this;
        let data;

        data = { field, value, additional_value };

        Pachno.trigger(Pachno.EVENTS.issue.update, {id: this.id});

        return new Promise(function (resolve, reject) {
            Pachno.fetch(issue.save_url, {
                    method: 'POST',
                    data
                })
                .then((json) => {
                    Pachno.trigger(Pachno.EVENTS.issue.updateDone, {id: issue.id});
                    Pachno.trigger(Pachno.EVENTS.issue.updateJson, {json: json.issue});

                    resolve();
                });
        })
    }

    triggerEditField(field) {
        const $container_element = $(`#${field}_field`);
        const $element = $(`[data-editable-field][data-issue-id="${this.id}"][data-field="${field}"]`);
        const $textarea = $(`[data-editable-textarea][data-issue-id="${this.id}"][data-field="${field}"]`);
        const editor = getEditor($textarea.attr('id'));

        $container_element.addClass('force-visible');
        $element.addClass('editing');
        editor.codemirror.focus();
    }

    setupListeners() {
        const $body = $('body');
        const issue = this;

        $body.off('click', `.issue-field[data-issue-id="${this.id}"] .trigger-issue-set-posted-by`);
        $body.on('click', `.issue-field[data-issue-id="${this.id}"] .trigger-issue-set-posted-by`, function (event) {
            event.preventDefault();
            const $element = $(this);
            const identifiable_type = $element.data('identifiable-type');
            const value = $element.data('identifiable-value');

            issue.postAndUpdate('posted_by', value, identifiable_type);
        });

        $body.off('click', `.issue-field[data-issue-id="${this.id}"] .trigger-issue-set-assigned-to`);
        $body.on('click', `.issue-field[data-issue-id="${this.id}"] .trigger-issue-set-assigned-to`, function (event) {
            event.preventDefault();
            const $element = $(this);
            const identifiable_type = $element.data('identifiable-type');
            const value = $element.data('identifiable-value');

            issue.postAndUpdate('assigned_to', value, identifiable_type);
        });

        $body.off('click', `.issue-field[data-issue-id="${this.id}"] .trigger-issue-set-owned-by`);
        $body.on('click', `.issue-field[data-issue-id="${this.id}"] .trigger-issue-set-owned-by`, function (event) {
            event.preventDefault();
            const $element = $(this);
            const identifiable_type = $element.data('identifiable-type');
            const value = $element.data('identifiable-value');

            issue.postAndUpdate('owned_by', value, identifiable_type);
        });

        $body.off('click', `[data-trigger-issue-update][data-issue-id="${this.id}"]`);
        $body.on('click', `[data-trigger-issue-update][data-issue-id="${this.id}"]`, function () {
            const $element = $(this);
            const value = $element.data('field-value') !== undefined ? $element.data('field-value') : $element.val();
            $element.addClass('submitting');
            issue.postAndUpdate($element.data('field'), value)
                .then(() => {
                    $element.removeClass('submitting');
                })
        });

        $body.off('click', `.editable[data-editable-field][data-issue-id="${this.id}"]`);
        $body.on('click', `.editable[data-editable-field][data-issue-id="${this.id}"]`, function () {
            const $element = $(this);
            issue.triggerEditField($element.data('field'));
        });

        $body.off('click', `[data-trigger-save][data-issue-id="${this.id}"]`);
        $body.on('click', `[data-trigger-save][data-issue-id="${this.id}"]`, function () {
            const $element = $(this);
            const $textarea = $(`[data-editable-textarea][data-issue-id=${issue.id}][data-field=${$element.data('field')}]`);
            const $value_container = $(`[data-editable-field][data-issue-id=${issue.id}][data-field=${$element.data('field')}]`);
            const editor = getEditor($textarea.attr('id'));
            const field = $element.data('field');
            const $container_element = $(`#${field}_field`);
            issue.postAndUpdate(field, editor.value())
                .then(() => {
                    $value_container.removeClass('editing');
                    $container_element.removeClass('force-visible');
                })
        });

        $body.off('keypress', `[data-trigger-save-on-blur][data-issue-id="${this.id}"]`);
        $body.on('keypress', `[data-trigger-save-on-blur][data-issue-id="${this.id}"]`, function (event) {
            if (event.key === 'Enter') {
                $(this).blur();
            }
        });

        $body.off('blur', `[data-trigger-save-on-blur][data-issue-id="${this.id}"]`);
        $body.on('blur', `[data-trigger-save-on-blur][data-issue-id="${this.id}"]`, function () {
            const $element = $(this);
            const field = $element.data('field');

            if ($element.val() === issue[field])
                return;

            $element.addClass('saving');
            issue.postAndUpdate(field, $element.val())
                .then(() => {
                    $element.removeClass('saving');
                })
        });

        $body.off('click', `[data-trigger-cancel-editing][data-issue-id="${this.id}"]`);
        $body.on('click', `[data-trigger-cancel-editing][data-issue-id="${this.id}"]`, function () {
            const $element = $(this);
            const $value_container = $(`[data-editable-field][data-issue-id=${issue.id}][data-field=${$element.data('field')}]`);
            const $textarea = $(`[data-editable-textarea][data-issue-id=${issue.id}][data-field=${$element.data('field')}]`);
            const editor = getEditor($textarea.attr('id'));
            const field = $element.data('field');
            const $container_element = $(`#${field}_field`);
            editor.value(issue[field]);
            $value_container.removeClass('editing');
            $container_element.removeClass('force-visible');
        });

        Pachno.on(Pachno.EVENTS.issue.update, (json) => {
            if (json.id === issue.id) {
                $(`.issue-update-indicator[data-issue-id=${issue.id}]`).addClass('active');
            }
        });

        Pachno.on(Pachno.EVENTS.issue.updateDone, (json) => {
            if (json.id === issue.id) {
                $(`.issue-update-indicator[data-issue-id=${issue.id}]`).removeClass('active');
            }
        });

        Pachno.on(Pachno.EVENTS.issue.triggerEdit, function (PachnoApplication, data) {
            if (data.issue_id != issue.id)
                return;

            issue.triggerEditField(data.field);
        });

        Pachno.on(Pachno.EVENTS.issue.removeFile, function (PachnoApplication, data) {
            if (data.issue_id != issue.id)
                return;

            $(`[data-attachment][data-file-id="${data.file_id}"]`).remove();
            Pachno.UI.Dialog.dismiss();

            Pachno.fetch(data.url, {method: 'DELETE'})
                .then((json) => {
                    issue.updateFromJson(json.issue);
                    issue.updateVisibleValues();
                })
        });

        Pachno.on(Pachno.EVENTS.issue.removeAffectedItem, function (PachnoApplication, data) {
            if (data.issue_id != issue.id)
                return;

            Pachno.UI.Dialog.setSubmitting();
            $(`[data-affected-item][data-affected-item-id="${data.id}"]`).remove();
            Pachno.UI.Dialog.dismiss();

            Pachno.fetch(data.url, {method: 'DELETE'})
                .then((json) => {
                    issue.updateFromJson(json.issue);
                    issue.updateVisibleValues();
                })
        });

        Pachno.on(Pachno.EVENTS.upload.complete, function (PachnoApplication, data) {
            if (data.issue_id != issue.id)
                return;

            issue.number_of_files += 1;
            issue.updateVisibleValues();
        });

        Pachno.on(Pachno.EVENTS.issue.triggerUpdate, function (PachnoApplication, data) {
            if (data.issue_id != issue.id)
                return;

            issue.postAndUpdate(data.field, data.value);
        });

        Pachno.on(Pachno.EVENTS.issue.loadDynamicChoices, function (PachnoApplication, field) {
            return new Promise((resolve, reject) => {
                Pachno.fetch(issue.choices_url, {
                    data: `field=${field}`
                }).then((json) => {
                    let choices = [];
                    let index = 1;

                    for (const choice of json.data.choices) {
                        const icon = (choice.icon) ? {type: choice.icon.style, name: choice.icon.name} : undefined;
                        choices.push({
                            icon,
                            shortcut: `set ${field} ${index}`,
                            name: choice.name,
                            type: QuicksearchTypes.event,
                            event: Pachno.EVENTS.issue.triggerUpdate,
                            event_value: {field, value: choice.id, issue_id: issue.id}
                        })
                        index += 1;
                    }

                    Pachno.trigger(Pachno.EVENTS.quicksearchUpdateChoices, choices);
                })
            });
        });

        Pachno.on(Pachno.EVENTS.issue.updateJson, function (PachnoApplication, data) {
            if (data.json.id != issue.id) {
                return
            }

            const issue_json = (data.json.issue !== undefined) ? data.json.issue : data.json;

            if (issue.clone_element !== undefined) {
                const id = issue.element.id;
                issue.element.remove();
                issue.element = issue.clone_element;
                issue.element.id = id;
                issue.clone_element = undefined;
            }

            issue.element.removeClass('loading');
            issue.updateFromJson(issue_json);
            issue.updateVisibleValues(issue_json);
            Pachno.trigger(Pachno.EVENTS.issue.updateJsonComplete, issue);
        });
    }

    allowShortcuts(fields) {
        let choice = {
            icon: {name: 'edit', type: 'fas'},
            shortcut: 'set',
            name: 'Set issue properties',
            description: 'Update one or more properties of an issue',
            choices: []
        }

        for (const field_key in fields) {
            if (!fields.hasOwnProperty(field_key))
                continue;

            const field = fields[field_key];
            const field_choice = {
                shortcut: `set ${field_key}`,
                name: `Set issue ${field_key}`,
                description: `Quick edit this value`,
            };
            switch (field) {
                case FIELD_TYPES.BUILTIN:
                    switch (field_key) {
                        case 'priority':
                            field_choice.icon = {name: 'exclamation-circle', type: 'fas'};
                            break;
                        case 'resolution':
                            field_choice.icon = {name: 'clipboard-check', type: 'fas'};
                            break;
                        case 'category':
                            field_choice.icon = {name: 'chart-pie', type: 'fas'};
                            break;
                        case 'milestone':
                            field_choice.icon = {name: 'list-alt', type: 'fas'};
                            break;
                        default:
                            field_choice.icon = {name: 'edit', type: 'far'};
                    }

                    if (['title', 'reproduction_steps', 'description'].includes(field_key)) {
                        field_choice.type = QuicksearchTypes.event;
                        field_choice.event = Pachno.EVENTS.issue.triggerEdit;
                        field_choice.event_value = {field: field_key, issue_id: this.id};
                    } else if (field_key === 'votes') {
                        field_choice.name = 'Vote / unvote';
                        field_choice.description = 'Toggle your vote for this issue';
                        field_choice.type = QuicksearchTypes.event;
                        field_choice.event = Pachno.EVENTS.issue.triggerUpdate;
                        field_choice.event_value = {field: field_key, value: 1, issue_id: this.id}
                    } else {
                        field_choice.type = QuicksearchTypes.dynamic_choices;
                        field_choice.event = Pachno.EVENTS.issue.loadDynamicChoices;
                        field_choice.event_value = field_key;
                    }
                    break;
                case FIELD_TYPES.CLIENT_CHOICE:
                case FIELD_TYPES.COMPONENTS_CHOICE:
                case FIELD_TYPES.DROPDOWN_CHOICE_TEXT:
                case FIELD_TYPES.MILESTONE_CHOICE:
                case FIELD_TYPES.RADIO_CHOICE:
                case FIELD_TYPES.RELEASES_CHOICE:
                case FIELD_TYPES.STATUS_CHOICE:
                    field_choice.icon = {name: 'list-alt', type: 'fas'};
                    field_choice.type = QuicksearchTypes.dynamic_choices;
                    field_choice.event = Pachno.EVENTS.issue.loadDynamicChoices;
                    field_choice.event_value = field_key;
                    break;
            }

            choice.choices.push(field_choice);
        }

        Pachno.trigger(Pachno.EVENTS.quicksearchAddDefaultChoice, choice);
    }

    updateVisibleValues(json) {
        const $value_fields = $(`[data-dynamic-field-value][data-issue-id="${this.id}"]`);

        for (const element of $value_fields) {
            let $element = $(element);
            let field = $element.data('field');
            let $value_input;

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
                        $element.removeClass('no-value');
                        $element.removeClass('faded_out');
                        $value_input = $(`#issue_${this.id}_field_${field}_${this[field].value}`);
                    } else {
                        $element.html(($element.data('unknown') !== undefined) ? $element.data('unknown') : Pachno.T.issue.value_not_set);
                        $element.addClass('no-value');
                        $value_input = $(`#issue_${this.id}_field_${field}_0`);
                    }
                    if ($value_input.length) {
                        $value_input.checked = true;
                    }
                    break;
                case 'priority-icon':
                    if (!this.priority) {
                        $element.addClass('hidden');
                    } else {
                        $element.html(`<span class="priority priority_${this.priority.itemdata}" title="${this.priority.name}">${UI.fa_image_tag(this.priority.icon.name, { classes: 'priority-icon' }, this.priority.icon.style)}</span>`);
                        $element.removeClass('hidden');
                    }
                    break;
                case 'status':
                    $element.css({backgroundColor: this.status.color, color: this.status.text_color});
                    $element.html(`<span>${this.status.name}</span>`);
                    break;
                case 'issue_type_icon':
                    $element.html(`${UI.fa_image_tag(this.issue_type.fa_icon, { classes: `issuetype-icon issuetype-${this.issue_type.icon}` })}`);
                    for (const icon of ['bug_report', 'documentation_request', 'enhancement', 'feature_request', 'idea', 'epic', 'support_request', 'task', 'developer_report']) {
                        $element.removeClass(`issuetype-${icon}`);
                    }
                    $element.addClass(`issuetype-${this.issue_type.icon}`);
                    break;
                case 'issue_type':
                    $element.html(`${UI.fa_image_tag(this.issue_type.fa_icon, { classes: 'icon' })}<span class="name">${this.issue_type.name}</span>`);
                    for (const icon of ['bug_report', 'documentation_request', 'enhancement', 'feature_request', 'idea', 'epic', 'support_request', 'task', 'developer_report']) {
                        $element.removeClass(`issuetype-${icon}`);
                    }
                    $element.addClass(`issuetype-${this.issue_type.icon}`);
                    break;
                case 'title':
                    $element.html(this.title);
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
                case 'posted_by':
                case 'assigned_to':
                case 'owned_by':
                    if (this[field] && this[field].id) {
                        if (this[field].type == 'user') {
                            $element.html(`<span class="userlink trigger-backdrop" data-url="${this[field].card_url}"><span class="avatar medium"><img src="${this[field].avatar_url_small}"></span><span class="name">${this[field].name}</span></span>`);
                        } else {
                            $element.html(`<span>${this[field].name}</span>`);
                        }
                    } else {
                        $element.html(($element.data('unknown') !== undefined) ? $element.data('unknown') : Pachno.T.issue.value_not_set);
                    }
                    break;
                case 'number_of_subscribers':
                    $element.html(this.number_of_subscribers);
                    break;
                case 'number_of_affected_items':
                case 'number_of_files':
                case 'number_of_comments':
                case 'number_of_child_issues':
                    let $number_value_element = $element.find('.value');
                    if ($number_value_element.length) {
                        $number_value_element.html(this[field]);
                    } else {
                        $element.html(this[field]);
                    }
                    if (this[field] > 0) {
                        $element.removeClass('hidden');
                    } else {
                        $element.addClass('hidden');
                    }
                    break;
                case 'closed-message':
                    if (this['closed']) {
                        const message = $element.data('message').replace('%status_name', this.status.name).replace('%resolution', (this.resolution !== undefined) ? this.resolution.name : $element.data('unknown'));
                        $element.find('.content').html(message);
                        $element.removeClass('hidden');
                    } else {
                        $element.addClass('hidden');
                    }
                    break;
                case 'cover_image_toggle':
                    if (this.cover_image) {
                        $element.addClass('with-cover');
                    } else {
                        $element.removeClass('with-cover');
                    }
                    break;
                case 'cover_image':
                    if (this[field]) {
                        $element.css({ backgroundImage: `url('${this[field].url}')` });
                    } else {
                        $element.css({ backgroundImage: '' });
                    }
                    break;
                case 'cover_image_file':
                    if (this.cover_image && this.cover_image.id == $element.data('file-id')) {
                        $element.removeClass('trigger-set-cover');
                        $element.addClass('trigger-clear-cover');
                        $element.html(UI.fa_image_tag('minus-square', { classes: 'icon' }, 'far'));
                    } else {
                        $element.addClass('trigger-set-cover');
                        $element.removeClass('trigger-clear-cover');
                        $element.html(UI.fa_image_tag('images', { classes: 'icon' }, 'far'));
                    }
                    break;
                case 'closed':
                    if (this[field]) {
                        $element.removeClass('hidden');
                    } else {
                        $element.addClass('hidden');
                    }
                    break;
                case 'blocking':
                    if (this[field]) {
                        $element.removeClass('hidden');
                        $(`.trigger-blocking[data-issue-id="${this.id}"]`).addClass('hidden');
                        $(`.trigger-not-blocking[data-issue-id="${this.id}"]`).removeClass('hidden');
                    } else {
                        $element.addClass('hidden');
                        $(`.trigger-blocking[data-issue-id="${this.id}"]`).removeClass('hidden');
                        $(`.trigger-not-blocking[data-issue-id="${this.id}"]`).addClass('hidden');
                    }
                    break;
                case 'locked':
                    if (this[field]) {
                        $element.removeClass('hidden');
                        $(`.trigger-locked[data-issue-id="${this.id}"]`).addClass('hidden');
                        $(`.trigger-not-locked[data-issue-id="${this.id}"]`).removeClass('hidden');
                    } else {
                        $element.addClass('hidden');
                        $(`.trigger-locked[data-issue-id="${this.id}"]`).removeClass('hidden');
                        $(`.trigger-not-locked[data-issue-id="${this.id}"]`).addClass('hidden');
                    }
                    break;
                case 'editable':
                    if (!this[field]) {
                        $element.removeClass('hidden');
                    } else {
                        $element.addClass('hidden');
                    }
                    break;
                case 'menu':
                    $element.addClass('dynamic_menu');
                    $element.removeData('is-loaded');
                    $element.html(`<div class="list-mode"><div class="list-item"><span class="icon">${UI.fa_image_tag('spinner', {classes: 'fa-spin'})}</span></div></div>`);
                    break;
                case 'percent_complete':
                    $($element.find('.percent_filled')).css({width: this.percent_complete + '%'});
                    break;
            }
        }

        const $workflowTransitionsContainer = $(`[data-issue-workflow-transitions-container][data-issue-id="${this.id}"]`);
        if ($workflowTransitionsContainer.length) {
            $workflowTransitionsContainer.html('');
            for (const transition of this.transitions) {
                const tooltip = `<div class="tooltip from-above from-left">${transition.description}</div>`;
                let html = `<div class="tooltip-container">${tooltip}`;
                if (transition.template !== '') {
                    html += `<button class="button secondary highlight trigger-backdrop" type="button" data-url="${transition.backdrop_url}?project_id=${this.project.id}&issue_id=${this.id}">${transition.name}</button>`;
                } else {
                    html += `<button class="button secondary highlight trigger-workflow-transition" data-url="${transition.url.replace('%25project_key%25', this.project.key).replace('%25issue_id%25', this.id)}"><span>${transition.name}</span>${UI.fa_image_tag('spinner', {classes: 'fa-spin indicator'})}</button>`;
                }
                html += '</div>';
                $workflowTransitionsContainer.append(html);
            }
        }

        if (json !== undefined) {
            for (const field in json.fields) {
                if (!json.fields.hasOwnProperty(field))
                    continue;

                const $field = $(`#${field}_field`);
                if (!$field.length) {
                    continue;
                }

                if (json.visible_fields.hasOwnProperty(field) || (this[field] !== undefined && this[field] !== null && this[field] !== "")) {
                    $field.removeClass('hidden');
                    $field.removeClass('not-visible');
                } else {
                    $field.addClass('hidden');
                    $field.addClass('not-visible');
                }
            }
        }

        const $fieldslist = $('#issue_details_fieldslist');
        const $other_fields = $fieldslist.children('> ul > li:not(.hidden)');
        if ($other_fields.length) {
            $fieldslist.removeClass('not-visible');
        } else {
            $fieldslist.addClass('not-visible');
        }
    }

    triggerTransition(board, swimlane, status_ids, force_popup) {
        let show_popup = force_popup;
        let processed = false;

        this.clone_element.addClass('loading');

        for (const transition of this.transitions) {
            const includes = transition.status_ids.filter(value => status_ids.includes(value));
            if (!includes.length)
                continue;

            processed = true;
            if (!show_popup) {
                Pachno.fetch(transition.url.replace('%25project_key%25', this.project.key).replace('%25issue_id%25', this.id) + `?board_id=${board.id}&milestone_id=${board.selected_milestone_id}&swimlane_identifier=${swimlane.identifier}`, {method: 'POST'})
                    .then((json) => {
                        for (const issue of json.issues) {
                            Pachno.trigger(Pachno.EVENTS.issue.updateJson, {json: issue});
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        UI.Backdrop.show(transition.backdrop_url + `?issue_id=${this.id}&board_id=${board.id}&milestone_id=${board.selected_milestone_id}&swimlane_identifier=${swimlane.identifier}`);
                    })
            } else {
                UI.Backdrop.show(transition.backdrop_url + `?issue_id=${this.id}&board_id=${board.id}&milestone_id=${board.selected_milestone_id}&swimlane_identifier=${swimlane.identifier}`);
            }
        }

        if (!processed && !swimlane.has(this)) {
            switch (swimlane.identifier_type) {
                case SwimlaneTypes.ISSUES:
                    this.postAndUpdate('parent_issue_id', (swimlane.identifier_issue !== undefined) ? swimlane.identifier_issue.id : 0)
                    return;
                case SwimlaneTypes.GROUPING:
                case SwimlaneTypes.EXPEDITE:
                    this.postAndUpdate(swimlane.identifier_grouping, (swimlane.has_identifiables) ? swimlane.identifiables.find(() => true).id : 0);
                    return;
            }
        }
    }

    startDragging(x, y) {
        this.clone_element = this.createHtmlElement();
        this.clone_element.id = `whiteboard_issue_${this.id}_CLONE`;
        this.clone_element.insertAfter(this.element);
        this.clone_element.addClass('clone');
        const rect = this.element[0].getBoundingClientRect();
        this.clone_element.css({top: `${rect.top}px`, left: `${rect.left}px`});
        this.clone_element.data('original-x', x);
        this.clone_element.data('original-y', y);

        const $body = $('body');
        $body.off('dragover', this.dragDetect);
        $body.on('dragover', this.dragDetect.bind(this));
        this.event_throttled = false;

        return this.clone_element;
    }

    dragDetect(event) {
        if (this.event_throttled == true)
            return;

        if (this.clone_element === undefined)
            return;

        this.event_throttled = true;
        const X = this.clone_element.data('original-x');
        const Y = this.clone_element.data('original-y');
        const mouseX = event.pageX;
        const mouseY = event.pageY;
        this.clone_element.css({transform: `rotate(4deg) translateX(${mouseX - X}px) translateY(${mouseY - Y}px)`});
        setTimeout(() => {
            this.event_throttled = false;
        }, 30);
    }

    stopDragging(keep) {
        const $body = $('body');
        $body.off('dragover', this.dragDetect);

        if (keep === undefined) {
            this.clone_element.remove();
            this.element.removeClass('dragging');
        }
    }

    createHtmlElement() {
        let classes = [];
        if (this.closed) classes.push('issue_closed');
        if (this.blocking) classes.push('blocking');
        if (this.cover_image) classes.push('with-cover');
        const background = (this.cover_image) ? `background-image: url('${this.cover_image.url}');` : '';

        let html = `
<div id="whiteboard_issue_${this.id}" draggable="true" data-dynamic-field-value data-field="cover_image_toggle" data-issue-id="${this.id}" class="whiteboard-issue trigger-backdrop ${classes.join(' ')}" data-issue-id="${this.id}" data-url="${this.card_url}/board_id/${this.board_id}">
    <div class="issue-header" style="${background}" data-dynamic-field-value data-field="cover_image" data-issue-id="${this.id}">
        <span class="issue-number">${this.issue_no}</span>
        <span class="issue-title" data-dynamic-field-value data-field="title" data-issue-id="${this.id}">${this.title}</span>
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
    <div class="issue-info"></div>
    <div class="indicator">${UI.fa_image_tag('spinner', {'classes': 'fa-spin icon'})}</div>
</div>
`;
        let $html = $(html);
        let $info = $html.find('.issue-info');
        let child_issues_hidden_class = (this.number_of_child_issues > 0) ? '' : 'hidden';
        let files_hidden_class = (this.number_of_files > 0) ? '' : 'hidden';
        let comments_hidden_class = (this.number_of_comments > 0) ? '' : 'hidden';
        let priority_hidden_class = (this.priority) ? '' : 'hidden';
        let priority_icon = (this.priority) ? UI.fa_image_tag(this.priority.icon.name, { classes: 'priority-icon' }, this.priority.icon.style) : '';
        let priority_class = (this.priority) ? `priority priority_${this.priority.itemdata}` : '';
        let priority_span = (this.priority) ? `<span class="${priority_class}" title="${this.priority.name}">${priority_icon}</span>` : '';

        $info.append(`<span class="attachments ${child_issues_hidden_class}" data-dynamic-field-value data-field="number_of_child_issues" data-issue-id="${this.id}">${UI.fa_image_tag('tasks')}<span class="value">${this.number_of_child_issues}</span></span>`);
        $info.append(`<span class="attachments ${files_hidden_class}" data-dynamic-field-value data-field="number_of_files" data-issue-id="${this.id}">${UI.fa_image_tag('paperclip')}<span class="value">${this.number_of_files}</span></span>`);
        $info.append(`<span class="attachments ${comments_hidden_class}" data-dynamic-field-value data-field="number_of_comments" data-issue-id="${this.id}">${UI.fa_image_tag('comments', [], 'far')}<span class="value">${this.number_of_comments}</span></span>`);
        $info.append(`<span class="status-badge" style="background-color: ${this.status.color}; color: ${this.status.text_color};" data-dynamic-field-value data-field="status" data-issue-id="${this.id}"><span>${this.status.name}</span></span>`);
        $info.append(`<span class="attachments ${priority_hidden_class}" data-dynamic-field-value data-field="priority-icon" data-issue-id="${this.id}">${priority_span}</span>`);

        if (this.assigned_to !== undefined && this.assigned_to !== null) {
            if (this.assigned_to.type == 'user') {
                $info.append(`<span class="assignee" data-dynamic-field-value data-field="assigned_to" data-issue-id="${this.id}"><span class="avatar medium"><img src="${this.assigned_to.avatar_url_small}"></span></span>`)
            }
        }
        return $html;
    }
}

export const FIELD_TYPES = {
    BUILTIN: 0,
    DROPDOWN_CHOICE_TEXT: 1,
    INPUT_TEXT: 2,
    INPUT_TEXTAREA_MAIN: 3,
    INPUT_TEXTAREA_SMALL: 4,
    RADIO_CHOICE: 5,
    RELEASES_CHOICE: 8,
    COMPONENTS_CHOICE: 10,
    EDITIONS_CHOICE: 12,
    STATUS_CHOICE: 13,
    USER_CHOICE: 14,
    TEAM_CHOICE: 15,
    CALCULATED_FIELD: 18,
    DATE_PICKER: 19,
    MILESTONE_CHOICE: 20,
    CLIENT_CHOICE: 21,
    DATETIME_PICKER: 22,
};

export default Issue;
window.Issue = Issue;