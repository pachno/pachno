import UI from "../helpers/ui";
import Issue from "./issue";
import $ from "jquery";


class Milestone {
    constructor(json) {
        this.id = json.id;
        this.is_closed = json.closed;
        this.is_sprint = json.is_sprint;
        this.name = json.name;
        this.starting_date = json.starting_date;
        this.scheduled_date = json.scheduled_date;
        this.percentage_complete = json.percentage_complete;
        this.issues_count = json.issues_count;
        this.url = json.url;
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
        <div class="header">
            <span class="name">${this.name}</span>
            <span class="info">
                <span class="info-item">${UI.fa_image_tag('file-alt', {}, 'far')}&nbsp;${this.issues_count}</span>
                <span class="icon expander">${UI.fa_image_tag('spinner', {classes: 'fa-spin indicator'}, 'far')}${UI.fa_image_tag('chevron-down')}</span>
            </span>
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
            this.issues.push(new Issue(issue_json));
        }
    }

    addIssue(issue) {
        this.issues.push(issue);
    }

    fetchIssues() {
        Pachno.fetch(this.url, { method: 'GET' })
            .then((json) => {
                this.addIssues(json.milestone.issues);
                this.verifyIssues();
            });
    }

    verifyIssues() {
        for (const issue of this.issues) {
            if (issue.processed) {
                continue;
            }

            this.element.find('.issues').append(issue.element);
            issue.processed = true;
        }
    }

    getHtmlElement() {
        return this.element;
    }
}

window.Milestone = Milestone;
export default Milestone;