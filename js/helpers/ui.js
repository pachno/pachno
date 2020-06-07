import $ from "jquery";
import {fetchHelper, formSubmitHelper} from "./fetch";

const tabSwitcher = function ($tab, target, $tabSwitcher, change_hash) {
    if (!change_hash) {
        change_hash = false;
    }

    $tabSwitcher.children().removeClass('selected');
    $tab.addClass('selected');
    $('#' + $tabSwitcher.prop('id') + '_panes').children().each(function () {
        const $pane = $(this);
        if ($pane.data('tab-id') == target) {
            $pane.show();
        } else {
            $pane.hide();
        }
    });
    if (change_hash) {
        if (history.replaceState) {
            window.history.replaceState(null, null, '#' + target);
        }
        else {
            window.location.hash = target;
        }
    }
};

const UI = {
    Message: {
        /**
         * Clears all popup messages from the effect queue
         */
        clear: () => {
            $('#pachno_successmessage').hide();
            $('#pachno_failuremessage').hide();
        },

        /**
         * Shows an error popup message
         *
         * @param title string The title to show
         * @param content string Error details
         */
        error: (title, content) => {
            $('#pachno_failuremessage').hide();
            $('#pachno_failuremessage_title').html(title);
            $('#pachno_failuremessage_content').html(content);
            $('#pachno_failuremessage').show();
            $('#pachno_successmessage').hide();
        },

        /**
         * Shows a "success"-style popup message
         *
         * @param title string The title to show
         * @param content string Message details
         */
        success: (title, content) => {
            $('#pachno_successmessage').hide();
            $('#pachno_successmessage_title').html(title);
            $('#pachno_successmessage_content').html(content);
            $('#pachno_successmessage').show();
            $('#pachno_failuremessage').hide();

            setTimeout(() => {
                $('#pachno_successmessage').hide();
            }, 5000);
        }
    },
    Dialog: {
        show: (title, content, options) => {
            UI.Message.clear();
            $('#dialog_title').html(title);
            $('#dialog_content').html(content);
            $('#dialog_yes').attr('href', 'javascript:void(0)');
            $('#dialog_no').attr('href', 'javascript:void(0)');
            $('#dialog_yes').off('click');
            $('#dialog_no').off('click');
            $('#dialog_yes').removeClass('disabled');
            $('#dialog_no').removeClass('disabled');
            if (options.yes.click) {
                $('#dialog_yes').on('click', options.yes.click);
            }
            if (options.yes.href) {
                $('#dialog_yes').attr('href', options.yes.href);
            }
            if (options.no.click) {
                $('#dialog_no').on('click', options.no.click);
            }
            if (options.no.href) {
                $('#dialog_no').attr('href', options.no.href);
            }
            $('#dialog_backdrop_content').show();
            $('#dialog_backdrop').show();
        },

        showModal: (title, content) => {
            UI.Message.clear();
            $('#dialog_modal_title').html(title);
            $('#dialog_modal_content').html(content);
            $('#dialog_backdrop_modal_content').show();
            $('#dialog_backdrop_modal').show();
        },

        dismiss: () => {
            $('#dialog_backdrop_content').hide();
            $('#dialog_backdrop').hide();
        },

        dismissModal: () => {
            $('#dialog_backdrop_modal_content').hide();
            $('#dialog_backdrop_modal').hide();
        }
    },

    Backdrop: {
        show: (url, callback) => {
            return new Promise(resolve => {
                const showBackdrop = () => new Promise(_resolve => {
                    $('#fullpage_backdrop_content').hide();
                    $('#fullpage_backdrop').show();
                    $('body').css({'overflow': 'hidden'});
                    $('#fullpage_backdrop_indicator').show();
                    _resolve();
                });

                showBackdrop()
                    .then(() => {
                        if (url != undefined) {
                            fetchHelper(url, {
                                method: 'GET',
                                loading: {indicator: '#fullpage_backdrop_indicator'},
                                success: {
                                    update: '#fullpage_backdrop_content',
                                    callback: function () {
                                        $('#fullpage_backdrop_content').show();
                                        $('#fullpage_backdrop_indicator').hide();
                                        // Pachno.Helpers.MarkitUp($('#textarea.markuppable'));
                                        if (callback)
                                            setTimeout((callback)(), 300);
                                    }},
                                failure: {hide: 'fullpage_backdrop'}
                            });
                        } else {
                            console.error('Trying to show backdrop but no url');
                            console.trace();
                        }
                    })
                    .then(resolve);
            });
        },

        reset: (callback) => {
            $('body').css({'overflow': 'auto'});
            $('#fullpage_backdrop').hide();
            // Pachno.Core._resizeWatcher();
            if (callback) callback();
        }
    },

    tabSwitcher
};

