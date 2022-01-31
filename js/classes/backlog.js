import $ from 'jquery';
import Sortable from 'sortablejs';
import Milestone, {Templates as MilestoneTemplates} from "@/classes/milestone";
import {BoardTypes, SwimlaneTypes} from "@/classes/board";
import UI from "@/helpers/ui";
import Pachno from "@/classes/pachno";
import {debounce} from "@/tools/tools";
import {Templates} from "@/classes/issue";
import Swimlane from "@/classes/swimlane";

class Backlog {
    constructor(board_json) {
        this.board_json = board_json;
        /**
         * @type {Milestone[]}
         */
        this.milestones = [];
        for (const milestone_json of this.board_json.milestones) {
            milestone_json.url = `${board_json.url}&mode=backlog&milestone_id=${milestone_json.id}`;
            milestone_json.mark_finished_url = `${milestone_json.mark_finished_url}&board_id=${board_json.id}`;
            this.milestones.push(new Milestone(milestone_json));
        }

        this.milestones.push(new Milestone({ id: 0, name: 'Backlog', url: `${board_json.url}&mode=backlog&milestone_id=0`, is_closed: false, visible_roadmap: true }));

        this.initializeMilestones();
//
//     Pachno.Project.Planning._initializeFilterSearch();
//
//     if ($('#epics-list')) {
//         Pachno.Helpers.fetch($('#epics-list').dataset.epicsUrl, {
//             method: 'GET',
//             success: {
//                 update: '#epics-list',
//                 callback: function (json) {
//                     var completed_milestones = $('.milestone-box.available.initialized');
//                     var multiplier = 100 / Pachno.Project.Planning.options.milestone_count;
//                     var pct = Math.floor((completed_milestones.length + 1) * multiplier);
//                     $('#planning_percentage_filler').css({width: pct + '%'});
//
//                     $('#epics_toggler_button').prop('disabled', false);
//                     Pachno.Project.Planning.initializeEpicDroptargets();
//                     $('body').on('click', '.epic', function (e) {
//                         Pachno.Project.Planning.toggleEpicFilter(this);
//                     });
//                 }
//             }
//         });
//     }
//
//     if ($('#builds-list')) {
//         Pachno.Helpers.fetch($('#builds-list').dataset.releasesUrl, {
//             method: 'GET',
//             success: {
//                 update: '#builds-list',
//                 callback: function (json) {
//                     Pachno.Project.Planning.initializeReleaseDroptargets();
//                     $('body').on('click', '.release', function (e) {
//                         Pachno.Project.Planning.toggleReleaseFilter(this);
//                     });
//                 }
//             }
//         });
//     }
// };
        this.updateBoardClass();
        this.setupListeners();
    }

