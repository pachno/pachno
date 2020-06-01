import $ from "jquery";
import {EVENTS as WidgetEvents} from "./index";

const setupListeners = function () {
    Pachno.on(WidgetEvents.update, () => {
        $('.fancy-tag-input-container').each(function () {
            let $container = $(this);

            let $input = $($container.find('input[type=text]')[0]);
            let values = $input.val().split(',');
            values.each((value) => {
                let real_value = value.trim();
            })
        });
    })
};

export default setupListeners;
