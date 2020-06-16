import Issue from "./issue";

class Swimlane {
    constructor(json) {
        this.issues = [];
        this.name = json.name;
        this.has_identifiables = (json.has_identifiables);
        this.identifier_issue = json.identifier_issue;
        this.identifier = json.identifier;

        for (const issue_json of json.issues) {
            this.issues.push(new Issue(issue_json));
        }
    }

    addIssues(issues) {
        for (const issue_json of issues) {
            this.issues.push(new Issue(issue_json));
        }
    }
}

export default Swimlane;
window.Swimlane = Swimlane;