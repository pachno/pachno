import $ from "jquery";
import {debounce} from "../tools/tools";
import Swimlane from "./swimlane";
import UI from "../helpers/ui";
import Pachno from "./pachno";
import {watchIssuePopupForms} from "../helpers/issues";
import {Templates} from "./issue";
import backlog from "@/classes/backlog";

export const SwimlaneTypes = {
    NONE: '',
    ISSUES: 'issues',
    GROUPING: 'grouping',
    EXPEDITE: 'expedite',
    EPICS: 'epics'
};

export const BoardTypes = {
    GENERIC: 0,
    SCRUM: 1,
    KANBAN: 2
};

class Board {
    constructor(board_json) {
        this.id = undefined;
        this.background_color = undefined;
        this.background_file_url = undefined;
        this.name = undefined;
        this.type = undefined;
        this.whiteboardUrl = undefined;
        this.swimlane_type = undefined;
        this.swimlane_identifier = undefined;
        this.swimlane_field_values = undefined;
        this.report_issue_url = undefined;
        this.sort_milestones_url = undefined;
        this.epic_issue_type_id = undefined;

        /**
         * Swimlanes
         * @type {Swimlane[]}
         */
        this.swimlanes = undefined;
        this.columns = undefined;
        this.users = new Set();

        const is_scrum = parseInt(board_json.type) == BoardTypes.SCRUM;

        if (is_scrum) {
            const $selectedInput = $('input[name=selected_milestone]:checked');
            this.selected_milestone_id = ($selectedInput.length) ? parseInt($selectedInput.val()) : 0;
        } else {
            this.selected_milestone_id = undefined;
        }

        /**
         * @type {Milestone}
         */
        this.selected_milestone = undefined;

        this.updateSelectedMilestone();

        this.setJson(board_json, !is_scrum);
        this.setupListeners();

        $('#planning_indicator').hide();
        $('#planning_filter_title_input').prop('disabled', false);
    }

    setJson(board_json, fetchSwimlanes = true) {
        if (fetchSwimlanes === true) {
            fetchSwimlanes = (this.type !== board_json.type || this.swimlane_type !== board_json.swimlane_type || this.swimlane_identifier !== board_json.swimlane_identifier || this.swimlane_field_values.length !== board_json.swimlane_field_values.length);
            if (!fetchSwimlanes) {
                if (this.swimlane_field_values.length || board_json.swimlane_field_values.length) {
                    let difference = this.swimlane_field_values
                        .filter(x => !board_json.swimlane_field_values.includes(x))
                        .concat(board_json.swimlane_field_values.filter(x => !this.swimlane_field_values.includes(x)));

                    fetchSwimlanes = difference.length > 0;
                }
            }
        }

        this.id = board_json.id;
        this.background_color = board_json.background_color;
        this.background_file_url = board_json.background_file_url;
        this.name = board_json.name;
        this.type = parseInt(board_json.type);
        this.whiteboardUrl = board_json.url;
        this.swimlane_type = board_json.swimlane_type;
        this.swimlane_identifier = board_json.swimlane_identifier;
        this.swimlane_field_values = board_json.swimlane_field_values;
        this.columns = board_json.columns;
        this.report_issue_url = board_json.report_issue_url;
        this.sort_milestones_url = board_json.sort_milestones_url;
        this.epic_issue_type_id = board_json.epic_issue_type_id;

        this.updateBackgroundColor();
        this.updateBoardClass();
        this.updateVisibleWhiteboard();

        if (fetchSwimlanes && this.columns.length && (this.selected_milestone_id || this.type !== BoardTypes.SCRUM)) {
            this.fetchSwimlanes();
        } else {
            $('#whiteboard_indicator').hide();
        }
    }