    updateBoardClass() {
        const $container = $('#content_container');
        $container.removeClass('type-generic');
        $container.removeClass('type-kanban');
        $container.removeClass('type-scrum');
        switch (parseInt(this.board_json.type)) {
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

    boardType() {
        switch (parseInt(this.board_json.type)) {
            case BoardTypes.KANBAN:
                return 'kanban';
            case BoardTypes.SCRUM:
                return 'scrum';
            default:
            case BoardTypes.GENERIC:
                return 'generic';
        }
    }

    addIssue(issue_json) {
        const issue = Pachno.addIssue(issue_json, undefined, Templates.row);
        for (const milestone of this.milestones) {
            if (milestone.matches(issue)) {
                milestone.addIssue(issue);
                milestone.verifyIssues();
                break;
            }
        }
    }

    getMilestone(milestone_id) {
        return this.milestones.find(milestone => milestone.id == milestone_id);
    }

    initializeMilestone(milestone) {
        const $milestones_container = $('#milestones-list');
        const $backlog_container = $('#board-backlog-container');

        return new Promise((resolve, reject) => {
            const milestone_element = milestone.getHtmlElement(MilestoneTemplates.backlog, this.boardType());
            if (milestone.id == 0) {
                $backlog_container.append(milestone_element);
            } else {
                $milestones_container.append(milestone_element);
            }

            this.initializeMilestoneDragDropSorting(milestone);

            if (!milestone.is_closed) {
                milestone.fetchIssues()
                    .then(resolve)
                    .catch(reject);
            } else {
                resolve();
            }
        });
    }

    initializeMilestones() {
        const $milestones_container = $('#milestones-list');
        const milestone_promises = [];

        const sortMilestones = ($list) => {
            const data = {
                milestone_ids: []
            };
            $list.find('.milestone-box.draggable').each(function () {
                data.milestone_ids.push($(this).data('milestone-id'));
            });
            Pachno.fetch(this.board_json.sort_milestones_url, {
                method: 'POST',
                data
            });

        }

        for (const milestone of this.milestones) {
            milestone_promises.push(this.initializeMilestone(milestone));
        }

        Promise.all(milestone_promises)
            .then(() => {
                $('#planning_filter_title_input').prop('disabled', false);
                const milestones_sortable = Sortable.create($milestones_container[0], {
                    direction: 'vertical',
                    group: 'milestones',
                    onUpdate: function (event) { sortMilestones($(event.to)); },
                    draggable: '.milestone-box.draggable'
                });
            });
    }

    initializeMilestoneDragDropSorting (milestone) {
        const $milestone = $('#milestone_' + milestone.id);
        const milestone_issues = $milestone.find('.milestone-issues.jsortable');
        const sortIssues = ($list) => new Promise((resolve, reject) => {
            const url = milestone.url;
            let data = {
                issue_ids: []
            };
            $list.find('.milestone-issue').each(function () {
                const $issue = $(this);
                data.issue_ids.push($issue.data('issue-id'));
            });
            Pachno.fetch(url, {
                    method: 'POST',
                    data
                })
                .then(resolve)
                .catch(reject);
        });

        const milestone_issues_sortable = Sortable.create(milestone_issues[0], {
            direction: 'vertical',
            group: 'milestone-issues',
            onAdd: function (event) {
                const $list = $(event.to);
                $list.addClass('disabled');
                const $item = $(event.item);
                const issue_id = $item.data('issue-id');
                Pachno.trigger(Pachno.EVENTS.issue.triggerUpdate, {
                    issue_id,
                    field: 'milestone',
                    value: milestone.id
                });
                milestone.issues.push(Pachno.getIssue(issue_id));
                sortIssues($list)
                    .then(() => {
                        $list.removeClass('disabled');
                    });
            },
            onRemove: function (event) {
                const $item = $(event.item);
                const issue_id = $item.data('issue-id');
                milestone.issues = milestone.issues.filter(issue => issue.id != issue_id);
            },
            onUpdate: function (event) {
                const $list = $(event.to);
                $list.addClass('disabled');
                sortIssues($list)
                    .then(() => {
                        $list.removeClass('disabled');
                    });
            }
        });
    }

    setupListeners() {
        const backlog = this;
        const $body = $('body');
        $body.off('click', '.trigger-delete-milestone');
        $body.on('click', '.trigger-delete-milestone', function (event) {
            event.preventDefault();
            event.stopPropagation();
            const t = Pachno.T.agile.backlog[backlog.boardType()].milestone;

            UI.Dialog.show(t.delete_title, t.delete_message, {yes: {click: function() { Pachno.trigger(Pachno.EVENTS.agile.deleteMilestone, { url: $(this).data('url'), milestone_id: $(this).data('milestone-id') });}}, no: {click: Pachno.UI.Dialog.dismiss}});
        });

        $body.off('click', '.trigger-show-issues');
        $body.on('click', '.trigger-show-issues', function (event) {
            $('#milestone_' + $(this).val() + '_issues').toggleClass('collapsed');
        });

        $body.off('click', '.trigger-refresh-issues');
        $body.on('click', '.trigger-refresh-issues', function (event) {
            const milestone = backlog.getMilestone($(this.data('milestone-id')));
            milestone.clearCounts();
            milestone.fetchIssues();
        });

        $body.off('click', '.trigger-toggle-closed-issues');
        $body.on('click', '.trigger-toggle-closed-issues', function (event) {
            $('.milestone-issues').toggleClass('hide-closed');
        });

        const filterInput = function (event) {
            const value = $(event.target).val();
            if (value.length >= 3) {
                const matching = new RegExp(value, "i");
                $('.milestone-issue').each(function () {
                    const $issue_card = $(this);
                    if ($issue_card.find('.issue-title')[0].innerHTML.search(matching) !== -1) {
                        $issue_card.removeClass('filtered');
                    } else {
                        $issue_card.addClass('filtered');
                    }
                });
            } else {
                $('.milestone-issue').removeClass('filtered');
            }
        }

        const $filter_input = $('#planning_filter_title_input');
        $filter_input.on('keyup', debounce(filterInput, 250));
//         if (Pachno.ift_observers[fk])
//             clearTimeout(Pachno.ift_observers[fk]);
//         if ((pfti.val().length >= 3 || pfti.val().length == 0) && pfti.val() != pfti.data('last-value')) {
//             Pachno.ift_observers[fk] = setTimeout(function () {
//                 Pachno.Project.Planning.filterTitles(pfti.val(), whiteboard);
//                 pfti.data('last-value', pfti.val());
//             }, 500);
//         }
//     });

        Pachno.on(Pachno.EVENTS.agile.deleteMilestone, (_, data) => {
            const $existing_milestone = $(`[data-milestone][data-milestone-id="${data.milestone_id}"]`);

            if ($existing_milestone.length) {
                $existing_milestone.remove();
            }

            // if (!$('#agileboards').find('[data-agileboard]').length) {
            //     $('#onboarding-no-boards').removeClass('hidden');
            // }

            Pachno.UI.Dialog.dismiss();
            Pachno.fetch(data.url, { method: 'DELETE' });
        });

        Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
            const json = data.json;
            switch (data.form) {
                case 'edit-agileboard-form':
                    backlog.board_json = json.board;
                    backlog.updateBoardClass();
                    break;
                case 'report_issue_form':
                    backlog.addIssue(json.issue);
                    break;
                case 'mark_milestone_finished_form':
                    const $existing_milestone = $(`[data-milestone][data-milestone-id="${json.milestone.id}"]`);

                    if ($existing_milestone.length) {
                        $existing_milestone.remove();
                    }

                    if (json.unresolved_action == 'reassign') {
                        const milestone = backlog.getMilestone(json.new_milestone.id);
                        milestone.issues_count_open += parseInt(json.number_of_reassigned_issues);
                        milestone.fetchIssues();
                    } else if (json.unresolved_action == 'add_new') {
                        const milestone = new Milestone(json.new_milestone);
                        backlog.milestones.push(milestone);
                        backlog.initializeMilestone(milestone);
                    }
                    break;
                case 'edit_milestone_form':
                    const milestone = new Milestone(json.milestone);
                    backlog.milestones.push(milestone);
                    backlog.initializeMilestone(milestone);
                    break;
            }
        });

        Pachno.on(Pachno.EVENTS.issue.updateJsonComplete, (_, issue) => {
            for (const milestone of backlog.milestones) {
                milestone.addOrRemove(issue);
                milestone.verifyIssues();
            }

        });
    }
}

