import $ from "jquery";
import Pachno from "../classes/pachno";

const addAssignee = function (event) {
    event.preventDefault();
    event.stopPropagation();

    const $button = $(this);
    const $row = $button.parents('.row');
    const $table = $row.parents('.flexible-table');
    const url = $table.data('url');
    const $role = $row.find('input[name=role_id]:checked');
    const role_id = ($role.length) ? $role.val() : 0;

    let assignee_type;
    let assignee_id;

    $row.addClass('submitting');
    $button.attr('disabled', true);

    if (!$row.data('id')) {
        assignee_type = 'user';
        assignee_id = $row.data('email');
    } else {
        assignee_type = $row.data('identifiable-type');
        assignee_id = $row.data('id');
    }

    Pachno.fetch(url, {
        method: 'POST',
        data: {
            assignee_type,
            assignee_id,
            role_id
        }
    }).then((json) => {
        $button.html(Pachno.UI.fa_image_tag('check', { classes: 'icon' }));
        const $assignee_list = $('#project_team_list');
        if ($assignee_list.length) {
            $assignee_list.append(json.content);
        }
    }).catch(() => {
        $button.removeAttr('disabled');
        $row.removeClass('submitting')
    });
}

const removeAssignee = function (PachnoApplication, data) {
    const url = data.url;
    Pachno.UI.Dialog.setSubmitting();

    Pachno.fetch(url, {
        method: 'POST'
    })
    .then((json) => {
        Pachno.UI.Dialog.dismiss();
        const $row = $(`.row[data-assignee-type=${json.assignee_type}][data-assignee-id=${json.assignee_id}]`);
        $row.remove();
    });
}

const setupListeners = function () {
    const $body = $("body");

    $body.on('click', ".trigger-assign-to-project", addAssignee);
    Pachno.on(Pachno.EVENTS.project.removeAssignee, removeAssignee);
};

export {
    setupListeners
}