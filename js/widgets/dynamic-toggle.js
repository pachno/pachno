import $ from "jquery";
import {fetchHelper} from "../helpers/fetch";

const setupListeners = function () {
    $("body").on("click", ".dynamic-toggle", function (event) {
        event.preventDefault();
        const $item = $(this);
        $item.addClass('submitting');

        fetchHelper($item.data('url'), {
            method: 'POST'
        }).then((json) => {
            $item.removeClass('submitting');
            $item.prop('checked', (json.value == 1));
        }).catch((error) => {
            console.error(error);
            $item.removeClass('submitting');
        })
    });
};

export default setupListeners;
