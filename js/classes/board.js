import {debounce} from "../tools/tools";
import Swimlane from "./swimlane";
import UI from "../helpers/ui";

const SwimlaneTypes = {
    NONE: '',
    ISSUES: 'issues',
    GROUPING: 'grouping',
    EXPEDITE: 'expedite'
};

const BoardTypes = {
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
        this.swimlanes = undefined;
        this.columns = undefined;

        this.selected_milestone_id = 0;

        this.updateSelectedMilestone();

        this.setJson(board_json);
        this.setupListeners();

        $('#planning_indicator').hide();
        $('#planning_filter_title_input').prop('disabled', false);
    }

    setJson(board_json) {
        let fetchSwimlanes = (this.type !== board_json.type || this.swimlane_type !== board_json.swimlane_type || this.swimlane_identifier !== board_json.swimlane_identifier || this.swimlane_field_values.length !== board_json.swimlane_field_values.length);
        if (!fetchSwimlanes) {
            if (this.swimlane_field_values.length || board_json.swimlane_field_values.length) {
                let difference = this.swimlane_field_values
                    .filter(x => !board_json.swimlane_field_values.includes(x))
                    .concat(board_json.swimlane_field_values.filter(x => !this.swimlane_field_values.includes(x)));

                fetchSwimlanes = difference.length > 0;
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

        this.updateBackgroundColor();
        this.updateBoardClass();
        this.updateVisibleWhiteboard();

        if (fetchSwimlanes && this.columns.length) {
            this.fetchSwimlanes();
        } else {
            $('#whiteboard_indicator').hide();
        }
    }

    updateVisibleWhiteboard() {
        $('#onboarding-no-board-columns').addClass('hidden');
        $('#onboarding-no-milestones').addClass('hidden');
        $('#onboarding-no-active-sprint').addClass('hidden');
        $('#whiteboard').hide();
        if (!this.columns.length || (this.selected_milestone_id === 0 && this.type === BoardTypes.SCRUM)) {
            if (this.selected_milestone_id === 0 && this.type === BoardTypes.SCRUM) {
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
        const $container = $('#project_planning');
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
    }

    updateSelectedMilestone(trigger_reload = false) {
        const $selectedInput = $('input[name=selected_milestone]:checked');
        const previous_milestone_id = this.selected_milestone_id;
        this.selected_milestone_id = ($selectedInput.length) ? parseInt($selectedInput.val()) : 0;

        if (this.selected_milestone_id !== previous_milestone_id && trigger_reload) {
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

        $('#whiteboard_indicator').show();
        if (this.selected_milestone_id !== 0 || this.type !== BoardTypes.SCRUM) {
            Pachno.fetch(`${this.whiteboardUrl}&milestone_id=${this.selected_milestone_id}`, { method: 'GET' })
                .then((json) => this.setSwimlanes(json.swimlanes));
        } else {
            $('#whiteboard_indicator').hide();
        }
    }

    setSwimlanes(swimlanes) {
        const $whiteboard_indicator = $('#whiteboard_indicator');
        if (swimlanes.length) {
            this.swimlanes = swimlanes.map(json => new Swimlane(json));
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
        for (const swimlane of this.swimlanes) {
            if ($(`.swimlane[data-swimlane-identifier=${swimlane.identifier}]`).length) continue;

            const swimlane_html = `<div class="row swimlane empty" data-swimlane-identifier="${swimlane.identifier}"></div>`;
            const $swimlane = $(swimlane_html);
            if (this.swimlane_type !== SwimlaneTypes.NONE) {
                let header_name = '';
                if (swimlane.identifier_issue) {
                    const closed_class = (swimlane.identifier_issue.closed) ? 'closed' : '';
                    header_name = `<a class="issue-number" href="${swimlane.identifier_issue.href}">${swimlane.identifier_issue.issue_no}</a>`;
                    header_name += `<span class="name issue_header ${closed_class}">${swimlane.identifier_issue.title}</span>`;
                } else {
                    header_name = `<span class="name">${swimlane.name}</span>`;
                }
                const header_html = `<div class="swimlane-header"><div class="header">${header_name}</div>`;
                $swimlane.append(header_html);
            }
            $swimlane.append(`<div class="columns-container scroll-sync" id="${swimlane.identifier}-columns"><div class="columns"></div></div>`);
            $whiteboard_content.append($swimlane);
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
                if (this.swimlane_type === SwimlaneTypes.NONE) {
                    html += `
                            <div class="form-container">
                                <div class="row">
                                    <div class="form name">
                                        <div class="form-row">
                                            <span class="input invisible trigger-report-issue" data-status-ids="${status_ids}">
                                                <span class="placeholder">${UI.fa_image_tag('plus')}<span><?= __('Add card'); ?></span></span>
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
        for (const column of this.columns) {
            const isInColumn = (issue) => issue.status.id && column.status_ids.includes(issue.status.id);
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
                    if (issue.processed) continue;

                    $swimlaneContainer.removeClass('empty');
                    if (this.swimlane_type == SwimlaneTypes.NONE) {
                        $add_card_form.before(issue.element);
                    } else {
                        $swimlane.append(issue.element);
                    }
                    issue.processed = true;
                }
            }
            for (const status_id of column.status_ids) {
                $(`.column-count[data-status-id=${status_id}]`).html(num_issues[`status_${status_id}`]);
            }
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
            $whiteboard_indicator.hide();
        } catch (error) {
            console.trace(error);
            console.error(error);
        }
    };

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
                additional_params: '&milestone_id=' + parseInt(milestone_id) + '&board_id=' + this.id,
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

    addColumn(column, swimlanes) {
        this.columns.push(column);
        if (this.swimlanes === undefined) {
            this.setSwimlanes(swimlanes);
        } else {
            for (const swimlane of swimlanes) {
                const board_swimlane = this.swimlanes.find(lane => lane.identifier == swimlane.identifier);
                board_swimlane.addIssues(swimlane.issues);
            }
        }
        for (const column of this.columns) {
            for (const status_id of column.status_ids) {
                $(`#add_next_column_status_${status_id}`).attr('disabled', true);
                $(`label[for=add_next_column_status_${status_id}]`).addClass('disabled');
            }
        }
        this.updateWhiteboard();
    };

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

        const $filter_input = $('#planning_filter_title_input');
        $filter_input.on('keyup', debounce(this.filterInput, 250).bind(this));
        Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
            const json = data.json;
            switch (data.form) {
                case 'edit-agileboard-form':
                    board.setJson(json.board);
                    break;
                case 'add-first-column-form':
                case 'add-another-column-form':
                    const $container = $('#add-next-column-input-container');
                    $container.before(json.component);
                    $container.removeClass('toggle-card');
                    board.addColumn(json.column, json.swimlanes);
                    $('#add-another-column-form').trigger("reset");
                    $('#onboarding-no-board-columns').addClass('hidden');
                    break;
                case 'edit_milestone_form':
                    $('#selected_milestone_input').append(json.component);
                    board.updateSelectedMilestone(true);
                    break;
            }
        });
    }
}

export default Board;
window.Board = Board;