    updateVisibleWhiteboard(selected_milestone_id) {
        selected_milestone_id = selected_milestone_id || this.selected_milestone_id;
        $('#onboarding-no-board-columns').addClass('hidden');
        $('#onboarding-no-milestones').addClass('hidden');
        $('#onboarding-no-active-sprint').addClass('hidden');
        $('#whiteboard').hide();
        $('.add_column_milestone_id').val(selected_milestone_id);
        if ((!this.columns || !this.columns.length) || (selected_milestone_id === 0 && this.type === BoardTypes.SCRUM)) {
            if (selected_milestone_id === 0 && this.type === BoardTypes.SCRUM) {
                if ($('#selected_milestone_input > .list-item').length > 3) {
                    $('#onboarding-no-active-sprint').removeClass('hidden');
                } else {
                    $('#onboarding-no-milestones').removeClass('hidden');
                }
            } else {
                $('#onboarding-no-board-columns').removeClass('hidden');
            }
        } else {
            $('#whiteboard').show();
        }
    }

    updateBoardClass() {
        const $container = $('#content_container');
        $container.removeClass('type-generic');
        $container.removeClass('type-kanban');
        $container.removeClass('type-scrum');
        switch (this.type) {
            case BoardTypes.SCRUM:
                $container.addClass('type-scrum');
                break;
            case BoardTypes.KANBAN:
                $container.addClass('type-kanban');
                break;
            case BoardTypes.GENERIC:
                $container.addClass('type-generic');
                break;
        }

        const $milestone_0_checkbox = $('#selected_milestone_0_generic');
        const $milestone_0_label = $('label[for="selected_milestone_0_generic"]');
        if (this.type == BoardTypes.SCRUM) {
            $milestone_0_checkbox.attr('disabled', true);
            $milestone_0_label.addClass('disabled');
            $milestone_0_label.find('.name').html(Pachno.T.agile.choose_sprint_label);
        } else {
            $milestone_0_checkbox.removeAttr('disabled');
            $milestone_0_label.removeClass('disabled');
            $milestone_0_label.find('.name').html(Pachno.T.agile.no_milestone_label);
        }
    }

    updateSelectedMilestone(trigger_reload = false) {
        const $selectedInput = $('input[name=selected_milestone]:checked');
        const previous_milestone_id = this.selected_milestone_id;
        this.selected_milestone_id = ($selectedInput.length) ? parseInt($selectedInput.val()) : 0;
        if (this.swimlanes) {
            for (const swimlane of this.swimlanes) {
                swimlane.selected_milestone_id = this.selected_milestone_id;
            }
        }

        this.updateVisibleWhiteboard();
        if (this.selected_milestone_id !== previous_milestone_id && trigger_reload && (this.type !== BoardTypes.SCRUM || this.selected_milestone_id !== 0)) {
            this.fetchSwimlanes();
        }
    }

    fetchSwimlanes() {
        $('#whiteboard .row.swimlane').remove();
        if (this.swimlanes) {
            for (const swimlane of this.swimlanes) {
                for (const issue of swimlane.issues) {
                    issue.processed = false;
                }
            }
        }
        this.users.clear();

        $('#whiteboard_indicator').show();
        if (this.selected_milestone_id !== 0 || this.type !== BoardTypes.SCRUM) {
            Pachno.fetch(`${this.whiteboardUrl}&milestone_id=${this.selected_milestone_id}`, { method: 'GET' })
                .then((json) => {
                    this.setMilestone(json.milestone);
                    this.setSwimlanes(json.swimlanes);
                });
        } else {
            $('#whiteboard_indicator').hide();
        }
    }

    setMilestone(milestone_json) {
        this.selected_milestone = new Milestone(milestone_json);
    }

    setSwimlanes(swimlanes) {
        const $whiteboard_indicator = $('#whiteboard_indicator');
        if (swimlanes.length) {
            this.swimlanes = swimlanes.map(json => new Swimlane(json, this, this.selected_milestone_id));
            this.updateWhiteboard();
        } else {
            this.swimlanes = swimlanes;
            $whiteboard_indicator.hide();
        }
    }

