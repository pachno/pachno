import $ from "jquery";
import {fetchHelper} from "./fetch";

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
        }
    },
    Dialog: {
        show: (title, content, options) => {
            this.Message.clear();
            $('#dialog_title').html(title);
            $('#dialog_content').html(content);
            $('#dialog_yes').setAttribute('href', 'javascript:void(0)');
            $('#dialog_no').setAttribute('href', 'javascript:void(0)');
            $('#dialog_yes').stopObserving('click');
            $('#dialog_no').stopObserving('click');
            $('#dialog_yes').removeClass('disabled');
            $('#dialog_no').removeClass('disabled');
            if (options.yes.click) {
                $('#dialog_yes').on('click', options.yes.click);
            }
            if (options.yes.href) {
                $('#dialog_yes').setAttribute('href', options.yes.href);
            }
            if (options.no.click) {
                $('#dialog_no').on('click', options.no.click);
            }
            if (options.no.href) {
                $('#dialog_no').setAttribute('href', options.no.href);
            }
            $('#dialog_backdrop_content').show();
            $('#dialog_backdrop').appear({duration: 0.2});
        },

        showModal: (title, content) => {
            this.Message.clear();
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
            const showBackdrop = () => new Promise(resolve => {
                $('#fullpage_backdrop_content').hide();
                $('#fullpage_backdrop').show();
                $('body').css({'overflow': 'hidden'});
                $('#fullpage_backdrop_indicator').show();
                resolve();
            })

            showBackdrop()
                .then(() => {
                    if (url != undefined) {
                        fetchHelper(url, {
                            method: 'GET',
                            loading: {indicator: 'fullpage_backdrop_indicator'},
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
                    }
                });
        },

        reset: (callback) => {
            $('body').css({'overflow': 'auto'});
            $('#fullpage_backdrop').fade({duration: 0.2});
            // Pachno.Core._resizeWatcher();
            if (callback) callback();
        }
    },

    tabSwitcher: ($tab, target, $tabSwitcher, change_hash) => {
        if (!change_hash) {
            change_hash = false;
        }

        $tabSwitcher.children().removeClass('selected');
        $tab.addClass('selected');
        $($tabSwitcher.prop('id') + '_panes').children().each(function (pane) {
            const $pane = $(pane);
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
    },

    tabSwitchFromHash: (menu) => {
        let hash = window.location.hash;

        if (hash != undefined && hash.indexOf('tab_') == 1) {
            this.tabSwitcher(hash.substr(1), menu);
        }
    }
};

$(document).ready(() => {
    $('body').on('click', '.tab-switcher .tab-switcher-trigger', function () {
        const $tabSwitcher = $(this).parent('.tab-switcher');
        const $tab = $(this);
        const target = $tab.data('tab-target');

        UI.tabSwitcher($tab, target, $tabSwitcher);
    });
})

export default UI;
