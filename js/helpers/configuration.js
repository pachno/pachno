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

    Pachno.fetch(url + '?user_id=' + data.user_id, {
            method: 'DELETE'
        })
        .then((json) => {
            Pachno.UI.Dialog.dismiss();
            const $row = $(`.row[data-user-id=${json.user_id}]`);
            $row.remove();
        });
}

const checkForUpdate = function (event) {
    const $button = $(this);

    event.preventDefault();
    event.stopPropagation();
    $button.parent().addClass('submitting');
    $button.attr('disabled', true);

    Pachno.fetch($button.data('url'), { method: 'get' })
        .then(json => {
            $button.removeAttr('disabled');
            $button.parent().removeClass('submitting');
            if (json.update_available == 1) {
                Pachno.UI.Dialog.show(
                    Pachno.T.configuration.update_available.header.replace('%version', json.version),
                    Pachno.T.configuration.update_available.content.replace('%version', '<span class="count-badge">' + json.version + '</span>'),
                    {
                        yes: { href: 'https://pach.no/releases/latest' },
                        no: { click: Pachno.UI.Dialog.dismiss }
                    }
                )
            } else {
                Pachno.UI.Message.success(Pachno.T.configuration.up_to_date.message);
            }
        })
        .catch(error => {
            $button.removeAttr('disabled');
            $button.parent().removeClass('submitting');
        });
}

const setupListeners = function () {
    const $body = $("body");

    $body.on('click', '.trigger-assign-to-client', addMember);
    $body.on('click', '.trigger-assign-to-team', addMember);
    Pachno.on(Pachno.EVENTS.client.removeUser, removeMember);
    Pachno.on(Pachno.EVENTS.team.removeUser, removeMember);

    $body.off('click', '.trigger-check-for-update');
    $body.on('click', '.trigger-check-for-update', checkForUpdate);

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

    $body.off('click', '.trigger-set-team-lead');
    $body.on('click', '.trigger-set-team-lead', function (event) {
        event.preventDefault();

        const $link = $(this);
        const $container = $('#team-lead-container');
        const url = $container.data('url');
        const user_id = $link.data('identifiable-value');

        $container.html(Pachno.UI.fa_image_tag('spinner', { classes: 'fa-spin' }));

        Pachno.fetch(url, {
            method: 'POST',
            data: {
                field: 'team_lead',
                user_id
            }
        }).then((json) => {
            $container.html(json.content);
        });
    });
}

export {
    setupListeners
};
