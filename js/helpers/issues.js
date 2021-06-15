import $ from "jquery";
import Pachno from "../classes/pachno";

import {getEditor} from "../widgets/editor";

const watchIssuePopupForms = () => {
    const $body = $('body');
    const watchedForms = new Set();

    $body.off('click', '#issue-card-popup .formatted-text-container');
    $body.on('click', '#issue-card-popup .formatted-text-container', function () {
        const $editorContainer = $(this).next();
        const $textarea = $editorContainer.find('textarea');
        const $form = $(this).parents('form');
        const editor = getEditor($textarea.attr('id'));
        $form.addClass('editing');
        watchedForms.add($form.attr('id'));
        setTimeout(() => {
            editor.focus();
            editor.codemirror.focus();
        }, 250);
    });

    Pachno.on(Pachno.EVENTS.formSubmitResponse, function (PachnoApplication, data) {
        const json = data.json;
        if (watchedForms.has(data.form)) {
            const $form = $(`#${data.form}`);
            $form.find('.formatted-text-container').html(json.changed[$form.data('field')].value);
            $form.removeClass('editing');
        }
    });
}

const setupListeners = () => {
    const $body = $('body');
    $body.off('click', '.trigger-set-cover');
    $body.on('click', '.trigger-set-cover', function (event) {
        event.preventDefault();
        event.stopPropagation();

        const $element = $(this);
        const issue = Pachno.getIssue($element.data('issue-id'));

        issue.postAndUpdate('cover_image', $element.data('file-id'));
    });

    $body.off('click', '.trigger-clear-cover');
    $body.on('click', '.trigger-clear-cover', function (event) {
        event.preventDefault();
        event.stopPropagation();

        const $element = $(this);
        const issue = Pachno.getIssue($element.data('issue-id'));

        issue.postAndUpdate('cover_image', 0);
    });

    Pachno.on(Pachno.EVENTS.issue.triggerDelete, function (PachnoApplication, data) {
        const url = data.url;
        const issue_id = data.issue_id;

        Pachno.UI.Dialog.setSubmitting();

        Pachno.fetch(url, { method: 'DELETE' })
            .then(json => {
                $(`[data-issue][data-issue-id="${issue_id}"]`).remove();
                const $issueDeletedMessage = $('#issue_deleted_message');
                if ($('[data-issue]').length === 0 && $issueDeletedMessage.length > 0) {
                    $issueDeletedMessage.removeClass('hidden');
                }
                Pachno.UI.Dialog.dismiss();
            })
    });

    Pachno.on(Pachno.EVENTS.issue.removeParentIssue, function (PachnoApplication, data) {
        const url = data.url;
        const issue_id = data.issue_id;

        Pachno.UI.Dialog.setSubmitting();

        Pachno.fetch(url, { method: 'DELETE', success: { update_issues_from_json: true } })
            .then(json => {
                $(`[data-issue][data-issue-id="${issue_id}"]`).remove();
                for (const json_issue of json.issues) {
                    if (parseInt(json_issue.id) !== issue_id) {
                        continue;
                    }

                    Pachno.UI.Dialog.show(
                        Pachno.T.issue.go_to_converted_issue.title,
                        Pachno.T.issue.go_to_converted_issue.message,
                        {
                            yes: { href: json_issue.href },
                            no: { click: Pachno.UI.Dialog.dismiss }
                        }
                    );
                }
            })
    });
}

export {
    watchIssuePopupForms,
    setupListeners
}