const tabSwitchFromHash = function (menu) {
    let hash = window.location.hash;

    if (hash != undefined && hash.indexOf('tab_') == 1) {
        tabSwitcher(hash.substr(1), menu);
    }
};

const loadComponentOptions = function ($item) {
    new Promise(function (resolve, reject) {
        const $container = $item.parents('.configurable-components-container'),
            $optionsContainer = $('body').find('.configurable-component-options').first(),
            url = $item.data('options-url');

        $optionsContainer.html('<div><i class="fas fa-spin fa-spinner"></i></div>');
        $container.addClass('active');
        $container.find('.configurable-component').removeClass('active');
        $item.addClass('active');

        fetchHelper(url, {
            success: { update: '#' + $optionsContainer.attr('id') }
        }).then(resolve);
    });
};

const autoBackdropLink = function (event) {
    const $button = $(this);
    $button.prop('disabled', true);
    $button.addClass('disabled');
    $button.addClass('submitting');

    UI.Backdrop.show($button.data('url'))
        .then(() => {
            $button.removeProp('disabled');
            $button.removeClass('submitting');
            $button.removeClass('disabled');
        });
};

const autoSubmitForm = function (event) {
    const $form = $(this);
    event.preventDefault();
    return submitForm($form);
};

const submitForm = function ($form) {
    const url = $form.attr('action');
    let options;

    if ($form.data('update-container')) {
        if ($form.data('update-insert') !== undefined) {
            options = { success: { update: { element: $form.data('update-container'), insertion: true }}};
        } else if ($form.data('update-replace') !== undefined) {
            options = { success: { update: { element: $form.data('update-container'), replace: true }}};
        } else {
            options = { success: { update: $form.data('update-container') }};
        }
    }

    return formSubmitHelper(url, $form.attr('id'), options)
        .then(() => {
            $form.removeClass('submitting');
            $form.find('button[type=submit]').removeProp('disabled');

            if ($form.data('auto-close') !== undefined) {
                UI.Backdrop.reset();
            }
        });
};

$(document).ready(() => {
    const $body = $('body');

    $body.on('click', '.tab-switcher .tab-switcher-trigger', function () {
        const $tabSwitcher = $(this).parent('.tab-switcher');
        const $tab = $(this);
        const target = $tab.data('tab-target');

        tabSwitcher($tab, target, $tabSwitcher);
    });

    $body.on('click', '.fullpage_backdrop_content .closer', () => UI.Backdrop.reset());
    $body.on('click', '.trigger-backdrop', autoBackdropLink);

    $body.on('submit', 'form[data-simple-submit]', autoSubmitForm);

    $body.on('click', '.trigger-open-component', function(event) {
        event.preventDefault();
        event.stopPropagation();

        const $item = $(this).parents('.configurable-component');
        loadComponentOptions($item);
    });

    $body.on("click", ".collapser", function (e) {
        let collapser_item = $(this),

            is_visible = collapser_item.hasClass('active'),
            collapseItem = function (item) {
                let target = item.data('target');
                if (target) {
                    $(target).removeClass('active');
                }
                item.removeClass('active');
            },

            expandItem = function (item) {
                let target = item.data('target');
                if (target) {
                    $(target).addClass('active');
                }
                item.addClass('active');
            };

        if (collapser_item.data('exclusive')) {
            $('.collapser.active').each(function () {
                collapseItem(collapser_item);
            });
        }
        if (!is_visible) {
            expandItem(collapser_item);
        } else {
            collapseItem(collapser_item);
        }
        e.stopPropagation();
        e.preventDefault();
    });

    $body.on('blur', 'form[data-interactive-form] input, form[data-interactive-form] textarea', function (event) {
        const $form = $(this).parents('form');
        $form.addClass('submitting');
        event.preventDefault();
        submitForm($form)
            .then(() => {
                $form.removeClass('submitting');
            })
            .catch((error) => {
                console.error(error);
                $form.removeClass('submitting');
            });
    });
})

export default UI;
