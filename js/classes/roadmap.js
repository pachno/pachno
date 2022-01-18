import $ from "jquery";
import Pachno from "../classes/pachno";
import { watchIssuePopupForms } from "../helpers/issues";

import Milestone, {Templates as MilestoneTemplates} from "./milestone";

class Roadmap {
    constructor(options) {
        this.milestones_url = options.milestones_url;
        /**
         * @type {Milestone[]}
         */
        this.milestones = [];
        this.milestone_types = 'regular';
        this.milestone_state = 'open';

        this.fetchMilestones();
        this.setupListeners();
    }

    setupListeners() {
        let roadmap = this;

        $('input[name=milestone_type]').on('click', function () {
            roadmap.milestone_types = $(this).val();
            roadmap.fetchMilestones();
        });

        $('input[name=milestone_state]').on('click', function () {
            roadmap.milestone_state = $(this).val();
            roadmap.fetchMilestones();
        });

        Pachno.on(Pachno.EVENTS.formSubmitResponse, (Pachno, data) => {
            const milestone = new Milestone(data.json.milestone);
            roadmap.milestones.push(milestone);
            roadmap.createMilestoneHtml();
        });

        watchIssuePopupForms();
    }

    fetchMilestones() {
        $('#project_roadmap').addClass('loading');
        $('#milestone-cards-container').html('');
        this.milestones = [];

        Pachno.fetch(this.milestones_url + `?milestone_type=${this.milestone_types}&state=${this.milestone_state}`, { method: 'GET' })
            .then(json => {
                for (const milestone_json of json.milestones) {
                    const milestone = new Milestone(milestone_json);
                    this.milestones.push(milestone);
                }
                this.createMilestoneHtml();
            });
    }

    createMilestoneHtml() {
        const $milestones_container = $('#milestone-cards-container');
        if (this.milestones.length === 0) {
            $('#onboarding-no-milestones').show();
            $milestones_container.hide();
        } else {
            for (const milestone of this.milestones) {
                if ($(`.milestone-container[data-milestone-id=${this.id}]`).length > 0) {
                    continue;
                }

                $milestones_container.append(milestone.getHtmlElement(MilestoneTemplates.roadmap));
                if (!milestone.is_closed) {
                    milestone.fetchIssues();
                }
            }
        }

        $('#project_roadmap').removeClass('loading');
    }
}

window.Roadmap = Roadmap;
export default Roadmap;
