import $ from "jquery";
import Pachno from "../classes/pachno";
import { clearPopupsAndButtons } from "../widgets";

const loadTransitionPopup = function (event) {
    if (event.isPropagationStopped()) {
        return;
    }

    event.preventDefault();
    event.stopPropagation();

    const $element = $(this);
    $element.addClass('submitting');
    $element.addClass('disabled');

    const url = $element.data('url');
    Pachno.fetch(url, { method: 'POST' })
        .then((json) => {
            for (const issue of json.issues) {
                Pachno.trigger(Pachno.EVENTS.issue.updateJson, { json: issue });
            }
            $element.removeClass('submitting');
            $element.removeClass('disabled');
            clearPopupsAndButtons();
        })
};

const setupListeners = () => {
    const $body = $('body');
    $body.off('click', '.trigger-workflow-transition');
    $body.on('click', '.trigger-workflow-transition', loadTransitionPopup);

    Pachno.on(Pachno.EVENTS.formSubmitResponse, (_, data) => {
        const json = data.json;
        const $form = $(`#${data.form}`);

        if ($form.data('workflow-form') === undefined)
            return;

        for (const issue of json.issues) {
            Pachno.trigger(Pachno.EVENTS.issue.updateJson, { json: issue });
        }

        Pachno.UI.Backdrop.reset();
    });
};

export const TRANSITION_ACTIONS = {
    ACTION_ASSIGN_ISSUE_SELF: 'assign_self',
    ACTION_ASSIGN_ISSUE: 'assign_user',
    ACTION_CLEAR_ASSIGNEE: 'clear_assignee',
    ACTION_SET_DUPLICATE: 'set_duplicate',
    ACTION_CLEAR_DUPLICATE: 'clear_duplicate',
    ACTION_SET_RESOLUTION: 'set_resolution',
    ACTION_CLEAR_RESOLUTION: 'clear_resolution',
    ACTION_SET_STATUS: 'set_status',
    ACTION_SET_MILESTONE: 'set_milestone',
    ACTION_CLEAR_MILESTONE: 'clear_milestone',
    ACTION_SET_PRIORITY: 'set_priority',
    ACTION_CLEAR_PRIORITY: 'clear_priority',
    ACTION_SET_PERCENT: 'set_percent',
    ACTION_CLEAR_PERCENT: 'clear_percent',
    ACTION_SET_REPRODUCABILITY: 'set_reproducability',
    ACTION_CLEAR_REPRODUCABILITY: 'clear_reproducability',
    ACTION_USER_START_WORKING: 'user_start_working',
    ACTION_USER_STOP_WORKING: 'user_stop_working',
    CUSTOMFIELD_CLEAR_PREFIX: 'customfield_clear_',
    CUSTOMFIELD_SET_PREFIX: 'customfield_set_',
};

export {
    setupListeners
}
