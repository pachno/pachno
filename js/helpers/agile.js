import $ from "jquery";
import Pachno from "../classes/pachno";

const setupListeners = () => {

    if ($('#project_planning').length)
        return;

    Pachno.on(Pachno.EVENTS.formSubmitResponse, (_, data) => {
        if (data.form === 'edit-agileboard-form') {
            const json = data.json;
            const $existing_board = $(`[data-agileboard][data-id="${json.board.id}"]`);

            if ($existing_board.length) {
                $existing_board.replaceWith(json.component);
            } else {
                $('#onboarding-no-boards').addClass('hidden');
                $('#agileboards').append(json.component);
                Pachno.UI.Backdrop.reset();
            }
        }
    });

    Pachno.on(Pachno.EVENTS.agile.deleteBoard, (_, data) => {
        const $existing_board = $(`[data-agileboard][data-id="${data.board_id}"]`);

        if ($existing_board.length) {
            $existing_board.remove();
        }

        if (!$('#agileboards').find('[data-agileboard]').length) {
            $('#onboarding-no-boards').removeClass('hidden');
        }

        Pachno.UI.Dialog.dismiss();
        Pachno.fetch(data.url, { method: 'DELETE' });
    });
}

export {
    setupListeners
}