    updateBackgroundColor(_color) {
        const color = _color ?? this.background_color;
        const $main_container = $('#main_container');
        if (this.background_file_url !== '' || color !== '') {
            $main_container.css({ backgroundColor: color });
            $main_container.addClass('shaded');

            $('.shadeable').addClass('shaded');
        } else {
            $main_container.css({ backgroundColor: '#FFF' });
            $main_container.removeClass('shaded');

            $('.shadeable').removeClass('shaded');
        }
    }

    verifySwimlanes() {
        const $whiteboard_content = $('#whiteboard-content');
        let has_swimlanes = false;
        for (const swimlane of this.swimlanes) {
            if ($(`.swimlane[data-swimlane-identifier=${swimlane.identifier}]`).length) {
                has_swimlanes = true;
                continue;
            }

            const swimlane_html = `<div class="row swimlane empty" data-swimlane-identifier="${swimlane.identifier}"></div>`;
            const $swimlane = $(swimlane_html);
            if (this.swimlane_type !== SwimlaneTypes.NONE) {
                let header_name = '';
                if (swimlane.identifier_issue) {
                    const closed_class = (swimlane.identifier_issue.closed) ? 'closed' : '';
                    header_name = '<span class="issue-container">';
                    header_name += `<a class="issue-number" href="${swimlane.identifier_issue.href}">`;
                    header_name += `<span>${swimlane.identifier_issue.issue_no}</span>`;
                    header_name += `<span class="status-badge" data-dynamic-field-value data-field="status" data-issue-id="${swimlane.identifier_issue.id}" style="background-color: ${swimlane.identifier_issue.status.color}; color: ${swimlane.identifier_issue.status.text_color};"><span>${swimlane.identifier_issue.status.name}</span></span>`
                    header_name += `</a>`;
                    header_name += `<span class="name issue_header ${closed_class} trigger-backdrop" data-url="${swimlane.identifier_issue.card_url}">`;
                    header_name += `<span data-dynamic-field-value data-field="title" data-issue-id="${swimlane.identifier_issue.id}">${swimlane.identifier_issue.title}</span>`;
                    header_name += '</span>';
                    header_name += '</span>';
                    header_name += `<button class="button secondary highlight trigger-report-issue trigger-backdrop" data-url="${this.report_issue_url}" data-additional-params="parent_issue_id=${swimlane.identifier_issue.id}">${UI.fa_image_tag('sticky-note', { classes: 'icon' }, 'far')}<span>${Pachno.T.agile.add_card_here}</span></button>`
                    header_name += `            
                        <div class="dropper-container">
                            <button class="button icon dropper dynamic_menu_link" type="button">${UI.fa_image_tag('ellipsis-v')}</button>
                            <div class="dropdown-container dynamic_menu" data-menu-url="${swimlane.identifier_issue.more_actions_url}">
                                <div class="list-mode">
                                    <div class="list-item disabled">
                                        <span class="icon">${UI.fa_image_tag('spinner', {'classes': 'fa-spin'})}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    header_name = '<span class="issue-container">';
                    header_name += `<span class="name issue_header">${swimlane.name}</span>`;
                    header_name += '</span>';
                    if (swimlane.has_identifiables) {
                        header_name += `<button class="button secondary highlight trigger-report-issue trigger-backdrop" data-url="${this.report_issue_url}" data-additional-params="${swimlane.identifier_grouping}_ids=${swimlane.identifiables.map(i => i.id).join(',')}">${UI.fa_image_tag('sticky-note', { classes: 'icon' }, 'far')}<span>${Pachno.T.agile.add_card_here}</span></button>`
                    } else {
                        header_name += `<button class="button secondary highlight trigger-report-issue trigger-backdrop" data-url="${this.report_issue_url}" data-additional-params="issuetype_id=${this.swimlane_field_values}">${UI.fa_image_tag('stream', { classes: 'icon'})}<span>${Pachno.T.agile.add_swimlane}</span></button>`
                    }
                }
                const header_html = `<div class="swimlane-header"><div class="header">${header_name}</div>`;
                $swimlane.append(header_html);
            }
            $swimlane.append(`<div class="columns-container scroll-sync" id="${swimlane.identifier}-columns"><div class="columns"></div></div>`);
            if (has_swimlanes && $(`.swimlane[data-swimlane-identifier="swimlane_0"]`).length) {
                $(`.swimlane[data-swimlane-identifier="swimlane_0"]`).prepend($swimlane);
            } else {
                $whiteboard_content.append($swimlane);
            }
        }
    }

    verifyColumns() {
        for (const swimlane of this.swimlanes) {
            const $swimlane = $(`.swimlane[data-swimlane-identifier=${swimlane.identifier}]`);
            const columnCount = this.columns.length;
            const paddingCount = columnCount - 1;
            $swimlane.css({ width: `calc(300px * ${columnCount} + .5em * ${paddingCount})` });
            const $swimlaneColumnsContainer = $(`.swimlane[data-swimlane-identifier=${swimlane.identifier}] .columns`);
            for (const column of this.columns) {
                const status_ids = column.status_ids.join(',')
                const column_id = `swimlane_${swimlane.identifier}_column_${column.id}`;
                if ($(`#${column_id}`).length) {
                    continue;
                }

                let html = `<div class="column" id="${column_id}" data-swimlane-identifier="${swimlane.identifier}" data-column-id="${column.id}" data-status-ids="${status_ids}">`;
                if (this.swimlane_type === SwimlaneTypes.NONE || !swimlane.has_identifiables) {
                    html += `
                            <div class="form-container">
                                <div class="row">
                                    <div class="form name">
                                        <div class="form-row">
                                            <span class="input invisible trigger-report-issue trigger-backdrop" data-url="${this.report_issue_url}" data-additional-params="status_ids=${status_ids}">
                                                <span class="placeholder">${UI.fa_image_tag('plus')}<span>${Pachno.T.agile.add_card}</span></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                }
                html += '</div>';
                $swimlaneColumnsContainer.append(html);
            }
        }
    }

    verifyIssues() {
        let column_issues = 0;
        for (const column of this.columns) {
            const isInColumn = function (issue) { return issue.status.id && column.status_ids.includes(issue.status.id)};
            let num_issues = {};
            for (const status_id of column.status_ids) {
                num_issues[`status_${status_id}`] = 0;
            }
            for (const swimlane of this.swimlanes) {
                const $swimlane = $(`#swimlane_${swimlane.identifier}_column_${column.id}`);
                const $add_card_form = $(`#swimlane_${swimlane.identifier}_column_${column.id} .form-container`);
                const $swimlaneContainer = $swimlane.parents('.swimlane');

                for (const issue of swimlane.issues) {
                    if (!isInColumn(issue)) continue;

                    num_issues[`status_${issue.status.id}`] += 1;
                    if (issue.processed || !swimlane.has(issue)) continue;

                    if (issue.assignee && issue.assignee.type == 'user') {
                        this.users.add(JSON.stringify(issue.assignee));
                    }

                    $swimlaneContainer.removeClass('empty');
                    if (this.swimlane_type == SwimlaneTypes.NONE || !swimlane.has_identifiables) {
                        $add_card_form.before(issue.element.detach());
                    } else {
                        $swimlane.append(issue.element.detach());
                    }
                    issue.processed = true;
                    issue.swimlane = swimlane.identifier;
                }
            }
            let count_total = 0;
            const $primary_count = $(`.column[data-column-id=${column.id}] .column-count.primary`);
            for (const status_id of column.status_ids) {
                $(`.column-count[data-status-id=${status_id}]`).html(num_issues[`status_${status_id}`]);
                count_total += parseInt(num_issues[`status_${status_id}`]);
            }
            if ($primary_count.length) {
                $primary_count.html(count_total);
            }
            column_issues += count_total;
        }

        if (this.swimlane_type == SwimlaneTypes.ISSUES || this.swimlane_type == SwimlaneTypes.EPICS) {
            for (const swimlane of this.swimlanes) {
                column_issues += 1;
            }
            column_issues -= 1;
        }

        const $milestoneUnhandledWarningContainer = $('#milestone-issues-unhandled');
        const $milestoneConfigurationWarningContainer = $('#milestone-issues-unconfigured');
        $milestoneUnhandledWarningContainer.addClass('hidden');
        $milestoneConfigurationWarningContainer.addClass('hidden');

        if (this.swimlane_type != SwimlaneTypes.NONE && column_issues < this.selected_milestone.issues_count - this.swimlanes.length - 1) {
            $milestoneConfigurationWarningContainer.find('.number_of_issues').html(this.selected_milestone.issues_count - column_issues);
            $milestoneConfigurationWarningContainer.removeClass('hidden');
        } else if (column_issues < this.selected_milestone.issues_count) {
            $milestoneUnhandledWarningContainer.find('.number_of_issues').html(this.selected_milestone.issues_count - column_issues);
            $milestoneUnhandledWarningContainer.removeClass('hidden');
        }
    }

    updateWhiteboard() {
        try {
            const $whiteboard_indicator = $('#whiteboard_indicator');
            const $whiteboard = $('#whiteboard');
            if (!$whiteboard.length) {
                $whiteboard_indicator.hide();
                return;
            }

            $whiteboard.removeClass('initialized');

            if (this.swimlane_type !== SwimlaneTypes.NONE) {
                $whiteboard.removeClass('no-swimlanes');
                $whiteboard.addClass('swimlanes');
            }
            else {
                $whiteboard.removeClass('swimlanes');
                $whiteboard.addClass('no-swimlanes');
            }
            $whiteboard.addClass('initialized');
            this.verifySwimlanes();
            this.verifyColumns();
            this.verifyIssues();
            this.updateAssigneesList();
            const $buttons = $('.trigger-report-issue');
            const url = (this.selected_milestone_id) ? this.report_issue_url + `&milestone_id=${this.selected_milestone_id}` : this.report_issue_url;
            $buttons.each(function () {
                const $button = $(this);
                $button.data('original-url', $button.data('url'));
                if ($button.data('additional-params')) {
                    $button.data('url', url + '&' + $button.data('additional-params'));
                } else {
                    $button.data('url', url);
                }
            });

            $whiteboard_indicator.hide();
        } catch (error) {
            console.trace(error);
            console.error(error);
        }
    }

    updateAssigneesList() {
        const $avatar_container = $('#board-assignees-list');
        $avatar_container.html('');
        for (const assignee of this.users) {
            const assignee_json = JSON.parse(assignee);
            $avatar_container.append(`<span class="avatar-container"><span class="avatar medium"><img src="${assignee_json.avatar_url_small}"></span><span class="name-container"><span class="name">${assignee_json.display_name}</span><span class="username">@${assignee_json.username}</span></span></span>`);
        }
    }

    getSwimlane(swimlane_identifier) {
        for (const swimlane of this.swimlanes) {
            if (swimlane.identifier === swimlane_identifier)
                return swimlane;
        }
    }

    filterInput(event) {
        const $filter_input = $(event.target);
        const value = $filter_input.val();
        if ((value.length >= 3 || value.length == 0)) {
            const $planning_indicator = $('#planning_indicator');
            $planning_indicator.show();

            const $project_planning = $('#project_planning');
            if (value !== '') {
                const matching = new RegExp(value, "i");
                $project_planning.addClass('issue_title_filtered');
                $('.issue-card').each(function () {
                    const $issue_card = $(this);
                    if ($issue_card.down('.value').innerHTML.search(matching) !== -1) {
                        $issue_card.addClass('title_unfiltered');
                    } else {
                        $issue_card.removeClass('title_unfiltered');
                    }
                });
            } else {
                $project_planning.removeClass('issue_title_filtered');
                $('.issue-card').each().removeClass('title_unfiltered');
            }
            $planning_indicator.hide();
        }
    }

    retrieveMilestoneStatus() {
        const $milestone_input = $('#selected_milestone_input');
        const milestone_id = $milestone_input.data('selected-value');
        if (milestone_id) {
            Pachno.fetch($milestone_input.data('status-url'), {
                data: 'milestone_id=' + parseInt(milestone_id) + '&board_id=' + this.id,
                method: 'GET',
                loading: {
                    hide: 'selected_milestone_status_details',
                    indicator: '#selected_milestone_status_indicator'
                },
                success: {
                    update: '#selected_milestone_status_details',
                    show: 'selected_milestone_status_details',
                    callback: function () {
                        $('#reportissue_button').data('milestone-id', milestone_id);
                    }
                }
            });
        }
    }

    addIssue(issue_json) {
        const issue = Pachno.addIssue(issue_json, this.id, Templates.card);
        if (this.swimlane_type === SwimlaneTypes.ISSUES && this.swimlane_identifier === "issuetype" && this.swimlane_field_values.includes(issue.issue_type.id)) {
            const swimlane = new Swimlane({
                issues: [],
                name: issue.title,
                identifier_type: "issues",
                identifier_grouping: "issuetype",
                has_identifiables: true,
                identifier_issue: issue_json,
                identifier: 'swimlane_' + issue.id
            }, this, this.selected_milestone_id);

            this.swimlanes.splice(this.swimlanes.length - 1, 0, swimlane);
        } else {
            for (const swimlane of this.swimlanes) {
                if (swimlane.has(issue)) {
                    swimlane.addIssue(issue);
                }
            }
        }
        this.updateWhiteboard();
    }

    addColumn(column, swimlanes) {
        this.columns.push(column);
        if (swimlanes !== undefined) {
            if (this.swimlanes === undefined || !this.swimlanes.length) {
                this.setSwimlanes(swimlanes);
            } else {
                for (const swimlane of swimlanes) {
                    const board_swimlane = this.swimlanes.find(lane => lane.identifier == swimlane.identifier);
                    board_swimlane.addIssues(swimlane.issues);
                }
            }
        }
        for (const column of this.columns) {
            for (const status_id of column.status_ids) {
                $(`#add_next_column_status_${status_id}`).attr('disabled', true);
                $(`label[for=add_next_column_status_${status_id}]`).addClass('disabled');
            }
        }
        this.updateWhiteboard();
    }

    setupDragDrop() {
        let dragged_issue = undefined;
        const board = this;
        const $body = $('body');

        const dragStart = function (event) {
            const $issue = $(event.target);
            dragged_issue = Pachno.getIssue($issue.data('issue-id'));
            dragged_issue.startDragging(event.clientX, event.clientY);
            event.originalEvent.dataTransfer.setData('text/plain', dragged_issue.id);
            event.originalEvent.dataTransfer.effectAllowed = "move";
            event.originalEvent.dataTransfer.dropEffect = "move";
            event.currentTarget.classList.add('dragging');
            $('#whiteboard').addClass('is-dragging');

            const $columns = $('.whiteboard-columns .column[data-status-ids]');
            const current_swimlane = board.swimlanes.find(swimlane => swimlane.has(dragged_issue));

            for (const column of $columns) {
                let $column = $(column);
                let status_id_data = $column.data('status-ids');
                let status_ids = (Number.isNaN(status_id_data)) ? status_id_data.split(',') : [status_id_data];

                $column.removeClass('drop-valid drop-highlight drop-origin');

                if (status_ids.includes(parseInt(dragged_issue.status.id))) {
                    if (current_swimlane.identifier !== $column.data('swimlane-identifier')) {
                        $column.addClass('drop-valid');
                    } else {
                        $column.addClass('drop-origin');
                    }

                    continue;
                }

                for (const status of dragged_issue.available_statuses) {
                    if (status_ids.includes(parseInt(status.id))) {
                        $column.addClass('drop-valid');
                    }
                }
            }
        };

        const dragOverColumn = function (event) {
            if (event.isPropagationStopped())
                return

            const $column = $(event.target);
            $column.addClass('drop-highlight');
            dragged_issue.dragDetect(event);
            event.preventDefault();
            event.stopPropagation();
        };

        const drop = function (event) {
            if (event.isPropagationStopped())
                return;

            // event.originalEvent.dataTransfer.clearData();
            // const issue = Pachno.getIssue(event.originalEvent.dataTransfer.getData('text/plain'));
            const $dropped_target = $('.whiteboard-columns .column.drop-valid.drop-highlight');
            if ($dropped_target.length) {
                dragged_issue.stopDragging(true);
                const $target_issue = $dropped_target.find('.whiteboard-issue.drop-target');
                if ($target_issue.length) {
                    if ($target_issue.hasClass('drop-indicator-above')) {
                        dragged_issue.clone_element.detach().insertBefore($target_issue);
                    } else {
                        dragged_issue.clone_element.detach().insertAfter($target_issue);
                    }
                } else {
                    const $add_card_indicator = $dropped_target.find('.form-container');
                    if ($add_card_indicator.length) {
                        dragged_issue.clone_element.detach().insertBefore($add_card_indicator);
                    } else {
                        $dropped_target.append(dragged_issue.clone_element.detach());
                    }
                }
                dragged_issue.clone_element.removeClass('clone');
                dragged_issue.clone_element.css({ top: '', transform: '', left: ''});
                let swimlane_identifier = $dropped_target.data('swimlane-identifier');
                let swimlane = board.getSwimlane(swimlane_identifier);
                let status_id_data = $dropped_target.data('status-ids');
                let status_ids = (Number.isNaN(status_id_data)) ? status_id_data.split(',') : [status_id_data];
                dragged_issue.triggerTransition(board, swimlane, status_ids, event.shiftKey);
            } else {
                dragged_issue.stopDragging();
            }

            $('#whiteboard').removeClass('is-dragging');
            $('.whiteboard-columns .column').removeClass('drop-valid drop-highlight drop-origin');

            const $whiteboard_issues = $('.whiteboard-issue');
            $whiteboard_issues.removeClass('drop-target drop-indicator-above drop-indicator-below');

            event.stopPropagation();
            event.preventDefault();
        };

        const dragLeaveColumn = function (event) {
            if (event.isPropagationStopped() || event.isDefaultPrevented())
                return;

            const $column = $(event.target);
            $column.removeClass('drop-highlight');
            event.stopPropagation();
        };

        const dragOverIssue = function (event) {
            const $element = $(this);
            const $column = $element.parents('.column');
            const detectAbove = function ($element) {
                const rect = $element[0].getBoundingClientRect();
                const y = event.clientY - rect.top;

                return (y < rect.height / 2);
            }

            const above = detectAbove($element);
            const issue = Pachno.getIssue($element.data('issue-id'));

            issue.element.addClass('drop-target');
            issue.element.addClass((above) ? 'drop-indicator-above' : 'drop-indicator-below');
            dragged_issue.dragDetect(event);
            $column.addClass('drop-highlight');
            event.stopImmediatePropagation();
            event.preventDefault();
        };

        const dragLeaveIssue = function (event) {
            const $element = $(this);
            const $column = $element.parents('.column');
            $element.removeClass('drop-indicator-above drop-indicator-below drop-target');
            $column.removeClass('drop-highlight');
            event.preventDefault();
        };

        $body.off('dragstart', '.whiteboard-issue');
        $body.on('dragstart', '.whiteboard-issue', dragStart);

        $body.off('dragover', '.columns-container .column');
        $body.on('dragover', '.columns-container .column', dragOverColumn);

        $body.off('drop', '.columns-container .column');
        $body.on('drop', '.columns-container .column', drop);

        $body.off('dragleave', '.columns-container .column');
        $body.on('dragleave', '.columns-container .column', dragLeaveColumn);

        $body.off('dragover', '.columns-container .column .whiteboard-issue:not(.dragging):not(.clone)');
        $body.on('dragover', '.columns-container .column .whiteboard-issue:not(.dragging):not(.clone)', dragOverIssue);

        $body.off('dragleave', '.columns-container .column .whiteboard-issue:not(.dragging):not(.clone)');
        $body.on('dragleave', '.columns-container .column .whiteboard-issue:not(.dragging):not(.clone)', dragLeaveIssue);

        $body.off('drop', '.columns-container .column .whiteboard-issue:not(.dragging):not(.clone)');
        $body.on('drop', '.columns-container .column .whiteboard-issue:not(.dragging):not(.clone)', drop);
    }

    setupListeners() {
        const board = this;
        const $body = $('body');

        $body.on('click', '#selected_milestone_input li', function (event) {
            const $input = $(event.target);
            const milestone_id = $input.data('input-value');
            board.retrieveMilestoneStatus(board.id, milestone_id);
        });
        $body.on('click', '.trigger-whiteboard-toggle-add-first-column', (event) => {
            const $container = $('#add-first-column-button-container');
            $container.toggleClass('active');
            if ($container.hasClass('active')) {
                $('#first-column-name').focus();
            }
        });
        $body.on('click', '.trigger-whiteboard-toggle-add-next-column', (event) => {
            const $container = $('#add-next-column-input-container');
            $container.toggleClass('toggle-card');
            if ($container.hasClass('toggle-card')) {
                $('#next-column-name').focus();
            }
        });
        $body.on('mouseover', '.backdrop_box .color-picker', function () {
            $('#fullpage_backdrop').addClass('see-through');
        });
        $body.on('mouseout', '.backdrop_box .color-picker', function () {
            $('#fullpage_backdrop').removeClass('see-through');
        });
        $body.on('change', '.backdrop_box .color-picker input', function (event) {
            const checkbox = $(event.target);
            if (checkbox.prop('checked')) {
                board.updateBackgroundColor(checkbox.val());
            }
        });

        watchIssuePopupForms();

        const $filter_input = $('#planning_filter_title_input');
        $filter_input.on('keyup', debounce(this.filterInput, 250).bind(this));
        Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
            const json = data.json;
            switch (data.form) {
                case 'edit-agileboard-form':
                    board.setJson(json.board);
                    break;
                case 'report_issue_form':
                    board.addIssue(json.issue);
                    break;
                case 'add-first-column-form':
                case 'add-another-column-form':
                    const $container = $('#add-next-column-input-container');
                    $container.before(json.component);
                    $container.removeClass('toggle-card');
                    board.addColumn(json.column, json.swimlanes);
                    $('#add-another-column-form').trigger("reset");
                    board.updateVisibleWhiteboard();
                    break;
                case 'edit_milestone_form':
                    $('#milestone-list-no-milestones').hide();
                    $('#milestone-list-separator').after(json.component);
                    $(`#selected_milestone_${json.milestone.id}`).prop('checked', true);
                    board.updateSelectedMilestone(true);
                    break;
            }
        });

        Pachno.on(Pachno.EVENTS.issue.updateJsonComplete, (_, issue) => {
            let found = false;
            for (const swimlane of board.swimlanes) {
                if (swimlane.identifier_issue && swimlane.identifier_issue.id === issue.id)
                    return;

                if (issue.milestone?.id == board.selected_milestone_id) {
                    let updated = swimlane.addOrRemove(issue, !found);
                    if (found === false) {
                        found = updated;
                    }
                } else {
                    swimlane.removeIssue(issue);
                }
            }

            board.verifyIssues();
        });

        this.setupDragDrop();
    }
}

export default Board;
window.Board = Board;
