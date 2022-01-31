import UI from "../helpers/ui";
import Pachno from "./pachno";
import $ from "jquery";
import {Templates as IssueTemplates} from "./issue";
const milestoneRoadmapTemplate = require('@/templates/milestone/roadmap.njk');
const milestoneBacklogTemplate = require('@/templates/milestone/backlog.njk');
const issuePlaceholderTemplate = require('@/templates/issue/placeholder.njk');

export const Templates = {
    roadmap: 'roadmap',
    backlog: 'backlog'
}

class Milestone {
    constructor(json) {
        this.id = json.id;
        this.is_closed = json.closed == 1;
        this.is_sprint = json.is_sprint;
        this.name = json.name;
        this.starting_date = json.starting_date;
        this.scheduled_date = json.scheduled_date;
        this.percent_complete = json.percent_complete;
        this.issues_count = json.issues_count;
        this.issues_count_open = json.issues_count_open;
        this.issues_count_closed = json.issues_count_closed;
        this.url = json.url;
        this.backdrop_url = json.backdrop_url;
        this.board_url = json.board_url;
        this.mark_finished_url = json.mark_finished_url;
        this.visible_in_roadmap = json.visible_roadmap;
        this.reached = json.reached;
        this.points = json.points;
        this.hours = json.hours;
        this.hours_spent_formatted = json.hours_spent_formatted;
        this.hours_estimated_formatted = json.hours_estimated_formatted;
        this.can_edit = json.can_edit;

        this.template = undefined;

        /**
         * @type {Issue[]}
         */
        this.issues = [];
        this.element = undefined;
    }

    createHtmlElement(board_type) {
        let html = '';
        const options = { milestone: this, UI, T: Pachno.T, board_type };

        switch (this.template) {
            case Templates.roadmap:
                html = milestoneRoadmapTemplate(options);
                break;
            case Templates.backlog:
                html = milestoneBacklogTemplate(options);

        }

        return $(html);
    }

    addIssues(issues) {
        let issue_template;
        switch (this.template) {
            case Templates.roadmap:
                issue_template = IssueTemplates.card;
                break;
            case Templates.backlog:
                issue_template = IssueTemplates.row;
                break;
        }
        for (const issue_json of issues) {
            this.addIssue(Pachno.addIssue(issue_json, undefined, issue_template));
        }
    }

    addIssue(issue) {
        issue.processed = false;
        this.issues.push(issue);
    }

    has(issue) {
        return this.issues.some(existing_issue => existing_issue.id == issue.id);
    }

    matches(issue) {
        const milestone_id = (issue.milestone?.id) ? parseInt(issue.milestone.id) : 0;
        return milestone_id == this.id;
    }

    addOrRemove(issue) {
        if (!this.matches(issue)) {
            if (this.has(issue)) {
                this.issues = this.issues.filter(existing_issue => existing_issue.id != issue.id);
                issue.processed = false;
            }
        } else if (!this.has(issue) && this.matches(issue)) {
            issue.processed = false;
            this.issues.push(issue);
        }
    }

    addPlaceholderIssues() {
        const $milestone_issues = this.element.find('.milestone-issues');
        const placeholderTemplate = issuePlaceholderTemplate();
        $milestone_issues.html('');

        for (let cc = 1; cc <= this.issues_count_open; cc += 1) {
            $milestone_issues.append($(placeholderTemplate));
        }
    }

    fetchIssues() {
        return new Promise((resolve, reject) => {
            const $milestone_card = this.element.find('.milestone-card');
            if ($milestone_card.length) {
                $milestone_card.addClass('loading');
            }

            this.issues = [];
            this.addPlaceholderIssues();

            Pachno.fetch(this.url, { method: 'GET' })
                .then((json) => {
                    this.addIssues(json.milestone.issues);
                    if ($milestone_card.length) {
                        $milestone_card.removeClass('loading');
                    }
                    this.verifyIssues();
                    resolve();
                }).catch(reject);
        });
    }

    clearCounts() {
        $('#milestone_' + this.id + '_points_count').html('-');
        $('#milestone_' + this.id + '_issues_count').html('-');
        $('#milestone_' + this.id + '_hours_count').html('-');
    }

    updateCounts() {
        const milestone_id = this.id;
        let sums = {
            issues: {
                open: 0,
                closed: 0
            },
            estimates: {
                points: 0,
                hours: 0,
                minutes: 0
            },
            spent: {
                points: 0,
                hours: 0,
                minutes: 0
            }
        };

        for (const issue of this.issues) {
            sums.estimates.points += issue.estimated_time.values.points > 0;
            sums.estimates.hours += issue.estimated_time.values.hours > 0;
            sums.estimates.minutes += issue.estimated_time.values.minutes > 0;
            sums.spent.points += issue.spent_time.values.points > 0;
            sums.spent.hours += issue.spent_time.values.hours > 0;
            sums.spent.minutes += issue.spent_time.values.minutes > 0;
            if (issue.closed) {
                sums.issues.closed += 1;
            } else {
                sums.issues.open += 1;
            }
        }

        if (sums.issues.closed > 0) {
            $('#milestone_' + milestone_id + '_issues_count').html(sums.issues.open + ' (' + sums.issues.closed + ')');
        } else {
            $('#milestone_' + milestone_id + '_issues_count').html(sums.issues.open);
        }

        if (sums.spent.points + sums.estimates.points > 0) {
            $('#milestone_' + milestone_id + '_points_count').html(sums.spent.points + ' / ' + sums.estimates.points);
        } else {
            $('#milestone_' + milestone_id + '_points_count').html(0);
        }

        if (sums.spent.hours + sums.spent.minutes + sums.estimates.hours + sums.estimates.minutes > 0) {
            let spent_hours_string = '' + sums.spent.hours;
            let estimated_hours_string = '' + sums.estimates.hours;
            if (sums.spent.minutes > 0) {
                spent_hours_string += ':' + ((sums.spent.minutes < 10) ? '0' : '') + sums.spent.minutes;
            }
            if (sums.estimates.minutes > 0) {
                estimated_hours_string += ':' + ((sums.estimates.minutes < 10) ? '0' : '') + sums.estimates.minutes;
            }
            $('#milestone_' + milestone_id + '_hours_count').html(spent_hours_string + ' / ' + estimated_hours_string);
        } else {
            $('#milestone_' + milestone_id + '_hours_count').html(0);
        }
    }

    verifyIssues() {
        this.element.find('.issues .placeholder').remove();
        this.updateCounts();
        for (const issue of this.issues) {
            if (issue.processed) {
                continue;
            }
            this.element.find('.issues').append(issue.element);
            issue.element.removeClass('whiteboard-issue');
            issue.element.addClass('milestone-issue');
            issue.processed = true;
        }
    }

    getHtmlElement(template, board_type) {
        this.template = template;
        if (this.element === undefined) {
            this.element = this.createHtmlElement(board_type);
        }

        return this.element;
    }
}

window.Milestone = Milestone;
export default Milestone;