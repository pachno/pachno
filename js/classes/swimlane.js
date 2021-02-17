import Issue from "./issue";
import Pachno from "./pachno";
import {SwimlaneTypes} from "./board";

class Swimlane {
    constructor(json, board_id) {
        /**
         * @type {Issue[]}
         */
        this.issues = [];
        this.name = json.name;
        this.board_id = board_id;
        this.has_identifiables = (json.has_identifiables);
        this.identifier_issue = (json.identifier_issue) ? Pachno.addIssue(json.identifier_issue, board_id) : undefined;
        this.identifier_grouping = json.identifier_grouping;
        this.identifier_type = json.identifier_type;
        this.identifiables = json.identifiables;
        this.identifier = json.identifier;

        for (const issue_json of json.issues) {
            this.issues.push(Pachno.addIssue(issue_json, this.board_id));
        }
    }

    addIssues(issues) {
        for (const issue_json of issues) {
            this.issues.push(Pachno.addIssue(issue_json, this.board_id));
        }
    }

    addIssue(issue) {
        this.issues.push(issue);
    }

    addOrRemove(issue, add = true) {
        if (!this.has(issue)) {
            for (const index in this.issues) {
                if (!this.issues.hasOwnProperty(index))
                    continue;

                // if (this.issues[index].id == issue.id)
                this.issues = this.issues.filter(existing_issue => existing_issue.id != issue.id);
            }
        } else if (add) {
            let found = this.issues.some(existing_issue => existing_issue.id == issue.id);
            if (!found) {
                this.issues.push(issue);
            }
            return true;
        }

        return false;
    }

    /**
     * Check if an issue is inside this swimlane
     * @param {Issue} issue
     * @returns {boolean}
     */
    has(issue) {
        if (this.identifier === 'swimlane_0') {
            return true;
        }

        switch (this.identifier_type) {
            case SwimlaneTypes.ISSUES:
                return (this.identifier_issue.id === issue.parent_issue_id);
            case SwimlaneTypes.GROUPING:
            case SwimlaneTypes.EXPEDITE:
                // debugger;
                if (!this.identifiables) {
                    return false;
                }

                for (const identifiable_id in this.identifiables) {
                    if (!this.identifiables.hasOwnProperty(identifiable_id)) continue;

                    if (issue[this.identifier_grouping] !== undefined && this.identifiables[identifiable_id].id === issue[this.identifier_grouping].id) {
                        return true;
                    }
                }
                break;
        }

        return false;
    }
}

export default Swimlane;
window.Swimlane = Swimlane;