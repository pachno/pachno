import UI from "../helpers/ui";
import Pachno from "./pachno";
import $ from "jquery";

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
        this.url = json.url;
        this.backdrop_url = json.backdrop_url;
        /**
         * @type {Issue[]}
         */
        this.issues = [];
        this.element = this.createHtmlElement();
    }

    createHtmlElement() {
        let classes = [];
        if (this.is_closed) classes.push('milestone-closed');

        let html = `
<div class="milestone-container ${classes.join(',')}" data-milestone-id="${this.id}">
    <div class="milestone milestone-card">
        <div class="header trigger-backdrop" data-url="${this.backdrop_url}">
            <span class="name">${this.name}</span>
            <span class="info">
                <span class="info-item">${UI.fa_image_tag('file-alt', {}, 'far')}&nbsp;${this.issues_count}</span>
                <span class="icon indicator">${UI.fa_image_tag('spinner', {classes: 'fa-spin'})}</span>
                <span class="icon expander">${UI.fa_image_tag('chevron-down')}</span>
            </span>
            <div class="percent-container">
                <span class="percent-header">${Pachno.T.roadmap.percent_complete.replace('%percentage', this.percent_complete)}</span>
                <span class="percent_unfilled">
                    <span class="percent_filled" style="width: ${this.percent_complete}%;"></span>
                </span>
            </div>
        </div>
        <div class="issues"></div>
    </div>
</div>
`;
        const $html = $(html);

        return $html;
    }

    addIssues(issues) {
        for (const issue_json of issues) {
            this.issues.push(Pachno.addIssue(issue_json));
        }
    }

    addIssue(issue) {
        this.issues.push(issue);
    }

    fetchIssues() {
        const $milestone_card = this.element.find('.milestone-card');
        $milestone_card.addClass('loading');

        Pachno.fetch(this.url, { method: 'GET' })
            .then((json) => {
                this.addIssues(json.milestone.issues);
                $milestone_card.removeClass('loading');
                this.verifyIssues();
            });
    }

    verifyIssues() {
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

    getHtmlElement() {
        return this.element;
    }
}

window.Milestone = Milestone;
export default Milestone;