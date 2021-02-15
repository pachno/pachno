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

export {
    setupListeners
}
