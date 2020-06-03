import $ from "jquery";
import Pachno from "../classes/pachno";
import { EVENTS as WidgetEvents } from "./index";

const updateFancyDropdownLabel = function ($dropdown) {
    let $label = $dropdown.find('> .value');
    if ($label.length > 0) {
        var auto_close = false;
        var values = [];
        $dropdown.find('input[type=checkbox],input[type=radio]').each(function () {
            var $input = $(this);

            if ($input.attr('type') == 'radio') {
                auto_close = true;
            }

            if ($input.is(':checked')) {
                var $label = $($input.next('label')),
                    $value = $($label.find('.value')[0]);

                if ($value.text() != '') {
                    values.push($value.text());
                }
            }
        });

        if (values.length > 0) {
            $dropdown.removeClass('no-value');
            $label.html(values.join(', '));
        } else {
            $dropdown.addClass('no-value');
            $label.html($dropdown.data('default-label'));
        }

        if (auto_close) {
            $dropdown.removeClass('active');
        }
    }
}

const updateFancyDropdownValues = function (event) {
    event.stopPropagation();
    event.stopImmediatePropagation();
    event.preventDefault();
    let $dropdown = $(this).closest('.fancy-dropdown');
    updateFancyDropdownLabel($dropdown);
};

const setupListeners = function () {
    const $body = $('body');

    $body.on('change', '.fancy-dropdown input[type=checkbox]', updateFancyDropdownValues);
    $body.on('change', '.fancy-dropdown input[type=radio]', updateFancyDropdownValues);

    Pachno.on(WidgetEvents.update, () => {
        $('.fancy-dropdown').each(function () {
            updateFancyDropdownLabel($(this));
        });
    })
};

export default setupListeners;
