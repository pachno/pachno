import $ from "jquery";
import Pachno from "../classes/pachno";

const setClientContact = function (url, field, $link, $container) {
    const user_id = $link.data('identifiable-value');
    $container.html(Pachno.UI.fa_image_tag('spinner', { classes: 'fa-spin' }));

    Pachno.fetch(url, {
        method: 'POST',
        data: {
            field,
            user_id
        }
    }).then((json) => {
        $container.html(json.content);
    });
}

const addMember = function (event) {
    event.preventDefault();
    event.stopPropagation();

    const $button = $(this);
    const $row = $button.parents('.row');
    const $table = $row.parents('.flexible-table');
    const url = $table.data('url');

    let user_id;

    $row.addClass('submitting');
    $button.attr('disabled', true);

    if (!$row.data('id')) {
        user_id = $row.data('email');
    } else {
        user_id = $row.data('id');
    }

    Pachno.fetch(url, {
        method: 'POST',
        data: {
            user_id
        }
    }).then((json) => {
        $button.html(Pachno.UI.fa_image_tag('check', { classes: 'icon' }));
        const $members_list = $('.assignee-results-list');
        if ($members_list.length) {
            $members_list.append(json.content);
        }
    }).catch(() => {
        $button.removeAttr('disabled');
        $row.removeClass('submitting')
    });
}

const removeMember = function (PachnoApplication, data) {
    const url = data.url;
    Pachno.UI.Dialog.setSubmitting();

    Pachno.fetch(url, {
            method: 'DELETE'
        })
        .then((json) => {
            Pachno.UI.Dialog.dismiss();
            const $row = $(`.row[data-user-id=${json.user_id}]`);
            $row.remove();
        });
}

const setupListeners = function () {
    const $body = $("body");

    $body.on('click', ".trigger-assign-to-client", addMember);
    $body.on('click', ".trigger-assign-to-team", addMember);
    Pachno.on(Pachno.EVENTS.client.removeUser, removeMember);
    Pachno.on(Pachno.EVENTS.team.removeUser, removeMember);

    $body.off('click', '.trigger-set-client-external-contact');
    $body.on('click', '.trigger-set-client-external-contact', function (event) {
        event.preventDefault();

        const $link = $(this);
        const $container = $('#client-external-contact-container');
        const url = $container.data('url');

        setClientContact(url, 'external_contact', $link, $container);
    });

    $body.off('click', '.trigger-set-client-internal-contact');
    $body.on('click', '.trigger-set-client-internal-contact', function (event) {
        event.preventDefault();

        const $link = $(this);
        const $container = $('#client-internal-contact-container');
        const url = $container.data('url');

        setClientContact(url, 'internal_contact', $link, $container);
    });
}

export {
    setupListeners
};
