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

    $body.off('click', '.trigger-start-time-tracking');
    $body.on('click', '.trigger-start-time-tracking', function (event) {
        event.preventDefault();
        event.stopPropagation();

        const $element = $(this);
        $element.parent().addClass('submitting');
        $element.attr('disabled', true);
        const issue = Pachno.getIssue($element.data('issue-id'));
        const url = $element.data('url');

        issue.fetchAndUpdate(url, 'POST')
            .then(() => {
                $element.parent().removeClass('submitting');
                $element.removeAttr('disabled');
            })
    });

    const updateTimeTracking = function ($element, parameters) {
        $element.parent().addClass('submitting');
        $element.attr('disabled', true);
        const issue = Pachno.getIssue($element.data('issue-id'));
        const url = issue.current_time_tracking.url + parameters;

        issue.fetchAndUpdate(url, 'POST')
            .then(() => {
                $element.parent().removeClass('submitting');
                $element.removeAttr('disabled');
            })
    }

    $body.off('click', '.trigger-stop-time-tracking');
    $body.on('click', '.trigger-stop-time-tracking', function (event) {
        event.preventDefault();
        event.stopPropagation();

        updateTimeTracking($(this), '?is_completed=1&is_paused=1');
    });

    $body.off('click', '.trigger-pause-time-tracking');
    $body.on('click', '.trigger-pause-time-tracking', function (event) {
        event.preventDefault();
        event.stopPropagation();

        updateTimeTracking($(this), '?is_completed=0&is_paused=1');
    });

    $body.off('click', '.trigger-resume-time-tracking');
    $body.on('click', '.trigger-resume-time-tracking', function (event) {
        event.preventDefault();
        event.stopPropagation();

        updateTimeTracking($(this), '?is_completed=0&is_paused=0');
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

    const updateInteractiveTimers = () => {
        const $timers = $('[data-interactive-timer]');
        for (const element of $timers) {
            const $element = $(element);
            if ($element.data('started-at') === undefined || $element.data('paused') !== undefined) {
                continue;
            }

            const started_date = new Date($element.data('started-at'));
            const now_date = new Date(Date.now());
            let diff = Math.abs(now_date - started_date) / 1000;
            let days = Math.floor(diff / 86400);
            diff -= days * 86400;
            days = String(days).padStart(2, '0');

            // calculate (and subtract) whole hours
            let hours = Math.floor(diff / 3600) % 24;
            diff -= hours * 3600;
            hours = String(hours).padStart(2, '0');

            // calculate (and subtract) whole minutes
            let minutes = Math.floor(diff / 60) % 60;
            diff -= minutes * 60;
            minutes = String(minutes).padStart(2, '0');

            // what's left is seconds
            let seconds = diff % 60;
            seconds = String(seconds).padStart(2, '0');

            let time_string = `${hours}:${minutes}`;
            if (days > 0) {
                time_string = `${days}:${time_string}`;
            }

            $element.find('.value').html(time_string);
        }

        setTimeout(updateInteractiveTimers, 1000);
    };

    updateInteractiveTimers();
}

export {
    watchIssuePopupForms,
    setupListeners
}
