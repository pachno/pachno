import Milestone from "./milestone";

class Roadmap {
    constructor(options) {
        this.milestones_url = options.milestones_url;
        /**
         * @type {Milestone[]}
         */
        this.milestones = [];
        this.milestone_types = 'regular';

        this.fetchMilestones();
        this.setupListeners();
    }

    setupListeners() {
        let roadmap = this;

        $('input[name=milestone_type]').on('click', function () {
            roadmap.milestone_types = $(this).val();
            roadmap.fetchMilestones();
        });
    }

    fetchMilestones() {
        $('#project_roadmap').addClass('loading');
        $('#milestone-cards-container').html('');
        this.milestones = [];
        Pachno.fetch(this.milestones_url + `?milestone_type=${this.milestone_types}`, { method: 'GET' })
            .then(json => {
                for (const milestone_json of json.milestones) {
                    const milestone = new Milestone(milestone_json);
                    this.milestones.push(milestone);
                }
                this.createMilestoneHtml();
            });
    }

    createMilestoneHtml() {
        if (this.milestones.length === 0) {
            $('#onboarding-no-milestones').show();
        } else {
            const $milestones_container = $('#milestone-cards-container');
            for (const milestone of this.milestones) {
                if ($(`.milestone-container[data-milestone-id=${this.id}]`).length > 0) {
                    continue;
                }

                $milestones_container.append(milestone.getHtmlElement());
                if (!milestone.closed) {
                    milestone.fetchIssues();
                }
            }
        }

        $('#project_roadmap').removeClass('loading');
    }
}

window.Roadmap = Roadmap;
export default Roadmap;
