import $ from "jquery";
import setupFancyDropdownListeners from "./fancydropdown";
import setupFancyTagInputListeners from "./fancytaginput";
import setupEditorListeners from "./editor";
import setupDynamicMenuListeners from "./dynamic-menu";
import setupDynamicToggleListeners from "./dynamic-toggle";
import setupNotificationListeners from "./notifications";
import Pachno from "../classes/pachno";
import { EVENTS as FetchEvents } from "../helpers/fetch";
import 'simplebar';
import 'simplebar/dist/simplebar.css';
import AirDatepicker from "air-datepicker";
import localeEn from 'air-datepicker/locale/en';
import hljs from 'highlight.js';
import Sortable from "sortablejs";

export const EVENTS = {
    update: 'widgets-update'
};

const calendars = {};

const updateWidgets = function () {
    return new Promise(function (resolve, reject) {
        let self = this;
        $("img[data-src]:not([data-src-processed])").each(function() {
            let $img = $(this);
            $img.attr('src', $img.data('src')).data('src-processed', true);
        });

        $('[data-auto-sortable]:not([data-processed])').each(function () {
            const element = $(this)[0];
            const draggable_class = $(this).data('draggable-class');
            const url = $(this).data('sortable-url');

            const saveWorkflowOrder = () => {
                const sortable_elements = [];
                for (const sortable_element of element.children) {
                    sortable_elements.push(sortable_element.dataset.id);
                }

                Pachno.fetch(url, {
                    method: 'POST',
                    data: {
                        sortable_elements
                    }
                });
            };

            Sortable.create(element, {
                sort: true,
                handle: '.handle',
                draggable: '.' + draggable_class,
                onEnd: saveWorkflowOrder
            });

            $(this).data('processed', true);
        });

        $('.auto-calendar:not([data-processed])').each(function () {
            let options = { inline: true, locale: localeEn };
            const dates = $(this).data('dates');
            if (dates !== undefined) {
                if (Number.isInteger(dates)) {
                    options.selectedDates = [dates];
                } else if (dates.length > 0) {
                    options.selectedDates = dates.split(',').map(n => parseInt(n));
                }
            }
            if ($(this).data('range') || (options.selectedDates !== undefined && options.selectedDates.length == 2)) {
                options.range = true;
            }
            const datepicker = new AirDatepicker('#' + $(this).attr('id'), options);
            calendars[$(this).attr('id')] = datepicker;
            $(this).data('processed', true);
        });

        Pachno.trigger(EVENTS.update);

        hljs.highlightAll();
        resolve();
    });
}

const clearPopupsAndButtons = function (event) {
    if (event !== undefined) {
        if (['INPUT'].indexOf(event.target.nodeName) !== -1) {
            return;
        }
    }

    $('.dropper.active').removeClass('active');
    $('.fancy-dropdown.active').removeClass('active');
}

const toggleExpander = function (event) {
    event.preventDefault();

    $(this).closest('.expandable').toggleClass('expanded');
};

const toggleSidebarCollapsed = function (event) {
    event.stopPropagation();
    event.preventDefault();

    $(this).closest('.sidebar').toggleClass('collapsed');
};

const toggleSidebar = function (event) {
    event.stopPropagation();
    event.preventDefault();

    $('.sidebar').toggleClass('collapsed');
};

const toggleDropper = function (e) {
    e.stopPropagation();
    e.preventDefault();

    const toggleDropdown = function ($element) {
        if ($element.data('target')) {
            $($element.data('target')).toggleClass('force-active');
        } else {
            $element.toggleClass("active");
        }
    };

    const $element = $(this);
    const is_visible = $element.hasClass('active');
    clearPopupsAndButtons();
    if (!is_visible) {
        toggleDropdown($element);
    }
};

const setupListeners = function () {
    setupDynamicMenuListeners();
    setupDynamicToggleListeners();
    setupEditorListeners();
    setupFancyDropdownListeners();
    setupFancyTagInputListeners();
    setupNotificationListeners();

    const $body = $('body');

    $body.on('click', '.expandable .expander', toggleExpander);
    $body.on('click', '.sidebar .collapser a', toggleSidebarCollapsed);
    $body.on('click', '.dropper', toggleDropper);
    $body.on('click', '.menu-toggler', toggleSidebar);

    $body.on("click", function (e) {
        if (['INPUT'].indexOf(e.target.nodeName) !== -1) {
            return;
        } else if (e.target && $(e.target).parents('.popup_box').length) {
            return;
        } else if (e.target && typeof(e.target.hasAttribute) == 'function' && e.target.hasAttribute('onclick')) {
            return;
        } else if (e.target && e.target.classList.contains('dropper')) {
            return;
        }

        clearPopupsAndButtons();

        if (e.target && jQuery(e.target).parents('#searchfor_autocomplete_choices').length > 0)
            return;
        // if (Pachno.autocompleter !== undefined) {
        //     Pachno.autocompleter.options.forceHide();
        // }

        e.stopPropagation();
    });

    Pachno.on(FetchEvents.updated, updateWidgets);
    Pachno.on(Pachno.EVENTS.ready, updateWidgets);
};

export default setupListeners;
export {
    calendars,
    clearPopupsAndButtons
}
