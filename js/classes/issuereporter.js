import UI from "../helpers/ui";

class IssueReporter {
    constructor() {
        this.setupListeners();
    }

    updateFields() {
        const url = $('#report_form').data('fields-url');
        const issue_type_id = document.querySelector('input[name="issuetype_id"]:checked').value;

        if (issue_type_id != 0) {
            $('#report_form_issue_type_selector').addClass('hidden');
        }
        if ($('#project_id').val() != 0 && issue_type_id != 0) {
            $('#report_form').removeClass('hidden');

            Pachno.fetch(url, {
                loading: {indicator: '#report_issue_more_options_indicator'},
                data: 'issuetype_id=' + issue_type_id,
            }).then(json => {
                for (const fieldname of json.available_fields) {
                    let $field_container = $(`#${fieldname}_div`);
                    if ($field_container.length) {
                        if (json.fields[fieldname]) {
                            $field_container.removeClass('hidden');
                            if ($(`#${fieldname}_id`)) {
                                $(`#${fieldname}_id`).prop('disabled', false);
                            }
                            if ($(`#${fieldname}_value`)) {
                                $(`#${fieldname}_value`).prop('disabled', false);
                            }
                            if (json.fields[fieldname].values) {
                                let container = $(`#${fieldname}_div`).find('.dropdown-container');
                                if (container) {
                                    container.html('');
                                    let markup = `<input type="radio" value="" name="${fieldname}_id" id="report_issue_${fieldname}_id_0" class="fancy-checkbox">
                                                        <label for="report_issue_${fieldname}_id_0" class="list-item">
                                                        <span class="name value">Not selected</span>
                                                        </label>`;
                                    container.append(markup);
                                    for (var opt in json.fields[fieldname].values) {
                                        let value = opt.substr(1);
                                        let description = json.fields[fieldname].values[opt];
                                        let markup = `<input type="radio" value="${value}" name="${fieldname}_id" id="report_issue_${fieldname}_id_${value}" class="fancy-checkbox">
                                                        <label for="report_issue_${fieldname}_id_${value}" class="list-item">
                                                        <span class="name value">${description}</span>
                                                        </label>`;
                                        container.append(markup);
                                    }
                                }
                            }
                            (json.fields[fieldname].required) ? $(`#${fieldname}_label`).addClass('required') : $(`#${fieldname}_label`).removeClass('required');
                        } else {
                            if ($(`#${fieldname}_div`)) {
                                $(`#${fieldname}_div`).addClass('hidden');
                            }
                            if ($(`#${fieldname}_id`)) {
                                $(`#${fieldname}_id`).prop('disabled', true);
                            }
                            if ($(`#${fieldname}_value`)) {
                                $(`#${fieldname}_value`).prop('disabled', true);
                            }
                        }
                    }
                }

                $('#report_issue_title_input').focus();
                $('#report_issue_more_options_indicator').hide();
            })
        } else {
            $('#report_form_issue_type_selector').removeClass('hidden');
            $('#report_form').addClass('hidden');
            const $reportissueContainer = $('#reportissue_container');
            $reportissueContainer.removeClass('huge');
            $reportissueContainer.addClass('large');
        }
    }

    setupListeners() {
        const $body = $('body');
        const reporter = this;
        $body.off('click', 'input[type=radio].report-issue-type-selector');
        $body.on('click', 'input[type=radio].report-issue-type-selector', function (event) {
            reporter.updateFields();
        });

        $body.off('click', '.restart-reportissue-form');
        $body.on('click', '.restart-reportissue-form', function (event) {
            event.preventDefault();
            $('#issue-reported-confirmation').addClass('hidden');
            $('#report_form_issue_type_selector').removeClass('hidden');
            $('#report_form').addClass('hidden');
        });

        $body.off('click', '#issuetype_list .list-item');
        $body.on('click', '#issuetype_list .list-item', function (event) {
            event.preventDefault();

            const $issueType = $(this);
            const $reportissueContainer = $('#reportissue_container');

            $('#report_issue_issue_type_' + $issueType.data('id')).click();
            $reportissueContainer.addClass('huge');
            $reportissueContainer.removeClass('large');
        });

        Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
            const json = data.json;
            switch (data.form) {
                case 'report_issue_form':
                    $('#report_issue_form')[0].reset();
                    $('#report_form').addClass('hidden');
                    $('#issue-reported-confirmation').removeClass('hidden');
                    const $reportissueContainer = $('#reportissue_container');
                    $reportissueContainer.removeClass('huge');
                    $reportissueContainer.addClass('large');
                    const $reportedIssueLink = $('#reported-issue-container');
                    const issue = json.issue;
                    $reportedIssueLink.attr('href', issue.href);
                    $reportedIssueLink.html(`${UI.fa_image_tag(issue.issue_type.fa_icon, {classes: `icon issuetype-icon issuetype-${issue.issue_type.type}`})}<span class="name">${issue.issue_no} - ${issue.title}</span>${UI.fa_image_tag('external-link-alt')}`);
                    break;
            }
        });
    }
}

export default IssueReporter;
window.IssueReporter = IssueReporter;