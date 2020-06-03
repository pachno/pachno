import $ from "jquery";
import setupFancyDropdownListeners from "./fancydropdown";
import setupFancyTagInputListeners from "./fancytaginput";
import setupEditorListeners from "./editor";
import setupDynamicMenuListeners from "./dynamic-menu";
import setupNotificationListeners from "./notifications";
import Pachno from "../classes/pachno";
import { EVENTS as FetchEvents } from "../helpers/fetch";

export const EVENTS = {
    update: 'widgets-update'
};

const updateWidgets = function () {
    return new Promise(function (resolve, reject) {
        let self = this;
        $("img[data-src]:not([data-src-processed])").each(function() {
            let $img = $(this);
            $img.attr('src', $img.data('src')).data('src-processed', true);
        });

        Pachno.trigger(EVENTS.update);

        resolve();
    });
}

const clearPopupsAndButtons = function (event) {
    $('.dropper.active').removeClass('active');
    $('.fancy-dropdown.active').removeClass('active');
}

const toggleExpander = function (event) {
    event.preventDefault();

    $(this).closest('.expandable').toggleClass('expanded');
};

const toggleSidebar = function (event) {
    event.stopPropagation();
    event.preventDefault();

    $(this).closest('.sidebar').toggleClass('collapsed');
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
    setupFancyDropdownListeners();
    setupFancyTagInputListeners();
    setupEditorListeners();
    setupDynamicMenuListeners();
    setupNotificationListeners();

    const $body = $('body');

    $body.on('click', '.expandable .expander', toggleExpander);
    $body.on('click', '.sidebar .collapser a', toggleSidebar);
    $body.on('click', '.dropper', toggleDropper);

    $body.on("click", function (e) {
        if (['INPUT'].indexOf(e.target.nodeName) !== -1) {
            return;
        } else if (e.target && $(e.target).parents('.popup_box').length) {
            return;
        } else if (e.target && typeof(e.target.hasAttribute) == 'function' && e.target.hasAttribute('onclick')) {
            return;
        } else if (e.target && typeof(e.target.hasAttribute) == 'function' && e.target.hasAttribute('onclick')) {
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