export default Backlog;
window.Backlog = Backlog;

/*
Pachno.Project.Planning._initializeFilterSearch = function(whiteboard) {
    Pachno.ift_observers = {};
    var pfti = $('#planning_filter_title_input');
    pfti.data('previous-value', '');
    var fk = 'pfti';
    if (whiteboard == undefined) whiteboard = false;
    pfti.on('keyup', function (event, element) {
        if (Pachno.ift_observers[fk])
            clearTimeout(Pachno.ift_observers[fk]);
        if ((pfti.val().length >= 3 || pfti.val().length == 0) && pfti.val() != pfti.data('last-value')) {
            Pachno.ift_observers[fk] = setTimeout(function () {
                Pachno.Project.Planning.filterTitles(pfti.val(), whiteboard);
                pfti.data('last-value', pfti.val());
            }, 500);
        }
    });
};

Pachno.Project.Planning.toggleMilestoneIssues = function(milestone_id) {
    var mi_issues = $('#milestone_'+milestone_id+'_issues');
    var mi = $('#milestone_'+milestone_id);
    mi.find('.toggle-issues').toggleClass('button-pressed');
    if (!mi.hasClass('initialized')) {
        mi.find('.toggle-issues').prop('disabled', true);
        mi_issues.removeClass('collapsed');
        Pachno.Project.Planning.getMilestoneIssues(mi);
    } else {
        $('#milestone_'+milestone_id+'_issues').toggleClass('collapsed');
    }
};

Pachno.Project.Planning.initialize = function (options) {
    Pachno.Project.Planning.options = options;

    Pachno.Project.Planning._initializeFilterSearch();

    if ($('#epics-list')) {
        Pachno.Helpers.fetch($('#epics-list').dataset.epicsUrl, {
            method: 'GET',
            success: {
                update: '#epics-list',
                callback: function (json) {
                    var completed_milestones = $('.milestone-box.available.initialized');
                    var multiplier = 100 / Pachno.Project.Planning.options.milestone_count;
                    var pct = Math.floor((completed_milestones.length + 1) * multiplier);
                    $('#planning_percentage_filler').css({width: pct + '%'});

                    $('#epics_toggler_button').prop('disabled', false);
                    Pachno.Project.Planning.initializeEpicDroptargets();
                    $('body').on('click', '.epic', function (e) {
                        Pachno.Project.Planning.toggleEpicFilter(this);
                    });
                }
            }
        });
    }

    if ($('#builds-list')) {
        Pachno.Helpers.fetch($('#builds-list').dataset.releasesUrl, {
            method: 'GET',
            success: {
                update: '#builds-list',
                callback: function (json) {
                    Pachno.Project.Planning.initializeReleaseDroptargets();
                    $('body').on('click', '.release', function (e) {
                        Pachno.Project.Planning.toggleReleaseFilter(this);
                    });
                }
            }
        });
    }
};

Pachno.Project.Planning.filterTitles = function (title, whiteboard) {
    $('#planning_indicator').show();
    if (title !== '') {
        var matching = new RegExp(title, "i");
        $('#project_planning').addClass('issue_title_filtered');
        $(whiteboard ? '.whiteboard-issue' : '.milestone-issue').each(function (issue) {
            if (whiteboard) {
                if (issue.find('.issue_header').innerHTML.search(matching) !== -1) {
                    issue.addClass('title_unfiltered');
                } else {
                    issue.removeClass('title_unfiltered');
                }
            }
            else {
                if (issue.find('.issue_link').find('a').innerHTML.search(matching) !== -1) {
                    issue.addClass('title_unfiltered');
                } else {
                    issue.removeClass('title_unfiltered');
                }
            }
        });
    } else {
        $('#project_planning').removeClass('issue_title_filtered');
        $(whiteboard ? '.whiteboard-issue' : '.milestone-issue').each(function (issue) {
            issue.removeClass('title_unfiltered');
        });
    }
    $('#planning_indicator').hide();
};

Pachno.Project.Planning.insertIntoMilestone = function (milestone_id, content, recalculate) {
    var milestone_list = $('#milestone_' + milestone_id + '_issues');
    var $milestone_list_container = milestone_list.parents('.milestone-issues-container');
    $milestone_list_container.removeClass('empty');
    $('#milestone_' + milestone_id + '_unassigned').hide();
    if (milestone_id == 0) {
        milestone_list.append(content);
    } else {
        milestone_list.prepend(content);
    }
    if (recalculate == 'all') {
        Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails();
    } else {
        Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails(milestone_list);
    }
    Pachno.Project.Planning.calculateNewBacklogMilestoneDetails();
    if (milestone_id != 0) {
        setTimeout(Pachno.Project.Planning.sortMilestoneIssues({target: 'milestone_' + milestone_id + '_issues'}), 250);
    }
};

Pachno.Project.Planning.retrieveIssue = function (issue_id, url, existing_element) {
    Pachno.Helpers.fetch(url, {
        params: 'issue_id=' + issue_id,
        method: 'GET',
        loading: {indicator: (!$(existing_element)) ? 'retrieve_indicator' : 'issue_' + issue_id + '_indicator'},
        success: {
            callback: function (json) {
                if (json.deleted == '1') {
                    if ($(existing_element)) $(existing_element).parents('.milestone-issue').remove();
                }
                else if (json.epic) {
                    if (!$(existing_element)) {
                        $('#add_epic_container').prepend(json.component);
                        setTimeout(Pachno.Project.Planning.initializeEpicDroptargets, 250);
                    } else {
                        $(existing_element).parents('.milestone-issue').replace(json.component);
                    }
                } else {
                    if (!$(existing_element)) {
                        if (json.issue_details.milestone && json.issue_details.milestone.id) {
                            if ($('#milestone_'+json.issue_details.milestone.id).hasClass('initialized')) {
                                Pachno.Project.Planning.insertIntoMilestone(json.issue_details.milestone.id, json.component);
                            }
                        } else {
                            Pachno.Project.Planning.insertIntoMilestone(0, json.component);
                        }
                    } else {
                        var json_milestone_id = (json.issue_details.milestone && json.issue_details.milestone.id != undefined) ? parseInt(json.issue_details.milestone.id) : 0;
                        if (parseInt($(existing_element).parents('.milestone-box').data('milestone-id')) == json_milestone_id) {
                            $(existing_element).parents('.milestone-issue').replace(json.component);
                            Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails($('#milestone_' + json_milestone_id + '_issues'));
                            Pachno.Project.Planning.calculateNewBacklogMilestoneDetails();
                        } else {
                            $(existing_element).parents('.milestone-issue').remove();
                            Pachno.Project.Planning.insertIntoMilestone(json_milestone_id, json.component, 'all');
                        }
                    }
                }
                if (json.issue_details.milestone && json.issue_details.milestone.id && json.milestone_percent_complete != null) {
                    $('#milestone_' + json.issue_details.milestone.id + '_percentage_filler').css({width: json.milestone_percent_complete + '%'});
                }
                Pachno.Project.Planning.filterTitles($('#planning_filter_title_input').val());
            }
        }
    });
};

Pachno.Core.Pollers.Callbacks.planningPoller = function () {
    var pc = $('#project_planning');
    if (!Pachno.Core.Pollers.Locks.planningpoller && pc) {
        Pachno.Core.Pollers.Locks.planningpoller = true;
        var data_url = pc.dataset.pollUrl;
        var retrieve_url = pc.dataset.retrieveIssueUrl;
        var last_refreshed = pc.dataset.lastRefreshed;
        Pachno.Helpers.fetch(data_url, {
            method: 'GET',
            params: 'last_refreshed=' + last_refreshed,
            success: {
                callback: function (json) {
                    pc.dataset.lastRefreshed = get_current_timestamp();
                    for (var i in json.ids) {
                        if (json.ids.hasOwnProperty(i)) {
                            var issue_details = json.ids[i];
                            var issue_element = $('#issue_' + issue_details.issue_id);
                            if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
                                Pachno.Project.Planning.retrieveIssue(issue_details.issue_id, retrieve_url, 'issue_' + issue_details.issue_id);
                            }
                        }
                    }
                    for (var i in json.backlog_ids) {
                        if (json.backlog_ids.hasOwnProperty(i)) {
                            var issue_details = json.backlog_ids[i];
                            var issue_element = $('#issue_' + issue_details.issue_id);
                            if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
                                Pachno.Project.Planning.retrieveIssue(issue_details.issue_id, retrieve_url, 'issue_' + issue_details.issue_id);
                            }
                        }
                    }
                    for (var i in json.epic_ids) {
                        if (json.epic_ids.hasOwnProperty(i)) {
                            var issue_details = json.epic_ids[i];
                            var issue_element = $('#epic_' + issue_details.issue_id);
                            if (!issue_element || parseInt(issue_element.dataset.lastUpdated) < parseInt(issue_details.last_updated)) {
                                Pachno.Project.Planning.retrieveIssue(issue_details.issue_id, retrieve_url, 'epic_' + issue_details.issue_id);
                            }
                        }
                    }
                    Pachno.Core.Pollers.Locks.planningpoller = false;
                }
            },
            exception: {
                callback: function () {
                    Pachno.Core.Pollers.Locks.planningpoller = false;
                }
            }
        });
    }
};

Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails = function (list) {
    var list_issues = $(list).find('.issue-container').not('.child_issue');
    var closed_issues = $(list).find('.issue-container.issue_closed').not('.child_issue');
    var visible_issues = list_issues.filter(':visible');
    var sum_estimated_points = 0;
    var sum_estimated_hours = 0;
    var sum_estimated_minutes = 0;
    var sum_spent_points = 0;
    var sum_spent_hours = 0;
    var sum_spent_minutes = 0;
    visible_issues.each(function (index) {
        var elm = $(this);
        if (!elm.hasClass('child_issue')) {
            if (elm.dataset.estimatedPoints !== undefined)
                sum_estimated_points += parseInt(elm.dataset.estimatedPoints);
            if (elm.dataset.estimatedHours !== undefined)
                sum_estimated_hours += parseInt(elm.dataset.estimatedHours);
            if (elm.dataset.estimatedMinutes !== undefined)
                sum_estimated_minutes += parseInt(elm.dataset.estimatedMinutes);
            if (elm.dataset.spentPoints !== undefined)
                sum_spent_points += parseInt(elm.dataset.spentPoints);
            if (elm.dataset.spentHours !== undefined)
                sum_spent_hours += parseInt(elm.dataset.spentHours);
            if (elm.dataset.spentMinutes !== undefined)
                sum_spent_minutes += parseInt(elm.dataset.spentMinutes);
        }
    });
    var num_visible_issues = visible_issues.length;
    var milestone_id = $(list).parents('.milestone-box').data('milestone-id');

    if (num_visible_issues === 0) {
        if (list_issues.length > 0) {
            $('#milestone_' + milestone_id + '_unassigned').hide();
            $('#milestone_' + milestone_id + '_unassigned_filtered').show();
        } else {
            $('#milestone_' + milestone_id + '_unassigned').show();
            $('#milestone_' + milestone_id + '_unassigned_filtered').hide();
        }
        $(list).parents('.milestone-issues-container').addClass('empty');
    } else {
        $('#milestone_' + milestone_id + '_unassigned').hide();
        $('#milestone_' + milestone_id + '_unassigned_filtered').hide();
        $(list).parents('.milestone-issues-container').removeClass('empty');
    }
    if (num_visible_issues !== list_issues.length && milestone_id != '0') {
        $('#milestone_' + milestone_id + '_issues_count').html(num_visible_issues + ' (' + list_issues.length + ')');
    } else {
        $('#milestone_' + milestone_id + '_issues_count').html(num_visible_issues);
    }
    sum_spent_hours += Math.floor(sum_spent_minutes / 60);
    sum_estimated_hours += Math.floor(sum_estimated_minutes / 60);
    sum_spent_minutes = sum_spent_minutes % 60;
    sum_estimated_minutes = sum_estimated_minutes % 60;
    $('#milestone_' + milestone_id + '_points_count').html(sum_spent_points + ' / ' + sum_estimated_points);
    if (sum_spent_minutes != 0) {
        sum_spent_hours += ':' + ((sum_spent_minutes.toString().length == 1) ? '0' : '') + sum_spent_minutes;
    }
    if (sum_estimated_minutes != 0) {
        sum_estimated_hours += ':' + ((sum_estimated_minutes.toString().length == 1) ? '0' : '') + sum_estimated_minutes;
    }
    $('#milestone_' + milestone_id + '_hours_count').html(sum_spent_hours + ' / ' + sum_estimated_hours);
};

Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails = function () {
    $('.milestone-box.initialized').find('.milestone-issues').each(function (index) {
        var was_collapsed = $(this).hasClass('collapsed');
        $(this).removeClass('collapsed');
        Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails(this);
        if (was_collapsed && parseInt($(this).parents('.milestone-box').data('milestone-id')) !== 0) $(this).addClass('collapsed');
    });
};

Pachno.Project.Planning.calculateNewBacklogMilestoneDetails = function (event, ui) {
    if (event === undefined || $(ui.item).hasClass('new_milestone_marker')) {
        var nbmm = (event === undefined) ? $('#new_backlog_milestone_marker') : $(ui.placeholder[0]);
        var num_issues = 0;
        var sum_points = 0;
        var sum_hours = 0;
        var sum_minutes = 0;
        var include_closed = $('#milestones-list').hasClass('show_closed');
        $('.milestone-issue').removeClass('included');
        nbmm.parents('.milestone-issues').children().each(function (elm) {
            elm.addClass('included');
            if (!(elm.hasClass('new_milestone_marker') && !elm.hasClass('ui-sortable-helper')) && !elm.hasClass('ui-element-placeholder')) {
                if (!elm.hasClass('new_milestone_marker')) {
                    if (include_closed || !elm.hasClass('issue_closed'))
                        num_issues++;
                    if (!elm.hasClass('child_issue')) {
                        if (elm.find('.issue-container').dataset.estimatedPoints !== undefined)
                            sum_points += parseInt(elm.find('.issue-container').dataset.estimatedPoints);
                        if (elm.find('.issue-container').dataset.estimatedHours !== undefined)
                            sum_hours += parseInt(elm.find('.issue-container').dataset.estimatedHours);
                        if (elm.find('.issue-container').dataset.estimatedMinutes !== undefined)
                            sum_minutes += parseInt(elm.find('.issue-container').dataset.estimatedMinutes);
                    }
                }
            } else {
                throw $break;
            }
        });
        sum_hours += Math.floor(sum_minutes / 60);
        sum_minutes = sum_minutes % 60;
        $('#new_backlog_milestone_issues_count').html(num_issues);
        $('#new_backlog_milestone_points_count').html(sum_points);
        if (sum_minutes != 0) {
            sum_hours += ':' + ((sum_minutes.toString().length == 1) ? '0' : '') + sum_minutes;
        }
        $('#new_backlog_milestone_hours_count').html(sum_hours);
    }
};

Pachno.Project.Planning.sortMilestones = function (event, ui) {
    var list = $(event.target);
    var url = list.data('sort-url');
    var items = '';
    list.children().each(function (milestone, index) {
        if (milestone.data('milestone-id') !== undefined) {
            items += '&milestone_ids['+index+']=' + milestone.data('milestone-id');
        }
    });
    Pachno.Helpers.fetch(url, {
        method: 'POST',
        data: items,
        loading: {indicator: '#planning_indicator'}
    });
};

Pachno.Project.Planning.doSortMilestoneIssues = function (list) {
    var url = list.parents('.milestone-box').data('issues-url');
    var items = '';
    list.children().each(function (issue) {
        if (issue.data('issue-id') !== undefined) {
            items += '&issue_ids[]=' + issue.data('issue-id');
        }
    });
    Pachno.Helpers.fetch(url, {
        method: 'POST',
        data: items,
        loading: {indicator: list.parents('.milestone-box').find('.planning_indicator')}
    });
};

Pachno.Project.Planning.sortMilestoneIssues = function (event, ui) {
    var list = $(event.target);
    var issue = $(ui.item[0]);
    if (issue.dataset.sortCancel) {
        issue.dataset.sortCancel = null;
        $(this).sortable("cancel");
    } else {
        if (ui !== undefined && ui.item.hasClass('new_milestone_marker')) {
            Pachno.Project.Planning.calculateNewBacklogMilestoneDetails();
        } else {
            Pachno.Project.Planning.doSortMilestoneIssues(list);
        }
    }
};

Pachno.Project.Planning.moveIssue = function (event, ui) {
    var issue = $(ui.item[0]);
    if (issue.dataset.sortCancel) {
        issue.dataset.sortCancel = null;
        $(this).sortable("cancel");
    } else {
        if (issue.hasClass('milestone-issue')) {
            var list = $(event.target);
            var url = list.parents('.milestone-box').data('assign-issue-url');
            var original_list = $(ui.sender[0]);
            Pachno.Helpers.fetch(url, {
                data: 'issue_id=' + issue.data('issue-id'),
                loading: {indicator: list.parents('.milestone-box').find('.planning_indicator')},
                complete: {
                    callback: function (json) {
                        if (list.parents('.milestone-box').hasClass('initialized')) {
                            issue.find('.issue-container').dataset.lastUpdated = get_current_timestamp();
                            Pachno.Project.Planning.doSortMilestoneIssues(list);
                            Pachno.Core.Pollers.Callbacks.planningPoller();
                            Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails(list);
                            Pachno.Project.Planning.calculateMilestoneIssueVisibilityDetails(original_list);
                        } else {
                            issue.remove();
                            var milestone_id = list.parents('.milestone-box').data('milestone-id');
                            $('#milestone_' + milestone_id + '_issues_count').html(json.issues);
                            $('#milestone_' + milestone_id + '_points_count').html(json.points);
                            $('#milestone_' + milestone_id + '_hours_count').html(json.hours);
                        }
                    }
                }
            });
        }
    }
};

Pachno.Project.Planning.initializeMilestoneDragDropSorting = function (milestone) {
    var milestone_issues = $(milestone).find('.milestone-issues.jsortable');
    if (milestone_issues.hasClass('ui-sortable')) {
        milestone_issues.sortable('destroy');
    }
    milestone_issues.sortable({
        handle: '.draggable',
        connectWith: '.jsortable.intersortable',
        update: Pachno.Project.Planning.sortMilestoneIssues,
        receive: Pachno.Project.Planning.moveIssue,
        sort: Pachno.Project.Planning.calculateNewBacklogMilestoneDetails,
        start: function (event) {
            $('.milestone-issues-container').each(function (index) {
                $(this).addClass('issue-drop-target');
            })
        },
        stop: function (event) {
            $('.milestone-issues-container').each(function (index) {
                $(this).removeClass('issue-drop-target');
            })
        },
        over: function (event) { $(this).addClass('drop-hover'); },
        out: function (event) { $(this).removeClass('drop-hover'); },
        tolerance: 'pointer',
        helper: function(event, ui) {
            var $clone =  $(ui).clone();
            $clone .css('position','absolute');
            return $clone.get(0);
        }
    });
};

Pachno.Project.Planning.initializeReleaseDroptargets = function () {
    $('#builds-list .release').not('ui-droppable').droppable({
        drop: Pachno.Project.Planning.assignRelease,
        accept: '.milestone-issue',
        tolerance: 'pointer',
        hoverClass: 'drop-hover'
    });
};

Pachno.Project.Planning.initializeEpicDroptargets = function () {
    $('#epics-list .epic').not('.ui-droppable').droppable({
        drop: Pachno.Project.Planning.assignEpic,
        accept: '.milestone-issue',
        tolerance: 'pointer',
        hoverClass: 'drop-hover'
    });
};

Pachno.Project.Planning.toggleReleaseFilter = function (release) {
    if (release !== 'auto' && $('#epics-list') && $('#epics-list').hasClass('filtered'))
        Pachno.Project.Planning.toggleEpicFilter('auto');
    if ($('#builds-list').hasClass('filtered') && (release == 'auto' || ($(release) && $(release).hasClass('selected')))) {
        $('#builds-list').removeClass('filtered');
        $('#builds-list').children().each(function (rel) {
            rel.removeClass('selected');
        });
        $('.milestone-issue').each(function (issue) {
            issue.removeClass('filtered');
        });
    } else if ($(release)) {
        $('#builds-list').addClass('filtered');
        $('#builds-list').children().each(function (rel) {
            rel.removeClass('selected');
        });
        $(release).addClass('selected');
        var release_id = $(release).data('release-id');
        $('.milestone-issue').each(function (issue) {
            (issue.data('release-' + release_id) === undefined) ? issue.addClass('filtered') : issue.removeClass('filtered');
        });
    }

    Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails();
};

Pachno.Project.Planning.toggleEpicFilter = function (epic) {
    if (epic !== 'auto' && $('#builds-list') && $('#builds-list').hasClass('filtered'))
        Pachno.Project.Planning.toggleReleaseFilter('auto');
    if ($('#epics-list').hasClass('filtered') && (epic == 'auto' || ($(epic) && $(epic).hasClass('selected')))) {
        $('#epics-list').removeClass('filtered');
        $('#epics-list').children().each(function (ep) {
            ep.removeClass('selected');
        });
        $('.milestone-issue').each(function (issue) {
            issue.removeClass('filtered');
        });
    } else if ($(epic)) {
        $('#epics-list').addClass('filtered');
        $('#epics-list').children().each(function (ep) {
            ep.removeClass('selected');
        });
        $(epic).addClass('selected');
        var epic_id = $(epic).data('issue-id');
        $('.milestone-issue').each(function (issue) {
            (issue.data('parent-' + epic_id) === undefined) ? issue.addClass('filtered') : issue.removeClass('filtered');
        });
    }

    Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails();
};

Pachno.Project.Planning.toggleClosedIssues = function () {
    $('#milestones-list').toggleClass('show_closed');
    Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails();
    Pachno.Project.Planning.calculateNewBacklogMilestoneDetails();
    Pachno.Main.Profile.clearPopupsAndButtons();
};

Pachno.Project.Planning.assignRelease = function (event, ui) {
    var issue = $(ui.draggable[0]);
    issue.data('sort-cancel', true);
    if (issue.hasClass('milestone-issue')) {
        var release = $(event.target);
        var release_id = $(event.target).data('release-id');
        var url = release.data('assign-issue-url');
        Pachno.Helpers.fetch(url, {
            data: 'issue_id=' + issue.data('issue-id'),
            loading: {indicator: release.find('.planning_indicator')},
            complete: {
                callback: function (json) {
                    $('#release_' + release_id + '_percentage_filler').css({width: json.closed_pct + '%'});
                    Pachno.Core.Pollers.Callbacks.planningPoller();
                    issue.data('release-' + release_id, true);
                }
            }
        });
    }
};

Pachno.Project.Planning.updateNewMilestoneIssues = function () {
    var num_issues = $('.milestone-issue.included').length;
    $('#milestone_include_num_issues').html(num_issues);
    $('#milestone_include_issues').show();
    $('#include_selected_issues').value(1);
};

Pachno.Project.Planning.addEpic = function (form) {
    var url = form.action;
    Pachno.Helpers.fetch(url, {
        form: form,
        loading: {indicator: '#new_epic_indicator'},
        success: {
            callback: function (json) {
                $(form).reset();
                $(form).parents('li').removeClass('selected');
                Pachno.Core.Pollers.Callbacks.planningPoller();
            }
        }
    });
};

Pachno.Project.Planning.assignEpic = function (event, ui) {
    var issue = $(ui.draggable[0]);
    issue.data('sort-cancel', true);
    if (issue.hasClass('milestone-issue')) {
        var epic = $(event.target);
        var epic_id = $(event.target).data('issue-id');
        var url = epic.data('assign-issue-url');
        Pachno.Helpers.fetch(url, {
            data: 'issue_id=' + issue.data('issue-id'),
            loading: {indicator: epic.find('.planning_indicator')},
            complete: {
                callback: function (json) {
                    $('#epic_' + epic_id + '_percentage_filler').css({width: json.closed_pct + '%'});
                    $('#epic_' + epic_id + '_estimate').html(json.estimate);
                    $('#epic_' + epic_id + '_child_issues_count').html(json.num_child_issues);
                    issue.data('parent-' + epic_id, true);
                    Pachno.Core.Pollers.Callbacks.planningPoller();
                }
            }
        });
    }
};

Pachno.Project.Planning.destroyMilestoneDropSorting = function (milestone) {
    if (milestone === undefined) {
        $('.milestone-issues.ui-sortable').sortable('destroy');
    } else {
        $(milestone).find('.milestone-issues.ui-sortable').sortable('destroy');
    }
};

Pachno.Project.Planning.getMilestoneIssues = function (milestone) {
    if (milestone.hasClass('initialized')) {
        return Promise.resolve();
    }

    let updateMilestoneIssuesContent = function (response) {
        $('#milestone_' + milestone_id + '_issues').html(response.content);
        return response;
    };

    let ti_button = milestone.find('.toggle-issues');

    if (ti_button) {
        ti_button.addClass('disabled');
        ti_button.addClass('submitting');
    }

    var milestone_id = milestone.data('milestone-id');

    return new Promise(function (resolve, reject) {
        fetch(milestone.data('issues-url'))
            .then((_) => _.json())
            .then(updateMilestoneIssuesContent)
            .then(function (response) {
                milestone.addClass('initialized');

                if (Pachno.Project.Planning.options.dragdrop) {
                    Pachno.Project.Planning.initializeMilestoneDragDropSorting(milestone);
                }

                if (milestone.hasClass('available')) {
                    var completed_milestones = $('.milestone-box.available.initialized');
                    var multiplier = 100 / Pachno.Project.Planning.options.milestone_count;
                    var pct = Math.floor(completed_milestones.length * multiplier);
                    $('#planning_percentage_filler').css({width: pct + '%'});

                    if (completed_milestones.length == (Pachno.Project.Planning.options.milestone_count - 1)) {
                        $('#planning_loading_progress_indicator').hide();
                        if (!Pachno.Core.Pollers.planningpoller)
                            Pachno.Core.Pollers.planningpoller = new PeriodicalExecuter(Pachno.Core.Pollers.Callbacks.planningPoller, 15);

                        $('#planning_indicator').hide();
                        $('#planning_filter_title_input').prop('disabled', false);
                    }
                }

                if (! milestone.find('.planning_indicator').hidden) milestone.find('.planning_indicator').hide();
            })
            .then(Pachno.Project.Planning.calculateAllMilestonesVisibilityDetails)
            .then(function () {
                if (ti_button) {
                    ti_button.removeClass('disabled');
                    ti_button.removeClass('submitting');
                }

                resolve();
            })
            .catch(function (error) {
                milestone.addClass('initialized');
                milestone.find('.milestone_error_issues').each(Element.show);

                reject(error);
            });
    });
};
*/