import UI from "../helpers/ui";
import $ from "jquery";

class Issue {
    constructor(json, board_id) {
        this.id = json.id;
        this.board_id = board_id;
        this.href = json.href;
        this.title = json.title;
        this.issue_no = json.issue_no;
        this.state = json.state;
        this.closed = json.closed;
        this.deleted = json.deleted;
        this.created_at = json.created_at;
        this.created_at_iso = json.created_at_iso;
        this.updated_at = json.updated_at;
        this.updated_at_iso = json.updated_at_iso;
        this.category = json.category;
        this.priority = json.priority;
        this.severity = json.severity;
        this.more_actions_url = json.more_actions_url;
        this.posted_by = json.posted_by;
        this.assignee = json.assignee;
        this.status = json.status;
        this.card_url = json.card_url;
        this.blocking = json.blocking;
        this.milestone = json.milestone;
        this.number_of_files = json.number_of_files;
        this.number_of_comments = json.number_of_comments;
        this.issue_type = json.issue_type;
        this.parent_issue_id = json.parent_issue_id;
        this.processed = false;
        this.element = this.createHtmlElement();
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
        if (this.assignee !== undefined) {
            if (this.assignee.type == 'user') {
                $info.append(`<span class="assignee"><span class="avatar medium"><img src="${this.assignee.avatar_url_small}"></span></span>`)
            }
        }
        return $html;
    }
}

export default Issue;
window.Issue = Issue;