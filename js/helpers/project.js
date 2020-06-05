import $ from "jquery";
import Pachno from "../classes/pachno";

const projectFormSubmit = function (event) {
    event.preventDefault();
    const $form = $(this);

    const project_id = $form.data('project-id');
    const url = $form.attr('action');
    Pachno.fetch(url, {
        form: $form.attr('id'),
        success: {
            callback: function (json) {
                if ($('#project_name_span'))
                    $('#project_name_span').html($('#project_name_input').val());
                if ($('#project_description_span')) {
                    if ($('#project_description_input').val()) {
                        $('#project_description_span').html(json.project_description);
                        $('#project_no_description').hide();
                    } else {
                        $('#project_description_span').html('');
                        $('#project_no_description').show();
                    }
                }
                if ($('#project_key_span'))
                    $('#project_key_span').html(json.project_key);
                if ($('#sidebar_link_scrum') && $('#use_scrum').val() == 1)
                    $('#sidebar_link_scrum').show();
                else if ($('#sidebar_link_scrum'))
                    $('#sidebar_link_scrum').hide();

                ['edition', 'component'].each(function (element) {
                    if ($('#enable_' + element + 's').val() == 1) {
                        $('#add_' + element + '_button').show();
                        $('#project_' + element + 's').show();
                        $('#project_' + element + 's_disabled').hide();
                    } else {
                        $('#add_' + element + '_button').hide();
                        $('#project_' + element + 's').hide();
                        $('#project_' + element + 's_disabled').show();
                    }
                });

                if (project_id && $('#project_box_' + project_id).length) {
                    $('#project_box_' + project_id).html(json.content);
                }
            }
        }
    });
}


const setupAdminListeners = function () {
    const $body = $("body");

    $body.on("submit", "form[data-submit-project-settings]", projectFormSubmit);
};
