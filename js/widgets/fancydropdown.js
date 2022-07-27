import $ from "jquery";
import Pachno from "../classes/pachno";
import {clearPopupsAndButtons, EVENTS as WidgetEvents} from "./index";

const updateFancyDropdownLabel = function ($dropdown) {
    let $label = $dropdown.find('> .value');
    if ($label.length > 0) {
        let auto_close = false;
        let values = [];
        $dropdown.find('input[type=checkbox],input[type=radio]').each(function () {
            const $input = $(this);

            if ($input.attr('type') == 'radio') {
                auto_close = true;
            }

            if ($input.is(':checked')) {
                const $label = $($input.next('label'));
                const $value = $($label.find('.value').first());

                if ($value.text() != '') {
                    values.push($value.text());
                } else if ($input.val() != "0") {
                    console.error('Could not find a .value for item', $input);
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

const updateFancyDropdowns = function () {
    $('.fancy-dropdown').each(function () {
        updateFancyDropdownLabel($(this));
    });
};

const toggleFancyDropdown = function (event) {
    const $dropdown = $(this);
    const is_visible = $dropdown.hasClass('active');
    clearPopupsAndButtons(event);

    if (!is_visible) {
        $dropdown.toggleClass('active');
    }
    event.stopPropagation();
}

const filterFilterOptionsElement = function (element) {
    const filtervalue = element.val().toLowerCase(),
        $filterContainer = $(element.parents('.dropdown-container').find('.filter-values-container'));

    if (filtervalue === '') {
        $filterContainer.removeClass('filtered');
    } else {
        $filterContainer.addClass('filtered');
        if (filtervalue !== element.data('previousValue')) {
            $filterContainer.find('.filtervalue').each(function () {
                var $filterElement = $(this);
                if ($filterElement.hasClass('sticky'))
                    return;

                if (filtervalue !== '') {
                    if ($filterElement.text().toLowerCase().indexOf(filtervalue) !== -1 || $filterElement.hasClass('selected')) {
                        $filterElement.addClass('visible');
                    } else {
                        $filterElement.removeClass('visible');
                    }
                } else {
                    $filterElement.addClass('visible');
                }
                $filterElement.removeClass('highlighted');
            });
            element.data('previousValue', filtervalue);
        }
    }
};

const setupListeners = function () {
    const $body = $('body');

    $body.on('change', '.fancy-dropdown input[type=checkbox]', updateFancyDropdownValues);
    $body.on('change', '.fancy-dropdown input[type=radio]', updateFancyDropdownValues);
    $body.on("click", ".fancy-dropdown", toggleFancyDropdown);

    $body.on("keyup", ".fancy-dropdown .filter-container input[type=search],.dropdown-container .filter-container input[type=search]", function (e) {
        const $filterInput = $(this);

        $filterInput.data('previousValue', '');
        filterFilterOptionsElement($filterInput);
    });

    Pachno.on(WidgetEvents.update, updateFancyDropdowns);
};

export default setupListeners;
