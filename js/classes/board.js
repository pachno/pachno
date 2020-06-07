import {debounce} from "../tools/tools";

class Board {
    constructor(options) {
        const $whiteboard_container = $('#planning_whiteboard');
        const $whiteboard = $('#whiteboard');
        const $milestone_input = $('#selected_milestone_input');
        this.whiteboardUrl = $whiteboard_container.data('whiteboard-url');
        // this.columnUrl = $whiteboard_container.data('column-url');

        if ($whiteboard.length) {
            this.retrieveWhiteboard($whiteboard);
        } else {
            $('#whiteboard_indicator').hide();
        }

        this.setupListeners();

        const milestone_id = $milestone_input.data('selected-value');
        if (milestone_id) {
            const board_id = $milestone_input.data('selected-board-value');
            this.retrieveMilestoneStatus(board_id, milestone_id);
        }

        $('#planning_indicator').hide();
        $('#planning_filter_title_input').prop('disabled', false);
    }

    retrieveWhiteboard($whiteboard) {
        $whiteboard.removeClass('initialized');
        const $milestone_input = $('#selected_milestone_input');
        const milestone_id = ($milestone_input.data('selected-value')) ? parseInt($milestone_input.data('selected-value')) : 0;

        Pachno.fetch(this.whiteboardUrl, {
            additional_params: '&milestone_id=' + milestone_id,
            method: 'GET',
            loading: {
                indicator: '#whiteboard_indicator',
                callback: function() {
                    $whiteboard.find('.thead .column_count.primary').each(function (cc) {
                        cc.html('-');
                    });
                    $whiteboard.data('milestone-id', milestone_id);
                }
            },
            success: {
                callback: function(json) {
                    if (json.swimlanes) {
                        $whiteboard.removeClass('no-swimlanes');
                        $whiteboard.addClass('swimlanes');
                    }
                    else {
                        $whiteboard.removeClass('swimlanes');
                        $whiteboard.addClass('no-swimlanes');
                    }
                    $whiteboard.addClass('initialized');
                    $whiteboard.find('.tbody').remove();
                    $('#whiteboard-headers').append(json.component);
                    setTimeout(function () {
                        // debugger;
                        // Pachno.Project.Planning.Whiteboard.calculateColumnCounts();
                        // Pachno.Project.Planning.Whiteboard.calculateSwimlaneCounts();
                        // Pachno.Project.Planning.Whiteboard.initializeDragDrop();
                    }, 250);
                }
            }
        });
    };

    filterInput(event) {
        // debugger;
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

    retrieveMilestoneStatus(board_id, milestone_id) {
        const $milestone_input = $('#selected_milestone_input');
        Pachno.fetch($milestone_input.data('status-url'), {
            additional_params: '&milestone_id=' + parseInt(milestone_id) + '&board_id=' + parseInt(board_id),
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

    toggleEditMode() {
        $('#project_planning').toggleClass('edit-mode');
        const $onboarding = $('#onboarding-no-board-columns');
        if ($onboarding) {
            $onboarding.hide();
        }
    }

    addColumn() {
        Pachno.fetch(this.columnUrl, {
            loading: {
                indicator: '#planning_indicator'
            },
            method: 'POST',
            success: {
                callback: function(json) {
                    $('#planning_whiteboard_columns_form_row').append(json.component);
                    // Pachno.Project.Planning.Whiteboard.setSortOrder();
                }
            }
        });
    };

    setupListeners() {
        const board = this;
        const $body = $('body');
        $body.on('click', '#selected_milestone_input li', function () {
            const milestone_id = $(this).data('input-value');
            const board_id = $(this).data('board-value');
            board.retrieveMilestoneStatus(board_id, milestone_id);
        });
        $body.on('click', '.trigger-whiteboard-edit-mode', this.toggleEditMode);
        $body.on('click', '.trigger-whiteboard-add-column', (event) => { event.preventDefault(); board.addColumn(); });
        // $(window).on('hashchange', Pachno.Project.Planning.Whiteboard.checkNav);

        const $filter_input = $('#planning_filter_title_input');
        $filter_input.on('keyup', debounce(this.filterInput, 250).bind(this));

        $('#main_container').addClass('shaded');
    }
}

export default Board;
window.Board = Board;
