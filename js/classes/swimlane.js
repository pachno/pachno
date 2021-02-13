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
        this.identifier_issue = json.identifier_issue;
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
                if (!this.identifiables) {
                    return false;
                }

                // debugger;
                for (const identifiable_id in this.identifiables) {
                    if (!this.identifiables.hasOwnProperty(identifiable_id)) continue;

                    if (issue[this.identifier_grouping] !== undefined && identifiable_id === issue[this.identifier_grouping].id) {
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