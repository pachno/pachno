import $ from "jquery";
import {fetchHelper} from "./fetch";

const UI = {
    Message: {
        /**
         * Clears all popup messages from the effect queue
         */
        clear: () => {
            if ($('#pachno_successmessage').visible()) {
                $('#pachno_successmessage').fade({duration: 0.2});
            }
            if ($('#pachno_failuremessage').visible()) {
                $('#pachno_failuremessage').fade({duration: 0.2});
            }
        },

        /**
         * Shows an error popup message
         *
         * @param title string The title to show
         * @param content string Error details
         */
        error: (title, content) => {
            $('#pachno_failuremessage_title').html(title);
            $('#pachno_failuremessage_content').html(content);
            if ($('#pachno_successmessage').visible()) {
                Effect.Queues.get(Pachno.effect_queues.successmessage).each(function (effect) {
                    effect.cancel();
                });
                new Effect.Fade('pachno_successmessage', {queue: {position: 'end', scope: Pachno.effect_queues.successmessage, limit: 2}, duration: 0.2});
            }
            if ($('#pachno_failuremessage').visible()) {
                Effect.Queues.get(Pachno.effect_queues.failedmessage).each(function (effect) {
                    effect.cancel();
                });
                new Effect.Pulsate('pachno_failuremessage', {duration: 1, pulses: 4});
            } else {
                new Effect.Appear('pachno_failuremessage', {queue: {position: 'end', scope: Pachno.effect_queues.failedmessage, limit: 2}, duration: 0.2});
            }
            new Effect.Fade('pachno_failuremessage', {queue: {position: 'end', scope: Pachno.effect_queues.failedmessage, limit: 2}, delay: 30, duration: 0.2});
        },

        /**
         * Shows a "success"-style popup message
         *
         * @param title string The title to show
         * @param content string Message details
         */
        success: (title, content) => {
            $('#pachno_successmessage_title').html(title);
            $('#pachno_successmessage_content').html(content);
            if (title || content) {
                if ($('#pachno_failuremessage').visible()) {
                    Effect.Queues.get(Pachno.effect_queues.failedmessage).each(function (effect) {
                        effect.cancel();
                    });
                    new Effect.Fade('pachno_failuremessage', {queue: {position: 'end', scope: Pachno.effect_queues.failedmessage, limit: 2}, duration: 0.2});
                }
                if ($('#pachno_successmessage').visible()) {
                    Effect.Queues.get(Pachno.effect_queues.successmessage).each(function (effect) {
                        effect.cancel();
                    });
                    new Effect.Pulsate('pachno_successmessage', {duration: 1, pulses: 4});
                } else {
                    new Effect.Appear('pachno_successmessage', {queue: {position: 'end', scope: Pachno.effect_queues.successmessage, limit: 2}, duration: 0.2});
                }
                new Effect.Fade('pachno_successmessage', {queue: {position: 'end', scope: Pachno.effect_queues.successmessage, limit: 2}, delay: 10, duration: 0.2});
            } else if ($('#pachno_successmessage').visible()) {
                Effect.Queues.get(Pachno.effect_queues.successmessage).each(function (effect) {
                    effect.cancel();
                });
                new Effect.Fade('pachno_successmessage', {queue: {position: 'end', scope: Pachno.effect_queues.successmessage, limit: 2}, duration: 0.2});
            }
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
            $('#dialog_backdrop_modal').appear({duration: 0.2});
        },

        dismiss: () => {
            $('#dialog_backdrop_content').fade({duration: 0.2});
            $('#dialog_backdrop').fade({duration: 0.2});
        },

        dismissModal: () => {
            $('#dialog_backdrop_modal_content').fade({duration: 0.2});
            $('#dialog_backdrop_modal').fade({duration: 0.2});
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

    tabSwitcher: (visibletab, menu, change_hash) => {
        if (change_hash == null) change_hash = false;

        if ($(menu)) {
            $(menu).children().removeClass('selected');
            if ($(visibletab)) {
                $(visibletab).addClass('selected');
                $(menu + '_panes').children().hide();
            }
            if ($(visibletab + '_pane')) {
                $(visibletab + '_pane').show();
            }
            if (change_hash) {
                if (history.replaceState) {
                    window.history.replaceState(null, null, '#' + visibletab);
                }
                else {
                    window.location.hash = visibletab;
                }
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

export default UI;
